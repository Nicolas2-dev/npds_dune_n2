<?php

namespace Npds\Http;

use Npds\Support\Arr;

class Request
{

    /**
     * Instance unique de la requête (Singleton)
     *
     * @var self|null
     */
    protected static ?self $instance = null;

    /**
     * Méthode HTTP de la requête
     *
     * @var string
     */
    protected string $method;

    /**
     * Tableau des en-têtes HTTP
     *
     * @var array<string, string>
     */
    protected array $headers;

    /**
     * Tableau $_SERVER
     *
     * @var array<string, mixed>
     */
    protected array $server;

    /**
     * Tableau $_GET
     *
     * @var array<string, mixed>
     */
    protected array $query;

    /**
     * Tableau $_POST
     *
     * @var array<string, mixed>
     */
    protected array $post;

    /**
     * Tableau $_FILES
     *
     * @var array<string, mixed>
     */
    protected array $files;

    /**
     * Tableau $_COOKIE
     *
     * @var array<string, mixed>
     */
    protected array $cookies;

    /**
     * Crée une nouvelle instance de la requête.
     *
     * @param string $method  Méthode HTTP de la requête
     * @param array  $headers Tableau des en-têtes HTTP
     * @param array  $server  Tableau $_SERVER
     * @param array  $query   Tableau $_GET
     * @param array  $post    Tableau $_POST
     * @param array  $files   Tableau $_FILES
     * @param array  $cookies Tableau $_COOKIE
     */
    public function __construct(
        string $method,
        array $headers,
        array $server,
        array $query,
        array $post,
        array $files,
        array $cookies
    ) {
        $this->method = strtoupper($method);

        $this->headers = array_change_key_case($headers);

        $this->server  = $server;
        $this->query   = $query;
        $this->post    = $post;
        $this->files   = $files;
        $this->cookies = $cookies;
    }

    /**
     * Récupère l'instance unique de la requête (Singleton)
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (isset(static::$instance)) {
            return static::$instance;
        }

        // Récupère la méthode HTTP.
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';

        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            $method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        } elseif (isset($_REQUEST['_method'])) {
            $method = $_REQUEST['_method'];
        }

        // Récupère les en-têtes de la requête.
        $headers = apache_request_headers();

        return static::$instance = new static($method, $headers, $_SERVER, $_GET, $_POST, $_FILES, $_COOKIE);
    }

    /**
     * Retourne l'instance courante
     *
     * @return self
     */
    public function instance(): self
    {
        return $this;
    }

    /**
     * Retourne la méthode HTTP
     *
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Retourne le chemin de la requête
     *
     * @return string
     */
    public function path(): string
    {
        return parse_url($this->server['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    }

    /**
     * Retourne l'adresse IP du client
     *
     * @return string
     */
    public function ip(): string
    {
        if (! empty($this->server['HTTP_CLIENT_IP'])) {
            return $this->server['HTTP_CLIENT_IP'];
        } else if (! empty($this->server['HTTP_X_FORWARDED_FOR'])) {
            return $this->server['HTTP_X_FORWARDED_FOR'];
        }

        return $this->server['REMOTE_ADDR'];
    }

    /**
     * Détermine si la requête est AJAX
     *
     * @return bool
     */
    public static function ajax(): bool
    {
        if (! is_null($header = Arr::get(self::$server, 'HTTP_X_REQUESTED_WITH'))) {
            return strtolower($header) === 'xmlhttprequest';
        }

        return false;
    }

    /**
     * Retourne l'URL de la page précédente
     *
     * @return string|null
     */
    public function previous(): ?string
    {
        return Arr::get($this->server, 'HTTP_REFERER');
    }

    /**
     * Retourne une valeur du tableau $_SERVER
     *
     * @param string|null $key
     * @return mixed
     */
    public function server(?string $key = null): mixed
    {
        if (is_null($key)) {
            return $this->server;
        }

        return Arr::get($this->server, $key);
    }

    /**
     * Récupère une donnée de la requête GET ou POST selon la méthode
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function input(string $key, mixed $default = null): mixed
    {
        $input = ($this->method == 'GET') ? $this->query : $this->post;

        return Arr::get($input, $key, $default);
    }

    /**
     * Retourne une valeur du tableau $_GET
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function query(?string $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return $this->query;
        }

        return Arr::get($this->query, $key, $default);
    }

    /**
     * Retourne tous les fichiers téléchargés
     *
     * @return array<string, mixed>
     */
    public function files(): array
    {
        return $this->files;
    }

    /**
     * Retourne un fichier spécifique
     *
     * @param string $key
     * @return mixed|null
     */
    public function file(string $key): mixed
    {
        return Arr::get($this->files, $key);
    }

    /**
     * Vérifie si un fichier existe
     *
     * @param string $key
     * @return bool
     */
    public function hasFile(string $key): bool
    {
        return Arr::has($this->files, $key);
    }

    /**
     * Retourne tous les cookies
     *
     * @return array<string, mixed>
     */
    public function cookies(): array
    {
        return $this->cookies;
    }

    /**
     * Retourne un cookie spécifique
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function cookie(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->cookies, $key, $default);
    }

    /**
     * Vérifie si un cookie existe
     *
     * @param string $key
     * @return bool
     */
    public function hasCookie(string $key): bool
    {
        return Arr::has($this->cookies, $key);
    }

}
