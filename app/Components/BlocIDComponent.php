<?php

use App\Support\Facades\Block;
use App\Library\Components\BaseComponent;

/*
<?= Component::blocID('R1'); ?>        <!-- Bloc droite ID 1 -->
<?= Component::blocID('L2'); ?>        <!-- Bloc gauche ID 2 -->
<?= Component::blocID(['code' => 'R3']); ?> 
<?= Component::blocID(['L4']); ?>
*/

class BlocIDComponent extends BaseComponent
{
    /**
     * Rend le contenu d’un bloc côté R (droite) ou L (gauche) via son ID
     *
     * @param array|string $params Exemple : "R1" ou "L2", ou ['code' => 'R1']
     * @return string Contenu du bloc
     */
    public function render(array|string $params = []): string
    {
        // Récupérer le code
        $code = '';
        if (is_string($params)) {
            $code = $params;
        } elseif (is_array($params) && isset($params['code'])) {
            $code = $params['code'];
        } elseif (is_array($params) && isset($params[0])) {
            $code = $params[0];
        }

        if (!$code) {
            return '';
        }

        // Extraire le côté (R ou L) et le numéro
        $side = strtoupper(substr($code, 0, 1)); // R ou L
        $id   = substr($code, 1);               // numéro

        if (!in_array($side, ['R', 'L'])) {
            return ''; // côté invalide
        }

        // Appel de oneblock avec la syntaxe attendue
        return @Block::oneBlock($id, $side . 'B') ?: '';
    }
}

