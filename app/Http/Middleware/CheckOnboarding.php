<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOnboarding
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'student') {
            if (!$user->department || !$user->program || !$user->year_level || !$user->student_id) {
                // Persistent capture for intent if it exists (e.g., from login redirection)
                if (session()->has('url.intended') && !session()->has('onboarding_intent')) {
                    session(['onboarding_intent' => session('url.intended')]);
                }
                
                return redirect()->route('onboarding.department');
            }
        }

        return $next($request);
    }
}
