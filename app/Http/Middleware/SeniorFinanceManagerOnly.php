<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bank and User pages are only for Senior Finance Managers
 * (the same users who see these menus in the sidebar).
 */
class SeniorFinanceManagerOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isSeniorFinanceManager()) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
