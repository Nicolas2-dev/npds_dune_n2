<?php

namespace App\Library\Auth;

use App\Library\Groupe\Groupe;


class Auth
{

    /**
     * Récupère les informations d'un utilisateur depuis la table `users`.
     *
     * @param string $user Contenu du cookie encodé en base64 contenant les informations de l'utilisateur
     * @return array|null Retourne un tableau associatif contenant les informations de l'utilisateur,
     *                    ou null si l'utilisateur n'existe pas.
     */
    public static function getUserInfo(string $user): ?array
    {
        $cookie = explode(':', base64_decode($user));

        $result = sql_query("SELECT pass 
                             FROM " . sql_prefix('users') . " 
                             WHERE uname='$cookie[1]'");

        list($pass) = sql_fetch_row($result);

        if (($cookie[2] === md5($pass)) && ($pass !== '')) {
            $result = sql_query("SELECT uid, name, uname, email, femail, url, user_avatar, user_occ, user_from, user_intrest, user_sig, user_viewemail, user_theme, pass, storynum, umode, uorder, thold, noscore, bio, ublockon, ublock, theme, commentmax, user_journal, send_email, is_visible, mns, user_lnl 
                                 FROM " . sql_prefix('users') . " 
                                 WHERE uname='$cookie[1]'");

            if (sql_num_rows($result) == 1) {
                return sql_fetch_assoc($result);
            }
            //else {
            //
            //    // Pas d'echo ici, cela pourrait poser un problème d'affichage !
            //    // On retourne null ou on effectue un log.
            //    // echo '<strong>' . translate('Un problème est survenu') . '.</strong>';
            //}
        }

        return null;
    }

    /**
     * Vérifie et gère l'auto-enregistrement des utilisateurs.
     *
     * Si la configuration `autoRegUser` est activée et que l'utilisateur
     * ne dispose pas du droit de connexion, le cookie NPDS est réinitialisé.
     *
     * @return bool True si l'utilisateur peut rester connecté / auto-enregistré,
     *              False si le cookie NPDS a été réinitialisé.
     */
    public static function autoReg(): bool
    {
        global $autoRegUser, $user;

        $autoRegEnabled = (bool) $autoRegUser;

        if (!$autoRegEnabled) {
            if (isset($user)) {

                $cookie = explode(':', base64_decode($user));

                list($test) = sql_fetch_row(sql_query("SELECT open 
                                                       FROM " . sql_prefix('users_status') . " 
                                                       WHERE uid='$cookie[0]'"));

                if (!$test) {
                    setcookie('user', '', 0);

                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * Vérifie les autorisations NPDS.
     *
     * Retourne true ou false selon le type d'autorisation demandé.
     *
     * @param string $auto Type d'autorisation à vérifier. Les valeurs possibles peuvent être :
     *                     - 'admin'      : administrateur
     *                     - 'anonymous'  : utilisateur non connecté
     *                     - 'member'     : membre connecté
     *                     - 'group'      : groupe de membre
     *                     - 'all'        : tout le monde
     * @return bool True si l'utilisateur a l'autorisation, false sinon.
     */
    public static function autorisation(string $auto): bool
    {
        global $user, $admin;

        $affich = false;

        if (($auto == -1) and (!$user)) {
            $affich = true;
        }

        if (($auto == 1) and (isset($user))) {
            $affich = true;
        }

        if ($auto > 1) {
            $tab_groupe = Groupe::validGroup($user);

            if ($tab_groupe) {
                foreach ($tab_groupe as $groupevalue) {
                    if ($groupevalue == $auto) {
                        $affich = true;
                        break;
                    }
                }
            }
        }

        if ($auto == 0) {
            $affich = true;
        }

        if (($auto == -127) and ($admin)) {
            $affich = true;
        }

        return $affich;
    }

    /**
     * Vérifie le statut du visiteur.
     *
     * Cette fonction permet de savoir si le visiteur est :
     * - un membre enregistré
     * - un administrateur
     *
     * @param string $typeStatut Le type de statut à vérifier ('member' ou 'admin').
     * @return bool True si le visiteur correspond au type, false sinon.
     */
    public static function securStatic(string $typeStatut): bool
    {
        global $user, $admin;

        return match ($typeStatut) {
            'member' => isset($user),
            'admin'  => isset($admin),
            default  => false,
        };
    }
}
