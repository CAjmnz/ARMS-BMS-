$(document).ready(function () {

    if ($('#categoryChart').length === 0) {
        return;
    }

    // Category breakdown chart
    $.get(BASE_URL + 'dashboard/chart_categories', function (res) {
        var ctx = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: res.labels,
                datasets: [{
                    data: res.values,
                    backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b', '#36b9cc', '#858796', '#5a5c69']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }, 'json');

    // Borrowing trend chart
    $.get(BASE_URL + 'dashboard/chart_trend', function (res) {
        var ctx = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: res.labels,
                datasets: [{
                    label: 'Borrowings',
                    data: res.values,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78,115,223,0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }, 'json');

});