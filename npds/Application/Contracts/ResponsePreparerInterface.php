<?php

namespace Npds\Application\Contracts;


interface ResponsePreparerInterface
{
    
    /**
     * Prépare une réponse normalisée à partir d'une valeur fournie.
     *
     * Transforme la valeur d'entrée en une structure prête à être retournée au client
     * (par exemple un tableau prêt à être sérialisé en JSON). Le comportement peut
     * varier selon le type de $value (scalaire, tableau, objet JsonSerializable, etc.).
     *
     * @param  mixed  $value  Valeur brute à préparer pour la réponse.
     * @return array          Tableau structuré représentant la réponse prête.
     */
    public function prepareResponse(mixed $value): array;

    /**
     * Indique si l'instance est prête à générer ou renvoyer des réponses.
     *
     * Vérifie l'état interne (initialisation, ressources disponibles, authentification,
     * etc.) et renvoie true si tout est prêt pour produire des réponses.
     *
     * @return bool  true si prêt pour produire des réponses, false sinon.
     */
    public function readyForResponses(): bool;

}