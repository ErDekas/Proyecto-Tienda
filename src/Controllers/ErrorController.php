<?php

namespace Controllers;

use Lib\Pages;

class ErrorController
{

    public static function error404()
    {
        $pages = new Pages();
        $pages->render('error/error404', ['titulo' => 'Página no encontrada']);
    }
}
