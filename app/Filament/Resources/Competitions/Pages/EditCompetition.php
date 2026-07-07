<?php

namespace App\Filament\Resources\Competitions\Pages;

use App\Filament\Pages\MissingResults;
use App\Filament\Resources\Competitions\CompetitionResource;
use App\Models\Competition;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompetition extends EditRecord
{
    protected static string $resource = CompetitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            ...parent::getFormActions(),
            Action::make('Missing')
                ->url(fn (Competition $record) => MissingResults::getUrl(['competition' => $record->id])),
        ];
    }
}
