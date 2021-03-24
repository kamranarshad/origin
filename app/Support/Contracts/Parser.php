<?php

namespace App\Support\Contracts;

interface Parser
{
    /**
     * Set data to be parsed.
     *
     * @param  $data
     * @return mixed
     */
    public function setData($data);

    /**
     * Retrieve a array representation of the data encapsulated within our Parser.
     *
     * @return array
     */
    public function toArray(): array;
}
