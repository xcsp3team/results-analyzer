<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("solver_id")->unsigned();
            $table->bigInteger("benchmark_id")->unsigned();
            $table->float("time");
            $table->string("status");
            $table->integer("unsupported");
            $table->integer("bug");
            $table->timestamps();
            $table->foreign("solver_id")->references("id")->on("solvers")->onDelete("cascade");
            $table->foreign("benchmark_id")->references("id")->on("benchmarks")->onDelete("cascade");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('results');
    }
}
