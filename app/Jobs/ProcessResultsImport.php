<?php

namespace App\Jobs;

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

    public function __construct(private $path, private $solver_id, private $record, private $user_id)
    {
    }

    public function handle(): void
    {
        $fullPath = Storage::disk('local')->path($this->path);
        $is_cop = $this->record->type == 'cop';

        // Un seul chargement au lieu d'une requête par entrée : fullname => id
        $benchmarks = $this->record->benchmarks()->pluck('id', 'fullname');

        $errors = false;
        $str_error = '';
        $nb = 0;
        $rows = [];
        $bounds = [];   // benchmark_id => [time => bound]

        DB::beginTransaction();
        try {
            $items = Items::fromFile($fullPath, ['pointer' => '/results']);
            foreach ($items as $entry) {
                $nb++;
                $validator = Validator::make((array)$entry, [
                    'time' => ['required', 'integer', 'min:-1'],
                    'status' => ['required', Rule::in(['SAT', 'UNSAT', 'UNKNOWN', 'OPTIMUM'])],
                    'unsupported' => ['required', 'boolean'],
                ]);
                if ($validator->fails()) {
                    $errors = true;
                    $str_error = implode("\n", $validator->errors()->all());
                    break;
                }

                $benchmark_id = $benchmarks[$entry->name] ?? null;
                if ($benchmark_id === null) {
                    $errors = true;
                    $str_error = "Benchmark not found : $entry->name";
                    break;
                }

                $rows[] = [
                    'benchmark_id' => $benchmark_id,
                    'solver_id' => $this->solver_id,
                    'time' => $entry->time,
                    'status' => $entry->status,
                    'bug' => $entry->bug ?? 0,
                    'unsupported' => $entry->unsupported,
                ];

                if ($is_cop) {
                    $dedup = [];
                    foreach ($entry->bounds ?? [] as $b) {
                        if (isset($b->time, $b->bound))
                            $dedup[(string)$b->time] = $b->bound;   // même time : on garde la dernière
                    }
                    $bounds[$benchmark_id] = $dedup;
                }

                if (count($rows) >= 100) {
                    $this->flush($rows, $bounds);
                    $rows = [];
                    $bounds = [];
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

            $this->flush($rows, $bounds);
            DB::commit();

            Notification::make()
                ->title('Import done')
                ->success()
                ->sendToDatabase(User::find($this->user_id))
                ->send();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;   // déclenche failed()
        } finally {
            Storage::disk('local')->delete($this->path);
        }
    }

    private function flush(array $rows, array $bounds): void
    {
        if ($rows === [])
            return;

        DB::table('results')->insert($rows);

        if ($bounds === [])
            return;

        $ids = DB::table('results')
            ->where('solver_id', $this->solver_id)
            ->whereIn('benchmark_id', array_keys($bounds))
            ->pluck('id', 'benchmark_id');

        $insert = [];
        foreach ($bounds as $benchmark_id => $list)
            foreach ($list as $time => $bound)
                $insert[] = [
                    'result_id' => $ids[$benchmark_id],
                    'time' => $time,
                    'bound' => $bound,
                ];

        foreach (array_chunk($insert, 1000) as $part)
            DB::table('result_bounds')->upsert($part, ['result_id', 'time'], ['bound']);
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
