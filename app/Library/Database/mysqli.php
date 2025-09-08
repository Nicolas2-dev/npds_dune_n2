<?php

use App\Library\Log\Log;

/************************************************************************/
/* DUNE by NPDS                                                         */
/*                                                                      */
/* NPDS Copyright (c) 2001-2024 by Philippe Brunier                     */
/* =========================                                            */
/*                                                                      */
/* Multi DataBase Support - MysqlI                                      */
/* Copyright (c) JIRECK 2013                                            */
/* Mise à jour 2017/2024 jpb, nicolas2                                  */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 3 of the License.       */
/************************************************************************/

global $debugmysql;

define('NPDS_DEBUG', $debugmysql);

$sql_nbREQ = 0;

/**
 * Initialise la connexion à la base de données.
 * 
 * @return mysqli|false Retourne l'objet mysqli si la connexion est réussie, sinon false.
 */
function Mysql_Connexion(): mysqli|false
{
   $ret_p = sql_connect();

   if (!$ret_p) {
      $Titlesitename = 'NPDS';

      if (file_exists('storage/meta/meta.php')) {
         include 'storage/meta/meta.php';
      }

      if (file_exists('storage/static/database.txt')) {
         global $mysql_error, $dbhost, $dbname;
         include 'storage/static/database.txt';
      }

      die();
   }

   return $ret_p;
}

/**
 * Crée la connexion MySQLi avec gestion des erreurs et persistante optionnelle.
 * 
 * @return mysqli|false
 */
function sql_connect(): mysqli|false
{
   global $mysql_p, $dbhost, $dbuname, $dbpass, $dbname, $dblink, $mysql_error;

   try {
      mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

      $host = ($mysql_p || !isset($mysql_p)) ? 'p:' . $dbhost : $dbhost;

      $dblink = mysqli_init();
      $dblink->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
      $dblink->options(MYSQLI_INIT_COMMAND, "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
      $dblink->options(MYSQLI_SET_CHARSET_NAME, 'utf8mb4');

      if (!$dblink->real_connect($host, $dbuname, $dbpass, $dbname)) {
         throw new mysqli_sql_exception('Impossible de se connecter à la base de données');
      }

      return $dblink;
   } catch (mysqli_sql_exception $e) {
      $mysql_error = $e->getMessage();

      error_log($message = sprintf('Erreur de connexion SQL : %s', $mysql_error));

      if (defined('NPDS_DEBUG') && NPDS_DEBUG) {
         Log::ecrireLog('mysql', $message, '');
      }

      return false;
   }
}

/**
 * Retourne le dernier message d'erreur SQL.
 */
function sql_error(): string
{
   global $dblink;

   if (!$dblink) {
      return 'Pas de connexion à la base de données';
   }

   $error = mysqli_error($dblink);

   if ($error) {
      // Log l'erreur pour le debugging
      error_log('Erreur SQL : ' . $error);
   }

   return $error;
}

/**
 * Exécute une requête SQL avec échappement amélioré pour INSERT/UPDATE.
 * 
 * @param string $sql
 * @return mysqli_result|false
 */
function sql_query(string $sql): mysqli_result|false
{
   global $sql_nbREQ, $dblink;

   $sql_nbREQ++;

   // affiche toutes les requêtes du portail
   //var_dump($sql);

   // Fonction d'échappement améliorée
   $escape_value = function ($value) use ($dblink) {
      // D'abord on retire les slashes existants
      $value = stripslashes($value);

      // On échappe avec mysqli_real_escape_string
      $value = mysqli_real_escape_string($dblink, $value);

      // Debug
      if (defined('NPDS_DEBUG') && NPDS_DEBUG) {
         error_log('Valeur avant échappement : ' . $value);
         error_log('Valeur après échappement : ' . $value);
      }

      return $value;
   };

   if (stripos($sql, 'INSERT') === 0 || stripos($sql, 'UPDATE') === 0) {
      $pattern = '/^(INSERT\s+INTO.*?VALUES\s*\()(.*)(\))$|^(UPDATE.*?SET\s+)(.*?)(\s*WHERE.*|\s*$)/is';

      if (preg_match($pattern, $sql, $matches)) {

         // INSERT
         if (!empty($matches[2])) {
            $values = $matches[2];

            // On traite chaque valeur entre guillemets
            $values = preg_replace_callback(
               '/\'((?:[^\'\\\\]|\\\\.)*)\'/s',
               function ($m) use ($escape_value) {
                  return "'" . $escape_value($m[1]) . "'";
               },
               $values
            );

            $sql = $matches[1] . $values . $matches[3];

            // UPDATE   
         } elseif (!empty($matches[5])) {
            $values = $matches[5];
            $values = preg_replace_callback(
               '/=\s*\'((?:[^\'\\\\]|\\\\.)*)\'/s',
               function ($m) use ($escape_value) {
                  return "= '" . $escape_value($m[1]) . "'";
               },
               $values
            );

            $sql = $matches[4] . $values . $matches[6];
         }
      }
   }

   if (defined('NPDS_DEBUG') && NPDS_DEBUG) {
      error_log($message = sprintf('Requête finale : %s', $sql));

      Log::ecrireLog('mysql', $message, '');
   }

   $query_id = mysqli_query($dblink, $sql);

   if (!$query_id) {
      // Utilisation de sql_error() pour récupérer l'erreur de requête
      error_log($message = sprintf('Échec de la requête : %s - Erreur : %s', $sql, sql_error()));

      if (defined('NPDS_DEBUG') && NPDS_DEBUG) {
         Log::ecrireLog('mysql', $message, '');
      }

      return false;
   }

   return $query_id;
}

/**
 * Retourne le résultat en tableau associatif.
 */
function sql_fetch_assoc(mysqli_result|null $q_id = null): array|null
{
   if (empty($q_id)) {
      global $query_id;

      $q_id = $query_id;
   }

   return mysqli_fetch_assoc($q_id);
}

/**
 * Retourne le résultat en tableau numérique.
 */
function sql_fetch_row(mysqli_result|null $q_id = null): array|null
{
   if (empty($q_id)) {
      global $query_id;

      $q_id = $query_id;
   }

   return mysqli_fetch_row($q_id);
}

/**
 * Retourne le résultat en tableau mixte.
 */
function sql_fetch_array(mysqli_result|null $q_id = null): array|null
{
   if (empty($q_id)) {
      global $query_id;

      $q_id = $query_id;
   }

   return mysqli_fetch_array($q_id);
}

/**
 * Retourne le résultat sous forme d'objet.
 */
function sql_fetch_object(mysqli_result|null $q_id = null): object|null
{
   if (empty($q_id)) {
      global $query_id;

      $q_id = $query_id;
   }

   return mysqli_fetch_object($q_id);
}

/**
 * Retourne le nombre de lignes du résultat.
 */
function sql_num_rows(mysqli_result|null $q_id = null): int
{
   if (empty($q_id)) {
      global $query_id;

      $q_id = $query_id;
   }

   return mysqli_num_rows($q_id);
}

/**
 * Retourne le nombre de champs dans la requête.
 */
function sql_num_fields(mysqli_result|null $q_id = null): int
{
   global $dblink;

   if (empty($q_id)) {
      global $query_id;

      $q_id = $query_id;
   }

   return mysqli_field_count($dblink);
}

/**
 * Retourne le nombre de lignes affectées par INSERT, UPDATE, DELETE.
 */
function sql_affected_rows(): int
{
   global $dblink;

   return mysqli_affected_rows($dblink);
}

/**
 * Retourne le dernier ID AUTO_INCREMENT.
 */
function sql_last_id(): int
{
   global $dblink;

   return mysqli_insert_id($dblink);
}

/**
 * Liste les tables d'une base de données.
 */
function sql_list_tables(string $dbnom = ''): mysqli_result|false
{
   if (empty($dbnom)) {
      global $dbname;

      $dbnom = $dbname;
   }

   return sql_query("SHOW TABLES FROM $dbnom");
}

/**
 * Sélectionne la base de données courante.
 */
function sql_select_db(): bool
{
   global $dbname, $dblink;

   if (!mysqli_select_db($dblink, $dbname)) {
      return false;
   } else {
      return true;
   }
}

/**
 * Libère les ressources de la requête.
 */
function sql_free_result(mysqli_result $q_id) //: void //: bool
{
   if ($q_id instanceof mysqli_result) {
      return mysqli_free_result($q_id);
   }
}

/**
 * Ferme la connexion MySQL.
 */
function sql_close() // : void // : bool
{
   global $dblink, $mysql_p;

   if (!$mysql_p) {
      return mysqli_close($dblink);
   }
}

/**
 * Retourne le nom complet d'une table avec préfixe.
 */
function sql_prefix(string $table = ''): string
{
   return PREFIX . $table;
}
