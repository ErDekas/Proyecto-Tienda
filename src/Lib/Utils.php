<?php

namespace Lib;

class Utils
{
    public static function isAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['usuario']) && $_SESSION["usuario"]["rol"] === 'admin';
    }

    public static function isSession()
    {
        return isset($usuario) && !empty($usuario);
    }

    public static function mensajeError()
    {
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
    }
}
