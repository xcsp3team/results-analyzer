<?php

namespace App\Filament\Resources\Competitions\RelationManagers;

use App\Filament\Resources\Benchmarks\BenchmarkResource;
use App\Models\Competition;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class Benchmarks2RelationManager extends RelationManager
{
    protected static string $relationship = 'benchmarks2';

    protected static ?string $relatedResource = BenchmarkResource::class;

    public static function canViewForRecord(Competition|\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type === 'csp';
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
