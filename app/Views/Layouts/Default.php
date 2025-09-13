<?php
if (View::exists($theme_file = 'themes/'. $theme .'/View/Bootstrap/header_before')) {
    echo View::make($theme_file);
}
?>

<!DOCTYPE html>
<html lang="<?= Language::languageIso(1, '', 0); ?>">
<head>
    <meta charset="utf-8">
    <title><?= isset($title) ? $title : 'Page'; ?> - <?= Config::get('app.name'); ?></title>

    <!-- metatag a faire -->

    <!-- favico -->
    <?php Event::fire('assets.favico'); ?>

    <!-- meta canocical -->
    <link rel="canonical" href="<?= Request::url(); ?>" />

    <!-- meta humans.txt -->
    <?php if (file_exists('humans.txt')): ?>
        <link type="text/plain" rel="author" href="<?= site_url('humans.txt'); ?>">
    <?php endif; ?>

    <!-- meta backend -->
    <link href="<?= site_url('backend/RSS0.91'); ?>" title="<?= config('app.sitename'); ?> - RSS 0.91" rel="alternate" type="text/xml">
    <link href="<?= site_url('backend/RSS1.0'); ?>" title="<?= config('app.sitename'); ?> - RSS 1.0" rel="alternate" type="text/xml">
    <link href="<?= site_url('backend/RSS2.0'); ?>" title="<?= config('app.sitename'); ?> - RSS 2.0" rel="alternate" type="text/xml">
    <link href="<?= site_url('backend/ATOM'); ?>" title="<?= config('app.sitename'); ?> - ATOM" rel="alternate" type="application/atom+xml">

    <?php
    if (View::exists($theme_file = 'themes/Base/View/Bootstrap/header_head')) {
        echo View::make($theme_file);
    }

    if (View::exists($theme_file = 'themes/'. $theme .'/View/Bootstrap/header_head')) {
        echo View::make($theme_file);
    }
    ?>

    <!-- import css et js -->
    <?php Event::fire('assets.css'); ?>
    <?php Event::fire('assets.header.js'); ?>
</head>

<?php 

// faire listener ou middleware
//refererUpdate();

// faire listener ou middleware
//counterUpdate();

if (file_exists('themes/' . $theme . '/Bootstrap/body_onload.php')) {
    $onload_init = ' onload="init();"';
} else {
    $onload_init = '';
}

$ContainerGlobal = '<div id="container">';

if (!$ContainerGlobal) {
    echo '<body' . $onload_init . ' class="body" data-bs-theme="' . config('theme.theme_darkness') . '">';
} else {
    echo '<body' . $onload_init . ' data-bs-theme="' . config('theme.theme_darkness') . '">';
    echo $ContainerGlobal;
}

$Start_Page = str_replace('/', '', Config::get('app.Start_Page'));

// landing page
if (stristr($_SERVER['REQUEST_URI'], $Start_Page) && View::exists('themes/' . $theme . '/View/partials/header/header_landing')) {
    $Xcontent = View::make('themes/'. $theme . '/View/partials/header/header_landing');
} else {
    $Xcontent = View::make('themes/'. $theme . '/View/partials/header/header');
}

//echo Metalang::metaLang(Language::affLangue($Xcontent));
echo Language::affLangue($Xcontent);

if (View::exists($theme_file = 'themes/'. $theme .'/View/Bootstrap/header_after')) {
    echo View::make($theme_file);
}

$moreclass = 'col';

echo '<div id="corps" class="container-fluid n-hyphenate">
    <div class="row g-3">';

switch ($pdst) {

    case '-1':
        echo '<div id="col_princ" class="col-12">';
        break;

    case '1':
        Theme::colsyst('#col_LB');

        echo '<div id="col_LB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::leftBlocks($moreclass);

        echo '</div>
            </div>
        <div id="col_princ" class="col-lg-6">';
        break;

    case '2':
    case '6':
        echo '<div id="col_princ" class="col-lg-9">';
        break;

    case '3':
        Theme::colsyst('#col_LB');

        echo '<div id="col_LB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::leftBlocks($moreclass);

        echo '</div>
        </div>';

        Theme::colsyst('#col_RB');

        echo '<div id="col_RB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::rightBlocks($moreclass);

        echo '</div>
            </div>
        <div id="col_princ" class="col-lg-6">';
        break;

    case '4':
        echo '<div id="col_princ" class="col-lg-6">';
        break;

    case '5':
        Theme::colsyst('#col_RB');

        echo '<div id="col_RB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::rightBlocks($moreclass);

        echo '</div>
            </div>
            <div id="col_princ" class="col-lg-9">';
        break;

    default:
        Theme::colsyst('#col_LB');

        echo '<div id="col_LB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::leftBlocks($moreclass);

        echo '</div>
            </div>
        <div id="col_princ" class="col-lg-9">';
        break;
}

echo $content;

$moreclass = 'col-12';

switch ($pdst) {

    case '-1':
    case '3':
    case '5':
        echo '</div>
                </div>
            </div>';
        break;

    case '1':
    case '2':
        echo '</div>';

        Theme::colsyst('#col_RB');

        echo '<div id="col_RB" class="collapse show col-lg-3 ">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::rightBlocks($moreclass);

        echo '</div>
                </div>
            </div>
        </div>';
        break;

    case '4':
        echo '</div>';

        Theme::colsyst('#col_LB');

        echo '<div id="col_LB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::leftBlocks($moreclass);

        echo '</div>
        </div>';

        Theme::colsyst('#col_RB');

        echo '<div id="col_RB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::rightBlocks($moreclass);

        echo '</div>
                </div>
            </div>
        </div>';
        break;

    case '6':
        echo '</div>';

        Theme::colsyst('#col_LB');

        echo '<div id="col_LB" class="collapse show col-lg-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-1">';

        Block::leftBlocks($moreclass);

        echo '</div>
                </div>
            </div>
        </div>';
        break;

    default:
        echo '</div>
            </div>
        </div>';
        break;
}

if (View::exists($theme_file = 'themes/'. $theme .'/View/Bootstrap/footer_before')) {
    echo View::make($theme_file);
}

$Xcontent = View::make('themes/'. $theme . '/View/partials/footer/footer');

$ContainerGlobal = '</div>';

if (! empty($ContainerGlobal)) {
    $Xcontent .= $ContainerGlobal;
}

//echo Metalang::metaLang(Language::affLangue($Xcontent));
echo Language::affLangue($Xcontent);

if (View::exists($theme_file = 'themes/Base/View/Bootstrap/footer_after')) {
    echo View::make($theme_file);
}

if (View::exists($theme_file = 'themes/'. $theme .'/View/Bootstrap/footer_after')) {
    echo View::make($theme_file);
}

Event::fire('assets.footer.js'); 

?>
</body>
</html>

<?php

// faire listener ou middleware
//include 'sitemap.php';

//sql_close();

?>