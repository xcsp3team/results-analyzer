<?php

namespace App\Jobs;

use App\Models\Benchmark;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use JsonMachine\Items;

class ProcessResultsImport implements ShouldQueue
{
    use Queueable;


    /**
     * Create a new job instance.
     */
    public function __construct(private $path, private $solver_id, private $record, private $user_id)
    {
    }

    public function handle(): void
    {
        $fullPath = Storage::disk('local')->path($this->path);
        DB::beginTransaction();
        $buffer = [];
        try {
            $items = Items::fromFile($fullPath, ['pointer' => '/results']);
            $nb = 0;
            $errors = false;
            foreach ($items as $entry) {
                $nb++;
                $validator = Validator::make((array)$entry, [
                    'time' => ['required', 'integer', 'min:-1'],
                    'status' => ['required', Rule::in(['SAT', 'UNSAT', 'UNKNOWN', 'OPTIMUM'])],
                    'unsupported' => ["required", "boolean"],
                    ]);
                if ($validator->fails()) {
                    $errors = true;
                    $str_error = "";
                    foreach ($validator->errors()->all() as $error)
                        $str_error .= $error . "\n";
                    logger($str_error);
                    logger(json_encode($entry));
                    break;
                }
                $b = Benchmark::where("fullname", $entry->name)->first();
                if ($b == null) {
                    $errors = true;
                    $str_error = "Benchmark not found : $entry->name";
                    break;
                }
                if ($this->record->type != 'cop') {
                    $buffer[] = [
                        'benchmark_id' => $b->id,
                        'solver_id' => $this->solver_id,
                        'time' => $entry->time,
                        'status' => $entry->status,
                        'bug' => $entry->bug ?? 0,
                        'unsupported' => $entry->unsupported,
                    ];
                } else {
                    $buffer[] = [
                        'benchmark_id' => $b->id,
                        'solver_id' => $this->solver_id,
                        'time' => $entry->time,
                        'status' => $entry->status,
                        'bounds' => json_encode($entry->bounds),
                        'bug' => $entry->bug??0,
                        'unsupported' => $entry->unsupported,
                    ];
                }

                if (count($buffer) >= 100) {
                    DB::table('results')->insert($buffer);
                    $buffer = [];
                }
            }
            if ($errors) {
                DB::rollBack();
                Notification::make()
                    ->title('Import failed')
                    ->body("At entry number $nb. Import canceled: $str_error")
                    ->danger()
                    ->sendToDatabase(User::find($this->user_id));
                return;
            }
            if ($buffer !== []) {
                DB::table('results')->insert($buffer);
            }

        } finally {
            DB::commit();
            if ($errors == false) {
                Notification::make()
                    ->title('Import done')
                    ->success()
                    ->sendToDatabase(User::find($this->user_id))
                    ->send();
            }
            Storage::disk('local')->delete($this->path); // toujours nettoyé, même en cas d'échec
        }
    }

    public function failed(\Throwable $exception): void
    {
        Notification::make()
            ->title('Import failed')
            ->body($exception->getMessage())
            ->danger()
            ->sendToDatabase(User::find($this->user_id));

        Storage::disk('local')->delete($this->path);
    }

}
