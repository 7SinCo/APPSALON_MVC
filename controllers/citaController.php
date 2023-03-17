<?php

namespace Controllers;

use MVC\Router;

class citaController
{
    public static function index(Router $router)
    {
        session_start();
        // debuguear($_SESSION);

        isAuth();

        $router->render("cita/index", [
            "nombre" => $_SESSION["nombre"],
            "id" => $_SESSION["id"],
        ]);
    }
}
