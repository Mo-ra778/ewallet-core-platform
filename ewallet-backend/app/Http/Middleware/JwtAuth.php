<?php

namespace App\Http\Middleware;

use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuth
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التوثيق (JWT Token) مفقود أو غير صالح.',
                'data' => null,
            ], 401);
        }

        $token = substr($header, 7);
        $entity = $this->jwtService->getAuthenticatedEntity($token);

        if (!$entity) {
            return response()->json([
                'success' => false,
                'message' => 'انتهت صلاحية الجلسة أو رمز التوثيق غير صالح.',
                'data' => null,
            ], 401);
        }

        // Attach authenticated user model to request
        $request->setUserResolver(fn() => $entity);
        $request->attributes->set('auth_entity', $entity);

        return $next($request);
    }
}
