<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Pages\MissingResults;
use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Models\Evaluation;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEvaluation extends EditRecord
{
    protected static string $resource = EvaluationResource::class;

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
                ->url(fn(Evaluation $record) => MissingResults::getUrl(['evaluation' => $record->id])),
        ];
    }
}
