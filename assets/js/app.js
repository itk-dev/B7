require('../css/app.css');

import $ from 'jquery';
import survey from './survey';

$(document).ready(function () {

    let app = survey($);
    app.init();
});
