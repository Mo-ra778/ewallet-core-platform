<?php

use App\Http\Middleware\CheckUserStatus;
use App\Http\Middleware\JwtAuth;
use App\Http\Middleware\RoleCheck;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'jwt.auth' => JwtAuth::class,
            'check.status' => CheckUserStatus::class,
            'role.check' => RoleCheck::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson() || $request->has('debug')) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'class' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => explode("\n", $e->getTraceAsString()),
                ], 500);
            }
        });
    })->create();

// Automatically redirect storage path on Serverless (Vercel)
if (isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL']) || (function_exists('posix_getpwuid') && !is_writable(storage_path()))) {
    $serverlessStorage = '/tmp/storage';
    if (!is_dir($serverlessStorage)) {
        @mkdir("{$serverlessStorage}/framework/views", 0777, true);
        @mkdir("{$serverlessStorage}/framework/sessions", 0777, true);
        @mkdir("{$serverlessStorage}/framework/cache", 0777, true);
        @mkdir("{$serverlessStorage}/logs", 0777, true);
    }
    $app->useStoragePath($serverlessStorage);
}

return $app;
