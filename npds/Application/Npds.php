<?php


namespace Npds\Application;

use Two\Container\Container;
use Npds\Application\Contracts\ResponsePreparerInterface;


Class Npds extends Container implements ResponsePreparerInterface
{

    /**
     * Indique si l'application a "démarré".
     *
     * @var bool
     */
    protected $booted = false;




    public function __construct()
    {
        //
    }











    /**
     * Préparez la valeur donnée en tant qu'objet Response.
     *
     * @param  mixed  $value
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function prepareResponse($value): array
    {
        if (! $value instanceof SymfonyResponse) {
            $value = new Response($value);
        }

        return $value->prepare($this['request']);
    }

    /**
     * Déterminez si l'application est prête pour les réponses.
     *
     * @return bool
     */
    public function readyForResponses(): bool
    {
        return $this->booted;
    }





}