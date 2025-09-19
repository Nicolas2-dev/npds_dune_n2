<?php

use App\Library\Components\BaseComponent;

/*
<?= Component::calcul([
    'opex' => '+',
    'premier' => 10,
    'deuxieme' => 5
]); ?>
<!-- Retournera 15 -->
*/

class CalculComponent extends BaseComponent
{
    /**
     * Effectue un calcul simple
     *
     * @param array $params ['opex' => '+', 'premier' => 10, 'deuxieme' => 5]
     * @return string|float Résultat du calcul ou message d'erreur
     */
    public function render(array $params = []): string|float
    {
        $opex     = $params['opex'] ?? '';
        $premier  = $params['premier'] ?? 0;
        $deuxieme = $params['deuxieme'] ?? 0;

        return match ($opex) {
            '+' => $premier + $deuxieme,
            '-' => $premier - $deuxieme,
            '*' => $premier * $deuxieme,
            '/' => $deuxieme == 0 ? 'Division by zero !' : $premier / $deuxieme,
            default => 'Opérateur inconnu'
        };
    }
}
