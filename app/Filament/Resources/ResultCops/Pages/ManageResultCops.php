<?php

namespace App\Filament\Resources\ResultCops\Pages;

use App\Filament\Resources\ResultCops\ResultCopsResource;
use App\Filament\Resources\Results\ResultResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageResultCops extends ManageRecords
{
    protected static string $resource = ResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
