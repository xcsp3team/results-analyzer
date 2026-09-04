<?php

namespace App\Console\Commands;

use App\Livewire\Evaluation;
use App\Models\Solver;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
        $db = "old_xcsp26"; //$this->ask('Name of the old database?');

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $passwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            exit(1);
        }

        // Export all solvers

        $this->info("Delete data");
        DB::delete("delete from solvers");
        DB::delete("delete from evaluations");
        DB::delete("delete from benchmarks");
        DB::delete("delete from results");


        $query = $pdo->query("SELECT * FROM solvers");
        $query->setFetchMode(\PDO::FETCH_ASSOC);
        $solvers = $query->fetchAll();
        foreach ($solvers as $solver)
            DB::insert("INSERT INTO solvers values(:id,:name,:version,:params,:authors,:created_at,:updated_at)",$solver);


        $this->info("Export " . count($solvers) . " solvers\n");

        $query = $pdo->query("SELECT * FROM competitions");
        $query->setFetchMode(\PDO::FETCH_ASSOC);
        $competitions = $query->fetchAll();
        foreach ($competitions as $competition) {
            $this->info("Export competition  " . $competition['name'] . " " . $competition["track"]);

            $competition_id = $competition['id'];
            DB::insert("insert into evaluations values(:id,:name,:track,:type,:defaulttime,:public,:rank,:slug, :created_at,:updated_at)", $competition);


            if ($competition['type'] == "cop") {
            } else {
                $query = $pdo->query("SELECT * FROM benchmarks WHERE competition_id=$competition_id");
                $query->setFetchMode(\PDO::FETCH_ASSOC);
                $benchmarks = $query->fetchAll();
                foreach ($benchmarks as $benchmark) {
                    $benchmark["evaluation_id"] = $competition_id;
                    unset($benchmark["competition_id"]);
                    unset($benchmark["created_at"]);
                    unset($benchmark["updated_at"]);
                    unset($benchmark["status"]);
                    DB::insert("INSERT INTO benchmarks(id,name,fullname,family, nb_variables,nb_clauses,info_domains,info_constraints,useless_vars,evaluation_id) " .
                                      "values(:id,:name,:fullname,:family,:evaluation_id,:nb_variables,:nb_clauses,:info_domains,:info_constraints,:useless_vars, :evaluation_id)", $benchmark);
                }
            }


            if ($competition['type'] == "cop") {

            } else {

            }

        }
    }
}
