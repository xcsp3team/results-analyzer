<?php

namespace App\Filament\Imports;

use App\Models\Benchmark;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class BenchmarkImporter extends Importer
{
    protected static ?string $model = Benchmark::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(['required', 'max:255']),
            ImportColumn::make('fullname')
                ->rules(['required', 'max:1000']),
            ImportColumn::make('family')
                ->rules(['required', 'max:255']),
            ImportColumn::make('nb_variables')
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('nb_clauses')
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('info_domains')
                ->rules(['max:255']),
            ImportColumn::make('info_constraints')
                ->rules(['max:255']),
            ImportColumn::make('type')
                ->rules(['max:255']),
            ImportColumn::make('useless_vars')
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    public function resolveRecord(): Benchmark
    {
        $benchmark = new Benchmark();
        $benchmark->evaluation_id = $this->options['evaluation_id'];
        return $benchmark;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your benchmark import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
