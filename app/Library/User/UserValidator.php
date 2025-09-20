<?php

namespace App\Library\User;

use App\Support\Facades\Forum;


class UserValidator
{

    /**
     * Liste des noms d'utilisateurs réservés.
     *
     * @var array<string>
     */
    private static array $reserved = [
        'root', 'adm', 'linux', 'webmaster', 'admin', 'god',
        'administrator', 'administrador', 'nobody', 'anonymous',
        'anonimo', 'an€nimo', 'operator', 'dune', 'netadm'
    ];


    /**
     * Valide à la fois le nom d'utilisateur et l'email
     *
     * @param string $uname Nom d'utilisateur à vérifier
     * @param string $email Email à vérifier
     *
     * @return string|null Message d'erreur ou null si tout est OK
     */
    public static function validateUser(string $uname, string $email): ?string
    {
        $err = self::username($uname);
        if ($err) {
            return $err;
        }

        $err = self::email($email, $uname);
        if ($err) {
            return $err;
        }

        return null; 
    }

    /**
     * Valide un identifiant utilisateur.
     *
     * Vérifie que l'identifiant :
     * - n'est pas vide
     * - ne contient que des caractères autorisés (a-z, A-Z, 0-9, _ et -)
     * - ne dépasse pas 25 caractères
     * - n'est pas un nom réservé
     * - ne contient pas d'espaces
     * - n'est pas déjà utilisé dans la base de données
     *
     * @param string $uname Identifiant à valider
     * @return string|null Message d'erreur si invalide, sinon null
     */
    public static function username(string $uname): ?string
    {
        if (empty($uname) || preg_match('#[^a-zA-Z0-9_-]#', $uname)) {
            return translate('Erreur : identifiant invalide');
        }

        if (strlen($uname) > 25) {
            return translate('Votre surnom est trop long. Il doit faire moins de 25 caractères.');
        }

        if (self::isReserved($uname)) {
            return translate('Erreur : nom réservé.');
        }

        if (str_contains($uname, ' ')) {
            return translate('Il ne peut pas y avoir d\'espace dans le surnom.');
        }

        if (sql_num_rows(sql_query("SELECT uname 
                                    FROM " . sql_prefix('users') . " 
                                    WHERE uname='" . addslashes($uname) . "'")) > 0) {
            return translate('Erreur : cet identifiant est déjà utilisé');
        }

        return null;
    }

    /**
     * Valide une adresse email.
     *
     * Vérifie que l'adresse :
     * - n'est pas vide
     * - correspond au format standard d'une adresse email
     * - ne contient pas d'espaces
     * - possède un domaine valide (via DNS MX)
     * - n'est pas déjà utilisée dans la base de données (sauf pour l'utilisateur "edituser")
     *
     * @param string $email Adresse email à valider
     * @param string $uname Identifiant utilisateur (optionnel, utilisé pour vérifier l'exclusion "edituser")
     * @return string|null Message d'erreur si invalide, sinon null
     */
    public static function email(string $email, string $uname = ''): ?string
    {
        if (empty($email) || !preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $email)) {
            return translate('Erreur : Email invalide');
        }

        if (str_contains($email, ' ')) {
            return translate('Erreur : une adresse Email ne peut pas contenir d\'espaces');
        }

        if (Forum::checkDnsMail($email) === false) {
            return translate('Erreur : DNS ou serveur de mail incorrect');
        }

        if ($uname !== 'edituser' && sql_num_rows(sql_query("SELECT email FROM " . sql_prefix('users') . " WHERE email='$email'")) > 0) {
            return translate('Erreur : adresse Email déjà utilisée');
        }

        return null;
    }

    /**
     * Ajoute dynamiquement de nouveaux noms réservés.
     *
     * @param array<string> $words Liste de mots à ajouter aux noms réservés
     * @return void
     */
    public static function addReserved(array $words): void
    {
        foreach ($words as $word) {
            self::$reserved[] = strtolower($word);
        }
        self::$reserved = array_unique(self::$reserved);
    }

    /**
     * Retourne la liste complète des noms réservés.
     *
     * @return array<string> Tableau des noms réservés
     */
    public static function getReserved(): array
    {
        return self::$reserved;
    }

    /**
     * Vérifie si un identifiant est un nom réservé.
     *
     * La comparaison est insensible à la casse.
     *
     * @param string $uname Identifiant à vérifier
     * @return bool true si l'identifiant est réservé, false sinon
     */
    public static function isReserved(string $uname): bool
    {
        return in_array(strtolower($uname), self::$reserved, true);
    }

}
