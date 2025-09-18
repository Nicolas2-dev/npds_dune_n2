<?php

// Route Download

/**
 * Download Pattern
 */
Route::pattern('dcategory', '[a-zA-Z]+');
Route::pattern('sortby',    '[a-zA-Z]+');
Route::pattern('sortorder', '[a-zA-Z]+');

/**
 * Download Main
 */
Route::get(
    'download/{dcategory?}/{sortby?}/{sortorder?}',        
    'App\Http\Controllers\Front\Download\Download@main'
);

