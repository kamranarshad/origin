<?php

namespace App\Support\Exporters;

use App\Support\Contracts\Exporter;

class JsonExporter implements Exporter
{
    protected string $filename;

    protected array $data = [];

    /**
     * Set filename for the exported file.
     *
     * @param  $filename
     * @return mixed
     */
    public function setFilename($filename): JsonExporter
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Set data to be export.
     *
     * @param  array $data
     * @return mixed
     */
    public function setData(array $data): JsonExporter
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Retrieve a array representation of the data encapsulated within our Parser.
     */
    public function toFile()
    {
        $fp = fopen("{$this->filename}.json", 'w');

        fwrite($fp, json_encode($this->data));

        fclose($fp);
    }
}
