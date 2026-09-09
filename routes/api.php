<?php

use App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Evaluation;
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
                $row->$col = (int)$row->$col;
        }
        return $row;
    });
}


// --------------------------- CSP/SAT --------------------------------------


Route::post("/competitions", [Admin::class, "storecompetition"]);

Route::get("/solvers", function () {
    return Solver::all();
});
Route::post("/solvers", [Admin::class, "storesolver"]);


Route::post("/competitions/solvers", [Admin::class, "storesolverincompetition"]);

Route::get("/competitions/exportcop/{idc}", [Admin::class, "exportcop"]);
Route::get("/competitions/exportcsp/{idc}", [Admin::class, "exportcsp"]);

Route::get("/competitions/maxsolved/{idc}/{timelimit}", function ($idc, $timelimit) {
    $c = Evaluation::findOrFail($idc);
    if ($c->type == "cop") {
        return DB::select("SELECT results_cop.solver_id ,count(*) as nb FROM results_cop,benchmarks_cop " .
            "WHERE benchmark_id=benchmarks_cop.id and competition_id=? and time >= 0 and time < ? " .
            "GROUP BY solver_id " .
            "ORDER BY count(*) desc", [$idc, $timelimit])[0]->nb;
    } else {
        return DB::select("SELECT results.solver_id ,count(*) as nb FROM results,benchmarks " .
            "WHERE benchmark_id=benchmarks.id and competition_id=? and  time < ? " .
            "GROUP BY solver_id " .
            "ORDER BY count(*) desc", [$idc, $timelimit])[0]->nb;
    }
});






















