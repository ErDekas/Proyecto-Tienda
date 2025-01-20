<?php

namespace Lib;
// Clase con metodos para sanetizar y validar los campos de los formularios
class Validar
{

    public static function sanitizarString(string $input): string
    {
        return strip_tags(trim($input));
    }

    public static function sanitizarEmail(string $email): string
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    public static function sanitizarTelefono(string $phone): string
    {
        return preg_replace('/[^0-9+\-\(\) ]/', '', $phone);
    }

    public static function sanitizarInt($input): int
    {
        return (int) preg_replace('/[^0-9-]/', '', $input);
    }

    public static function sanitizarDouble($input): float
    {
        $cleaned = preg_replace('/[^0-9\.-]/', '', str_replace(',', '.', $input));
        return (float) $cleaned;
    }

    public static function validarPassword(string $password): bool
    {
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&#.$($)$-$_])[A-Za-z\d$@$!%*?&#.$($)$-$_]{8,15}$/';
        return preg_match($pattern, $password);
    }

    public static function validarString(string $input): bool
    {
        return !empty($input) && is_string($input);
    }

    public static function validarNombre(string $input): bool
    {
        return preg_match('/^[a-zA-Z\s]+$/', $input);
    }

    public static function validarApellidos(string $input): bool
    {
        return preg_match('/^[a-zA-Z\s]+$/', $input);
    }

    public static function validarEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validarInt($input): bool
    {
        return filter_var($input, FILTER_VALIDATE_INT) !== false;
    }

    public static function validarDouble($input): bool
    {
        return filter_var($input, FILTER_VALIDATE_FLOAT) !== false;
    }

    public static function validarDate(string $date): bool
    {
        $dateArray = explode('-', $date);
        return count($dateArray) === 3 && checkdate((int) $dateArray[1], (int) $dateArray[2], (int) $dateArray[0]);
    }

    public static function validarDireccion(string $input): bool
    {
        return preg_match('/^[\p{L}\d\s.,\/º-]+$/u', trim($input)) === 1;
    }

    public static function validarCiudad(string $input): bool
    {
        return preg_match('/^[\p{L}\s]+$/u', trim($input)) === 1;
    }
}
