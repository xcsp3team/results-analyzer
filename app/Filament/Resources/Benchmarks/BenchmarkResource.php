<?php

namespace App\Filament\Resources\Benchmarks;

use App\Filament\Pages\MissingResults;
use App\Filament\Resources\Benchmarks\Pages\ManageBenchmarks;
use App\Models\Benchmark;
use App\Models\Evaluation;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class BenchmarkResource extends Resource
{
    protected static ?string $model = Benchmark::class;
    
    protected static bool $shouldRegisterNavigation = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('fullname')
                    ->required(),
                TextInput::make('family')
                    ->required(),
                Select::make('evaluation_id')
                    ->relationship('evaluation', 'name', modifyQueryUsing: fn($query) => $query->where("type", "cop"))
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} {$record->track}")
                    ->required(),
                TextInput::make('status')
                    ->required(),
                TextInput::make('nb_variables')
                    ->required()
                    ->numeric(),
                TextInput::make('nb_clauses')
                    ->required()
                    ->numeric(),
                TextInput::make('info_domains'),
                TextInput::make('info_constraints'),
                TextInput::make('useless_vars')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('evaluation')
                    ->state(fn(Benchmark $record) => $record->evaluation->fullname())
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('family')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('nb_variables')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nb_clauses')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('info_domains')
                    ->searchable(),
                TextColumn::make('info_constraints')
                    ->searchable(),
                TextColumn::make('useless_vars')
                    ->numeric()
                    ->sortable(),
            ])->defaultPaginationPageOption(25)
            ->filters([
                SelectFilter::make('evaluation')
                    ->relationship('evaluation', 'name', modifyQueryUsing: fn($query) => $query->where("type", "csp"))
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} {$record->track}")

            ])
            ->deferFilters(false)
            ->recordActions([

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBenchmarks::route('/'),
        ];
    }
}
