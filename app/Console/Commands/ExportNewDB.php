<?php

namespace App\Console\Commands;

use App\Livewire\Evaluation;
use App\Models\Solver;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use PDO;

#[Signature('app:export-new-db')]
#[Description('Command description')]
class ExportNewDB extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $host = env('DB_HOST');
        $user = env('DB_USERNAME');
        $passwd = env('DB_PASSWORD');
        $db = $this->ask('Name of the old database?');

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            exit(1);
        }

        // Export all solvers
        $query = $pdo->query("SELECT * FROM competitions");
        $query->setFetchMode(\PDO::FETCH_ASSOC);
        $solvers = $query->fetchAll();
        foreach ($solvers as $solver) {
            Solver::create($solver);
        }


        $this->info("Export " . count($solvers) . " solvers\n");

        $query = $pdo->query("SELECT * FROM competitions");
        $query->setFetchMode(\PDO::FETCH_ASSOC);
        $competitions = $query->fetchAll();
        foreach ($competitions as $competition) {
            $this->info("Export competition  " . $competition['name'] . " " . $competition["track"]);
            Evaluation::create($competition);
            $competition_id = $competition['id'];
            if ($competition['type'] == "cop") {
                $query = $pdo->query("SELECT * FROM benchmark WHERE competition_id=$competition_id");
                $query->setFetchMode(\PDO::FETCH_ASSOC);
                $benchmarks = $query->fetchAll();
                foreach ($benchmarks as $benchmark) {
                }
            } else {

            }


            if ($competition['type'] == "cop") {

            } else {

            }

        }
    }
}
