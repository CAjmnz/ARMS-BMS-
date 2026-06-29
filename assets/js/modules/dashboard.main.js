(function () {

    $(document).ready(function () {

        // ─────────────────────────────────────────────
        // LOGS DATATABLE
        // ─────────────────────────────────────────────
        if ($('#logsTable').length) {
            $('#logsTable').DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50],
                order: [],
                columnDefs: [{ orderable: false, targets: 0 }],
                language: {
                    search:       'Search:',
                    lengthMenu:   'Show _MENU_ entries',
                    info:         'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty:    'Showing 0 to 0 of 0 entries',
                    infoFiltered: '(filtered from _MAX_ total)',
                    paginate: {
                        first:    'First',
                        last:     'Last',
                        next:     '&raquo;',
                        previous: '&laquo;'
                    }
                }
            });
        }

        // ─────────────────────────────────────────────
        // READ CHART DATA FROM PHP
        // ─────────────────────────────────────────────
        function getJsonData(id) {
            var el = document.getElementById(id);
            if (!el) return [];
            try { return JSON.parse(el.value); } catch (e) { return []; }
        }

        var statusData = getJsonData('chart_status_data');
        var roleData   = getJsonData('chart_role_data');
        var birthData  = getJsonData('chart_birth_data');

        var birthLabels = birthData.labels || [];
        var birthCounts = birthData.counts || [];

        // ─────────────────────────────────────────────
        // CHART HELPERS
        // ─────────────────────────────────────────────
        function createDonutChart(id, labels, data, colors) {
            var el = document.getElementById(id);
            if (!el) return;
            new Chart(el.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data:            data,
                        backgroundColor: colors,
                        borderWidth:     0,
                        hoverOffset:     6
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 12, font: { size: 11 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    var total = context.dataset.data
                                        .reduce(function (a, b) { return a + b; }, 0);
                                    var pct = total > 0
                                        ? Math.round((context.parsed / total) * 100) : 0;
                                    return ' ' + context.label + ': ' + context.parsed + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        function createBarChart(id, labels, data, label) {
            var el = document.getElementById(id);
            if (!el) return;
            new Chart(el.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label:           label || 'Count',
                        data:            data,
                        backgroundColor: 'rgba(99, 102, 241, 0.7)',
                        borderColor:     'rgba(99, 102, 241, 1)',
                        borderWidth:     1,
                        borderRadius:    4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        }

        // ─────────────────────────────────────────────
        // RENDER CHARTS
        // ─────────────────────────────────────────────
        createDonutChart(
            'statusDonutChart',
            ['Active', 'Inactive'],
            statusData,
            ['#16c784', '#f87171']
        );

        createDonutChart(
            'roleDonutChart',
            ['Admins', 'Regular'],
            roleData,
            ['#6366f1', '#f59e0b']
        );

        createBarChart(
            'logsBarChart',
            birthLabels,
            birthCounts,
            'Users'
        );

    });

})();