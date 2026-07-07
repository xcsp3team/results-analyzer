<?php

namespace App\Filament\Resources\Competitions\RelationManagers;

use App\Filament\Resources\BenchmarkCops\BenchmarkCopResource;
use App\Models\Competition;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class BenchmarksCopRelationManager extends RelationManager
{
    protected static string $relationship = 'benchmarksCop';

    protected static ?string $relatedResource = BenchmarkCopResource::class;

    public static function canViewForRecord(Competition|\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type === 'cop';
    }
    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
