<!-- Tailwind & FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
    body {
    background-color: rgba(245, 248, 250, 1) !important;
    }

    .dashboard-box {
        background: white;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    .input-date {
        appearance: none;
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 14px;
        color: #374151;
        transition: all 0.2s ease-in-out;
        width: 130px;
    }

    .input-date:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 1px #2563eb;
    }

    .refresh-btn {
        background: none;
        border: none;
        color: #1e3a8a;
        font-size: 16px;
        padding: 6px 10px;
        border-radius: 6px;
        transition: background 0.2s ease;
    }

    .refresh-btn:hover {
        background-color: #e5e7eb;
    }

    .ticket-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 20px;
    }

    .ticket-summary .card {
        flex: 1 1 200px;
        max-width: 220px;
        min-width: 150px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -4px rgba(0,0,0,0.03);
        transition: transform 0.2s ease;
    }

    .ticket-summary .card:hover {
        transform: translateY(-2px);
    }

    .card-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-text h2 {
        margin: 0;
        font-size: 28px;
        color: #000;
    }

    .card-label {
        font-size: 14px;
        color: #555;
        margin-top: 4px;
    }

    .card-icon-waiting { font-size: 20px; color: #363e43ff; }
    .card-icon-open { font-size: 20px; color: orange; }
    .card-icon-progress { font-size: 20px; color: #00AAB0; }
    .card-icon-ticket { font-size: 20px; color: #4881AD; }
    .card-icon-done { font-size: 20px; color: #1AA119; }

    .chart-section {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 30px;
        justify-content: center;
    }

    .chart-container {
        flex: 1 1 100%;
        min-width: 300px;
        max-width: 600px;
        background: #fff;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        box-sizing: border-box;
    }

    .date-filter {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 20px;
        align-items: flex-start;
    }

    .filter-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
</style>

<div class="dashboard-box">
    <!-- Filter Tanggal -->
    <div class="date-filter">
        <label class="text-lg font-semibold text-gray-700 mb-1">FILTER TANGGAL</label>
        <form method="get" action="">
            <div class="filter-group">
                <input type="date" name="start_date" value="<?= $startDate ?>" class="input-date" required>
                <span class="mx-1 text-gray-500">-</span>
                <input type="date" name="end_date" value="<?= $endDate ?>" class="input-date" required>
                <button type="submit" class="refresh-btn" title="Filter">
                    <i class="fa-solid fa-filter"></i>
                </button>
                <a href="?start_date=<?= date('Y-m-d') ?>&end_date=<?= date('Y-m-d') ?>" class="refresh-btn" title="Hari Ini">
                    <i class="fa-solid fa-rotate-right"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Statistik Card -->
    <div class="ticket-summary">
        <div class="card">
            <div class="card-content">
                <div class="card-text">
                    <h2><?= $totalWaiting ?></h2>
                    <div class="card-label">Ticket Waiting</div>
                </div>
                <div class="card-icon-waiting"><i class="fa-solid fa-ticket"></i></div>
            </div>
        </div>
        <div class="card">
            <div class="card-content">
                <div class="card-text">
                    <h2><?= $totalOpen ?></h2>
                    <div class="card-label">Ticket Open</div>
                </div>
                <div class="card-icon-open"><i class="fa-solid fa-ticket"></i></div>
            </div>
        </div>
        <div class="card">
            <div class="card-content">
                <div class="card-text">
                    <h2><?= $totalInProgress ?></h2>
                    <div class="card-label">Ticket In Progress</div>
                </div>
                <div class="card-icon-progress"><i class="fa-solid fa-ticket"></i></div>
            </div>
        </div>
        <div class="card">
            <div class="card-content">
                <div class="card-text">
                    <h2><?= $totalDone ?></h2>
                    <div class="card-label">Ticket Done</div>
                </div>
                <div class="card-icon-done"><i class="fa-solid fa-ticket"></i></div>
            </div>
        </div>
        <div class="card">
            <div class="card-content">
                <div class="card-text">
                    <h2><?= $totalTicket ?></h2>
                    <div class="card-label">Total Ticket</div>
                </div>
                <div class="card-icon-ticket"><i class="fa-solid fa-ticket"></i></div>
            </div>
        </div>
    </div>

    <!-- Grafik -->
    <div class="chart-section">
        <div class="chart-container" style="display: flex; gap: 20px;">
            <div style="flex: 1;">
                <canvas id="donutChart" width="300" height="300"></canvas>
            </div>
            <div id="customDonutLegend" style="display: flex; flex-direction: column; justify-content: center; gap: 8px;"></div>
        </div>

        <div class="chart-container">
            <canvas id="barChart"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
    const donutCtx = document.getElementById('donutChart').getContext('2d');
    const donutData = [<?= $totalWaiting ?>, <?= $totalOpen ?>, <?= $totalInProgress ?>, <?= $totalDone ?>];
    const donutLabels = ['Waiting', 'Open', 'In Progress', 'Done'];
    const donutColors = ['#28333bff', '#FF1C71', '#417FA9', '#27465E'];
    const totalTickets = donutData.reduce((a, b) => a + b, 0);

    const centerTextPlugin = {
        id: 'centerText',
        beforeDraw(chart) {
            const { width, height } = chart;
            const ctx = chart.ctx;
            ctx.restore();
            ctx.font = 'bold 22px sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#000';
            ctx.fillText(totalTickets, width / 2, height / 2 - 10);
            ctx.font = '14px sans-serif';
            ctx.fillText('Ticket', width / 2, height / 2 + 15);
            ctx.save();
        }
    };

    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: donutLabels,
            datasets: [{
                data: donutData,
                backgroundColor: donutColors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: false,
            plugins: {
                legend: { display: false },
                datalabels: {
                    color: '#ffffff',
                    formatter: (value, ctx) => {
                        const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        return ((value / total) * 100).toFixed(1) + '%';
                    }
                }
            },
            cutout: '70%'
        },
        plugins: [ChartDataLabels, centerTextPlugin]
    });

    // Legend Donat
    document.getElementById('customDonutLegend').innerHTML = donutLabels.map((label, i) => `
        <div style="display: flex; align-items: center;">
            <div style="width: 12px; height: 12px; background: ${donutColors[i]}; margin-right: 6px; border-radius: 3px;"></div>
            <span>${label}</span>
        </div>
    `).join('');

    // Bar Chart
    new Chart(document.getElementById('barChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Ticket Mandiri', 'RoomChat', 'WhatsApp'],
            datasets: [{
                label: 'Jumlah Pengaduan',
                data: [<?= $viaMandiri ?>, <?= $viaRoomchat ?>, <?= $viaWA ?>],
                backgroundColor: ['#00AAB0', '#FF1C71', '#27465E']
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Analisis Perbandingan Media Pengaduan'
                }
            }
        }
    });
</script>
