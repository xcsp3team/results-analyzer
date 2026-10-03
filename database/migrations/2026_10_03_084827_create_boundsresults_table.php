<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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

        DB::table('results')
            ->whereNotNull('bounds')
            ->orderBy('id')
            ->chunkById(500, function ($rows) {
                $insert = [];
                foreach ($rows as $r) {
                    $bounds = json_decode(str_replace("'", '"', $r->bounds));
                    if (!is_array($bounds))
                        continue;
                    foreach ($bounds as $b) {
                        if (!isset($b->time, $b->bound))
                            continue;
                        $insert[] = [
                            'result_id' => $r->id,
                            'time'      => $b->time,
                            'bound'     => $b->bound,
                        ];
                    }
                }
                foreach (array_chunk($insert, 1000) as $part)
                    DB::table('result_bounds')->upsert($part, ['result_id', 'time'], ['bound']);
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
