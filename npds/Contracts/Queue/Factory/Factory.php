<?php

namespace Npds\Contracts\Queue;

interface Factory
{
    /**
     * Resolve a queue connection instance.
     *
     * @param  string|null  $name
     * @return \Npds\Contracts\Queue\Queue
     */
    public function connection($name = null);
}
