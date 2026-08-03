<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'AuthController::exlogin');
$routes->get('event/(:segment)', 'AuthController::index/$1');
$routes->get('login/(:segment)', 'AuthController::exlogin/$1');
$routes->post('checklogin', 'AuthController::checkLogin');
$routes->post('sendOtp', 'AuthController::sendOtp');
$routes->get('testEncrypt', 'AuthController::testEncrypt');
$routes->post('verifyOtp', 'AuthController::verifyOtp');
$routes->post('verifyOtp', 'AuthController::verifyOtp');
$routes->get('logout', 'AuthController::logout');
$routes->get('generate', 'AuthController::generate_main_event_code');
$routes->match(['get', 'post'], 'api/v1/payment/razorpay_callback', 'App\Controllers\Api\DashboardController::razorpayCallback');
$routes->get('api/v1/dashboard/fascia', 'App\Controllers\Api\DashboardController::fascia');
$routes->post('api/v1/dashboard/fascia', 'App\Controllers\Api\DashboardController::saveFascia');
$routes->set404Override('\App\Controllers\AuthController::error404');

$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::exhibitor_dashboard');
    $routes->get('profile', 'DashboardController::index');
    $routes->get('dashboard/profile-fragment', 'DashboardController::profileFragment');
    $routes->get('participation-letter', 'DashboardController::generateParticipationLetter');
    $routes->get('exit-permit', 'DashboardController::generateExitPermit');
    $routes->get('casual-gst-deatils', 'DashboardController::casual_gst');
    $routes->get('important-information', 'DashboardController::important_information');
    $routes->get('accommodation', 'DashboardController::accommodation');
    $routes->get('empanelled-sand', 'DashboardController::empanelled_sand');
    $routes->get('contact-details', 'DashboardController::contact_details');
    $routes->get('other-informations', 'DashboardController::other_information');
    $routes->get('raw-space', 'DashboardController::raw_space');
    $routes->get('casual-gst', 'DashboardController::casual_gst_get');
    $routes->get('exhibitor-badges', 'DashboardController::exhibitor_badge');
    $routes->post('dashboard/submit_exhibitor_badge', 'DashboardController::submit_exhibitor_badge');
    $routes->post('dashboard/remove_cart_item', 'DashboardController::remove_cart_item');
    $routes->post('dashboard/add_to_cart', 'DashboardController::add_to_cart');
    $routes->get('dashboard/cart_items', 'DashboardController::cart_items');
    $routes->get('fascia', 'DashboardController::fascia');
    $routes->get('upload-stand-design', 'DashboardController::fascia');
    $routes->post('fascia/upload-info', 'DashboardController::uploadInfo');
    $routes->get('additional-furniture', 'DashboardController::additional_furniture_list');
    $routes->post('profile/save', 'DashboardController::save');
    $routes->get('payment/success', 'DashboardController::success');
    $routes->get('payment/failed', 'DashboardController::failed');
    $routes->get('guidelines/(:segment)', 'DashboardController::getGuidelines');
    $routes->get('visitor-invitation', 'DashboardController::visitor_invitation');
    $routes->get('reference-image', 'DashboardController::reference_image');
    
});