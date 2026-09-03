<?php

namespace App\Filament\Resources\Evaluations\Tables;

use App\Filament\Pages\Bugs;
use App\Filament\Pages\MissingResults;
use App\Models\Evaluation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvaluationsTable
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
                TextColumn::make("benchmarks_count")
                    ->label("Nb benchs")
                    ->state(fn($record) => $record->benchmarks()->count())
                    ->numeric()
                //TextColumn::make("solvers")->state(fn(Evaluation $record) => count($record->solvers()))->numeric(),
                //IconColumn::make('public')
                //    ->boolean(),
                //TextColumn::make('slug')
                //    ->searchable()
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make("init")
                    ->label("Init results")
                    ->schema(function ($record) {
                        $tmp = [];
                        foreach ($record->solvers as $solver) {
                            $tmp[] = Checkbox::make("s_" . $solver->id)
                                ->label($solver->name . " " . $solver->version)
                                ->default(true);
                        }
                        return $tmp;
                    })
                    ->action(function (array $data, Evaluation $record) {
                        $ids = array_map(
                            fn($key) => (int)substr($key, 2),
                            array_keys(array_filter($data))
                        );
                        $record->initResults($ids);
                    }),
                Action::make('Errors')
                    ->url(fn(Evaluation $record) => Bugs::getUrl(['evaluation' => $record->id])),
                Action::make('Missing')
                    ->url(fn(Evaluation $record) => MissingResults::getUrl(['evaluation' => $record->id])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
