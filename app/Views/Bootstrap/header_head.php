<?php

use App\Support\Facades\Css;
use App\Support\Facades\Theme;
use App\Support\Facades\Assets as AssetManager;

// web font V5
AssetManager::addCss(path: 'shared/font-awesome/css/all.min.css');

$theme = Theme::getTheme();
$skin  = Theme::getSkin();

if ($skin !== '' && in_array(substr($theme, -2), ['sk', 'SK'], true)) {
    //
    AssetManager::addCss(path: 'skins/' . $skin . '/bootstrap.min.css');

    //
    AssetManager::addCss(path: 'skins/' . $skin . '/extra.css');
} else {
    //
    AssetManager::addCss(path: 'shared/bootstrap/dist/css/bootstrap.min.css');

    //
    AssetManager::addCss(path: 'shared/bootstrap/dist/css/extra.css');
}

//
AssetManager::addCss(path: 'shared/formvalidation/dist/css/formValidation.min.css');

//
AssetManager::addCss(path: 'shared/jquery/jquery-ui.min.css');

//
AssetManager::addCss(path: 'shared/bootstrap-table/dist/bootstrap-table.min.css');

//
AssetManager::addCss(path: 'shared/prism/prism.css');

//
AssetManager::addJsHeader(path: 'shared/jquery/jquery.min.js');

//
Css::loadCss();

//
AssetManager::addCss(path: 'shared/formvalidation/dist/css/formValidation.min.css');

//
AssetManager::addCss(path: 'shared/jquery/jquery-ui.min.css');

//
AssetManager::addCss(path: 'shared/bootstrap-table/dist/bootstrap-table.min.css');

//
AssetManager::addCss(path: 'shared/prism/prism.css');
