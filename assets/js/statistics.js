import Chart from 'chart.js';
import $ from 'jquery';
require('jquery-datetimepicker/build/jquery.datetimepicker.min.css');
import 'jquery-datetimepicker';

$.datetimepicker.setLocale('da');
$('#from').datetimepicker({
    dayOfWeekStart: 1,
    timepicker: false,
    format: 'd/m/Y'
});
$('#to').datetimepicker({
    dayOfWeekStart: 1,
    timepicker: false,
    format: 'd/m/Y'
});

let surveyCtx = document.getElementById('survey-statistics');
let surveyChart = new Chart(surveyCtx, {
    type: 'bar',
    data: {
        labels: ['Meget utilfreds', 'Utilfreds', 'Hverken/eller', 'Tilfreds', 'Meget tilfreds'],
        datasets: [
            {
                label: 'Denne periodes svar',
                backgroundColor: [
                    'rgb(54, 162, 235)',
                    'rgb(54, 162, 235)',
                    'rgb(54, 162, 235)',
                    'rgb(54, 162, 235)',
                    'rgb(54, 162, 235)'
                ],
                data: $('#survey-votes').data('votes')
            },
            {
                label: 'Svar indtil denne periode',
                backgroundColor: [
                    'rgb(75, 192, 192)',
                    'rgb(75, 192, 192)',
                    'rgb(75, 192, 192)',
                    'rgb(75, 192, 192)',
                    'rgb(75, 192, 192)'
                ],
                data: $('#survey-average-votes').data('votes')
            }
        ]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                },
                scaleLabel: {
                    display: true,
                    labelString: '% af samlet svar'
                }
            }]
        }
    }
});

let percentageCtx = document.getElementById('percentage');
let pieChart = new Chart(percentageCtx, {
    type: 'pie',
    data: {
        labels: ['Meget utilfreds', 'Utlfreds', 'Hverken/eller', 'Ttilfreds', 'Meget tilfreds'],
        datasets: [{
            label: '% of Votes',
            data: $('#survey-votes').data('votes'),
            backgroundColor: [
                'rgb(54, 162, 235)',
                'rgb(75, 192, 192)',
                'rgb(255, 159, 64)',
                'rgb(255, 205, 86)',
                'rgb(153, 102, 255)'
            ]
        }]
    }
});

let timeLineCtx = document.getElementById('time-line');
let lineChart = new Chart(timeLineCtx, {
    type: 'line',
    data: {
        labels: $('#all-votes-labels').data('labels'),
        datasets: [{
            label: 'Svar-gennemsnit',
            data: $('#all-votes').data('votes'),
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    min: 0,
                    max: 5,
                    stepSize: 1,
                    callback: function (value, index, values) {

                        switch (value) {
                            case 1:
                                return 'Meget utilfreds';
                            case 2:
                                return 'Utilfreds';
                            case 3:
                                return 'Hverken/eller';
                            case 4:
                                return 'Tilfreds';
                            case 5:
                                return 'Meget tilfreds';
                        }
                    }
                }
            }]
        }
    }
});