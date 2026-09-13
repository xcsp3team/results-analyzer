# Result Analyzer

This application allows to compare a set of combinatorial solvers on a given evaluation. Each evaluation is composed on
a set of benchmarks. An evaluation can be based on SAT, CSP or COP problems. The application is composed of two parts.

- The backend part allows to create evaluations, solvers and to import benchmarks and results.
- The frontend part is used to compare solvers on a set of benchmarks.

## Requirements

- PHP 8.3+
- composer
- npm
- Database (sqlite, Mysql....)

## Installation (local usage)

1. Clone the project : git clone https://github.com/xcsp3team/results-analyzer.git
2. `composer install`
3. `npm install`
4. `cp .env.example .env`
5. `php artisan key:generate`
6. Modify the .env file (database, cache, session).
7. Migrate the database `php artisan migrate`
8. Create an admin user `php artisan make:filament-user`
9. Optionally: create an XCSP evaluation example (XCSP 2026 competition) `php artisan db:seed`
10. Optionally: create a SAT evaluation example (SAT 2006 competition) #TODO

For a production deployment, please see [the documentation](https://laravel.com/docs/13.x/deployment).

## Serve the application

`composer run dev`

## Backend part

The admin part is http://localhost:8000/admin. Log in. You have different menus:

- Evaluations: the list of available evaluation. You can see the missing results, the buggy results. You can also
  initialize the best results among some selected solvers. You can edit the competition and see its benchmarks. Finally,
  you can create a competition. You can import benchmarks and results (see below).
- Solvers: the list of available solvers. You can edit them or add a solver.
- Results : All results, you can filter them using different possibilities.

## Adding benchmarks

In the edit page of an evaluation, you can import a set of benchmarks using a json file. The file must have this format:

```
{
 "benchmarks" : [
	{
		"name": "test1",
		"fullname": "/data/test1.xml",
		"family": "test",
		"nb_variables": 10,
		"nb_clauses": 20,
		"info_domains": "",
		"info_constraints": "",
		"useless_vars": 0
	},
	{
		"name": "test2",
		"fullname": "/data/test2.xml",
		"family": "test",
		"nb_variables": 12,
		"nb_clauses": 30,
		"info_domains": "",
		"info_constraints": "",
		"useless_vars": 0,
		"type": "min VAR"
	}
 ]
}
```

The fields are:

- name: the name of the benchmark has it is displayed in the frontend part.
- fullname: When you import results, the fullname is used to find the benchmark in the database. It must be unique.
- family: the family of the benchmark.
- nb_variables: the number of variables.
- nb_clauses: the number of constraints/clauses.
- info_domains (for CSP/COP problems). It must be a strng of type "#types:1 #values:25025 (#1001:25)".
- info_constraints: It mus be of type "#intension:42551 #ordered:1" for CSP/COP problems or "#2:30 #3: 40" for SAT
  problems.
- useless_vars: if any.
- Only for COP problems. The kind of optimisation. Must start with min or max.

## Adding Results

In the edit page of an evaluation, you can import a set of results using a json file. The file is related to only one
solver.
For SAT/CSP problems, the file must have this format:

```
{
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
}
```

The fields are:

- fullname: the fullname of the benchmark. It is used to find the benchmark in the database.
- status: SAT/UNSAT/UNKNOWN
- time: the time required to solve the instance
- unsupported: true if the solver does not support one constraint (usefully for CSP instances)
- bug: if the solver appears buggy on this instance.

For COP problems, the status can also be OPTIMUM. The time is -1 if the solver is not able to find the optimum or is the
time required to prove the optimality. You also must save the bounds evolution using an additional field:

`"bounds": [{'bound': 199, 'time': 12}, {'bound': 190, 'time': 15}, {'bound': 100, 'time': 42}]`

## Frontend part

It is quite intuitive. One the evaluation selected, you can see the results using 3 different pages (details, chart,
comparison).
You can select/deslected some solvers. You can also filters benchmarks using different kinds of filtering.

An helping page can give you additional information.

## Authors

Gilles Audemard (gilles.audemard@univ-artois.fr). 
