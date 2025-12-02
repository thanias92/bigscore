<?php

use yii\helpers\Html;
use yii\web\JsExpression;

$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;

?>

<!-- Load amCharts -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    #salesChart,
    #productChart {
        width: 100%;
        height: 350px;
    }

    .card:hover {
        transform: scale(1.02);
        transition: all 0.3s ease;
    }
</style>

    <div class="bg-white p-4 rounded shadow-sm mb-4">
    <div class="dashboard-index">
    <h1><?= Html::encode($this->title) ?></h1>
        <div class="row">
            <div class="col-md-4">
                <?= Html::a(
                    '<div class="card border shadow-sm mb-4">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-wallet fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-muted">Total Pemasukan</h6>
                            <h4 class="mb-0">Rp ' . number_format($totalPemasukan, 0, ',', '.') . '</h4>
                        </div>
                    </div>
                </div>',
                    ['laporan/pemasukan'],
                    ['class' => 'text-decoration-none']
                ) ?>
            </div>

            <div class="col-md-4">
                <?= Html::a(
                    '<div class="card border shadow-sm mb-4">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-arrow-circle-up fa-2x text-danger"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-muted">Total Pengeluaran</h6>
                            <h4 class="mb-0">Rp ' . number_format($totalPengeluaran, 0, ',', '.') . '</h4>
                        </div>
                    </div>
                </div>',
                    ['laporan/pengeluaran'],
                    ['class' => 'text-decoration-none']
                ) ?>
            </div>

            <div class="col-md-4">
                <?= Html::a(
                    '<div class="card border shadow-sm mb-4">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-muted">Tagihan Belum Dibayar</h6>
                            <h4 class="mb-0">Rp ' . number_format($tagihanBelumDibayar, 0, ',', '.') . '</h4>
                        </div>
                    </div>
                </div>',
                    ['pemasukan/index'],
                    ['class' => 'text-decoration-none']
                ) ?>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card border shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Analisis Penjualan Tahunan dan Bulanan</h5>
                        <div id="salesChart"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Produk Terjual</h5>
                        <div id="productChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(new JsExpression('
am5.ready(function() {
    var root = am5.Root.new("salesChart");
    root.setThemes([am5themes_Animated.new(root)]);

    var chart = root.container.children.push(am5xy.XYChart.new(root, {
        layout: root.verticalLayout
    }));

    // Tambahkan legend
    var legend = chart.children.push(am5.Legend.new(root, {
        centerX: am5.p50,
        x: am5.p50
    }));

    var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
        categoryField: "bulan",
        renderer: am5xy.AxisRendererX.new(root, {})
    }));

    var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
        renderer: am5xy.AxisRendererY.new(root, {})
    }));

    var data = [];
    const bulan = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const pemasukan = ' . json_encode($analisisPenjualan["bulan"]["pemasukan"]) . ';
    const pengeluaran = ' . json_encode($analisisPenjualan["bulan"]["pengeluaran"]) . ';
    const rataPemasukan = ' . json_encode($analisisPenjualan["tahun"]["pemasukan"]) . ';
    const rataPengeluaran = ' . json_encode($analisisPenjualan["tahun"]["pengeluaran"]) . ';

    for (let i = 0; i < 12; i++) {
        data.push({
            bulan: bulan[i],
            pemasukan: pemasukan[i],
            pengeluaran: pengeluaran[i],
            rataPemasukan: rataPemasukan[i],
            rataPengeluaran: rataPengeluaran[i]
        });
    }

    xAxis.data.setAll(data);

    var series1 = chart.series.push(am5xy.ColumnSeries.new(root, {
        name: "Pemasukan",
        xAxis: xAxis,
        yAxis: yAxis,
        valueYField: "pemasukan",
        categoryXField: "bulan",
        fill: am5.color("#00c0ef"),
        stroke: am5.color("#00c0ef")
    }));
    series1.data.setAll(data);

    var series2 = chart.series.push(am5xy.ColumnSeries.new(root, {
        name: "Pengeluaran",
        xAxis: xAxis,
        yAxis: yAxis,
        valueYField: "pengeluaran",
        categoryXField: "bulan",
        fill: am5.color("#f56954"),
        stroke: am5.color("#f56954")
    }));
    series2.data.setAll(data);

    var series3 = chart.series.push(am5xy.LineSeries.new(root, {
        name: "Rata-rata Pemasukan",
        xAxis: xAxis,
        yAxis: yAxis,
        valueYField: "rataPemasukan",
        categoryXField: "bulan",
        stroke: am5.color("#3c8dbc")
    }));
    series3.data.setAll(data);

    var series4 = chart.series.push(am5xy.LineSeries.new(root, {
        name: "Rata-rata Pengeluaran",
        xAxis: xAxis,
        yAxis: yAxis,
        valueYField: "rataPengeluaran",
        categoryXField: "bulan",
        stroke: am5.color("#d81b60")
    }));
    series4.data.setAll(data);

    // Tambahkan semua series ke legend
    legend.data.setAll(chart.series.values);

    chart.appear(1000, 100);
});

'));

$this->registerJs(new JsExpression('
am5.ready(function() {
    // PIE CHART
    var root = am5.Root.new("productChart");
    root.setThemes([am5themes_Animated.new(root)]);

    var chart = root.container.children.push(am5percent.PieChart.new(root, {
    layout: root.verticalLayout,
    innerRadius: am5.percent(40),
    width: am5.percent(100),
    paddingRight: 80
}));

    var series = chart.series.push(am5percent.PieSeries.new(root, {
        valueField: "value",
        categoryField: "product"
    }));

    series.data.setAll(' . json_encode(array_map(function ($key, $val) {
    return ['product' => $key, 'value' => $val];
}, array_keys($produkTerjual), array_values($produkTerjual))) . ');

    chart.appear(1000, 100);
});
'));
?>