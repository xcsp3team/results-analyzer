<?php

namespace App\Console\Commands;

use App\Livewire\Evaluation;
use App\Models\Benchmark;
use App\Models\Result;
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
        $db = $this->ask('Name of the old database?');

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
            DB::insert("INSERT INTO solvers values(:id,:name,:version,:params,:authors,:created_at,:updated_at)", $solver);


        $this->info("Export " . count($solvers) . " solvers\n");

        $query = $pdo->query("SELECT * FROM competitions");
        $query->setFetchMode(\PDO::FETCH_ASSOC);
        $competitions = $query->fetchAll();
        foreach ($competitions as $competition) {
            $this->info("Export competition  " . $competition['name'] . " " . $competition["track"]);

            $competition_id = $competition['id'];
            DB::insert("insert into evaluations values(:id,:name,:track,:type,:defaulttime,:public,:rank,:slug, :created_at,:updated_at)", $competition);


            if ($competition['type'] == "cop") {
                $query = $pdo->query("SELECT * FROM benchmarks_cop WHERE competition_id=$competition_id");
                $query->setFetchMode(\PDO::FETCH_ASSOC);
                $benchmarks = $query->fetchAll();
                foreach ($benchmarks as $benchmark) {
                    $b = new Benchmark();
                    $b->name = $benchmark["name"];
                    $b->fullname = $benchmark["fullname"];
                    $b->family = $benchmark["family"];
                    $b->nb_variables = $benchmark["nb_variables"];
                    $b->nb_constraints = $benchmark["nb_constraints"];
                    $b->info_domains = $benchmark["info_domains"];
                    $b->info_constraints = $benchmark["info_constraints"];
                    $b->useless_vars = $benchmark["useless_vars"];
                    $b->type = $benchmark["type"];
                    $b->evaluation_id = $benchmark["competition_id"];
                    $b->save();


                    $benchmark_id = $benchmark["id"];
                    $query = $pdo->query("SELECT * FROM results_cop WHERE benchmark_id=$benchmark_id");
                    $results = $query->fetchAll();
                    foreach ($results as $result) {
                        $r = new Result();
                        $r->benchmark_id = $b->id;
                        $r->solver_id = $result["solver_id"];
                        $r->bounds = $result["bounds"];
                        $r->status = "SAT";
                        if ($result['time'] != -1) {
                            if ($result['bounds'] == '[]')
                                $r->status = "UNSAT";
                            else
                                $r->status = "OPTIMUM";
                        } else {
                            if ($result['bounds'] == '[]')
                                $r->status = "UNKNOWN";
                        }
                        $r->bug = $result["bug"];
                        $r->unsupported = $result["unsupported"];
                        $r->time = $result["time"];
                        $r->save();
                    }
                }
            } else {
                $query = $pdo->query("SELECT * FROM benchmarks WHERE competition_id=$competition_id");
                $query->setFetchMode(\PDO::FETCH_ASSOC);
                $benchmarks = $query->fetchAll();
                foreach ($benchmarks as $benchmark) {
                    $b = new Benchmark();
                    $b->name = $benchmark["name"];
                    $b->fullname = $benchmark["fullname"];
                    $b->family = $benchmark["family"];
                    $b->nb_variables = $benchmark["nb_variables"];
                    $b->nb_constraints = $benchmark["nb_constraints"];
                    $b->info_domains = $benchmark["info_domains"];
                    $b->info_constraints = $benchmark["info_constraints"];
                    $b->useless_vars = $benchmark["useless_vars"];
                    $b->evaluation_id = $benchmark["competition_id"];
                    $b->save();


                    // Register all results
                    $benchmark_id = $benchmark["id"];
                    $query = $pdo->query("SELECT * FROM results WHERE benchmark_id=$benchmark_id");
                    $results = $query->fetchAll();
                    foreach ($results as $result) {
                        $r = new Result();
                        $r->benchmark_id = $b->id;
                        $r->solver_id = $result["solver_id"];
                        $r->status = $result["status"];
                        $r->bug = $result["bug"];
                        $r->unsupported = $result["unsupported"];
                        $r->time = $result["time"];
                        $r->save();
                    }
                }
            }


            if ($competition['type'] == "cop") {

            } else {

            }

        }
    }
}
