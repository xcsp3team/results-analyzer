<?php

namespace App\Filament\Resources\Solvers\Pages;

use App\Filament\Resources\Solvers\SolverResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSolvers extends ManageRecords
{
    protected static string $resource = SolverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
