<?php

namespace App\Jobs;

use App\Models\Benchmark;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use JsonMachine\Items;

class ProcessBenchmarksImport implements ShouldQueue
{
    use Queueable;


    /**
     * Create a new job instance.
     */
    public function __construct(private $path, private $record, private $user_id)
    {
    }

    public function handle(): void
    {
        $fullPath = Storage::disk('local')->path($this->path);
        DB::beginTransaction();

        $buffer = [];
        try {
            $items = Items::fromFile($fullPath, ['pointer' => '/benchmarks']);
            $nb = 0;
            $errors = false;
            foreach ($items as $entry) {
                $nb++;
                $validator = Validator::make((array)$entry, [
                    'name' => ['required', 'string'],
                    'fullname' => ['required', 'string'],
                    'family' => ['required', 'string'],
                    'nb_variables' => ['required', 'integer'],
                    'nb_constraints' => ['required', 'integer'],
                    'info_domains' => ['required', 'string'],
                    'info_constraints' => ['required', 'string'],
                    'useless_vars' => ['required', 'integer'],
                    'type' => ['start_with: min,max']
                ]);
                if ($validator->fails()) {
                    $errors = true;
                    $str_error = $validator->errors()->all()[0];
                    break;
                }


                if ($this->record->type != 'cop') {
                    $buffer[] = [
                        'evaluation_id' => $this->record->id,
                        'name' => $entry->name,
                        'fullname' => $entry->fullname,
                        'family' => $entry->family,
                        'nb_variables' => $entry->nb_variables,
                        'nb_constraints' => $entry->nb_constraints,
                        'info_domains' => $entry->info_domains,
                        'info_constraints' => $entry->info_constraints,
                        'useless_vars' => $entry->useless_vars,
                    ];
                } else {
                    $buffer[] = [
                        'evaluation_id' => $this->record->id,
                        'name' => $entry->name,
                        'fullname' => $entry->fullname,
                        'family' => $entry->family,
                        'nb_variables' => $entry->nb_variables,
                        'nb_constraints' => $entry->nb_constraints,
                        'info_domains' => $entry->info_domains,
                        'info_constraints' => $entry->info_constraints,
                        'useless_vars' => $entry->useless_vars,
                        'type' => $entry->type,
                    ];
                }

                if (count($buffer) >= 500) {
                    DB::table('benchmarks')->insert($buffer);
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
                DB::table('benchmarks')->insert($buffer);
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
