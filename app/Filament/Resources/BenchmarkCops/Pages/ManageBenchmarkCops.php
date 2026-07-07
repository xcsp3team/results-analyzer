<?php

namespace App\Filament\Resources\BenchmarkCops\Pages;

use App\Filament\Resources\BenchmarkCops\BenchmarkCopResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBenchmarkCops extends ManageRecords
{
    protected static string $resource = BenchmarkCopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
