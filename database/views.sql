create view competition_solver as
(
SELECT solvers.id as solver_id, competition_id, count(*) as nb_benchmarks
from solvers
         join results on results.solver_id = solvers.id
         join benchmarks on results.benchmark_id = benchmarks.id
group by solvers.id, competition_id
)

create view competition_solver_cop as
(
SELECT solvers.id as solver_id, competition_id, count(*) as nb_benchmarks
from solvers
         join results_cop on results_cop.solver_id = solvers.id
         join benchmarks_cop on results_cop.benchmark_id = benchmarks_cop.id
group by solvers.id, competition_id
)
