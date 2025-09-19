<?php

namespace App\Components;

use App\Library\Components\BaseComponent;

/*
Exemple d'appel :
    <?= Component::reporterror(['error_message' => $exception->getMessage()]); ?>
*/

class ReportErrorComponent extends BaseComponent
{
    /**
     * Rend un formulaire pour envoyer un rapport d’erreur.
     *
     * @param array $params Doit contenir 'error_message' (string)
     *
     * @return string HTML
     */
    public function render(array $params = []): string
    {
        $errorMessage = $params['error_message'] ?? '';
        $action = $params['action'] ?? '/report-error';
        $buttonLabel = $params['button_label'] ?? 'Envoyer le rapport';

        return '<form method="post" action="' . htmlspecialchars($action) . '">
                    <input type="hidden" name="error_message" value="' . htmlspecialchars($errorMessage) . '">
                    <button type="submit" class="btn btn-warning">' . htmlspecialchars($buttonLabel) . '</button>
                </form>';
    }
}
