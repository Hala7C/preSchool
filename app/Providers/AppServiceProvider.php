<?php

namespace App\Providers;

use App\Http\Middleware\Employee;
use App\Models\Student;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'student' => Student::class,
            'employee' => Employee::class,
        ]);
    }
}
