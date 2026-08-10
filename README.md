# Result Analyzer Backend

This is the backend part of the result analyzer framework.
This framwork is used to compare results from solvers (optimisation, satisfaction...)
on a set of benchmarks.

The backend part is based on [Laravel](https://laravel.com). The frontend part is based on [React](https://react.dev/).

## Requirements for the backend part
 - PHP 8.3+
 - composer
 - Database (sqlite, Mysql....)

## Installation (local usage)

 1. Clone the project : git clone #TODO
 2. `composer install`
 3. `cp .env.example .env` 
 4. `php artisan key:generate` 
 5. Modify the .env file (database, cache, session). 
 5. Migrate the database `php artisan migrate`  
 6. Create an admin user `php artisan make:filament-user` 
 7. Optionally: create an evaluation example (XCSP 2026 competition) `php artisan db:seed`

For a production deployement, please see [the documentation](https://laravel.com/docs/13.x/deployment).

## Serve the application

`php artisan serve`

## Backend part

The admin part is http://localhost:8000. Log in. You have different menus:

- Evaluations: the list of available evaluation. You can see the missing results, the buggy results. You can also initialize the best results among some selected solvers. You can edit the competition and see all solvers abd benchmarks of it. Finally, you can create a competition.
- Solvers: the list of available solvers. You can edit them or add a solver.
- Benchmarks: the list of all benchmarks.
- Results : All results, you can filter them using different possibilities.

## Adding data

 - First of all, create the evaluation: https://xcsp26.alfweb.net/admin/competitions/create
 - Add benchmarks to the evaluation. You can add them one by one :(. 
 - Add results to the evaluation. The best way is to use the Python script (you need the package TODO).
