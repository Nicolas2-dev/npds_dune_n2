<div class="container content">
    <div class="row">
        <div class="col-md-12">

            <h1>400</h1>

            <?= __('Référent : {0}', Request::header('referer')); ?>

            <hr />

            <h3>Mauvaise requête</h3>
            <p>Cela pourrait être le résultat d'une requête de page invalide.</p>

            <div class="row mt-4">
                <div class="col-md-12 d-flex justify-content-center">
                    <?= Component::backtohome(); ?>
                </div>
            </div>

        </div>
    </div>
</div>
