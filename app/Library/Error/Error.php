<?php

namespace App\Library\Error;


class Error
{

    /**
     * Affiche un message d'erreur du forum et termine l'exécution.
     *
     * Cette fonction mappe les codes d'erreur internes du forum à des messages traduits,
     * affiche le message dans une alerte Bootstrap et inclut l'en-tête et le pied de page.
     *
     * @param string $e_code Code d'erreur du forum (ex: '0001', '0025', etc.)
     * @return void Cette fonction termine le script, ne retourne jamais.
     */
    public static function forumerror(string $e_code): void
    {
        global $sitename, $header;

        $errors = [
            '0001' => 'Pas de connexion à la base forums.',
            '0002' => 'Le forum sélectionné n\'existe pas.',
            '0004' => 'Pas de connexion à la base topics.',
            '0005' => 'Erreur lors de la récupération des messages depuis la base.',
            '0006' => 'Entrer votre pseudonyme et votre mot de passe.',
            '0007' => 'Vous n\'êtes pas le modérateur de ce forum, vous ne pouvez utiliser cette fonction.',
            '0008' => 'Mot de passe erroné, refaites un essai.',
            '0009' => 'Suppression du message impossible.',
            '0010' => 'Impossible de déplacer le topic dans le Forum, refaites un essai.',
            '0011' => 'Impossible de verrouiller le topic, refaites un essai.',
            '0012' => 'Impossible de déverrouiller le topic, refaites un essai.',
            '0013' => 'Impossible d\'interroger la base. <br />Error: sql_error()',
            '0014' => 'Utilisateur ou message inexistant dans la base.',
            '0015' => 'Le moteur de recherche ne trouve pas la base forum.',
            '0016' => 'Cet utilisateur n\'existe pas, refaites un essai.',
            '0017' => 'Vous devez obligatoirement saisir un sujet, refaites un essai.',
            '0018' => 'Vous devez choisir un icône pour votre message, refaites un essai.',
            '0019' => 'Message vide interdit, refaites un essai.',
            '0020' => 'Mise à jour de la base impossible, refaites un essai.',
            '0021' => 'Suppression du message sélectionné impossible.',
            '0022' => 'Une erreur est survenue lors de l\'interrogation de la base.',
            '0023' => 'Le message sélectionné n\'existe pas dans la base forum.',
            '0024' => 'Vous ne pouvez répondre à ce message, vous n\'en êtes pas le destinataire.',
            '0025' => 'Vous ne pouvez répondre à ce topic il est verrouillé. Contacter l\'administrateur du site.',
            '0026' => 'Le forum ou le topic que vous tentez de publier n\'existe pas, refaites un essai.',
            '0027' => 'Vous devez vous identifier.',
            '0028' => 'Mot de passe erroné, refaites un essai.',
            '0029' => 'Mise à jour du compteur des envois impossible.',
            '0030' => 'Le forum dans lequel vous tentez de publier n\'existe pas, merci de recommencez',
            '0031' => '', // Code spécial retourne 0
            '0035' => 'Vous ne pouvez éditer ce message, vous n\'en êtes pas le destinataire.',
            '0036' => 'Vous n\'avez pas l\'autorisation d\'éditer ce message.',
            '0037' => 'Votre mot de passe est erroné ou vous n\'avez pas l\'autorisation d\'éditer ce message, refaites un essai.',
            '0101' => 'Vous ne pouvez répondre à ce message.'
        ];

        if ($e_code === '0031') {
            return;
        }

        $error_msg = $errors[$e_code] ?? 'Erreur inconnue.';

        if (!isset($header)) {
            include 'header.php';
        }

        echo '<div class="alert alert-danger"><strong>' . $sitename . '<br />' . translate('Erreur du forum') . '</strong><br />';
        echo translate('Code d\'erreur :') . ' ' . $e_code . '<br /><br />';
        echo translate($error_msg) . '<br /><br />';
        echo '<a href="javascript:history.go(-1)" class="btn btn-secondary">' . translate('Retour en arrière') . '</a><br /></div>';

        include 'footer.php';
        die();
    }

}
