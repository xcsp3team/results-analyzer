<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Competition;
use App\Models\Solver;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

function castIds($rows, array $columns = ['id'])
{
    return collect($rows)->map(function ($row) use ($columns) {
        foreach ($columns as $col) {
            if (isset($row->$col))
                $row->$col = (int) $row->$col;
        }
        return $row;
    });
}

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// --------------------------- CSP/SAT --------------------------------------
Route::get("/competitions", function () {
    return Competition::all();
});

Route::get("/competitions/{slug}", function ($slug) {
    return Competition::whereRaw("slug=?", [$slug])->firstOrFail();
});


Route::get("/solversincompetition/{id}", function ($id) {
    $c = Competition::findOrFail($id);
    return $c->displaysolvers();
})->where("id", "[0-9]+");


Route::get("/solver/{id}", function ($id) {
    return Solver::findOrFail($id);
})->where("id", "[0-9]+");


Route::get("/benchmarksincompetition/{id}", function ($id) {
    $c = Competition::findOrFail($id);
    return $c->benchmarks;
})->where("id", "[0-9]+");

Route::get("/solverresultincompetition/{idc}/{ids}", function ($idc, $ids) {
    $s = Solver::findOrFail($ids);
    return $s->results($idc);
})->where("idc", "[0-9]+")->where("ids", "[0-9]+");

Route::get("/scatter/{idc}/{ids1}/{ids2}/{selection}", function ($idc, $ids1, $ids2, $selection = "ALL") {
    $c = Competition::whereRaw("slug=?", [$idc])->firstOrFail();

    if ($selection == "ALL") {
        $tmp = "";
        $parameters = [$c->id, $ids1, $ids2];
    } else {
        $tmp = "and benchmarks.status=?";
        $parameters = [$c->id, $ids1, $ids2, $selection];
    }

    return DB::select("select r1.time as t1,r2.time as t2, benchmarks.*  ".
        "from results r1,results r2, benchmarks " .
        "WHERE r1.benchmark_id=r2.benchmark_id " .
        "and benchmarks.id=r1.benchmark_id " .
        "and benchmarks.competition_id=? " .
        "and r1.solver_id=? and r2.solver_id=? " .
        $tmp .
        " order by r1.time "
        , $parameters);
})->where("ids1", "[0-9]+")->where("ids2", "[0-9]+");


Route::post("/competitions", [\App\Http\Controllers\Admin::class, "storecompetition"]);

Route::get("/solvers", function() {return Solver::all();});
Route::post("/solvers", [\App\Http\Controllers\Admin::class, "storesolver"]);


Route::post("/competitions/solvers", [\App\Http\Controllers\Admin::class, "storesolverincompetition"]);

Route::get("/competitions/exportcop/{idc}",  [\App\Http\Controllers\Admin::class, "exportcop"]);
Route::get("/competitions/exportcsp/{idc}",  [\App\Http\Controllers\Admin::class, "exportcsp"]);

Route::get("/competitions/maxsolved/{idc}/{timelimit}", function($idc, $timelimit) {
    $c = Competition::findOrFail($idc);
    if($c->type == "cop") {
        return DB::select("SELECT results_cop.solver_id ,count(*) as nb FROM results_cop,benchmarks_cop ".
            "WHERE benchmark_id=benchmarks_cop.id and competition_id=? and time >= 0 and time < ? ".
            "GROUP BY solver_id ".
            "ORDER BY count(*) desc", [$idc, $timelimit])[0]->nb;
    } else {
        return DB::select("SELECT results.solver_id ,count(*) as nb FROM results,benchmarks ".
            "WHERE benchmark_id=benchmarks.id and competition_id=? and  time < ? ".
            "GROUP BY solver_id ".
            "ORDER BY count(*) desc", [$idc, $timelimit])[0]->nb;
    }
});


Route::get("/displaysolvers/{idc}", function($idc) {
    $c = Competition::findOrFail($idc);
    $tmp =  $c->displaysolvers();
    return castIds($tmp);
})->where("idc", "[0-9]*");


Route::get("/solver_id/{name}", function($name) {
    $s = Solver::where("name", $name)->first();
    if($s != null)
        return $s->id;
    $s = new Solver();
    $s->name = $name;
    $s->save();
    return $s->id;
});




















