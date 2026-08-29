<?php

namespace App\Filament\Resources\ResultCops;

use App\Filament\Resources\ResultCops\Pages\ManageResultCops;
use App\Models\Result_cop;
use App\Models\ResultCops;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class ResultCopsResource extends Resource
{
    protected static ?string $model = Result_cop::class;
    protected static ?int $navigationSort = 3;
    protected static string|UnitEnum|null $navigationGroup = "Results";
    protected static ?string $navigationLabel = "COP";


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('solver_id')
                    ->relationship('solver', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} {$record->version}")
                    ->required(),
                TextInput::make('solver_id')
                    ->required()
                    ->numeric(),
                TextInput::make('benchmark_id')
                    ->required()
                    ->numeric(),
                TextInput::make('time')
                    ->required()
                    ->numeric(),
                TextInput::make('bounds')
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
                TextColumn::make('competition')
                    ->state(fn($record) => $record->benchmark->competition->fullname())->sortable(),
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
                    ->alignEnd()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bounds')->label("Best bound")->alignEnd()
                    ->state(function ($record) {
                        $tab = json_decode(str_replace("'", '"', $record->bounds));
                        //dd($tab);
                        if ($tab == null || count($tab) == 0)
                            return null;
                        return $tab[count($tab) - 1]->bound;
                    })->numeric()
                    ->searchable(),
                ToggleColumn::make('unsupported'),
                ToggleColumn::make('bug'),
            ])->defaultPaginationPageOption(50)
            ->filters([
                SelectFilter::make('competition')
                    ->label('Evaluation')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} {$record->track}")
                    ->relationship('benchmark.competition', 'name', modifyQueryUsing: fn($query) => $query->where("type", "cop"))
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
                        DB::update("UPDATE results_cop set bug=1 where solver_id = ? and benchmark_id in (SELECT id from benchmarks_cop where competition_id=? and family=?)",
                            [$record->solver_id, $record->benchmark->competition_id, $record->benchmark->family]);
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
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
            'index' => ManageResultCops::route('/'),
        ];
    }
}
