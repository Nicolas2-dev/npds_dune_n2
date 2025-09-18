<?php
if (View::exists($theme_file = 'Themes/NpdsBoostSK::Bootstrap/Header_before')) {
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
    if (View::exists($theme_file = 'Bootstrap/Header_head')) {
        echo View::make($theme_file);
    }

    if (View::exists($theme_file = 'Themes/NpdsBoostSK::Bootstrap/Header_head')) {
        echo View::make($theme_file);
    }
    ?>

    <!-- import css et js -->
    <?php Event::fire('assets.css'); ?>
    <?php Event::fire('assets.header.js'); ?>
</head>

<?php 

Theme_NpdsBoostSK::header()->leftBlock($pdst);

echo $content;

Theme_NpdsBoostSK::rightBlock($pdst)->footer();

Event::fire('assets.footer.js'); 

?>
</body>
</html>

<?php

// faire listener ou middleware
//include 'sitemap.php';

//sql_close();

?>