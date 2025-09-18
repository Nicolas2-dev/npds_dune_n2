<?php

// Dev Test Route
require_once __DIR__ . DS .'TestDev.php';

// Import Routes
require_once __DIR__ . DS .'Backend.php';
require_once __DIR__ . DS .'Banner.php';
require_once __DIR__ . DS .'Download.php';
require_once __DIR__ . DS .'Contact.php';
require_once __DIR__ . DS .'Language.php';


// Route Home & Start Page

/**
 * Start Page
 */
Route::get(
    '/', 
    'App\Http\Controllers\Front\Start\StartPage@index'
);

/**
 * Start Page Pattern
 */
Route::pattern('start', '[a-zA-Z]+');

/**
 * Start Page
 */
Route::get(
    '/index/{start?}', 
    'App\Http\Controllers\Front\Start\StartPage@index'
);
