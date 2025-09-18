<?php

// Route Bakend

/**
 * Backend Rss
 */
Route::get(
    'backend/{op?}',        
    'App\Http\Controllers\Front\Backend\Backend@index'
);
