<?php

namespace App\Library\Cookie;

use Npds\Config\Config;


class Cookie
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

    /**
     * Décode le cookie membre et vérifie certaines informations (ex. mot de passe).
     *
     * @param string $user Valeur du cookie à décoder
     * @return mixed Données décodées du cookie ou false si invalide
     */
    public function cookieDecode(string $user): ?array
    {
        global $language;

        $stop = false;

        if (array_key_exists('user', $_GET)) {
            if ($_GET['user'] != '') {
                $stop = true;
                $user = 'BAD-GET';
            }
        }

        if ($user) {
            $cookie = explode(':', base64_decode($user));

            settype($cookie[0], 'integer');

            if (trim($cookie[1]) != '') {
                $result = sql_query("SELECT pass, user_langue 
                                    FROM " . sql_prefix('users') . " 
                                    WHERE uname='$cookie[1]'");

                if (sql_num_rows($result) == 1) {
                    list($pass, $user_langue) = sql_fetch_row($result);

                    if (($cookie[2] == md5($pass)) and ($pass != '')) {
                        if ($language != $user_langue) {
                            sql_query("UPDATE " . sql_prefix('users') . " 
                                    SET user_langue='$language' 
                                    WHERE uname='$cookie[1]'");
                        }

                        return $cookie;
                    } else {
                        $stop = true;
                    }
                } else {
                    $stop = true;
                }
            } else {
                $stop = true;
            }

            if ($stop) {
                setcookie('user', '', 0);

                unset($user);
                unset($cookie);

                header('Location: index.php');
            }
        }

        return null;
    }

    /**
     * Définit les cookies de l'utilisateur pour la session et la langue.
     *
     * @param int    $setuid          ID de l'utilisateur
     * @param string $setuname        Nom d'utilisateur
     * @param string $setpass         Mot de passe en clair (sera hashé en MD5)
     * @param int    $setstorynum     Nombre d'histoires/éléments associés à l'utilisateur
     * @param int    $setumode        Mode utilisateur
     * @param int    $setuorder       Ordre d'affichage des contenus
     * @param int    $setthold        Seuil de filtrage
     * @param int    $setnoscore      Indicateur "pas de score"
     * @param int    $setublockon     Indicateur de blocage
     * @param string $settheme        Thème choisi
     * @param int    $setcommentmax   Nombre maximum de commentaires
     * @param string $user_langue     Code langue de l'utilisateur
     *
     * @return void
     */
    public function docookie(
        int $setuid,
        string $setuname,
        string $setpass,
        int $setstorynum,
        int $setumode,
        int $setuorder,
        int $setthold,
        int $setnoscore,
        int $setublockon,
        string $settheme,
        int $setcommentmax,
        string $user_langue
    ): void {
        $info = base64_encode("$setuid:$setuname:" . md5($setpass) . ":$setstorynum:$setumode:$setuorder:$setthold:$setnoscore:$setublockon:$settheme:$setcommentmax");

        $timeX = time() + (3600 * Config::get('cookie.user_cook_duration', 1));

        setcookie('user', $info, $timeX);

        if ($user_langue != '') {
            setcookie('user_language', $user_langue, $timeX);
        }
    }

}
