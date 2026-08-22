<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Agent;
use App\Models\User;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Config;

class JwtService
{
    private string $secretKey;
    private string $algorithm;
    private int $ttl;

    public function __construct()
    {
        $this->secretKey = Config::get('app.key') ?? 'ewallet-jwt-secret-key-32-chars-long!!';
        $this->algorithm = 'HS256';
        $this->ttl = 60 * 60 * 24 * 7; // 7 days in seconds
    }

    /**
     * Generate a JWT token for a given authenticatable model
     */
    public function generateToken(User|Agent|Admin $model, string $role = 'user'): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->ttl;

        $payload = [
            'iss' => Config::get('app.url', 'http://localhost'),
            'sub' => (string) $model->id,
            'role' => $role,
            'iat' => $issuedAt,
            'exp' => $expire,
            'model' => get_class($model),
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    /**
     * Decode and validate a JWT token
     */
    public function validateToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->secretKey, $this->algorithm));
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get the authenticated entity from a token
     */
    public function getAuthenticatedEntity(string $token): User|Agent|Admin|null
    {
        $payload = $this->validateToken($token);

        if (!$payload || !isset($payload->sub) || !isset($payload->role)) {
            return null;
        }

        return match ($payload->role) {
            'user' => User::find($payload->sub),
            'agent' => Agent::find($payload->sub),
            'admin', 'super_admin' => Admin::find($payload->sub),
            default => null,
        };
    }
}
