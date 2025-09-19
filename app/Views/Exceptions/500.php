<div class="container content">
    <div class="row">
        <div class="col-md-12">

            <h1>500</h1>

            <hr />

            <h3>Erreur interne du serveur</h3>
            <p>Une erreur est survenue sur le serveur web.</p>

            <div class="row mt-4">
                <div class="col-md-6 d-flex justify-content-center">
                    <?= Component::backtohome(); ?>
                </div>
                <div class="col-md-6 d-flex justify-content-center">
                    <?= Component::reporterror(['error_message' => $exception->getMessage()]); ?>
                </div>
            </div>

        </div>
    </div>
</div>
