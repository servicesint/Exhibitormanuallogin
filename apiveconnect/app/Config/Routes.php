<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Home::index');
$routes->group('api/auth/v1', function ($routes) {
    $routes->post('send-otp', 'Api\AuthController::sendOtp');
    $routes->post('verify-otp', 'Api\AuthController::verifyOtp');
    $routes->post('resend-otp', 'Api\AuthController::resendOtp');
});
$routes->get('api/v1/exhibitor-login/(:any)', '\App\Controllers\Api\AuthController::get_sub_events/$1');
$routes->group('api/v1', function ($routes) {
    $routes->match(['get', 'post'], 'payment/razorpay/callback', '\App\Controllers\Api\DashboardController::razorpayCallback');
});
$routes->get('api/v1/payment/success', 'App\Controllers\Api\DashboardController::paymentsuccess');
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api', 'filter' => 'jwt'], function ($routes) {
    $routes->get('dashboard/get_quotations',             'DashboardController::get_quotations');
    $routes->post('dashboard/save_neft_transfer',             'DashboardController::update_quotation');
    $routes->get('dashboard/get_quotation_details/(:num)',             'DashboardController::get_quotation_details/$1');
    $routes->get('dashboard',             'DashboardController::exhibitor_dashboard');
    $routes->get('profile',               'DashboardController::profile_index');
    $routes->post('cart/add',             'DashboardController::add_to_cart');
    $routes->get('cart/items',            'DashboardController::cart_items');
    $routes->post('cart/remove',          'DashboardController::remove_cart_item');
    $routes->get('cart/furniture',        'DashboardController::additional_furniture');
    $routes->post('cart/update-quantity', 'DashboardController::update_quantity');
    $routes->delete('cart/clear',         'DashboardController::clear_cart');
    $routes->post('orders/checkout',      'DashboardController::checkout');
    $routes->get('orders/list',           'DashboardController::past_orders');
    $routes->get('orders/(:num)',         'DashboardController::order_detail/$1');
    $routes->get('orders/(:num)/invoice', 'DashboardController::generate_invoice/$1');
    $routes->get('orders/quotation',      'DashboardController::generate_quotation');
    $routes->post('logout',               'AuthController::logout');
    $routes->get('dashboard/fascia',      'DashboardController::fascia');
    $routes->post('dashboard/fascia',     'DashboardController::saveFascia');
    $routes->post('exhibitor-badges',     'DashboardController::submit_exhibitor_badge');
    $routes->post('exhibitor-update/(:segment)/update', 'DashboardController::update_badge/$1');
    $routes->get('exhibitor-badges',                  'DashboardController::list');
    $routes->get('exhibitor-badges/(:any)/download',  'DashboardController::download_exhibitor_badge/$1');
    $routes->post('exhibitor-badges-delete/(:any)/delete', 'DashboardController::delete_exhibitor_badge/$1');
    $routes->get('orders/(:segment)/invoice_download', 'DashboardController::download_order_invoice/$1');
    $routes->get('dashboard/guidelines', 'DashboardController::getGuidelines');
    $routes->get('dashboard/guidelines/(:segment)', 'DashboardController::getPageContent');
    $routes->get('dashboard/fascia-menu', 'DashboardController::getFasicaMenu');
    $routes->get('dashboard/casual-gst', 'DashboardController::getFasicaMenu');
    $routes->get('dashboard/casual-gst-details', 'DashboardController::getCasualGstDetails');
    $routes->post('dashboard/casual-gst-details', 'DashboardController::submitCasualGst');
    $routes->get('locations/countries', 'DashboardController::getCountries');
    $routes->get('locations/states', 'DashboardController::getStates');
    $routes->get('locations/cities', 'DashboardController::getCities');
    $routes->post('dashboard/visitor-tickets', 'DashboardController::submitVisitorTicketRequest');
    $routes->post('dashboard/visitor-tickets/update', 'DashboardController::updateVisitorTicketRequest');
    $routes->get('dashboard/visitor-tickets/list', 'DashboardController::getVisitorTicketRequests');
    $routes->get('reference-image', 'DashboardController::getReferenceImage');
    $routes->get('dashboard/electricity-item', 'DashboardController::getElectricityItem');
    $routes->post('dashboard/profile/save', 'DashboardController::saveProfile');
    $routes->post('product-categories', 'DashboardController::getproduct');
    $routes->get('product-categories', 'DashboardController::getproduct');
    $routes->get('dashboard/online-forms-menu', 'DashboardController::onlineFormsMenu');
    $routes->post('dashboard/online-forms-settings', 'DashboardController::updateOnlineFormsSettings');
    // $routes->get('dashboard/fascia-menu', 'DashboardController::fasciaMenu');
    $routes->get('dashboard/submission-status', 'DashboardController::getSubmissionStatus');
    $routes->get('exhibitor/exit-permit/pdf', 'DashboardController::exitPermit');
    $routes->get('exhibitor/welcome-letter/pdf', 'DashboardController::welcomeLetter');
    $routes->get('exhibitor/participation-letter/pdf', 'DashboardController::participationLetters');
    $routes->post('dashboard/furniture-opt-out', 'DashboardController::furnitureOptOut');
    $routes->get('orders/pending', 'DashboardController::getPendingOrders');
    $routes->get('dashboard/download_quotation/(:segment)', 'DashboardController::download_quotation/$1');
    // $routes->get('dashboard/pending-payments', 'DashboardController::pending_payments');
    $routes->post('dashboard/profile/edit-request', 'DashboardController::editRequest');
});
