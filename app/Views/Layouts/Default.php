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
    <link href="backend.php?op=RSS0.91" title="<?= config('app.sitename'); ?> - RSS 0.91" rel="alternate" type="text/xml">
    <link href="backend.php?op=RSS1.0" title="<?= config('app.sitename'); ?> - RSS 1.0" rel="alternate" type="text/xml">
    <link href="backend.php?op=RSS2.0" title="<?= config('app.sitename'); ?> - RSS 2.0" rel="alternate" type="text/xml">
    <link href="backend.php?op=ATOM" title="<?= config('app.sitename'); ?> - ATOM" rel="alternate" type="application/atom+xml">

    <!-- import css et js -->
    <?php Event::fire('assets.css'); ?>
    <?php Event::fire('assets.header.js'); ?>
</head>
<body>

<?php require 'themes/'. $theme .'/Views/partials/header/header.php'; ?>

<div class="container">
    <?= $content; ?>
</div>

<?php require 'themes/'. $theme .'/Views/partials/footer/footer.php'; ?>

<!-- import js -->
<?php Event::fire('assets.footer.js'); ?>

</body>
</html>