<?php

namespace Npds\Events;

class Dispatcher
{

    /**
     * Instance singleton du dispatcher.
     *
     * @var self|null
     */
    protected static ?self $instance = null;

    /**
     * Liste des écouteurs pour chaque événement.
     *
     * @var array<string, array<int, array<int, callable>>>
     */
    protected array $listeners = [];

    /**
     * Liste triée des écouteurs par événement.
     *
     * @var array<string, array<int, callable>>
     */
    protected array $sorted = [];

    /**
     * Pile des événements en cours de déclenchement.
     *
     * @var string[]
     */
    protected array $firing = [];


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
     * Enregistre un écouteur pour un ou plusieurs événements.
     *
     * @param string|string[] $events   Nom(s) de l'événement
     * @param callable        $listener Callback à exécuter
     * @param int             $priority Priorité de l'écouteur (plus grand = exécuté en premier)
     *
     * @return void
     */
    public function listen(string|array $events, callable $listener, int $priority = 0): void
    {
        foreach ((array) $events as $event) {
            $this->listeners[$event][$priority][] = $listener;

            unset($this->sorted[$event]);
        }
    }

    /**
     * Vérifie si un événement possède des écouteurs.
     *
     * @param string $eventName Nom de l'événement
     *
     * @return bool
     */
    public function hasListeners(string $eventName): bool
    {
        return isset($this->listeners[$eventName]);
    }

    /**
     * Déclenche un événement et retourne la première réponse non nulle.
     *
     * @param string       $event   Nom de l'événement
     * @param array<mixed> $payload Données à passer aux écouteurs
     *
     * @return mixed
     */
    public function until(string $event, array $payload = []): mixed
    {
        return $this->fire($event, $payload, true);
    }

    /**
     * Retourne le dernier événement en cours de déclenchement.
     *
     * @return string|null
     */
    public function firing(): ?string
    {
        return last($this->firing);
    }

    /**
     * Déclenche un événement.
     *
     * @param string|object $event   Nom de l'événement ou objet
     * @param array         $payload Données à passer aux écouteurs
     * @param bool          $halt    Arrêter à la première réponse non nulle si true
     *
     * @return mixed|null Retourne soit le résultat du listener si $halt=true, soit un tableau des réponses, ou null
     */
    public function fire(string|object $event, array $payload = [], bool $halt = false): mixed
    {
        $responses = array();

        if (is_object($event)) {
            list ($payload, $event) = array(array($event), get_class($event));
        }

        // S'assure que la charge utile est un tableau.
        else if (! is_array($payload)) {
            $payload = array($payload);
        }

        $this->firing[] = $event;

        foreach ($this->getListeners($event) as $listener) {
            $response = call_user_func_array($listener, $payload);

            if (! is_null($response) && $halt) {
                array_pop($this->firing);

                return $response;
            } else if ($response === false) {
                break;
            }

            $responses[] = $response;
        }

        array_pop($this->firing);

        return $halt ? null : $responses;
    }

    /**
     * Retourne tous les écouteurs triés d’un événement.
     *
     * @param string $eventName Nom de l'événement
     *
     * @return array<int, callable>
     */
    public function getListeners(string $eventName): array
    {
        if (! isset($this->sorted[$eventName])) {
            $this->sortListeners($eventName);
        }

        return $this->sorted[$eventName];
    }

    /**
     * Trie les écouteurs d’un événement par priorité.
     *
     * @param string $eventName Nom de l'événement
     *
     * @return void
     */
    protected function sortListeners(string $eventName): void
    {
        $this->sorted[$eventName] = array();

        if (isset($this->listeners[$eventName])) {
            krsort($this->listeners[$eventName]);

            $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
        }
    }

    /**
     * Supprime tous les écouteurs d’un événement.
     *
     * @param string $event Nom de l'événement
     *
     * @return void
     */
    public function forget(string $event): void
    {
        unset($this->listeners[$event], $this->sorted[$event]);
    }

}
