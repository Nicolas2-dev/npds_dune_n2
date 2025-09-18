<p class="lead">
    <?= translate('Sélectionner une catégorie'); ?>
</p>
<div class="d-flex flex-column flex-sm-row flex-wrap justify-content-between my-3 border rounded">
    <p class="p-2 mb-0 ">
        <?php if (($cate == translate('Tous')) or ($cate == '')): ?>
            <i class="fa fa-folder-open fa-2x text-body-secondary align-middle me-2"></i>
            <strong><span class="align-middle"><?= translate('Tous'); ?></span>
            <span class="badge bg-secondary ms-2 float-end my-2"><?= $acount; ?></span></strong>
        <?php else: ?>
            <a href="<?= site_url('download/' . translate('Tous') . '/' . $sortby); ?>">
                <i class="fa fa-folder fa-2x align-middle me-2"></i>
                <span class="align-middle"><?= translate('Tous'); ?></span>
            </a><span class="badge bg-secondary ms-2 float-end my-2"><?= $acount; ?></span>
        <?php endif; ?>
    </p>       
    <?php foreach($lists as $list): ?>
        <p class="p-2 mb-0">
            <?php if ($list['category'] == $cate): ?>
                <i class="fa fa-folder-open fa-2x text-body-secondary align-middle me-2"></i>
                <strong class="align-middle"><?= Language::affLangue($list['category']); ?>
                    <span class="badge bg-secondary ms-2 float-end my-2"><?= $list['dcount']; ?></span>
                </strong>
            <?php else: ?>
                <a href="<?= site_url('download.php?dcategory=' . $list['category2'] . '/' . $list['sortby']); ?>">
                    <i class="fa fa-folder fa-2x align-middle me-2"></i>
                    <span class="align-middle"><?=  Language::affLangue($list['category']); ?></span>
                </a><span class="badge bg-secondary ms-2 my-2 float-end"><?= $list['dcount']; ?></span>
            <?php endif; ?>
        </p>
    <?php endforeach; ?>
</div> 