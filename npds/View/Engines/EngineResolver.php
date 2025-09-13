<?php

namespace Npds\View\Engines;

use Closure;


class EngineResolver
{

    /**
     * Tableau associatif contenant les closures permettant de créer chaque moteur.
     *
     * @var array<string, \Closure>
     */
    protected $resolvers = array();

    /**
     * Tableau des moteurs déjà résolus et instanciés.
     *
     * @var array<string, mixed>
     */
    protected $resolved = array();


    /**
     * Enregistre un moteur avec sa closure de résolution.
     *
     * @param string  $engine   Nom du moteur (ex: 'php', 'template').
     * @param \Closure $resolver Closure qui retourne une instance du moteur.
     *
     * @return void
     */
    public function register(string $engine, Closure $resolver): void
    {
        $this->resolvers[$engine] = $resolver;
    }

    /**
     * Résout et retourne une instance du moteur demandé.
     * 
     * Si le moteur a déjà été instancié, retourne l'instance existante.
     *
     * @param string $engine Nom du moteur à résoudre.
     *
     * @return mixed Instance du moteur correspondant.
     *
     * @throws \InvalidArgumentException Si le moteur n'est pas enregistré.
     */
    public function resolve(string $engine)
    {
        if (! isset($this->resolved[$engine])) {
            $resolver = $this->resolvers[$engine];

            $this->resolved[$engine] = call_user_func($resolver);
        }

        return $this->resolved[$engine];
    }

}
