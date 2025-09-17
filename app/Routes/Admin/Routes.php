<?php

// Route Admin Dashbord

/**
 * Admin Dashboard Main
 */
Route::get(
    'admin/dashboard/{deja_affiches?}', 
    'App\Http\Controllers\Admin\Dashboard\Dashboard@adminMain'
);

// Route Admin AblaLog

/**
 * Admin AblaLog
 */
Route::get(
    'admin/ablalog', 
    'App\Http\Controllers\Admin\AblaLog\AblaLog@log'
);

// Route Admain Auth

/**
 * Admin Login
 */
Route::get(
    'admin/login', 
    'App\Http\Controllers\Admin\Auth\Auth@login'
);


// Route Admin Users

/**
 * Admin Users Display
 */
Route::get(
    'admin/user', 
    'App\Http\Controllers\Admin\User\Users@displayUsers'
);