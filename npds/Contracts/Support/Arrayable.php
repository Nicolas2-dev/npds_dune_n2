<?php

namespace Npds\Contracts\Support;

/**
 * @template TKey of array-key
 * @template TValue
 */
interface Arrayable
{
    /**
     * Récupérer l’instance sous forme de tableau.
     *
     * @return array<TKey, TValue>
     */
    public function toArray();
}
