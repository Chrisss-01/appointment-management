<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'check.onboarding' => \App\Http\Middleware\CheckOnboarding::class,
        ]);
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('student/services*')) {
                session()->flash('auth_reason', 'Please log in to your student account to book an appointment.');
            }
            return '/login';
        });
        $middleware->redirectUsersTo(function () {
            $user = auth()->user();
            if (!$user) return '/';
            
            return match($user->role) {
                'admin' => '/admin/dashboard',
                'staff' => '/staff/dashboard',
                'student' => '/student/dashboard',
                default => '/',
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
