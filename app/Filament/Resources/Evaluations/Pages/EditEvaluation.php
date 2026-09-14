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
use Illuminate\Support\HtmlString;

class EditEvaluation extends EditRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        $solvers = [];
        foreach (Solver::all() as $solver) {
            $solvers[$solver->id] = $solver->name . " " . $solver->version;
        }
        $json_benchmark = '{
 "benchmarks" : [
	{
		"name": "test1",
		"fullname": "/data/test1.xml",
		"family": "test",
		"nb_variables": 10,
		"nb_constraints": 20,
		"info_domains": "#types:2 #values:67510 (#4:7310 #5:7654)",
		"info_constraints": "#lex:1 #element:14620 ",
		"useless_vars": 0
        ' . ($this->record->type == "cop" ? '"type": "max Var"' : '') . '
	},
	...
 ]
}';
        if ($this->record->type == "cop") {
            $json_results = '{
 "results" : [
	{
		"fullname": "/data/test1.xml",
		"status": "OPTIUMUM",
		"time": 10,
		"bounds": [{\'bound\': 199, \'time\': 12}, {\'bound\': 190, \'time\': 15}, {\'bound\': 100, \'time\': 42}]
		"unsupported": 0,
		"bug": 0
	},
	{
		"fullname": "/data/test2.xml",
		"status": "SAT",
		"time": -1,
		"bounds": [{\'bound\': 199, \'time\': 12},  {\'bound\': 100, \'time\': 42}]
		"unsupported": 0,
		"bug": 0
	}...
	]
}';
        } else {
            $json_results = '{
 "results" : [
	{
		"fullname": "/data/test1.xml",
		"status": "SAT",
		"time": 10,
		"unsupported": 0,
		"bug": 0
	},
	{
		"fullname": "/data/test2.xml",
		"status": "UNSAT",
		"time": 100,
		"unsupported": 0,
		"bug": 0
	}
	]
}';
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
                        ->required()
                        ->helperText(new HtmlString(
                            '<p>Json file must have this structure<br /></p><pre class="text-xs bg-gray-50 dark:bg-gray-800 p-3 rounded-lg overflow-x-auto">' . e(
                                $json_benchmark
                            ) . '</pre>'
                        )),
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
                        ->helperText(new HtmlString(
                            '<p>Json file must have this structure<br /></p><pre class="text-xs bg-gray-50 dark:bg-gray-800 p-3 rounded-lg overflow-x-auto">' . e(
                                $json_results
                            ) . '</pre>'
                        ))
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
