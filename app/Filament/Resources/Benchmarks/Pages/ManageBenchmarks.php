<?php

namespace App\Filament\Resources\Benchmarks\Pages;

use App\Filament\Resources\Benchmarks\BenchmarkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBenchmarks extends ManageRecords
{
    protected static string $resource = BenchmarkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
