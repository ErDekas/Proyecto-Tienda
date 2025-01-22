<?php

namespace Lib;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Security {
    private string $jwt_secret;
    private string $jwt_algorithm;
    private int $token_expiration;

    public function __construct() {
        $this->jwt_secret = $_ENV['JWT_SECRET'] ?? 'your-secret-key';
        $this->jwt_algorithm = 'HS256';
        $this->token_expiration = 3600; // 1 hour in seconds
    }

    public function generateToken(array $userData): string {
        $issuedAt = time();
        $expire = $issuedAt + $this->token_expiration;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'data' => $userData
        ];

        return JWT::encode($payload, $this->jwt_secret, $this->jwt_algorithm);
    }

    public function verifyToken(string $token): ?object {
        try {
            return JWT::decode($token, new Key($this->jwt_secret, $this->jwt_algorithm));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function generateEmailToken(): array {
        $token = bin2hex(random_bytes(32));
        $expiration = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        return [
            'token' => $token,
            'expiration' => $expiration
        ];
    }
}