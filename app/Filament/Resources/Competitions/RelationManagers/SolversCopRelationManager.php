<?php

namespace App\Filament\Resources\Competitions\RelationManagers;

use App\Models\Competition;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class SolversCopRelationManager extends RelationManager
{
    protected static string $relationship = 'solversCop';

    public static function canViewForRecord(Competition|\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type === 'cop';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make("id"),
                TextColumn::make('name')
                    ->state(fn($record) => $record->name . " " . $record->version)
                    ->searchable(),
                TextColumn::make('nb_benchmarks')->label('Nb benchmarks')
                    ->numeric()
            ])
            ->extraAttributes(['class' => 'divide-y divide-gray-200'])
            ->recordClasses(fn ($record) => 'hover:bg-gray-50 dark:hover:bg-white/5 transition-colors')
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->recordActions([
                Action::make("remove")->action(function ($record) {
                    DB::delete("DELETE FROM results_cop WHERE solver_id = ? and benchmark_id in (SELECT id from benchmarks_cop where competition_id=?)", [$record->solver_id, $record->competition_id]);
                })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
