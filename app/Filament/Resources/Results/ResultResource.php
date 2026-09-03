<?php

namespace App\Filament\Resources\Results;

use App\Filament\Resources\Results\Pages\ManageResults;
use App\Models\Result;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class ResultResource extends Resource
{
    protected static ?string $model = Result::class;
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = "CSP";
    protected static string|UnitEnum|null $navigationGroup = "Results";

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('solver_id')
                    ->relationship('solver', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} {$record->version}")
                    ->required(),
                TextInput::make('benchmark_id')
                    ->required()
                    ->numeric(),
                TextInput::make('time')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required(),
                TextInput::make('unsupported')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('bug')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('evaluation')
                    ->state(fn($record) => $record->benchmark->evaluation->fullname())->sortable(),
                TextColumn::make('benchmark.name')
                    ->sortable()->searchable(),
                TextColumn::make('benchmark.family')
                    ->label("Family")
                    ->sortable()->searchable(),
                TextColumn::make('solver_id')
                    ->label("Solver")
                    ->state(fn($record) => $record->solver->name . " " . $record->solver->version)
                    ->sortable(),
                TextColumn::make('time')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                ToggleColumn::make('unsupported'),
                ToggleColumn::make('bug'),
            ])->defaultPaginationPageOption(50)
            ->filters([
                SelectFilter::make('evaluation')
                    ->label('Evaluation')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} {$record->track}")
                    ->relationship('benchmark.evaluation', 'name', modifyQueryUsing: fn($query) => $query->where("type", "csp"))
                    ->preload()
                    ->searchable(),
                SelectFilter::make('solver')
                    ->relationship('solver', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} {$record->version}")
                    ->preload()
                    ->searchable(),
                TernaryFilter::make('bug')->label('Bug')
                    ->trueLabel('Buggy results')
                    ->falseLabel('Non buggy results')
                    ->placeholder('All results'),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                Action::make("family")
                    ->label("Make family buggy")
                    ->action(function ($record) {
                        DB::update("UPDATE results set bug=1 where solver_id = ? and benchmark_id in (SELECT id from benchmarks where evaluation_id=? and family=?)",
                            [$record->solver_id, $record->benchmark->competition_id, $record->benchmark->family]);
                    }),
                EditAction::make(),

                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('bug')->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $record->bug = 1;
                            $record->save();
                        }
                    })
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageResults::route('/'),
        ];
    }
}
