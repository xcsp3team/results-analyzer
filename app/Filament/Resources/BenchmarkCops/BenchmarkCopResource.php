<?php

namespace App\Filament\Resources\BenchmarkCops;

use App\Filament\Resources\BenchmarkCops\Pages\ManageBenchmarkCops;
use App\Models\Benchmark;
use App\Models\Benchmark_cop;
use App\Models\BenchmarkCop;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class BenchmarkCopResource extends Resource
{
    protected static ?string $model = Benchmark_cop::class;
    protected static ?string $navigationLabel = "COP";
    protected static string | UnitEnum | null $navigationGroup = "Benchmarks";
    protected static ?string $label = "COP";

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
                Select::make('competition_id')
                    ->relationship('competition', 'name', modifyQueryUsing: fn ($query) => $query->where("type", "cop"))
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} {$record->track}")
                    ->required(),
                TextInput::make('best_bound')
                    ->required(),
                Toggle::make('optim')->label("Optimum ?")
                    ->required(),
                TextInput::make('nb_variables')
                    ->required()
                    ->numeric(),
                TextInput::make('nb_constraints')
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
                TextColumn::make('competition')
                    ->state(fn(Benchmark_cop $record) => $record->competition->fullname())
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('family')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('best_bound')
                    ->label("Best Bound")
                    ->searchable(),
                TextColumn::make('optim')
                    ->label("Optimum ?"),
                TextColumn::make('nb_variables')
                    ->alignEnd()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nb_constraints')->alignEnd()
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
                SelectFilter::make('competition')
                    ->relationship('competition', 'name', modifyQueryUsing: fn ($query) => $query->where("type", "cop"))
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} {$record->track}")

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
            'index' => ManageBenchmarkCops::route('/'),
        ];
    }
}
