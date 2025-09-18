<h2><?= translate('Chargement de fichiers'); ?></h2>
<hr />

<?php if (isset($download_list)): ?>
    <?= $download_list; ?>
<?php endif; ?>

<?php if (isset($download_lists)): ?>
    <?= $download_lists; ?>
<?php endif; ?>

<?php if (isset($download_ban)): ?>
    <?= $download_ban; ?>
<?php endif; ?>