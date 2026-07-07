<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsCopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results_cop', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("solver_id")->unsigned();
            $table->bigInteger("benchmark_id")->unsigned();
            $table->float("time");
            $table->longText("bounds");
            $table->boolean("unsupported");
            $table->boolean("bug");
            $table->foreign("solver_id")->references("id")->on("solvers")->onDelete("cascade");
            $table->foreign("benchmark_id")->references("id")->on("benchmarks_cop")->onDelete("cascade");
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('results_cop');
    }
}
