
export default function ($) {

    let app = {

        /**
         * Initialization of the app.
         */
        init: function () {

            // Find container divs.
            let pageMainDiv = $("#page_main");
            let pageChoiceDiv = $("#page_choice");
            let pageThanksDiv = $("#page_thanks");

            // Get the content of containers.
            this.pageMain = pageMainDiv.html();
            this.pageChoice = pageChoiceDiv.html();
            this.pageThanks = pageThanksDiv.html();

            // Remove the containers.
            pageMainDiv.remove();
            pageChoiceDiv.remove();
            pageThanksDiv.remove();

            // Avoid touchmove events (scrolling).
            $(document).on('touchmove', function (e) {
                e.preventDefault();
            });

            app.showMainPage();
        },

        /**
         * Shows the main page and registers event listeners.
         */
        showMainPage: function () {
            // Clear timers and event handlers.
            app.clearClickHandlers();
            clearTimeout(app.timer);

            // Set check for update timer.
            app.timer = setTimeout(function () {
                app.testVersion(
                    app.version,
                    function () {
                        window.location.replace("index.html");
                    },
                    function () {
                        app.showMainPage();
                    }
                );
            }, 3600000);

            // Change the HTML content.
            $("#main").html(app.pageMain);
            $(".footer").show();

            // Setup the event listeners.
            $("#smiley1").on("touchstart click", function (e) {
                e.stopPropagation();
                e.preventDefault();
                app.showWhatPage(1);
            });
            $("#smiley2").on("touchstart click", function (e) {
                e.stopPropagation();
                e.preventDefault();
                app.showWhatPage(2);
            });
            $("#smiley3").on("touchstart click", function (e) {
                e.stopPropagation();
                e.preventDefault();
                app.showWhatPage(3);
            });
            $("#smiley4").on("touchstart click", function (e) {
                e.stopPropagation();
                e.preventDefault();
                app.showWhatPage(4);
            });
            $("#smiley5").on("touchstart click", function (e) {
                e.stopPropagation();
                e.preventDefault();
                app.showWhatPage(5);
            });
        },

        /**
         * Shows the what page, registers event listeners and timer.
         * @param nSmiley The selected smiley from main page.
         */
        showWhatPage: function (nSmiley) {
            // Clear timers and event handlers.
            app.clearClickHandlers();
            clearTimeout(app.timer);

            // Change the HTML content.
            $(".main").html(app.pageChoice);

            $(".footer").show();

            // Setup event listeners
            $("#choice1").on("touchstart click", function (e) {
                e.stopPropagation();
                e.preventDefault();
                app.showResultPage(nSmiley, 1);
            });
            $("#choice2").on("touchstart click", function (e) {
                e.stopPropagation();
                e.preventDefault();
                app.showResultPage(nSmiley, 2);
            });
            $("#choice3").on("touchstart click", function (e) {
                e.stopPropagation();
                e.preventDefault();
                app.showResultPage(nSmiley, 3);
            });

            // Change the text according to the selected smiley.
            $("#positive, #negative").hide();
            if (nSmiley > 3) {
                $("#positive").show();
            } else {
                $("#negative").show();
            }

            // Show the selected smiley and hide the others.
            $(".img_smiley").each(function (index) {
                if (index == 4 - (nSmiley - 1)) {
                    $(this).addClass("img_smiley_selected");
                } else {
                    $(this).addClass("img_smiley_hide");
                }
            });

            // Set timeout for page. If user has not selected a reason for the smiley before the timeout, commit answer with
            // what set to 0.
            app.timer = setTimeout(function () {
                let datetime = (new Date()).getTime();

                // Post data to server
                app.sendResultToServer(nSmiley, 0, datetime, function () {
                    // Test if the connection to the server is up
                    app.ping(
                        // If the connection is up, commit from local storage.
                        function () {
                            app.commitEntriesFromLocalStorage(function () {
                                app.showMainPage();
                            });
                        },
                        // If the connections is not up, go to main page.
                        function () {
                            app.showMainPage();
                        }
                    );
                });
            }, 20000);
        },

        /**
         * Shows the result page.
         * @param nSmiley the selected smiley.
         * @param nWhat   the reason for the smiley.
         */
        showResultPage: function (nSmiley, nWhat) {
            // Clear event handlers and timer.
            app.clearClickHandlers();
            clearTimeout(app.timer);

            // Set the HTML content.
            $("#main").html(app.pageThanks);

            $(".footer").show();

            let datetime = (new Date()).getTime();

            // Post data to server
            app.sendResultToServer(nSmiley, nWhat, datetime, function () {
                // Test if the connection to the server is up
                app.ping(
                    // If the connection is up, commit from local storage.
                    function () {
                        app.commitEntriesFromLocalStorage(function () {
                            app.timer = setTimeout(function () {
                                app.showMainPage();
                            }, 4000);
                        });},
                    // If the connections is not up, go to main page.
                    function () {
                        app.timer = setTimeout(function () {
                            app.showMainPage();
                        }, 4000);
                    }
                );
            });
        },

        /**
         * Sends a single result to the server.
         * @param smiley the selected smiley.
         * @param what the selected reason for the smiley.
         * @param datetime the time of the result.
         * @param callback the function to call when the function is done.
         */
        sendResultToServer: function (smiley, what, datetime, callback) {
            // Send the result to the server.
            $.ajax({
                url: window.location.pathname + "/reply",
                type: "POST",
                data: {action: "result", smiley: smiley, what: what, datetime: datetime},
                dataType: "json"
            })
                .done(function (response, textStatus, jqXHR) {
                    let resp = JSON.parse(JSON.stringify(response));

                    if (resp.result != "ok") {
                        // If an error occurred, save the entry to local storage.
                        app.saveEntryToLocalStorage(smiley, what, datetime);
                    }
                    callback();
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    // When the commit fails, save to local storage.
                    app.saveEntryToLocalStorage(smiley, what, datetime);
                    callback();
                });
        },

        /**
         * Recursively commit the list.
         * @param list the list of results to commit.
         * @param callback the function to call when the list is empty.
         */
        // Commit single entry, recursive until empty list
        commitListRecurse: function (list, callback) {
            // The stop condition is an empty list.
            if (list.length > 0) {
                // Pop top element of list.
                let ent = list.pop();

                // Commit the entry and then recursively commit the rest of the list.
                app.sendResultToServer(ent.smiley, ent.what, ent.datetime, function () {
                    app.commitListRecurse(list, callback);
                });
            } else {
                callback();
            }
        },

        /**
         * Commit the results that have not been sent.
         * @param callback the function to call when the list is empty.
         */
        commitEntriesFromLocalStorage: function (callback) {
            if (typeof(Storage) !== "undefined") {
                // Get local storage entries
                let entries = JSON.parse(localStorage.getItem("entries"));

                // Clear local storage entries
                localStorage.setItem("entries", JSON.stringify([]));

                // Recursively commit the list
                if (entries !== null) {
                    app.commitListRecurse(entries, callback);
                } else {
                    callback();
                }
            }
        },

        /**
         * Save a result to local storage.
         * @param smiley the selected smiley.
         * @param what the reason for the selected smiley.
         * @param datetime the time of the result.
         */
        // Save an entry to local storage
        saveEntryToLocalStorage: function (smiley, what, datetime) {
            if (typeof(Storage) !== "undefined") {
                let ent = {
                    smiley: smiley,
                    what: what,
                    datetime: datetime
                }

                let entries = JSON.parse(localStorage.getItem("entries"));

                if (entries == null) {
                    entries = [];
                }
                entries.push(ent);

                localStorage.setItem("entries", JSON.stringify(entries));
            }
        },

        /**
         * Ping the server.
         * @param success the function to call if the server answers.
         * @param failure the function to call if the ping failed.
         */
        ping: function (success, failure) {
            $.ajax({
                url: window.location,
                type: "GET"
            }).done(function (response, textStatus, jqXHR) {
                    success();
            }).fail(function (jqXHR, textStatus, errorThrown) {
                    failure();
            });
        },

        /**
         * Clear the event handlers for the smileys and the whats.
         */
        clearClickHandlers: function () {
            $("#smiley1 #smiley2 #smiley3 #smiley4 #smiley5 #choice1 #choice2 #choice3").off();
        }
    };

    return app;
}