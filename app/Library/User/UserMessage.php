<?php

namespace App\Library\User;

use App\Library\User\Traits\HiddenFormTrait;


class UserMessage
{

    /**
     * Affiche un message d'erreur pour l'utilisateur.
     *
     * @param string $message Message d'erreur
     * @param string $op      Contexte/opération ('only_newuser', 'new user', 'finish', etc.)
     *
     * @return string
     */
    public static function error(string $message, string $op): string
    {
        $html = '<h2>' . translate('Utilisateur') . '</h2>
        <div class="alert alert-danger lead">';

        $html .= $message;

        if (in_array($op, ['only_newuser', 'new user', 'finish'], true)) {

            $html .= UserHiddenForm::render();

            $html .=  '<input type="hidden" name="op" value="only_newuser" />
                <button class="btn btn-secondary mt-2" type="submit">' . translate('Retour en arrière') . '</button>
            </form>';
        } else {
            $html .=  '<a class="btn btn-secondary mt-4" href="javascript:history.go(-1)" title="' . translate('Retour en arrière') . '">' . translate('Retour en arrière') . '</a>';
        }

        $html .=  '</div>';

        return $html;
    }

    /**
     * Affiche un message simple (ex: confirmation de mot de passe).
     *
     * @param string $message Message à afficher
     *
     * @return string
     */
    public static function pass(string $message): string
    {
        return $message;
    }

}
