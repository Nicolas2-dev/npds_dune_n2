<?php

namespace App\Library\User;

use App\Library\User\UserMenu;
use App\Library\User\UserMessage;
use App\Library\User\UserPopover;
use App\Library\User\UserHiddenForm;


class User
{

    /**
     * Instance singleton du dispatcher.
     *
     * @var self|null
     */
    protected static ?self $instance = null;


    /**
     * Retourne l'instance singleton du dispatcher.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

    // UserPopover

    /**
     * Génère un avatar ou un popover utilisateur.
     *
     * Selon la valeur de `$avpop` :
     * - 1 : Affiche l'avatar seul.
     * - 2 : Affiche l'avatar avec un popover contenant les informations et liens de l'utilisateur.
     *
     * @param string $who Nom de l'utilisateur.
     * @param int $dim Taille de l'avatar (détermine la classe CSS `n-ava-$dim`).
     * @param int $avpop Mode d'affichage : 1 pour avatar seul, 2 pour popover.
     * @return string|null HTML de l'avatar ou du popover, ou null si l'utilisateur n'existe pas.
     */
    public function userPopover(string $who, int $dim, int $avpop): ?string
    {
        return UserPopover::render($who, $dim, $avpop);
    }
    
    /**
     * Affiche directement l'avatar ou le popover d'un utilisateur.
     *
     * Cette méthode utilise `userPopover()` pour générer le HTML et l'affiche immédiatement.
     *
     * @param string $who   Nom de l'utilisateur dont l'avatar/popover doit être affiché.
     * @param int    $dim   Taille de l'avatar (affecte la classe CSS `n-ava-$dim`).
     * @param int    $avpop Mode d'affichage : 
     *                      1 pour l'avatar seul, 
     *                      2 pour l'avatar avec un popover contenant les informations de l'utilisateur.
     *
     * @return void
     */
    public function displayPopover(string $who, int $dim, int $avpop): void
    {
        echo $this->userPopover($who, $dim, $avpop);
    }

    // UserMenu

    /**
     * Génère le menu utilisateur sous forme de HTML.
     *
     * Utilise la classe statique UserMenu pour construire le menu.
     *
     * @param bool   $minisite Indique si le miniSite est actif pour l'utilisateur.
     * @param string $uname    Nom d'utilisateur utilisé pour les liens miniSite.
     *
     * @return string|null Le HTML du menu utilisateur, ou null en cas d'erreur.
     */
    public function memberMenu(bool $minisite, string $uname): ?string
    {
        return UserMenu::render($minisite, $uname);
    }
    
    /**
     * Affiche directement le menu utilisateur.
     *
     * Cette méthode récupère le HTML via `memberMenu()` et l'affiche immédiatement.
     *
     * @param bool   $minisite Indique si le miniSite est actif pour l'utilisateur.
     * @param string $uname    Nom d'utilisateur utilisé pour les liens miniSite.
     *
     * @return void
     */
    public function displayMemberMenu(bool $minisite, string $uname): void
    {
        echo $this->memberMenu($minisite, $uname);
    }

    // UserMessage

    /**
     * Retourne le HTML d'un message d'erreur utilisateur.
     *
     * Utilise la classe statique UserMessage pour générer le message
     * en fonction de l'opération passée.
     *
     * @param string $message Contenu du message d'erreur
     * @param string $op      Contexte ou opération (ex : 'only_newuser', 'new user', 'finish')
     *
     * @return string|null Le HTML du message d'erreur, ou null si aucun message
     */
    public function messageError(string $message, string $op): ?string
    {
        return UserMessage::error($message, $op);
    }
    
    /**
     * Affiche directement un message d'erreur utilisateur.
     *
     * Utilise `messageError()` pour récupérer le HTML et l'affiche immédiatement.
     *
     * @param string $message Contenu du message d'erreur
     * @param string $op      Contexte ou opération
     *
     * @return void
     */
    public function displayMessageError(string $message, string $op): void
    {
        echo $this->messageError($message, $op);
    }

    /**
     * Retourne un message de confirmation ou d'information utilisateur.
     *
     * Utilise la classe statique UserMessage pour générer le message.
     *
     * @param string $message Contenu du message
     *
     * @return string|null Le message, ou null si vide
     */
    public function messagePass(string $message): ?string
    {
        return UserMessage::pass($message);
    }
    
    /**
     * Affiche directement un message de confirmation ou d'information utilisateur.
     *
     * Utilise `messagePass()` pour récupérer le message et l'affiche immédiatement.
     *
     * @param string $message Contenu du message
     *
     * @return void
     */
    public function displayMessagePass(string $message): void
    {
        echo $this->messagePass($message);
    }

    // UserHiddenForm

    /**
     * Retourne le HTML des champs cachés pour un formulaire utilisateur.
     *
     * Cette méthode utilise la classe statique UserHiddenForm pour générer
     * les champs <input type="hidden"> à inclure dans un formulaire.
     *
     * @return string|null Le HTML des champs cachés, ou null si aucun champ généré.
     */
    public function hiddenForm(): ?string
    {
        return UserHiddenForm::render();
    }
    
    /**
     * Affiche directement les champs cachés pour un formulaire utilisateur.
     *
     * Cette méthode utilise `hiddenForm()` pour récupérer le HTML
     * et l'affiche immédiatement.
     *
     * @return void
     */
    public function displayhIddenForm(): void
    {
        echo $this->hiddenForm();
    }

    // UserValidator

    /**
     * Valide un utilisateur et un email.
     *
     * @param string $uname Nom d'utilisateur
     * @param string $email Email de l'utilisateur
     * @return string|null Message d'erreur ou null si valide
     */
    public function validateUser(string $uname, string $email): ?string
    {
        return UserValidator::validateUser($uname, $email);
    }

    /**
     * Valide un identifiant utilisateur.
     *
     * Cette méthode vérifie que l'identifiant respecte les règles définies
     * dans UserValidator::username(), notamment :
     * - Caractères autorisés (a-z, A-Z, 0-9, _ et -)
     * - Longueur maximale de 25 caractères
     * - Pas de noms réservés
     * - Pas déjà utilisé dans la base de données
     *
     * @param string $uname L'identifiant utilisateur à valider.
     *
     * @return string|null Retourne un message d'erreur si l'identifiant est invalide, sinon null.
     */
    public function validateUsername(string $uname,): ?string
    {
        return UserValidator::username($uname);
    }

    /**
     * Valide une adresse email pour un utilisateur donné.
     *
     * Cette méthode utilise UserValidator::email() pour vérifier :
     * - Format valide d'une adresse email
     * - Absence d'espaces
     * - Domaine valide via DNS MX
     * - Non-utilisation déjà existante dans la base de données
     *
     * @param string $email L'adresse email à valider.
     * @param string $uname Nom d'utilisateur associé (optionnel, utilisé pour l'exclusion "edituser").
     *
     * @return string|null Retourne un message d'erreur si l'email est invalide, sinon null.
     */
    public function validateMail(string $email, string $uname = ''): ?string
    {
        return UserValidator::email($email, $uname);
    }

    /**
     * Vérifie si un nom d'utilisateur est réservé.
     *
     * @param string $uname Identifiant à tester
     * @return bool
     */
    public function validateIsReserved(string $uname): bool
    {
        return UserValidator::isReserved($uname);
    }

    /**
     * Retourne la liste des noms réservés.
     *
     * @return array<string>
     */
    public function validateGetReserved(): array
    {
        return UserValidator::getReserved();
    }

    /**
     * Ajoute des noms réservés dynamiquement.
     *
     * @param array<string> $words
     * @return void
     */
    public function validatAaddReserved(array $words): void
    {
        UserValidator::addReserved($words);
    }

}
