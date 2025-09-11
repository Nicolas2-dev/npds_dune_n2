<?php

namespace App\Library\Chat;

use App\Support\Sanitize;
use App\Support\Facades\Block;
use App\Support\Security\Hack;
use Npds\Support\Facades\Request;


class Chat
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
     * Retourne le nombre d'utilisateurs connectés au chat pour un contexte donné.
     *
     * @param string $pour Contexte ou identifiant pour filtrer les autorisations
     * @return int Nombre d'utilisateurs connectés
     */
    public function ifChat(string $pour): int
    {
        $auto = Block::autorisationBlock('params#' . $pour);

        $activeChatUsers = 0;

        if (count($auto) <= 1) {
            $activeWindow = time() - 60 * 3; // 3 minutes

            $activeChatUsers = sql_num_rows(sql_query(
                "SELECT DISTINCT ip 
                 FROM " . sql_prefix('chatbox') . " 
                 WHERE id='" . (int) $auto[0] . "' 
                 AND date >= " . $activeWindow . ""
            ));
        }

        return $activeChatUsers;
    }

    /**
     * Insère un message dans la table `chatbox`.
     *
     * @param string $username Nom de l'utilisateur qui envoie le message
     * @param string $message  Contenu du message
     * @param int    $dbname   ID de la base ou du contexte (table liée)
     * @param int    $id       ID du groupe pour filtrer les messages
     * @return void
     */
    public function insertChat(string $username, string $message, int $dbname, int $id): void
    {
        if ($message === '') {
            return;
        }

        // Nettoyage des données
        $username   = Hack::removeHack(stripslashes(Sanitize::fixQuotes(strip_tags(trim($username)))));
        $message    = Hack::removeHack(stripslashes(Sanitize::fixQuotes(strip_tags(trim($message)))));
        $ip         = Request::getip();

        // Insertion en base
        sql_query("INSERT INTO " . sql_prefix('chatbox') . " 
                   VALUES ('" . $username . "', '" . $ip . "', '" . $message . "', '" . time() . "', '$id', " . $dbname . ")");
    }
    
}
