<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


#[Signature('app:import-sat')]
#[Description('Import SAT competitions in database')]
class ImportSATData extends Command
{
    protected const META_URL = 'https://benchmark-database.de/getdatabase/meta';
    protected const BASE_URL = 'https://benchmark-database.de/getdatabase/base';
    protected const TRACK = 'main_2026';
    protected const EVALUATION_ID = 9;


    public function handle(): int
    {
        $metaPath = $this->downloadSqlite(self::META_URL, 'meta');
        $basePath = $this->downloadSqlite(self::BASE_URL, 'base');

        try {
            $this->registerConnection('benchmark_meta', $metaPath);
            $this->registerConnection('benchmark_base', $basePath);

            // hash => {variables, clauses}, indexé une seule fois pour un lookup O(1)
            $features = DB::connection('benchmark_base')
                ->table('features')
                ->select('hash', 'variables', 'clauses')
                ->get()
                ->keyBy('hash');

            $rows = DB::connection('benchmark_meta')
                ->table('features')
                ->join('track', 'track.hash', '=', 'features.hash')
                ->join('filename', 'filename.hash', '=', 'track.hash')
                ->where('track.value', self::TRACK)
                ->select('features.hash', 'features.family', 'filename.value as filename')
                ->get();

            $total = 0;

            $rows->chunk(500)->each(function ($chunk) use ($features, &$total) {
                $data = [];

                foreach ($chunk as $row) {
                    $feature = $features->get($row->hash);

                    if (!$feature) {
                        continue; // pas de correspondance dans base.db, on ignore
                    }

                    $data[] = [
                        // NB: ici name = filename.value. Remplacer par $row->family
                        // si c'est plutôt la famille que vous voulez comme "name".
                        'name' => preg_replace('/\.cnf\.xz$/', '', $row->filename),
                        'fullname' => $row->hash,
                        'family' => $row->family,
                        'evaluation_id' => self::EVALUATION_ID,
                        'nb_variables' => $feature->variables,
                        'nb_constraints' => $feature->clauses,
                        'useless_vars' => 0
                    ];
                }

                if (!empty($data)) {
                    // Nécessite un index UNIQUE sur (fullname, evaluation_id)
                    // pour que l'upsert fasse un update plutôt qu'un doublon.
                    DB::table('benchmarks')->upsert(
                        $data,
                        ['fullname', 'evaluation_id'],
                        ['name', 'nb_variables', 'nb_constraints']
                    );
                    $total += count($data);
                }
            });

            return $total;
        } finally {
            $this->cleanup($metaPath, $basePath);
        }
    }

    protected function downloadSqlite(string $url, string $label): string
    {
        $dir = storage_path('app/tmp');

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir . '/' . $label . '-' . uniqid() . '.sqlite';

        $response = Http::timeout(120)->get($url);
        $response->throw();

        file_put_contents($path, $response->body());

        return $path;
    }

    protected function registerConnection(string $name, string $path): void
    {
        config([
            "database.connections.{$name}" => [
                'driver' => 'sqlite',
                'database' => $path,
                'prefix' => '',
                'foreign_key_constraints' => false,
            ],
        ]);

        DB::purge($name);
    }

    protected function cleanup(string ...$paths): void
    {
        foreach ($paths as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
}
