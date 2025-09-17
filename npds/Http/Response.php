<?php

namespace Npds\Http;

class Response
{

    /**
     * Contenu de la réponse.
     *
     * @var string|object
     */
    protected string|object $content = '';

    /**
     * Code de statut HTTP.
     *
     * @var int
     */
    protected int $status = 200;

    /**
     * Tableau des en-têtes HTTP.
     *
     * @var array<string, string>
     */
    protected array $headers = [];

    /**
     * Statuts HTTP connus.
     *
     * @var array<int, string>
     */
    public static array $statuses = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    ];

    /**
     * Constructeur de la réponse.
     *
     * @param string|object|null $content Contenu de la réponse
     * @param int $status Code de statut HTTP
     * @param array<string, string> $headers En-têtes HTTP
     */
    public function __construct(string|object|null $content = '', int $status = 200, array $headers = [])
    {
        if (isset(self::$statuses[$status])) {
            $this->status = $status;
        }

        $this->headers = $headers;
        $this->content = $content ?? '';
    }

    /**
     * Envoie la réponse HTTP complète.
     *
     * @return void
     */
    public function send(): void
    {
        $protocol = $_SERVER['SERVER_PROTOCOL'];

        if (! headers_sent()) {
            $status = $this->status();

            header("$protocol $status " . self::$statuses[$status]);

            foreach ($this->headers as $name => $value) {
                header("$name: $value", true);
            }
        }

        echo $this->render();
    }

    /**
     * Rend le contenu de la réponse en chaîne de caractères.
     *
     * @return string
     */
    public function render(): string
    {
        $content = $this->content();

        if (is_object($content) && method_exists($content, '__toString')) {
            $content = $content->__toString();
        } else {
            $content = (string) $content;
        }

        return trim($content);
    }

    /**
     * Définit un en-tête HTTP.
     *
     * @param string $name Nom de l'en-tête
     * @param string $value Valeur de l'en-tête
     * @return $this
     */
    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Retourne tous les en-têtes HTTP.
     *
     * @return array<string, string>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Récupère ou définit le code de statut HTTP.
     *
     * @param int|null $status Nouveau code de statut (optionnel)
     * @return int|self
     */
    public function status(?int $status = null): int|self
    {
        if (is_null($status)) {
            return $this->status;
        } else if (isset(self::$statuses[$status])) {
            $this->status = $status;
        }

        return $this;
    }

    /**
     * Récupère le contenu de la réponse.
     *
     * @return string|object|null
     */
    public function content(): string|object
    {
        return $this->content;
    }

    /**
     * Setter explicite si besoin.
     */
    public function setContent(string|object $content): self
    {
        $this->content = $content;
        
        return $this;
    }

    /**
     * Convertit la réponse en chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

}
