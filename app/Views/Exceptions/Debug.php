<div class="row page-header">
    <h1>Erreur Npds !</h1>
</div>

<div class="row">
    <p>
        <?= $exception->getMessage(); ?> in <?= $exception->getFile(); ?> on line <?= $exception->getLine(); ?>
    </p>
    <br>
    <pre><?= $exception->getTraceAsString(); ?></pre>

    <div class="row mt-4">
        <div class="col-md-12 d-flex justify-content-center">
            <?= Component::backtohome(); ?>
        </div>
    </div>

</div>