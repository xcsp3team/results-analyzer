<?php

namespace App\Filament\Resources\Evaluations;

use App\Filament\Resources\Evaluations\Pages\CreateEvaluation;
use App\Filament\Resources\Evaluations\Pages\EditEvaluation;
use App\Filament\Resources\Evaluations\Pages\ListEvaluation;
use App\Filament\Resources\Evaluations\Schemas\EvaluationForm;
use App\Filament\Resources\Evaluations\Tables\EvaluationsTable;
use App\Models\Evaluation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return EvaluationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvaluationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\Benchmarks2RelationManager::class,
            RelationManagers\BenchmarksCopRelationManager::class,
            //RelationManagers\Solvers2RelationManager::class,
            //RelationManagers\SolversCopRelationManager::class,
        ];

    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvaluation::route('/'),
            'create' => CreateEvaluation::route('/create'),
            'edit' => EditEvaluation::route('/{record}/edit'),
        ];
    }
}
