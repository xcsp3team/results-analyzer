<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBenchmarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('benchmarks', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("fullname", 1000);
            $table->string("family");
            $table->bigInteger("evaluation_id")->unsigned();
            $table->string("status")->nullable();
            $table->bigInteger("best_bound")->nullable();
            $table->integer("nb_variables");
            $table->integer("nb_clauses");
            $table->string("info_domains")->nullable();
            $table->string("info_constraints")->nullable();
            $table->string("type")->nullable();
            $table->integer("useless_vars");
            $table->timestamps();
            $table->foreign("evaluation_id")->references("id")->on("evaluations")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('benchmarks');
    }
}
