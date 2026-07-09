<?php

namespace App\Filament\Pages;

use App\Models\Competition;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bugs extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'filament.pages.bugs';
    protected static bool $shouldRegisterNavigation = false; // si tu n'y accèdes que via un lien depuis une autre page

    public static ?string $slug = 'competitions/{competition}/bugs';

    public Competition $competition;

    public function mount(Competition $competition): void
    {
        $this->competition = $competition;
    }

    public function getTitle(): string
    {
        return "Bugs in results – {$this->competition->name} {$this->competition->track}" ;
    }

    public function table(Table $table): Table
    {
        $query = DB::table('results as r1')
            ->join('results as r2', 'r1.benchmark_id', '=', 'r2.benchmark_id')
            ->join('benchmarks', 'benchmarks.id', '=', 'r1.benchmark_id')
            ->join('solvers as s1', 's1.id', '=', 'r1.solver_id')
            ->join('solvers as s2', 's2.id', '=', 'r2.solver_id')
            ->whereColumn('r1.id', '<', 'r2.id')
            ->where('r1.status', '!=', 'UNKNOWN')
            ->where('r2.status', '!=', 'UNKNOWN')
            ->whereColumn('r1.status', '!=', 'r2.status')
            ->where('benchmarks.competition_id', $this->competition->id)
            ->select([
                DB::raw("CONCAT(r1.id, '-', r2.id) as id"),
                'benchmarks.id as benchmark_id',
                'benchmarks.name as benchmark_name',
                's1.name as s1_name',
                's1.version as s1_version',
                'r1.status as r1_status',
                's2.name as s2_name',
                's2.version as s2_version',
                'r2.status as r2_status',
            ])->limit(PHP_INT_MAX);

        $model = new class extends Model {
            protected $table = 'divergences';
            public $timestamps = false;
            protected $guarded = [];
            public $incrementing = false;
            protected $keyType = 'string';
        };

        return $table
            ->query($model->newQuery()->fromSub($query, 'divergences'))
            ->columns([
               TextColumn::make('benchmark_name')->label('Benchmark')->sortable()->searchable(),
               TextColumn::make('s1_name')->label('Solver 1')->sortable(),
                TextColumn::make('s1_version')->label('Version 1'),
                TextColumn::make('r1_status')->label('Statut 1')->badge(),
                TextColumn::make('s2_name')->label('Solver 2')->sortable(),
                TextColumn::make('s2_version')->label('Version 2'),
                TextColumn::make('r2_status')->label('Statut 2')->badge(),
            ])
            ->defaultSort('benchmark_name');
    }
}
