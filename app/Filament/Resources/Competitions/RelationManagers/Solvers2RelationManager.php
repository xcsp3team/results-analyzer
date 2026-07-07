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

class Solvers2RelationManager extends RelationManager
{
    protected static string $relationship = 'solvers2';
    protected static ?string $title = "Solvers";

    public static function canViewForRecord(Competition|\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type === 'csp';
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
                TextColumn::make('name')
                    ->state(fn($record) => $record->name . " " . $record->version)
                    ->searchable(),
                TextColumn::make('nb_benchmarks')->label('Nb benchmarks')
                    ->numeric()
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->recordActions([
                Action::make("remove")->action(function ($record) {
                  DB::delete("DELETE FROM results WHERE solver_id = ? and benchmark_id in (SELECT id from benchmarks where competition_id=?)", [$record->solver_id, $record->competition_id]);
                })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
