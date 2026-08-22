<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  string  $guardType  'admin' or 'agent'
     */
    public function handle(Request $request, Closure $next, string $guardType): Response
    {
        if ($guardType === 'admin') {
            if (!session()->has('admin_id')) {
                return redirect()->route('admin.login.form')->withErrors(['error' => 'يرجى تسجيل الدخول كمسؤول أولاً.']);
            }
        } elseif ($guardType === 'agent') {
            if (!session()->has('agent_id')) {
                return redirect()->route('agent.login.form')->withErrors(['error' => 'يرجى تسجيل الدخول كوكيل أولاً.']);
            }
        }

        return $next($request);
    }
}
