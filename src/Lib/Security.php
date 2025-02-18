<?php

namespace Lib;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class Security
{
    private string $jwt_secret;
    private string $jwt_algorithm;
    private int $token_expiration;
    public function __construct()
    {
        $this->jwt_secret = $_ENV['JWT_SECRET'] ?? 'your-secret-key';
        $this->jwt_algorithm = 'HS256';
        $this->token_expiration = 3600; // 1 hour in seconds
    }

    final public static function encryptPassw(string $passw)
    {
        return password_hash($passw, PASSWORD_BCRYPT);
    }

    final public static function validatePassw(string $passw, string $passwhash)
    {
        error_log("Validando contraseña:");
        error_log("Input: " . $passw);
        error_log("Hash: " . $passwhash);
        error_log("Resultado: " . (password_verify($passw, $passwhash) ? "true" : "false"));

        return password_verify($passw, $passwhash);
    }

    final public static function secretKey(): string
    {
        return $_ENV['JWT_SECRET'];
    }

    public function generateToken(array $userData): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->token_expiration;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'data' => $userData
        ];

        return JWT::encode($payload, $this->jwt_secret, $this->jwt_algorithm);
    }

    public function verifyToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key(self::secretKey(), 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function generateEmailToken(): array
    {
        $token = bin2hex(random_bytes(64));
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hours'));

        return [
            'token' => $token,
            'expiration' => $expiration
        ];
    }
}
