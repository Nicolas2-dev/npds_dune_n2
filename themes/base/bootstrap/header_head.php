<?php

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

define('CITRON', 'tarteaucitron');

// since NPDS Rev 16 this ressources are required dont remove

// web font V5
echo '<link rel="stylesheet" href="assets/shared/font-awesome/css/all.min.css" />';

// framework
echo '<link id="bsth" rel="stylesheet" href="assets/shared/bootstrap/dist/css/bootstrap.min.css" />';

// developpement
echo '<link id="bsthxtra" rel="stylesheet" href="assets/shared/bootstrap/dist/css/extra.css" />';

// form control
echo '<link rel="stylesheet" href="assets/shared/formvalidation/dist/css/formValidation.min.css" />';

//interface
echo '<link rel="stylesheet" href="assets/shared/jquery/jquery-ui.min.css" />';

// table
echo '<link rel="stylesheet" href="assets/shared/bootstrap-table/dist/bootstrap-table.min.css" />';

//
echo '<link rel="stylesheet" href="assets/shared/prism/prism.css" />';

//
echo '<script type="text/javascript" src="assets/shared/jquery/jquery.min.js"></script>';

if (defined('CITRON')) {
    if (function_exists('languageIso')) {
        //RGPD tool
        echo '
        <script type="text/javascript"> var tarteaucitronForceLanguage = "' . Language::languageIso(1, '', '') . '"; </script>
        <script type="text/javascript" src="shared/tarteaucitron/tarteaucitron.min.js"></script>
        <script type="text/javascript" src="assets/js/npds_tarteaucitron.js"></script>';
    }
}
