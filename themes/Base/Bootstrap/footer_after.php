<?php

use App\Library\Language\Language;

/************************************************************************/
/* NPDS DUNE : Net Portal Dynamic System .                              */
/* ===========================                                          */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2024 by Philippe Brunier   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

// core required dont remove

//
echo '<script type="text/javascript" src="'. asset_url('shared/bootstrap/dist/js/bootstrap.bundle.min.js').'"></script>';

//
echo '<script type="text/javascript" src="'. asset_url('shared/bootstrap-table/dist/bootstrap-table.min.js').'"></script>';

//
echo '<script type="text/javascript" src="'. asset_url('shared/bootstrap-table/dist/locale/bootstrap-table-' . Language::languageIso(1, "-", 1) . '.min.js').'" async="async"></script>';

//
echo '<script type="text/javascript" src="'. asset_url('shared/bootstrap-table/dist/extensions/mobile/bootstrap-table-mobile.min.js').'" async="async"></script>';

//
echo '<script type="text/javascript" src="'. asset_url('shared/bootstrap-table/dist/extensions/export/bootstrap-table-export.min.js').'" async="async"></script>';

//
echo '<script type="text/javascript" src="'. asset_url('shared/jquery/plugin/tableExport/tableExport.js').'" async="async"></script>';

//
echo '<script type="text/javascript" src="'. asset_url('shared/jscookie/js.cookie.js').'" async="async"></script>';

//
echo '<script type="text/javascript" src="'. asset_url('shared/jquery/jquery-ui.min.js').'" ></script>';

//
echo '<script type="text/javascript" src="'. asset_url('shared/bootbox/bootbox.min.js').'" async="async"></script>';

//
echo '<script type="text/javascript" src="'. asset_url('shared/prism/prism.js').'"></script>';

//
echo defined('CITRON')
    ? '<script type="text/javascript" src="'. asset_url('js/npds_tarteaucitron_service.js').'"></script>'
    : '';

// page-time pseudo-module : If you want to show the time used to generate each page uncomment those lines

/*
// php => 5.4 
$time = round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"], 5); 
    
echo '<span class="small" id="build_time">
    <i class="bi bi-clock-history me-1"></i>
    '.Language::affLangue('[french]Temps :[/french][english]Time:[/english][german]Zeit in Sekunden[/german][spanish]Tiempo en segundos :[/spanish][chinese]&#x5728;&#x51E0;&#x79D2;&#x949F;&#x7684;&#x65F6;&#x95F4; :[/chinese] '.$time.' [french]seconde(s)[/french][english]second(s)[/english]').'
</span>';
*/

echo '</footer>
    </div>
    <script type="text/javascript" src="'. asset_url('js/npds_adapt.js').'"></script>';
