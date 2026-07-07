<?php

namespace App\Filament\Resources\Competitions\Tables;

use App\Models\Competition;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
