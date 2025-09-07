<?php

namespace Npds\Support\Facades;

use Npds\Http\Response as HttpResponse;

class Response
{

    /**
     * Crée une réponse HTTP standard.
     *
     * @param string $content Le contenu de la réponse.
     * @param int    $status  Le code HTTP de la réponse (par défaut 200).
     * @param array  $headers Les en-têtes HTTP supplémentaires.
     *
     * @return HttpResponse Une instance de HttpResponse avec le contenu, le statut et les en-têtes.
     */
    public static function make(string $content, int $status = 200, array $headers = []): HttpResponse
    {
        return new HttpResponse($content, $status, $headers);
    }

    /**
     * Crée une réponse HTTP au format JSON.
     *
     * @param mixed $data        Les données à encoder en JSON.
     * @param int   $status      Le code HTTP de la réponse (par défaut 200).
     * @param array $headers     Les en-têtes HTTP supplémentaires.
     * @param int   $jsonOptions Options supplémentaires pour json_encode().
     *
     * @return HttpResponse Une instance de HttpResponse contenant le JSON.
     */
    public static function json(mixed $data, int $status = 200, array $headers = [], int $jsonOptions = 0): HttpResponse
    {
        $headers['Content-Type'] = 'application/json; charset=utf-8';

        return new HttpResponse(json_encode($data, $jsonOptions), $status, $headers);
    }

    /**
     * Crée une réponse HTTP au format JSONP.
     *
     * @param string $callback Nom de la fonction de rappel pour JSONP.
     * @param mixed  $data     Les données à encoder en JSON.
     * @param int    $status   Le code HTTP de la réponse (par défaut 200).
     * @param array  $headers  Les en-têtes HTTP supplémentaires.
     *
     * @return HttpResponse Une instance de HttpResponse contenant le JSONP.
     */
    public static function jsonp(string $callback, mixed $data, int $status = 200, array $headers = []): HttpResponse
    {
        $headers['Content-Type'] = 'application/javascript; charset=utf-8';

        return new HttpResponse($callback .'(' .json_encode($data) .');', $status, $headers);
    }

}
