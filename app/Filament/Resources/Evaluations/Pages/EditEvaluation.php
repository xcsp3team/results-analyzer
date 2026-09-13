<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Pages\MissingResults;
use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Jobs\ProcessBenchmarksImport;
use App\Jobs\ProcessResultsImport;
use App\Models\Evaluation;
use App\Models\Solver;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Query\Builder;

class EditEvaluation extends EditRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        $solvers = [];
        foreach (Solver::all() as $solver) {
            $solvers[$solver->id] = $solver->name . " " . $solver->version;
        }
        return [
            DeleteAction::make(),
            Action::make('benchmark')->label('Import Benchmarks')
                ->schema([
                    FileUpload::make('json_file')
                        ->label('JSON File')
                        ->disk('local')
                        ->directory('imports/benchmarks')
                        ->acceptedFileTypes(['application/json', 'text/plain'])
                        ->required(),
                ])->action(function ($record, $data) {
                    ProcessBenchmarksImport::dispatch($data['json_file'], $record, auth()->id());

                    Notification::make()
                        ->title('Import in progress')
                        ->success()
                        ->send();
                }),
            Action::make('results')->label('Import Results')->color("success")
                ->schema([
                    Select::make('solver_id')
                        ->options($solvers)
                        ->required()
                        ->searchable(['name']),
                    FileUpload::make('json_file')
                        ->label('JSON File')
                        ->disk('local')
                        ->directory('imports/results')
                        ->acceptedFileTypes(['application/json', 'text/plain'])
                        ->required(),
                ])->action(function ($record, $data) {
                    ProcessResultsImport::dispatch($data['json_file'], $data['solver_id'], $record, auth()->id());

                    Notification::make()
                        ->title('Import in progress')
                        ->success()
                        ->send();
                })
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
