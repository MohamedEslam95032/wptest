(function ($) {
    'use strict';

    /* ------------------------------------------------------------------------ */
    /*  24 Hours Visits Chart
     /* ------------------------------------------------------------------------ */
    var visits_chart_24h = $('#visits-chart-24h');

    if (visits_chart_24h.length > 0) {
        var labels = visits_chart_24h.data('labels');
        var views = visits_chart_24h.data('views');
        var unique = visits_chart_24h.data('unique');
        var visit_label = visits_chart_24h.data('visit-label');
        var unique_label = visits_chart_24h.data('unique-label');

        var ctx = document
            .getElementById('visits-chart-24h')
            .getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: visit_label,
                        data: views,
                        backgroundColor: ['rgba(255, 99, 132, 0.1)'],
                        borderColor: ['rgba(255, 99, 132, 1)'],
                        borderWidth: 2,
                    },
                    {
                        label: unique_label,
                        data: unique,
                        backgroundColor: ['rgba(54, 162, 235, 0.3)'],
                        borderColor: [
                            //'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                        ],
                        borderWidth: 2,
                    },
                ],
            },
            options: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                    },
                },
                scales: {
                    yAxes: [
                        {
                            ticks: {
                                beginAtZero: true,
                            },
                            gridLines: {
                                display: true,
                            },
                        },
                    ],
                    xAxes: [
                        {
                            gridLines: {
                                display: false,
                            },
                        },
                    ],
                },
                tooltips: {
                    callbacks: {
                        labelColor: function (tooltipItem, chart) {
                            if (tooltipItem.datasetIndex === 0) {
                                // For 'views_7d' dataset
                                return {
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    backgroundColor:
                                        'rgba(255, 99, 132, 1)',
                                };
                            } else if (tooltipItem.datasetIndex === 1) {
                                // For 'unique_7d' dataset
                                return {
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    backgroundColor:
                                        'rgba(54, 162, 235, 1)',
                                };
                            }
                        },
                        labelTextColor: function (tooltipItem, chart) {
                            return '#fff';
                        },
                    },
                },
            },
        });
    }



        /* ------------------------------------------------------------------------ */
        /*  7 days Visits Chart
         /* ------------------------------------------------------------------------ */
        var visits_chart_7d = $('#visits-chart-7d');

        if (visits_chart_7d.length > 0) {
            var labels_7d = visits_chart_7d.data('labels');
            var views_7d = visits_chart_7d.data('views');
            var unique_7d = visits_chart_7d.data('unique');
            var visit_label_7d = visits_chart_7d.data('visit-label');
            var unique_label_7d = visits_chart_7d.data('unique-label');

            var ctx = document
                .getElementById('visits-chart-7d')
                .getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels_7d,
                    datasets: [
                        {
                            label: visit_label_7d,
                            data: views_7d,
                            backgroundColor: ['rgba(255, 99, 132, 0.1)'],
                            borderColor: ['rgba(255, 99, 132, 1)'],
                            borderWidth: 2,
                        },
                        {
                            label: unique_label_7d,
                            data: unique_7d,
                            backgroundColor: ['rgba(54, 162, 235, 0.3)'],
                            borderColor: [
                                //'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                            ],
                            borderWidth: 2,
                        },
                    ],
                },
                options: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                        },
                    },
                    scales: {
                        yAxes: [
                            {
                                ticks: {
                                    beginAtZero: true,
                                },
                                gridLines: {
                                    display: true,
                                },
                            },
                        ],
                        xAxes: [
                            {
                                gridLines: {
                                    display: false,
                                },
                            },
                        ],
                    },
                    tooltips: {
                        callbacks: {
                            labelColor: function (tooltipItem, chart) {
                                if (tooltipItem.datasetIndex === 0) {
                                    // For 'views_7d' dataset
                                    return {
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        backgroundColor:
                                            'rgba(255, 99, 132, 1)',
                                    };
                                } else if (tooltipItem.datasetIndex === 1) {
                                    // For 'unique_7d' dataset
                                    return {
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        backgroundColor:
                                            'rgba(54, 162, 235, 1)',
                                    };
                                }
                            },
                            labelTextColor: function (tooltipItem, chart) {
                                return '#fff';
                            },
                        },
                    },
                },
            });
        }

        /* ------------------------------------------------------------------------ */
        /*  7 days Visits Chart
         /* ------------------------------------------------------------------------ */
        var visits_chart_30d = $('#visits-chart-30d');

        if (visits_chart_30d.length > 0) {
            var labels_30d = visits_chart_30d.data('labels');
            var views_30d = visits_chart_30d.data('views');
            var unique_30d = visits_chart_30d.data('unique');
            var visit_label_30d = visits_chart_30d.data('visit-label');
            var unique_label_30d = visits_chart_30d.data('unique-label');

            var ctx = document
                .getElementById('visits-chart-30d')
                .getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels_30d,
                    datasets: [
                        {
                            label: visit_label_30d,
                            data: views_30d,
                            backgroundColor: ['rgba(255, 99, 132, 0.1)'],
                            borderColor: ['rgba(255, 99, 132, 1)'],
                            borderWidth: 2,
                        },
                        {
                            label: unique_label_30d,
                            data: unique_30d,
                            backgroundColor: ['rgba(54, 162, 235, 0.3)'],
                            borderColor: [
                                //'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                            ],
                            borderWidth: 2,
                        },
                    ],
                },
                options: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                        },
                    },
                    scales: {
                        yAxes: [
                            {
                                ticks: {
                                    beginAtZero: true,
                                },
                                gridLines: {
                                    display: true,
                                },
                            },
                        ],
                        xAxes: [
                            {
                                gridLines: {
                                    display: false,
                                },
                            },
                        ],
                    },
                    tooltips: {
                        callbacks: {
                            labelColor: function (tooltipItem, chart) {
                                if (tooltipItem.datasetIndex === 0) {
                                    // For 'views_7d' dataset
                                    return {
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        backgroundColor:
                                            'rgba(255, 99, 132, 1)',
                                    };
                                } else if (tooltipItem.datasetIndex === 1) {
                                    // For 'unique_7d' dataset
                                    return {
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        backgroundColor:
                                            'rgba(54, 162, 235, 1)',
                                    };
                                }
                            },
                            labelTextColor: function (tooltipItem, chart) {
                                return '#fff';
                            },
                        },
                    },
                },
            });
        }

        /* ------------------------------------------------------------------------ */
        /*  Top Browsers
         /* ------------------------------------------------------------------------ */
        if ($('#top-browsers-doughnut-chart').length > 0) {
            var chartData = $('#top-browsers-doughnut-chart').data('chart');
            var ctx = document
                .getElementById('top-browsers-doughnut-chart')
                .getContext('2d');
            var myDoughnutChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [
                        {
                            data: chartData,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.5)',
                                'rgba(54, 162, 235, 0.5)',
                                'rgba(255, 206, 86, 0.5)',
                                'rgba(75, 192, 192, 0.5)',
                            ],
                            borderColor: [
                                'rgba(255 ,99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                            ],
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    cutoutPercentage: 60,
                    responsive: false,
                    tooltips: false,
                },
            });
        }

        /* ------------------------------------------------------------------------ */
        /*  Top Devices
         /* ------------------------------------------------------------------------ */
        if ($('#devices-doughnut-chart').length > 0) {
            var chartData = $('#devices-doughnut-chart').data('chart');
            var ctx = document
                .getElementById('devices-doughnut-chart')
                .getContext('2d');
            var myDoughnutChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [
                        {
                            data: chartData,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.5)',
                                'rgba(54, 162, 235, 0.5)',
                                'rgba(255, 206, 86, 0.5)',
                                'rgba(75, 192, 192, 0.5)',
                            ],
                            borderColor: [
                                'rgba(255 ,99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                            ],
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    cutoutPercentage: 60,
                    responsive: false,
                    tooltips: false,
                },
            });
        }

        /* ------------------------------------------------------------------------ */
        /*  Top Countries
         /* ------------------------------------------------------------------------ */
        if ($('#top-countries-doughnut-chart').length > 0) {
            var chartData = $('#top-countries-doughnut-chart').data('chart');
            var ctx = document
                .getElementById('top-countries-doughnut-chart')
                .getContext('2d');
            var myDoughnutChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [
                        {
                            data: chartData,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.5)',
                                'rgba(54, 162, 235, 0.5)',
                                'rgba(255, 206, 86, 0.5)',
                                'rgba(75, 192, 192, 0.5)',
                            ],
                            borderColor: [
                                'rgba(255 ,99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                            ],
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    cutoutPercentage: 60,
                    responsive: false,
                    tooltips: false,
                },
            });
        }

        /* ------------------------------------------------------------------------ */
        /*  Top Platforms
         /* ------------------------------------------------------------------------ */
        if ($('#top-platforms-doughnut-chart').length > 0) {
            var chartData = $('#top-platforms-doughnut-chart').data('chart');
            var ctx = document
                .getElementById('top-platforms-doughnut-chart')
                .getContext('2d');
            var myDoughnutChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [
                        {
                            data: chartData,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.5)',
                                'rgba(54, 162, 235, 0.5)',
                                'rgba(255, 206, 86, 0.5)',
                                'rgba(75, 192, 192, 0.5)',
                            ],
                            borderColor: [
                                'rgba(255 ,99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                            ],
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    cutoutPercentage: 60,
                    responsive: false,
                    tooltips: false,
                },
            });
        }
    
})(jQuery);
