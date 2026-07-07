<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBenchmarksCopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('benchmarks_cop', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("fullname", 1000);
            $table->string("family");
            $table->bigInteger("competition_id")->unsigned();
            $table->integer("nb_variables");
            $table->integer("nb_constraints");
            $table->integer("best_bound")->nullable();
            $table->integer("optim");
            $table->string("info_domains")->nullable();
            $table->string("info_constraints")->nullable();
            $table->integer("useless_vars");
            $table->string("type");
            $table->timestamps();
            $table->foreign("competition_id")->references("id")->on("competitions")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('benchmarks_cop');
    }
}
