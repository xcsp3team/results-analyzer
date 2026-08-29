<?php

namespace App\Http\Controllers;

use App\Models\Benchmark;
use App\Models\Benchmark_cop;
use App\Models\Evaluation;
use App\Models\Solver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PDO;

class Admin extends Controller
{


    public function storecompetition(Request $request)
    {
        $api = $request->is('api/*');
        if ($api)
            $data = $request->json()->all();
        else $data = $request->all();

        $validator = Validator::make($data, [
            'type' => ['required', Rule::in(['csp', 'cop', 'sat'])],
            'name' => "required",
            'track' => 'required',
            'slug' => 'required|unique:competitions',
            'timelimit' => 'required|integer',
            'benchmarks' => 'required'
        ]);


        if ($validator->fails()) {
            if ($api)
                return \response()->json($validator->errors(), 400);
            else return redirect("/competitions")->withErrors($validator)->withInput();
        }
        $benchmarks = $api ? $data["benchmarks"] : json_decode($data["benchmarks"], true);
        $keys = ["name", "family", "nb_variables", "nb_constraints", "info_domains", "info_constraints"];
        $errors = false;
        foreach ($benchmarks as $b) {
            foreach ($keys as $k)
                if (!array_key_exists($k, $b))
                    $errors = "$k :" . json_encode($b);
            if ($data["type"] == "cop" && array_key_exists("type", $b) == false)
                $errors = $k;
        }

        if ($errors) {
            if ($api)
                return \response()->json("{'benchmarks': ['The json object is malformed $errors']}", 400);
            else return redirect("/competitions")->withInput();
        }
        $c = new Evaluation();
        $c->name = $data["name"];
        $c->track = $data["track"];
        $c->type = $data["type"];
        $c->slug = $data["slug"];
        $c->defaulttime = $data["timelimit"];
        $c->public = 1;
        $c->save();


        foreach ($benchmarks as $b) {
            $filename = $b['name'];
            while (pathinfo($filename, PATHINFO_FILENAME) != $filename)
                $filename = pathinfo($filename, PATHINFO_FILENAME);

            if ($c->type == "cop") {
                $bench = new Benchmark_cop();
                $bench->fullname = $b["name"];
                $bench->name = $filename;
                $bench->family = $b['family'];
                $bench->competition_id = $c->id;
                $bench->nb_variables = $b['nb_variables'];
                $bench->nb_constraints = $b['nb_constraints'];
                $bench->best_bound = NULL;
                $bench->optim = 0;
                $bench->type = $b['type'];
                $bench->info_domains = $b['info_domains'];
                $bench->info_constraints = $b['info_constraints'];
                $bench->useless_vars = $b['useless_vars'] ?? 0;
                $bench->save();
            } else {
                $bench = new Benchmark();
                $bench->fullname = $b['name'];
                $bench->name = $filename;
                $bench->family = $b['family'];
                $bench->competition_id = $c->id;
                $bench->status = "UNKNOWN";
                $bench->nb_variables = $b['nb_variables'];
                $bench->nb_clauses = $b['nb_constraints'];
                $bench->info_domains = $b['info_domains'];
                $bench->info_constraints = $b['info_constraints'];
                $bench->useless_vars = $b['useless_vars'] ?? 0;
                $bench->save();
            }
        }
        if ($api)
            return response()->json($c, 201);

        return back();
    }


    public function storesolver(Request $request)
    {
        $api = $request->is('api/*');
        if ($api)
            $data = $request->json()->all();
        else $data = $request->all();
        $s = new Solver();
        $s->name = $data['name'];
        $s->version = $data['version'];
        $s->params = $data['params'];
        $s->authors = $data['authors'];
        $s->save();

        if ($api)
            return response()->json($s, 201);
        return back();
    }

    public function storesolverincompetition(Request $request)
    {
        $api = $request->is('api/*');
        $nb = 0;
        if ($api)
            $data = $request->json()->all();
        else {
            $data["solver"] = $request->input('solver');
            $data["competition"] = $request->input("competition");
            $data["trust"] = $request->input("trust") ?? 0;
            $data["results"] = json_decode(str_replace("'", '"', $request->input("results")), true)["results"];
        }
        $c = Evaluation::find($data['competition']);
        $solver = Solver::find($data['solver']);
        if ($c == false or $solver == false) {
            if ($api)
                return \response()->json("solver or competition undefined", 400);
            else abort(404);
        }

        $trust = $data['trust'] ?? 0;
        $results = $api ? $data['results'] : $data["results"];
        $error = false;
        foreach ($results as $r) {
            if ($c->type == "cop") {
                $benchmark = Benchmark_cop::whereRaw("fullname = ? and competition_id=?", [$r['name'], $c->id])->get();
                if (count($benchmark) == 0)
                    continue;

                $newbounds = [];
                foreach ($r['bounds'] as $b) {
                    $t = floor($b['time']);
                    $newbounds[$t] = $b['bound'];
                }
                $s = "[";
                $f = true;
                $last = false;
                foreach ($newbounds as $t => $b) {
                    if ($f == false)
                        $s = $s . ",";
                    $s = $s . "{'bound': $b, 'time':$t}";
                    $f = false;
                    $last = $b;
                }
                $s = $s . "]";
                $r['unsupported'] = $r['unsupported'] ?? 0;
                $r['bug'] = $r['bug'] ?? 0;
                $r['time'] = $r['time'] ?? -1;

                DB::insert("INSERT INTO results_cop values(NULL,?,?,?,?,?,?,NOW(),NOW());",
                    [$solver->id, $benchmark[0]->id, $r['time'], $s, $r['unsupported'], $r['bug']]);
                $nb++;
                if ($request->has('trust') && $last !== false && $r['unsupported'] == 0) {
                    $bench = $benchmark[0];
                    if (($bench->optim == 0 && $r['time'] != -1)
                        || $bench->best_bound == null || (str_starts_with(strtoupper($bench->type), "MIN") && $last < $bench->best_bound)
                        || (str_starts_with(strtoupper($bench->type), "MAX") && $last > $bench->best_bound)) {
                        $bench->best_bound = $last;
                        $bench->optim = $r['time'] == "-1" ? 0 : 1;
                        $bench->save();
                    }
                }
            } else {
                $b = Benchmark::whereRaw("fullname = ? and competition_id=?", [$r['name'], $c->id])->get();
                if (strtolower($r['status']) == "satisfiable") $r['status'] = "sat";
                if (strtolower($r['status']) == "unsatisfiable") $r['status'] = "unsat";
                $r['status'] = strtoupper($r['status']);
                $r['time'] = $r['time'] ?? 100000;
                if (count($b) == 0)
                    continue;
                $nb++;
                DB::insert("INSERT INTO results(solver_id,benchmark_id,time,status) values(?,?,?,?)", [$solver->id, $b[0]->id, $r['time'], $r['status']]);
                if ($trust && strtoupper($b[0]->status) == "UNKNOWN" && $r['status'] != "UNKNOWN" && $r['status'] != "UNSUPPORTED") {
                    $b[0]->status = $r['status'];
                    $b[0]->save();
                }
            }
        }
        if ($api)
            return response()->json($nb, 201);
        return back();
    }


    public function bestBounds($idc)
    {
        $c = Evaluation::findOrFail($idc);
        $pdo = DB::getPdo();
        $results = [];
        foreach ($c->benchmarks as $b) {
            $current = ["benchmark" => $b->name, "type" => $b->type];
            $query = $pdo->prepare("SELECT results_cop.*,solvers.name,solvers.version FROM results_cop,solvers WHERE benchmark_id=? and solvers.id=solver_id"); // Easiest way...
            $query->execute([$b->id]);
            $solvers = [];

            while ($tmp = $query->fetch(PDO::FETCH_OBJ)) {
                $bounds = json_decode(str_replace("'", '"', $tmp->bounds));
                $bb = count($bounds) == 0 ? null : $bounds[count($bounds) - 1]->bound;
                $tbb = count($bounds) == 0 ? null : $bounds[count($bounds) - 1]->time;
                $s = ["solver" => $tmp->name . " " . $tmp->version, "optim" => $tmp->time, "best_bound" => $bb, "time_best_bound" => $tbb];
                $solvers[] = $s;
            }
            $current["solvers"] = $solvers;
            $results[] = $current;
        }
        return $results;
    }

    public function exportcop($idc)
    {
        $c = Evaluation::findOrFail($idc);
        $pdo = DB::getPdo();
        $results = [];
        foreach ($c->benchmarks as $b) {
            $current = ["benchmark" => $b->name, "type" => $b->type];
            $query = $pdo->prepare("SELECT results_cop.*,solvers.name,solvers.version FROM results_cop,solvers WHERE benchmark_id=? and solvers.id=solver_id"); // Easiest way...
            $query->execute([$b->id]);
            $solvers = [];

            while ($tmp = $query->fetch(PDO::FETCH_OBJ)) {
                $bounds = json_decode(str_replace("'", '"', $tmp->bounds));
                $bb = count($bounds) == 0 ? null : $bounds[count($bounds) - 1]->bound;
                $tbb = count($bounds) == 0 ? null : $bounds[count($bounds) - 1]->time;
                $s = ["solver" => $tmp->name . " " . $tmp->version, "optim" => $tmp->time, "best_bound" => $bb, "time_best_bound" => $tbb];
                $solvers[] = $s;
            }
            $current["solvers"] = $solvers;
            $results[] = $current;
        }
        return $results;
    }

    public function exportcsp($idc)
    {
        $c = Evaluation::findOrFail($idc);
        $pdo = DB::getPdo();
        $results = [];
        foreach ($c->benchmarks as $b) {
            $current = ["benchmark" => $b->name, "status" => $b->status];
            $query = $pdo->prepare("SELECT results.*,solvers.name,solvers.version FROM results,solvers WHERE benchmark_id=? and solvers.id=solver_id"); // Easiest way...
            $query->execute([$b->id]);
            $solvers = [];

            while ($tmp = $query->fetch(PDO::FETCH_OBJ)) {
                $s = ["solver" => $tmp->name . " " . $tmp->version, "status" => $tmp->status, "time" => $tmp->time];
                $solvers[] = $s;
            }
            $current["solvers"] = $solvers;
            $results[] = $current;
        }
        return $results;
    }


}





































