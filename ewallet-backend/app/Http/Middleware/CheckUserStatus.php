<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User) {
            if ($user->status === 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'عذراً، لا يمكن تنفيذ العمليات المالية لأن حسابك قيد التحقق والمراجعة من قبل الإدارة. سيتم تفعيل كافة العمليات فور اعتماد الحساب.',
                    'data' => [
                        'status' => 'pending',
                    ],
                ], 403);
            }

            if ($user->status === 'suspended') {
                return response()->json([
                    'success' => false,
                    'message' => 'تم تعليق هذا الحساب من قبل الإدارة. يرجى مراجعة الدعم الفني.',
                    'data' => [
                        'status' => 'suspended',
                    ],
                ], 403);
            }

            if ($user->status === 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'تم رفض طلب التسجيل لهذا الحساب.',
                    'data' => [
                        'status' => 'rejected',
                    ],
                ], 403);
            }
        }

        return $next($request);
    }
}
