$(document).ready(function () {
    'use strict';

    var chartColors = [
        '#4e73df',
        '#1cc88a',
        '#f6c23e',
        '#e74a3b',
        '#36b9cc',
        '#858796',
        '#5a5c69'
    ];

    var categoryCanvas = document.getElementById('categoryChart');

    if (categoryCanvas) {
        $.getJSON(BASE_URL + 'dashboard/chart_categories')
            .done(function (res) {
                new Chart(categoryCanvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: res.labels,
                        datasets: [{
                            data: res.values,
                            backgroundColor: chartColors,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'bottom'
                        }
                    }
                });
            })
            .fail(function (xhr) {
                console.error('Unable to load category chart:', xhr.responseText);
            });
    }

    var trendCanvas = document.getElementById('trendChart');

    if (trendCanvas) {
        $.getJSON(BASE_URL + 'dashboard/chart_trend')
            .done(function (res) {
                new Chart(trendCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: res.labels,
                        datasets: [{
                            label: 'Borrowings',
                            data: res.values,
                            backgroundColor: '#10a66f',
                            borderColor: '#086a4d',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: false
                        },
                        scales: {
                            xAxes: [{
                                gridLines: {
                                    display: false
                                }
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    precision: 0,
                                    stepSize: 1
                                },
                                gridLines: {
                                    color: '#e1ebe6'
                                }
                            }]
                        }
                    }
                });
            })
            .fail(function (xhr) {
                console.error('Unable to load borrowing trend:', xhr.responseText);
            });
    }

    var mostBorrowedCanvas = document.getElementById('mostBorrowedChart');

    if (mostBorrowedCanvas) {
        $.getJSON(BASE_URL + 'dashboard/chart_most_borrowed')
            .done(function (res) {
                new Chart(mostBorrowedCanvas.getContext('2d'), {
                    type: 'horizontalBar',
                    data: {
                        labels: res.labels,
                        datasets: [{
                            label: 'Times Borrowed',
                            data: res.values,
                            backgroundColor: '#10a66f',
                            borderColor: '#086a4d',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: false
                        },
                        scales: {
                            xAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                    precision: 0,
                                    stepSize: 1
                                },
                                gridLines: {
                                    color: '#e1ebe6'
                                }
                            }],
                            yAxes: [{
                                gridLines: {
                                    display: false
                                }
                            }]
                        },
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    return tooltipItem.xLabel + ' time(s) borrowed';
                                }
                            }
                        }
                    }
                });
            })
            .fail(function (xhr) {
                console.error('Unable to load most borrowed chart:', xhr.responseText);
            });
    }

    /* Due-today rows open the notification page. */
    $('.dashboard-clickable-row').on('click', function () {
        var target = $(this).data('href');

        if (target) {
            window.location.href = target;
        }
    });

    $('.dashboard-clickable-row').on('keydown', function (event) {
        if (event.which === 13 || event.which === 32) {
            event.preventDefault();
            $(this).trigger('click');
        }
    });
});
