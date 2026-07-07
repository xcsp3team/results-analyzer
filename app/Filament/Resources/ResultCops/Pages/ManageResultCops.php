<?php

namespace App\Filament\Resources\ResultCops\Pages;

use App\Filament\Resources\ResultCops\ResultCopsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageResultCops extends ManageRecords
{
    protected static string $resource = ResultCopsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
