<?php

namespace App\Library\Database;

class Sql
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
     * Constructeur de la classe.
     * 
     * Initialise la connexion à la base de données en important
     * les fonctions MySQLi et en appelant la méthode connect().
     *
     * @return void
     */
    public function __construct() 
    {
        include_once APPPATH .'Library/Database/Drivers/Mysqli.php';

        self::connect();
    }

    /**
     * Crée une connexion MySQLi avec gestion des erreurs et options.
     *
     * @return \mysqli|false Retourne l'objet mysqli si la connexion est réussie, sinon false.
     */
    public function connect(): \mysqli|false
    {
        return Mysql_Connexion();
    }

    /**
     * Retourne le dernier message d'erreur SQL.
     *
     * @return string Le message d'erreur SQL.
     */
    public function error(): string
    {
        return sql_error();
    }

    /**
     * Exécute une requête SQL.
     *
     * @param string $sql La requête SQL à exécuter.
     * @return \mysqli_result|false Résultat de la requête ou false en cas d'échec.
     */
    public function query(string $sql): \mysqli_result|false
    {
        return sql_query($sql);
    }

    /**
     * Récupère une ligne du résultat sous forme de tableau associatif.
     *
     * @param \mysqli_result|null $q_id Résultat de la requête.
     * @return array|null Retourne la ligne en tableau associatif ou null si aucune.
     */
    public function fetch_assoc(\mysqli_result|null $q_id = null): array|null
    {
        return sql_fetch_assoc($q_id);
    }

    /**
     * Récupère une ligne du résultat sous forme de tableau numérique.
     *
     * @param \mysqli_result|null $q_id Résultat de la requête.
     * @return array|null Retourne la ligne en tableau numérique ou null si aucune.
     */
    public function fetch_row(\mysqli_result|null $q_id = null): array|null
    {
        return sql_fetch_row($q_id);
    }

    /**
     * Récupère une ligne du résultat sous forme de tableau mixte (associatif + numérique).
     *
     * @param \mysqli_result|null $q_id Résultat de la requête.
     * @return array|null Retourne la ligne en tableau mixte ou null si aucune.
     */
    public function fetch_array(\mysqli_result|null $q_id = null): array|null
    {
        return sql_fetch_array($q_id);
    }

    /**
     * Récupère une ligne du résultat sous forme d'objet.
     *
     * @param \mysqli_result|null $q_id Résultat de la requête.
     * @return object|null Retourne la ligne en objet ou null si aucune.
     */
    public function fetch_object(\mysqli_result|null $q_id = null): object|null
    {
        return sql_fetch_object($q_id);
    }

    /**
     * Retourne le nombre de lignes dans le résultat.
     *
     * @param \mysqli_result|null $q_id Résultat de la requête.
     * @return int Nombre de lignes du résultat.
     */
    public function num_rows(\mysqli_result|null $q_id = null): int
    {
        return sql_num_rows($q_id);
    }

    /**
     * Retourne le nombre de champs dans le résultat.
     *
     * @param \mysqli_result|null $q_id Résultat de la requête.
     * @return int Nombre de champs.
     */
    public function num_fields(\mysqli_result|null $q_id = null): int
    {
        return sql_num_fields($q_id);
    }

    /**
     * Retourne le nombre de lignes affectées par la dernière requête
     * (INSERT, UPDATE ou DELETE).
     *
     * @return int Nombre de lignes affectées.
     */
    public function affected_rows(): int
    {
        return sql_affected_rows();
    }

    /**
     * Retourne le dernier ID AUTO_INCREMENT généré.
     *
     * @return int L'ID généré.
     */
    public function last_id(): int
    {
        return sql_last_id();
    }

    /**
     * Liste les tables d'une base de données.
     *
     * @param string $dbnom Nom de la base (facultatif).
     * @return \mysqli_result|false Résultat de la requête ou false en cas d'échec.
     */
    public function list_tables(string $dbnom = ''): \mysqli_result|false
    {
        return sql_list_tables($dbnom);
    }

    /**
     * Sélectionne la base de données configurée.
     *
     * @return bool Retourne true si succès, false sinon.
     */
    public function select_db(): bool
    {
        return sql_select_db();
    }

    /**
     * Libère les ressources mémoire associées au résultat.
     *
     * @param \mysqli_result $q_id Résultat de la requête.
     * @return bool True si réussi, sinon false.
     */
    public function free_result(\mysqli_result $q_id)
    {
        return sql_free_result($q_id);
    }

    /**
     * Ferme la connexion MySQL.
     *
     * @return bool True si la fermeture est réussie, sinon false.
     */
    public function close()
    {
        return sql_close();
    }

    /**
     * Retourne le nom complet d'une table avec le préfixe configuré.
     *
     * @param string $table Nom de la table sans préfixe.
     * @return string Nom complet avec préfixe.
     */
    public function prefix(string $table = ''): string
    {
        return sql_prefix($table);
    }

}

