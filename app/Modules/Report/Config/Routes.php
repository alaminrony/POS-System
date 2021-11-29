<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('report', ['namespace' => 'App\Modules\Report\Controllers'], function($subroutes) {

    /*     * * Route for report ** */
    $subroutes->add('add_closing', 'Report::bdtask_0001_closing_form');
    $subroutes->add('closing_list', 'Report::closing_list');
    $subroutes->add('getclosing_data', 'Report::bdtask_CheckclosingList');
    $subroutes->add('sales_report', 'Report::sales_report');
    $subroutes->add('getsales_reportList', 'Report::bdtask_ChecksalesreportList');
    $subroutes->add('userwise_sales_report', 'Report::userwise_sales_report');
    $subroutes->add('customer_wise_sales_report_print', 'Report::customer_wise_sales_report_print');
    $subroutes->add('getuserwise_sales_reportList', 'Report::bdtask_CheckuserwisesalesreportList');
    $subroutes->add('productwise_sales_report', 'Report::productwise_sales_report');
    $subroutes->add('getproductwise_sales_reportList', 'Report::bdtask_CheckproductwisesalesreportList');
    $subroutes->add('productwise_cumulative_report', 'Report::productwise_cumulative_report');
    $subroutes->add('getproductwise_cumulative_report', 'Report::getproductwise_cumulative_report');
    $subroutes->add('categorywise_sales_report', 'Report::categorywise_sales_report');
    $subroutes->add('getcategorywise_sales_reportList', 'Report::bdtask_CheckcategorywisesalesreportList');
    $subroutes->add('employee_report', 'Report::employee_report');
    $subroutes->add('getemployee_reportList', 'Report::bdtask_CheckemployeereportList');
    $subroutes->add('employee_report_2', 'Report::employee_report_2');
    $subroutes->add('getemployee_reportList_2', 'Report::bdtask_CheckemployeereportList_2');
    $subroutes->add('purchase_report', 'Report::purchase_report');
    $subroutes->add('getpurchase_reportList', 'Report::bdtask_CheckpurchasereportList');
    $subroutes->add('purchase_report_categorywise', 'Report::categorywise_purchase_report');
    $subroutes->add('get_categorywise_purchaselist', 'Report::bdtask_CheckcategorywisepurchasereportList');

    $subroutes->add('department_wise_sales_report', 'Report::department_wise_sales_report');
    $subroutes->add('get_dep_wise_sales_reportList', 'Report::get_dep_wise_sales_reportList');
    $subroutes->add('department_wise_sales_report_print', 'Report::department_wise_sales_report_print');

    $subroutes->add('employee_wise_cumulative_report', 'Report::employee_wise_cumulative_report');
//    $subroutes->add('customer_wise_sales_report_print', 'Report::customer_wise_sales_report_print');
    $subroutes->add('get_employee_wise_cumulative_report', 'Report::get_employee_wise_cumulative_report');
});

