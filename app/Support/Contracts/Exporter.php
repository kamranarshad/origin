<?php

namespace App\Support\Contracts;

interface Exporter
{
    /**
     * Set filename for the exported file.
     *
     * @param  $filename
     * @return mixed
     */
    public function setFilename($filename);

    /**
     * Set data to be export.
     *
     * @param  array $data
     * @return mixed
     */
    public function setData(array $data);

    /**
     * Retrieve a array representation of the data encapsulated within our Parser.
     */
    public function toFile();
}
