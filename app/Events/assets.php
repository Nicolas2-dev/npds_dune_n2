<?php

use App\Support\Facades\Css;
use App\Support\Facades\Theme;
use App\Support\Facades\Language;
use App\Support\Facades\Assets as AssetManager;

// Assets Register
AssetManager::register();

// Header

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

// Footer

//
AssetManager::addJsFooter(path: 'js/npds_dicotransl.js');

//
AssetManager::addJsInlineFooter("
//<![CDATA[
    $(document).ready(function() {
        // Traduction
        var translator = $('body').translate({
            lang: 'fr',
            t: dict
        });
        translator.lang('" . Language::languageIso(1, '', 0) . "');

        // Gestion du clic sur 'plusdecontenu'
        $('.plusdecontenu').click(function() {
            var \$this = $(this);
            \$this.toggleClass('plusdecontenu');
            if (\$this.hasClass('plusdecontenu')) {
                \$this.text(translator.get('Plus de contenu'));
            } else {
                \$this.text(translator.get('Moins de contenu'));
            }
        });

        // Collapse des colonnes sur petit écran
        if (window.matchMedia) {
            const mq = window.matchMedia('(max-width: 991px)');
            mq.addListener(WidthChange);
            WidthChange(mq);
        }

        function WidthChange(mq) {
            if (mq.matches) {
                $('#col_LB, #col_RB').removeClass('show');
            } else {
                $('#col_LB, #col_RB').addClass('show');
            }
        }
    });
    
    // Gestion du thème
    (() => {
        'use strict';
        const storedTheme = localStorage.setItem('theme', '" . config('theme.theme_darkness') . "');
        const getStoredTheme = localStorage.getItem('theme');

        if (getStoredTheme === 'auto') {
            document.body.setAttribute(
                'data-bs-theme', 
                window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
            );
        } else {
            document.body.setAttribute('data-bs-theme', '" . config('theme.theme_darkness') . "');
        }
    })();
//]]>
");

AssetManager::addJsFooter(path: 'shared/bootstrap/dist/js/bootstrap.bundle.min.js');
//

AssetManager::addJsFooter(path: 'shared/bootstrap-table/dist/bootstrap-table.min.js');

//
AssetManager::addJsFooter(path: 'shared/bootstrap-table/dist/locale/bootstrap-table-' . Language::languageIso(1, "-", 1) . '.min.js');

//
AssetManager::addJsFooter(path: 'shared/bootstrap-table/dist/extensions/mobile/bootstrap-table-mobile.min.js');

//
AssetManager::addJsFooter(path: 'shared/bootstrap-table/dist/extensions/export/bootstrap-table-export.min.js');

//
AssetManager::addJsFooter(path: 'shared/jquery/plugin/tableExport/tableExport.js');

//
AssetManager::addJsFooter(path: 'shared/jscookie/js.cookie.js');

//
AssetManager::addJsFooter(path: 'shared/jquery/jquery-ui.min.js');

//
AssetManager::addJsFooter(path: 'shared/bootbox/bootbox.min.js');

//
AssetManager::addJsFooter(path: 'shared/prism/prism.js');

//
AssetManager::addJsFooter(path: 'shared/jquery/jquery.translate.js');

//
defined('CITRON') ?? AssetManager::addJsFooter(path: 'js/npds_tarteaucitron_service.js');

AssetManager::addJsFooter(path: 'js/npds_adapt.js');

Css::loadCss();