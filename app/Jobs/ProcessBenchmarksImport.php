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
                if ($this->record->type != 'cop') {
                    if (isset($entry->name, $entry->fullname, $entry->family, $entry->nb_variables, $entry->nb_clauses, $entry->info_domains, $entry->info_constraints, $entry->useless_vars) == false) {
                        $errors = true;
                        break;
                    }
                    $buffer[] = [
                        'evaluation_id' => $this->record->id,
                        'name' => $entry->name,
                        'fullname' => $entry->fullname,
                        'family' => $entry->family,
                        'nb_variables' => $entry->nb_variables,
                        'nb_clauses' => $entry->nb_clauses,
                        'info_domains' => $entry->info_domains,
                        'info_constraints' => $entry->info_constraints,
                        'useless_vars' => $entry->useless_vars,
                    ];
                } else {
                    if (isset($entry->name, $entry->fullname, $entry->family, $entry->nb_variables, $entry->nb_clauses, $entry->info_domains, $entry->info_constraints, $entry->useless_vars, $entry->type) == false) {
                        $errors = true;
                        break;
                    }
                    $buffer[] = [
                        'evaluation_id' => $this->record->id,
                        'name' => $entry->name,
                        'fullname' => $entry->fullname,
                        'family' => $entry->family,
                        'nb_variables' => $entry->nb_variables,
                        'nb_clauses' => $entry->nb_clauses,
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
                    ->body("At entry number $nb. Import canceled.")
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
