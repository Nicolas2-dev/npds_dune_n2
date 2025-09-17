<h1><?= $title ?></h1>
<hr>

<p><?= $content; ?></p>

<?php if (isset($paginator)): ?>
    <?= $paginator; ?>
<?php endif; ?>