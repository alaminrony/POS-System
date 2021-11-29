<?php

if (!isset($routes)) {
    $routes = \Config\Services::routes(true);
}

$routes->group('requisition', ['namespace' => 'App\Modules\Requisition\Controllers'], function ($subroutes) {

    /*     * * Route for invoice ** */
    $subroutes->add('add_requisition', 'Requisition::add_requisition');
    $subroutes->add('requsition_add', 'Requisition::requsition_add');
    $subroutes->add('requisition_list', 'Requisition::index');
    $subroutes->add('requisition_list_check', 'Requisition::bdtask_CheckRequisitionList');
    $subroutes->add('requisition_details/(:num)', 'Requisition::requisition_details/$1');
    $subroutes->add('requisition_edit/(:num)', 'Requisition::requisition_edit/$1');
    $subroutes->add('requsition_update', 'Requisition::requsition_update');
    $subroutes->add('delete_requisition/(:num)', 'Requisition::delete_requisition/$1');
    $subroutes->add('requisition_status_update', 'Requisition::requisition_status_update');
    $subroutes->add('requisition_not_approved', 'Requisition::requisition_not_approved');
    $subroutes->add('requisition_customer_data', 'Requisition::requisition_customer_data');
    $subroutes->add('search_medicine', 'Requisition::autocompleteproductsearch');
    $subroutes->add('medicine_details_data', 'Requisition::retrieve_product_data_inv');
    $subroutes->add('get_batch_stock', 'Requisition::retrieve_product_batchid');
});

