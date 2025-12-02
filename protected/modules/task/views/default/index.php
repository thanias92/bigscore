<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use yii\helpers\Url;
use yii\helpers\StringHelper; // Tambahkan ini jika Anda ingin membatasi panjang teks di sini

$this->title = 'Task Management';
$this->registerCssFile('https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<style>
    .border-card {
        border: 1px solid #E6EDFF !important;
    }

    #myDonutChart {
        width: 150px;
        height: 150px;
    }

    .chart-container {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-direction: row;
    }

    .canvas-wrapper {
        position: relative;
    }

    .legend-wrapper {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        height: 200px;
    }

    body {
        background-color: rgba(245, 248, 250, 1) !important;
    }
</style>

<div id="task" class="col-md-12 ">
    <div class="w-full bg-white p-4 px-5 rounded-xl">
        <div class="w-full flex flex-row justify-between items-center mb-5">
            <h3 class="">TASK</h3>
            <div class="flex flex-row items-center gap-3 ">
                <div>Filter Customer</div>
                <form method="get" action="<?= Url::to(['index#task']) ?>">
                    <select name="task_customer_id" onchange="this.form.submit();" id="filter-nama-pelanggan" style="padding: 5px; width: 220px; border-radius: 5px; font-size: 0.9rem;" class="w-[180px] px-2 py-2 rounded-xl border-gray-300 border-1">
                        <option value="">All</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= htmlspecialchars($customer->customer_id) ?>"
                                <?= $task_customer_id == $customer->customer_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($customer->customer_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

            </div>
        </div>
        <div class="flex flex-col items-start gap-4">
            <div class="col-md-12">
                <div class="grid grid-cols-5 sm:grid-cols-1 justify-between gap-4">
                    <div class="w-full">
                        <a href="/task/task/index" target="_blank">
                            <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                                <div class="flex flex-row justify-between w-full">
                                    <div class="text-gray-900 font-normal" style="font-size:32px;">
                                        <?= $task['totalTask'] ?>
                                    </div>
                                    <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                        <img src="/img/akun.svg" class="w-12" alt="">
                                    </div>
                                </div>
                                <div class="w-full text-gray-900" style="font-size:18px;margin-top:-8px;">
                                    Total Task
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="w-full">
                        <a href="/task/task/index?status=waiting" target="_blank">
                            <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                                <div class="flex flex-row justify-between w-full">
                                    <div class="text-gray-900 font-normal" style="font-size:32px;">
                                        <?= $task['taskWaiting'] ?>
                                    </div>
                                    <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                        <img src="/img/akun.svg" class="w-12" alt="">
                                    </div>
                                </div>
                                <div class="w-full text-gray-900" style="font-size:18px;margin-top:-8px;">
                                    Task Waiting
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="w-full">
                        <a href="/task/task/index?status=Open" target="_blank">
                            <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                                <div class="flex flex-row justify-between w-full">
                                    <div class="text-gray-900 font-normal" style="font-size:32px;">
                                        <?= $task['taskOpen'] ?>
                                    </div>
                                    <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                        <img src="/img/akun.svg" class="w-12" alt="">
                                    </div>
                                </div>
                                <div class="w-full text-gray-900" style="font-size:18px;margin-top:-8px;">
                                    Task Open
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="w-full">
                        <a href="/task/task/index?status=Progress" target="_blank">
                            <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                                <div class="flex flex-row justify-between w-full">
                                    <div class="text-gray-900 font-normal" style="font-size:32px;">
                                        <?= $task['taskInProgress'] ?>
                                    </div>
                                    <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                        <img src="/img/akun.svg" class="w-12" alt="">
                                    </div>
                                </div>
                                <div class="w-full text-gray-900" style="font-size:18px;margin-top:-8px;">
                                    Task In Progress
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="w-full">
                        <a href="/task/task/index?status=Done" target="_blank">
                            <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                                <div class="flex flex-row justify-between w-full">
                                    <div class="text-gray-900 font-normal" style="font-size:32px;">
                                        <?= $task['taskDone'] ?>
                                    </div>
                                    <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                        <img src="/img/akun.svg" class="w-12" alt="">
                                    </div>
                                </div>
                                <div class="w-full text-gray-900" style="font-size:18px;margin-top:-8px;">
                                    Task Done
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="w-full">
                        <a href="/task/task/index?status=Merge" target="_blank">
                            <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                                <div class="flex flex-row justify-between w-full">
                                    <div class="text-gray-900 font-normal" style="font-size:32px;">
                                        <?= $task['taskLate'] ?>
                                    </div>
                                    <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                        <img src="/img/akun.svg" class="w-12" alt="">
                                    </div>
                                </div>
                                <div class="w-full text-gray-900" style="font-size:18px;margin-top:-8px;">
                                    Task Late
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <div class="flex flex-col w-full px-4 py-2 border gap-4 rounded-lg justify-start border-card">
                            <div style="font-size:16px;" class="text-gray-900">Task Progress Overview</div>
                            <div class="chart-container">
                                <div class="canvas-wrapper">
                                    <canvas id="myDonutChart" width="200" height="200"></canvas>
                                </div>
                                <div class="legend-wrapper" id="customLegend"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <?=
                        $this->render('tabel-task', compact('task'));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--hapus Ticket-->
<!-- <div id="ticket" class="col-md-12 mt-4 mb-4">
    <div class="w-full bg-white p-4 px-5 rounded-xl">
        <div class="w-full flex flex-row justify-between items-center mb-5">
            <h3 class="">TICKET</h3>
            <div class="flex flex-row items-center gap-3 ">
                <div>Filter Customer</div>
                <form method="get" action="<?= Url::to(['index#ticket']) ?>">
                    <select name="ticket_customer_id" onchange="this.form.submit();" id="filter-nama-pelanggan" style="width:180px;" class="w-[180px] px-2 py-2 rounded-xl border-gray-300 border-1">
                        <option value="">All</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= htmlspecialchars($customer->customer_id) ?>"
                                <?= $ticket_customer_id == $customer->customer_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($customer->customer_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

            </div>
        </div>
        <div class="flex flex-col items-start gap-4">

            <div class="col-md-12">
                <div class="grid grid-cols-5 sm:grid-cols-1 justify-between gap-4">
                    <div class="w-full">
                        <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                            <div class="flex flex-row justify-between w-full">
                                <div class="text-gray-900 font-normal" style="font-size:32px;">
                                    <?= $ticket['totalTicket'] ?>
                                </div>
                                <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                    <img src="/img/akun.svg" class="w-12" alt="">
                                </div>
                            </div>
                            <div class="w-full text-gray-900" style="font-size:18px;margin-top:-8px;">
                                Total Ticket
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                            <div class="flex flex-row justify-between w-full">
                                <div class="text-gray-900 font-normal" style="font-size:32px;">
                                    <?= $ticket['ticketOpen'] ?>
                                </div>
                                <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                    <img src="/img/akun.svg" class="w-12" alt="">
                                </div>
                            </div>
                            <div class="w-full text-gray-900" style="font-size:18px;margin-top:-8px;">
                                Ticket Open
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                            <div class="flex flex-row justify-between w-full">
                                <div class="text-gray-900 font-normal" style="font-size:32px;">
                                    <?= $ticket['ticketInProgress'] ?>
                                </div>
                                <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                    <img src="/img/akun.svg" class="w-12" alt="">
                                </div>
                            </div>
                            <div class="w-full text-gray-900" style="font-size:18px;margin-top:-8px;">
                                Ticket In Progress
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                            <div class="flex flex-row justify-between w-full">
                                <div class="text-gray-900 font-normal" style="font-size:32px;">
                                    <?= $ticket['ticketDone'] ?>
                                </div>
                                <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                    <img src="/img/akun.svg" class="w-12" alt="">
                                </div>
                            </div>
                            <div class="w-full text-gray-900" style="font-size:18px;margin-top:-8px;">
                                Ticket Done
                            </div>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                            <div class="flex flex-row justify-between w-full">
                                <div class="text-gray-900 font-normal" style="font-size:32px;">
                                    <?= $ticket['ticketLate'] ?>
                                </div>
                                <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                    <img src="/img/akun.svg" class="w-12" alt="">
                                </div>
                            </div>
                            <div class="w-full text-gray-900" style="font-size:18px;margin-top:-8px;">
                                Ticket Late
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-5">
                        <div class="flex flex-col w-full px-4 py-2 border gap-4 rounded-lg justify-start border-card">
                            <div style="font-size:16px;" class="text-gray-900">Ticket Progress Overview</div>

                            <div class="chart-container" style="display: flex; gap: 10px;">
                                <div class="canvas-wrapper">
                                    <canvas id="myBarChart" width="300" height="200"></canvas>
                                </div>
                                <div class="legend-wrapper" id="customBarLegend" style="display: flex;flex-direction: column;justify-content: end;height: 200px;"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div> -->

<div id="implementasi" class="col-md-12 mt-4 mb-4">
    <div class="w-full bg-white p-4 px-5 rounded-xl">
        <div class="w-full flex flex-row justify-between items-center mb-5">
            <h3 class="">IMPLEMENTASI</h3>
            <div class="flex flex-row items-center gap-3 ">
                <div>Filter Customer</div>
                <form method="get" action="<?= Url::to(['index#implementasi']) ?>">
                    <select name="implementasi_customer_id" onchange="this.form.submit();" id="filter-nama-pelanggan" style="padding: 5px; width: 220px; border-radius: 5px; font-size: 0.9rem;" class="w-[180px] px-2 py-2 rounded-xl border-gray-300 border-1">
                        <option value="">All</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= htmlspecialchars($customer->customer_id) ?>"
                                <?= $implementasi_customer_id == $customer->customer_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($customer->customer_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

            </div>
        </div>
        <div class="flex flex-col items-start gap-4">

            <div class="col-md-12">
                <div class="grid grid-cols-5 sm:grid-cols-1 justify-between gap-4">
                    <div class="w-full">
                        <a href="/task/implementation/index" target="_blank">
                            <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                                <div class="flex flex-row justify-between w-full">
                                    <div class="text-gray-900 font-normal" style="font-size:32px;">
                                        <?= $implementasi['totalImplementasi'] ?>
                                    </div>
                                    <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                        <img src="/img/akun.svg" class="w-12" alt="">
                                    </div>
                                </div>
                                <div class="w-full text-gray-900" style="font-size:16px;margin-top:-8px;">
                                    Total Implementasi
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="w-full">
                        <a href="/task/implementation/index?status=open" target="_blank">
                            <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                                <div class="flex flex-row justify-between w-full">
                                    <div class="text-gray-900 font-normal" style="font-size:32px;">
                                        <?= $implementasi['implementasiOpen'] ?>
                                    </div>
                                    <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                        <img src="/img/akun.svg" class="w-12" alt="">
                                    </div>
                                </div>
                                <div class="w-full text-gray-900" style="font-size:16px;margin-top:-8px;">
                                    Implementasi Open
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="w-full">
                        <a href="/task/implementation/index?status=In Progress" target="_blank">
                            <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                                <div class="flex flex-row justify-between w-full">
                                    <div class="text-gray-900 font-normal" style="font-size:32px;">
                                        <?= $implementasi['implementasiInProgress'] ?>
                                    </div>
                                    <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                        <img src="/img/akun.svg" class="w-12" alt="">
                                    </div>
                                </div>
                                <div class="w-full text-gray-900" style="font-size:16px;margin-top:-8px;">
                                    Implementasi In Progress
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="w-full">
                        <a href="/task/implementation/index?status=done" target="_blank">
                            <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                                <div class="flex flex-row justify-between w-full">
                                    <div class="text-gray-900 font-normal" style="font-size:32px;">
                                        <?= $implementasi['implementasiDone'] ?>
                                    </div>
                                    <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                        <img src="/img/akun.svg" class="w-12" alt="">
                                    </div>
                                </div>
                                <div class="w-full text-gray-900" style="font-size:16px;margin-top:-8px;">
                                    Implementasi Done
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- <div class="w-full">
                        <div class="flex flex-col w-full px-4 py-8 border rounded-lg items-center justify-center border-card">
                            <div class="flex flex-row justify-between w-full">
                                <div class="text-gray-900 font-normal" style="font-size:32px;">
                                    <?= $implementasi['implementasiLate'] ?>
                                </div>
                                <div class="shadow-lg flex flex-col w-25 p-2 justify-center items-center rounded-xl bg-white">
                                    <img src="/img/akun.svg" class="w-12" alt="">
                                </div>
                            </div>
                            <div class="w-full text-gray-900" style="font-size:16px;margin-top:-8px;">
                                Implementasi Late
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>

            <div class="col-md-5">
                <div class="flex flex-col w-full px-4 py-2 border gap-4 rounded-lg justify-start border-card">
                    <div style="font-size:16px;" class="text-gray-900">Progress Implementasi</div>
                    <div class="chart-container" style="display: flex; gap: 10px;">
                        <div class="canvas-wrapper">
                            <canvas id="myBarChartIm" width="300" height="200"></canvas>
                        </div>
                        <div class="legend-wrapper" id="customBarLegendIm" style="display: flex;flex-direction: column;justify-content: end;height: 200px;"></div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<script>
    flatpickr("#tanggal_awal", {
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            console.log("Tanggal Awal:", dateStr);
        }
    });

    flatpickr("#tanggal_akhir", {
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr, instance) {
            console.log("Tanggal Akhir:", dateStr);
        }
    });


    const ctx = document.getElementById('myDonutChart').getContext('2d');
    const data = [<?= $task['taskOpen'] ?>, <?= $task['taskInProgress'] ?>, <?= $task['taskDone'] ?>];
    const totalTasks = <?= $task['totalTask'] ?>;


    const centerTextPlugin = {
        id: 'centerText',
        beforeDraw(chart) {
            const {
                width,
                height
            } = chart;
            const ctx = chart.ctx;
            ctx.restore();
            ctx.font = 'bold 20px sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#000';
            ctx.fillText(totalTasks, width / 2, height / 2 - 10);
            ctx.font = '14px sans-serif';
            ctx.fillText('Task', width / 2, height / 2 + 15);

            ctx.save();
        }
    };

    const myDonutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Open', 'In Progress', 'Done'],
            datasets: [{
                data: data,
                backgroundColor: [
                    '#FF1C71',
                    '#417FA9',
                    '#27465E'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: false,
            plugins: {
                legend: {
                    position: 'right',
                    display: false,
                },
                datalabels: {
                    color: '#ffffff',
                    formatter: (value, context) => {
                        const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        const percentage = (value / total * 100).toFixed(1) + '%';
                        return percentage;
                    }
                }
            }
        },
        plugins: [ChartDataLabels, centerTextPlugin]
    });


    document.getElementById('customLegend').innerHTML = myDonutChart.data.labels.map((label, i) => {
        const color = myDonutChart.data.datasets[0].backgroundColor[i];
        return `<div style="display:flex; align-items:center; margin-bottom:5px;">
                        <div style="width:12px; height:12px; background:${color}; margin-right:5px;"></div>
                        <span>${label}</span>
                    </div>`;
    }).join('');
</script>


<script>
    const barData = [<?= $ticket['ticketOpen'] ?>, <?= $ticket['ticketInProgress'] ?>, <?= $ticket['ticketDone'] ?>];
    const barLabels = ['Open', 'In Progress', 'Done'];
    const barColors = [
        '#FF1C71',
        '#417FA9',
        '#27465E'
    ];

    const ctxBar = document.getElementById('myBarChart').getContext('2d');
    const myBarChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [{
                label: 'Jumlah Ticket',
                data: barData,
                backgroundColor: barColors,
                borderColor: barColors.map(color => color.replace('0.7', '1')),
                borderWidth: 1,
                borderRadius: 8,
                barThickness: 30
            }]
        },
        options: {
            responsive: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        display: false
                    },
                    beginAtZero: true
                }
            }
        }
    });


    document.getElementById('customBarLegend').innerHTML = barLabels.map((label, i) => {
        return `
                <div style="display: flex; align-items: center; margin-bottom: 5px;">
                    <div style="width: 12px; height: 12px; background: ${barColors[i]}; margin-right: 6px;"></div>
                    <span>${label}</span>
                </div>
            `;
    }).join('');
</script>

<script>
    const barDataim = [<?= $implementasi['implementasiOpen'] ?>, <?= $implementasi['implementasiInProgress'] ?>, <?= $implementasi['implementasiDone'] ?>];
    const barLabelsim = ['Open', 'In Progress', 'Done'];
    const barColorsim = [
        '#FF1C71',
        '#417FA9',
        '#27465E'
    ];

    const ctxBarim = document.getElementById('myBarChartIm').getContext('2d');
    const myBarChartim = new Chart(ctxBarim, {
        type: 'bar',
        data: {
            labels: barLabelsim,
            datasets: [{
                label: 'Jumlah Task',
                data: barDataim,
                backgroundColor: barColorsim,
                borderColor: barColorsim.map(color => color.replace('0.7', '1')),
                borderWidth: 1,
                borderRadius: 8,
                barThickness: 30
            }]
        },
        options: {
            responsive: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    grid: {
                        display: false
                    },
                    beginAtZero: true
                }
            }
        }
    });


    document.getElementById('customBarLegendIm').innerHTML = barLabelsim.map((label, i) => {
        return `
                <div style="display: flex; align-items: center; margin-bottom: 5px;">
                    <div style="width: 12px; height: 12px; background: ${barColorsim[i]}; margin-right: 6px;"></div>
                    <span>${label}</span>
                </div>
            `;
    }).join('');
</script>
<?php

$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css'); // Pastikan Bootstrap Icons terdaftar
?>