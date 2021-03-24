<?php

namespace App\Support\Parsers;

use App\Support\Contracts\Parser;

class CsvParser implements Parser
{
    protected string $filename;
    protected array $data = [];

    /**
     * Set data to be parsed.
     *
     * @param  $data
     * @return $this
     */
    public function setData($data): CsvParser
    {
        $this->data = [];
        $this->filename = $data;

        return $this;
    }

    /**
     * Retrieve a array representation of the data encapsulated within our Parser.
     *
     * @return array
     */
    public function toArray(): array
    {
        $delimiter = $this->getDelimiter();

        if (($handle = fopen($this->filename, "r")) !== false) {
            $headers = fgetcsv($handle, 20000, $delimiter);

            while (($data = fgetcsv($handle, 20000, $delimiter)) !== false) {
                // use header row as key for data row.
                for ($i = 0; $i < count($headers); $i++) {
                    if (! isset($data[$i])) {
                        $data[$i] = '';
                    }

                    if ($this->isJson($data[$i])) {
                        $data[$i] = json_decode($data[$i], true);
                    }
                }

                for ($i = count($data); $i >= count($headers); $i--) {
                    unset($data[$i]);
                }

                $this->data[] = array_combine($headers, $data);
            }

            fclose($handle);
        }

        return $this->data;
    }

    /**
     * Detect if the correct delimiter is used.
     *
     * @return bool
     */
    private function getDelimiter()
    {
        $delimiters = array( ',' => 0, ';' => 0, "\t" => 0, '|' => 0, );

        $headers = '';
        $handle = fopen($this->filename, 'r');

        if ($handle) {
            $headers = fgets($handle);
            fclose($handle);
        }

        if ($headers) {
            foreach ($delimiters as $delimiter => &$count) {
                $count = count(str_getcsv($headers, $delimiter));
            }

            return array_search(max($delimiters), $delimiters);
        }

        return key($delimiters);
    }

    public function isJson($string)
    {
        json_decode($string, true);

        return (json_last_error() == JSON_ERROR_NONE);
    }
}
