<?php

namespace App\Console\Commands;


use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

#[Signature('app:import-xcsp26')]
#[Description('Import XCSP26 competition in database')]
class ImportXCSP26Data extends Command
{

    //Asset url
    protected string $assetUrl = 'https://github.com/xcsp3team/results-analyzer/releases/download/seed-data-xcsp26/xcsp26.sql.gz';

    public function handle()
    {
        $gzPath = storage_path('app/xcsp26.sql.gz');
        $sqlPath = storage_path('app/xcsp26.sql');

        $this->info('Loading data...');

        $response = Http::get($this->assetUrl);

        if ($response->failed()) {
            $this->error('Loading failed.');
            return 1;
        }

        file_put_contents($gzPath, $response->body());

        $this->info('Unzip...');
        exec("gunzip -f {$gzPath}");

        $this->info('Import in progress');

        $connection = config('database.default');
        $config = config("database.connections.$connection");

        $command = sprintf(
            'mysql -u%s -p%s -h%s %s < %s',
            $config['username'],
            $config['password'],
            $config['host'],
            $config['database'],
            $sqlPath
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            $this->error('Failed to import data.');
            return 1;
        }

        unlink($sqlPath);
        $this->info('Successfully imported data.');
        return 0;
    }
}
