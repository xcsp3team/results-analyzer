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
                if ($this->record->type != 'cop') {

                    if (isset($entry->time, $entry->status, $entry->unsupported, $entry->bug) == false) {
                        $errors = true;
                        break;
                    }
                    $b = Benchmark::where("fullname", $entry->fullname)->first();
                    if ($b == null) {
                        $errors = true;
                        break;
                    }
                    $buffer[] = [
                        'benchmark_id' => $b->id,
                        'solver_id' => $this->solver_id,
                        'time' => $entry->time,
                        'status' => $entry->status,
                        'bug' => $entry->bug,
                        'unsupported' => $entry->unsupported,
                    ];
                } else {

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
