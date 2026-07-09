<?php

namespace App\Filament\Resources\Competitions\Tables;

use App\Filament\Pages\MissingResults;
use App\Models\Competition;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompetitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')
                    ->searchable()->sortable(),
                TextColumn::make('track')
                    ->searchable()->sortable(),
                TextColumn::make('type')
                    ->searchable()->sortable(),
                TextColumn::make('defaulttime')
                    ->numeric()
                    ->sortable(),
                TextColumn::make("benchmarks_count")->label("Nb benchs")->counts("benchmarks")->numeric(),
                TextColumn::make("solvers")->state(fn(Competition $record) => count($record->solvers()))->numeric(),
                IconColumn::make('public')
                    ->boolean(),
                TextColumn::make('slug')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make("init")
                ->label("Init results")
                ->schema(function($record) {
                    $tmp = [];
                    foreach($record->solvers() as $solver) {
                        $tmp[] = Checkbox::make("s_" . $solver->id)
                                    ->label($solver->name . " " . $solver->version)
                                    ->default(true);
                    }
                    return $tmp;
                })
                ->action(function(array $data, Competition $record) {
                    $ids = array_map(
                        fn($key) => (int) substr($key, 2),
                        array_keys(array_filter($data))
                    );
                    $record->initResults($ids);
                }),
                Action::make('Missing')
                    ->url(fn (Competition $record) => MissingResults::getUrl(['competition' => $record->id])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
