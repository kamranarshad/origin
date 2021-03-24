<?php

namespace App\Console\Commands;

use App\Support\Exporters\JsonExporter;
use App\Support\Parsers\CsvParser;
use Illuminate\Console\Command;

class ParserCredit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parser:credit {--credit-filename=} {--movies-filename=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parser Credit';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CsvParser $parser, JsonExporter $exporter)
    {
        $movies = [];
        $genres = [];
        $keywords = [];
        $actors = [];

        $moviesData = $parser->setData($this->option('movies-filename'))->toArray();
        $creditsData = $parser->setData($this->option('credit-filename'))->toArray();

        foreach ($moviesData as $key => $value) {
            if (isset($value['genres'])) {
                foreach ($value['genres'] as $genre) {
                    $genres[] = $genre;
                }
            }

            $keywords[] = collect($value['keywords'])->flatMap(function ($item) {
                if (! empty($item['id']) && ! empty($item['name'])) {
                    return [
                        'id'   => $item['id'] ?? null,
                        'name' => $item['name'] ?? null,
                    ];
                }

                return null;
            })->filter()->toArray();

            $movie = collect($creditsData)->whereIn('title', [$value['title'], $value['original_title']])->first();
            $cast = collect($movie)->get('cast');

            $characters = collect($cast)->map(function ($item) {
                if (! empty($item['id']) && ! empty($item['character'])) {
                    return [
                        'id'   => $item['id'] ?? '',
                        'name' => $item['character'] ?? '',
                    ];
                }

                return null;
            })->filter()->toArray();

            $movies[] = [
                'title'      => $value['title'],
                'budget'     => $value['budget'],
                'keywords'   => collect($value['keywords'])->flatMap(function ($item) {
                    return [$item['id'] ?? null];
                })->filter()->toArray(),
                'genres'     => collect($value['genres'])->flatMap(function ($item) {
                    return [$item['id'] ?? null];
                })->filter()->toArray(),
                'character' => $characters,
            ];
        }

        foreach ($creditsData as $key => $value) {
            if (isset($value['cast']) && is_array($value['cast'])) {
                foreach ($value['cast'] as $cast) {
                    $actors[] = [
                        'id'   => $cast['id'] ?? '',
                        'name' => $cast['character'] ?? '',
                    ];
                }
            }
        }

        $exporter->setFilename('genres')
            ->setData(collect($genres)->unique('id')->values()->toArray())
            ->toFile();

        $exporter->setFilename('keywords')
            ->setData(collect($keywords)->unique('id')->values()->toArray())
            ->toFile();

        $exporter->setFilename('actors')
            ->setData(collect($actors)->unique('id')->values()->toArray())
            ->toFile();

        $exporter->setFilename('movies')
            ->setData(collect($movies)->values()->toArray())
            ->toFile();

        return 0;
    }
}
