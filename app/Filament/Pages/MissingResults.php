<?php

namespace App\Filament\Pages;

use App\Models\Evaluation;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MissingResults extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.missing-results';
    protected static bool $shouldRegisterNavigation = false; // si tu n'y accèdes que via un lien depuis une autre page

    public static ?string $slug = 'evaluations/{evaluation}/missing-results';

    public Evaluation $evaluation;

    public function mount(Evaluation $evaluation): void
    {
        $this->evaluation = $evaluation;
    }

    public function getTitle(): string
    {
        return "Missing results – {$this->evaluation->name}" . " " . $this->evaluation->track;
    }


    public function table(Table $table): Table
    {
        $solvers = $this->evaluation->solvers;
        $r = 'results';
        $b = 'benchmarks';

        $union = null;

        foreach ($solvers as $s) {
            $sub = DB::table($b)
                ->selectRaw(
                    "? as sid, ? as sname, ? as sversion, $b.id as bid, $b.name as bname, CONCAT(?, '-', $b.id) as id",
                    [$s->id, $s->name, $s->version, $s->id]
                )
                ->where('evaluation_id', $this->evaluation->id)
                ->whereNotIn('id', function ($q) use ($r, $s) {
                    $q->select('benchmark_id')->from($r)->where('solver_id', $s->id);
                });

            $union = $union === null ? $sub : $union->unionAll($sub);
        }

        $union ??= DB::table($b)->whereRaw('1=0');

        $model = new class extends Model {
            protected $table = 'missing'; // jamais interrogée directement
            public $timestamps = false;
            protected $guarded = [];
        };

        return $table
            ->query($model->newQuery()->fromSub($union, 'missing'))
            ->columns([
                TextColumn::make('sname')->label('Solver'),
                TextColumn::make('sversion')->label('Version'),
                TextColumn::make('bname')->label('Benchmark'),
            ])->defaultSort('sname');
    }

}
