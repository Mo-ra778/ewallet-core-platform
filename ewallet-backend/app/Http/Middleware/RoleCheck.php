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
            $adminId = session('admin_id');
            if (!$adminId || !\App\Models\Admin::where('id', $adminId)->exists()) {
                session()->forget(['admin_id', 'admin_username', 'admin_role']);
                return redirect()->route('admin.login.form')->withErrors(['error' => 'انتهت صلاحية الجلسة، يرجى تسجيل الدخول كمسؤول أولاً.']);
            }
        } elseif ($guardType === 'agent') {
            $agentId = session('agent_id');
            if (!$agentId || !\App\Models\Agent::where('id', $agentId)->exists()) {
                session()->forget(['agent_id', 'agent_name']);
                return redirect()->route('agent.login.form')->withErrors(['error' => 'انتهت صلاحية الجلسة، يرجى تسجيل الدخول كوكيل أولاً.']);
            }
        }

        return $next($request);
    }
}
