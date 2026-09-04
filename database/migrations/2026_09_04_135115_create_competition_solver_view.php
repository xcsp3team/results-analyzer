<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE VIEW `evaluation_solver` AS
            SELECT
                `solvers`.`id` AS `solver_id`,
                `benchmarks`.`evaluation_id` AS `evaluation_id`,
                COUNT(0) AS `nb_benchmarks`
            FROM (
                (`solvers`
                    JOIN `results` ON (`results`.`solver_id` = `solvers`.`id`))
                    JOIN `benchmarks` ON (`results`.`benchmark_id` = `benchmarks`.`id`)
            )
            GROUP BY `solvers`.`id`, `benchmarks`.`evaluation_id`
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS `evaluation_solver`');
    }
};
