<?php

namespace App\Filament\Resources\BenchmarkCops\Pages;

use App\Filament\Resources\Benchmarks\BenchmarkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBenchmarkCops extends ManageRecords
{
    protected static string $resource = BenchmarkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
