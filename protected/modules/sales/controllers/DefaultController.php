<?php

namespace app\modules\sales\controllers;

use app\modules\sales\DashboardService;
use yii\web\Controller;

/**
 * Default controller for the `sales` module
 */
class DefaultController extends Controller
{
  /**
   * Renders the index view for the module
   * @return string
   */
  public function actionIndex()
  {
    $dashboardService = new DashboardService();

    $summary = $dashboardService->getSalesSummary();
    $pipelineData = $dashboardService->getSalesPipelineData();
    $productSoldData = $dashboardService->getProductSoldData();

    // Data KPI Baru yang kita tambahkan
    $kpiData = $dashboardService->getKeyPerformanceData();
    $salesmanPerformance = $dashboardService->getIndividualSalesmanPerformance();

    return $this->render('index', [
      'summary' => $summary,
      'pipelineData' => $pipelineData,
      'productSoldData' => $productSoldData,
      'kpiData' => $kpiData,
      'salesmanPerformance' => $salesmanPerformance,
    ]);
  }
}
