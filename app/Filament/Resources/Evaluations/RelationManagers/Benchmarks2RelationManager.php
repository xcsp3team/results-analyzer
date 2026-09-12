<?php

namespace App\Filament\Resources\Evaluations\RelationManagers;

use App\Filament\Resources\Benchmarks\BenchmarkResource;
use App\Models\Evaluation;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class Benchmarks2RelationManager extends RelationManager
{
    protected static string $relationship = 'benchmarks';

    protected static ?string $relatedResource = BenchmarkResource::class;


    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
