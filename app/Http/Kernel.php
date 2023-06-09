<?php

namespace App\Http;

use App\Http\Middleware\BusExist;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'isAdmin'  =>  \App\Http\Middleware\Admin::class,
        'isUser'=> \App\Http\Middleware\User::class,
        'isManager'=> \App\Http\Middleware\Manager::class,
        'isBusSupervisor'=> \App\Http\Middleware\BusSupervisor::class,
        'isEmployee'=> \App\Http\Middleware\Employee::class,
        'isStudent'=>\App\Http\Middleware\Student::class,
        'isTeacher'=>\App\Http\Middleware\Teacher::class,
        'isAdminOrUser'=>\App\Http\Middleware\StudentAdmin::class,
        'isBusRegistry'=>\App\Http\Middleware\BusRegistry::class,
        'isStudentDistributed'=>\App\Http\Middleware\StudentDistributed::class,
        'isBusExist'=>\App\Http\Middleware\BusExist::class,
        'BusCapacities'=>\App\Http\Middleware\BusCapacity::class,
        'isTeacher\'sClass'=>\App\Http\Middleware\ClassTeacher::class,
        'TeacherSubject'=>\App\Http\Middleware\AddExamFromTeacher::class,
        'addExam'=>\App\Http\Middleware\ExamsRule::class,
        'role' => \App\Http\Middleware\Role::class,
        'myAuth'=>\App\Http\Middleware\MyAuth::class,
        'InitYearConfig'=>\App\Http\Middleware\InitYearConfig::class

    ];
}
