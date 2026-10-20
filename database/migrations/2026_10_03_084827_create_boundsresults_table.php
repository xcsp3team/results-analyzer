<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('result_bounds', function (Blueprint $table) {
            $table->unsignedBigInteger('result_id');
            $table->integer('time');
            $table->bigInteger('bound');
            $table->primary(['result_id', 'time']);
            $table->foreign('result_id')->references('id')->on('results')->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_bounds');
    }
};
