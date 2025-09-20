<?php

namespace App\Library\User;

use App\Support\Security\Hack;
use Npds\Support\Facades\Request;


class UserHiddenForm
{

    /**
     * Liste des champs cachés à générer dans le formulaire
     */
    private const SECURITY_FIELDS = [
        'uname', 'pass', 'name', 'user_from', 'user_occ', 'user_intrest', 'user_sig', 'user_lnl', 
        'C1','C2','C3','C4','C5','C6','C7','C8','M1','M2','T1','T2','B1'
    ];


    /**
     * Génère les champs cachés d'un formulaire pour un utilisateur.
     *
     * Récupère les valeurs via la classe Request plutôt que $_POST.
     *
     * @return string
     */
    public static function render(): string
    {
        $request = Request::getInstance();

        $fields = [
            'uname', 'name', 'email', 'user_avatar', 'user_from', 'user_occ', 'user_intrest',
            'user_sig', 'user_viewemail', 'pass', 'user_lnl',
            'C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8',
            'M1', 'M2', 'T1', 'T2', 'B1',
        ];

        $html = '<form action="user.php" method="post">';

        foreach ($fields as $field) {
            $value = $request->input($field, '');

            // Sécurité : nettoyage pour les champs texte et mdp
            $value = in_array($field, static::SECURITY_FIELDS)
                ? stripcslashes(Hack::removeHack($value))
                : htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

            $html .= '<input type="hidden" name="' . $field . '" value="' . $value . '" />';
        }

        return $html;
    }
}
