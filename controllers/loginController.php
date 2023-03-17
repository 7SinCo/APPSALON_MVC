<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Model\Usuario;

class loginController
{
    public static function login(Router $router)
    {
        $alertas = [];
        $auth = new Usuario;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                $usuario = Usuario::where("email", $auth->email);

                if ($usuario) {
                    // Verificar el password
                    if ($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        // Autenticar al usuario
                        session_start();

                        $_SESSION["id"] = $usuario->id;
                        $_SESSION["nombre"] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION["email"] = $usuario->email;
                        $_SESSION["login"] = true;

                        // Redireccionamiento
                        if ($usuario->admin == "1") {
                            $_SESSION["admin"] = $usuario->admin ?? null;
                            header("Location: /admin");
                        } else {
                            header("Location: /cita");
                        }
                    }
                } else {
                    Usuario::setAlerta("error", "El usuario no existe");
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            "alertas" => $alertas,
            "auth" => $auth
        ]);
    }

    public static function logout()
    {
        session_start();
        // debuguear($_SESSION); // Verificando estado de la sesión

        $_SESSION = [];
        // debuguear($_SESSION); // Verificando sesión cerrada

        header("Location: /");
    }

    public static function olvide(Router $router)
    {
        $alertas = [];
        $auth = new Usuario;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if (empty($alertas)) {
                $usuario = Usuario::where("email", $auth->email);

                if ($usuario && $usuario->confirmado === "1") {

                    // Generar un nuevo token
                    $usuario->crearToken();
                    $usuario->guardar();

                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Enviar el email
                    Usuario::setAlerta("exito", "Hemos enviado las instrucciones a tu email");
                } else {
                    Usuario::setAlerta("error", "El usuario no existe o no está confirmado");
                }
            }
        }
        // Enviar alertas antes de renderizar la vista
        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password', [
            "alertas" => $alertas
        ]);
    }

    public static function recuperar(Router $router)
    {
        $alertas = [];
        $error = false;
        $token = s($_GET['token']);

        // Buscar usuario por token
        $usuario = Usuario::where("token", $token);
        if (empty($usuario)) {
            Usuario::setAlerta("error", "Token no válido");
            $error = true;
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $password->validarPassword();

            if (empty($alertas)) {
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();

                $usuario->token = null;

                $resultado = $usuario->guardar();
                if ($resultado) {
                    header("Location: /");
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            "alertas" => $alertas,
            "error" => $error
        ]);
    }

    public static function crear(Router $router)
    {
        $usuario = new Usuario;
        // Alertas vacías
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
            // debuguear($usuario);

            if (empty($alertas)) {
                // Verificar que el usuario no esté registrado
                $resultado = $usuario->existeUsuario();

                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hasehar el password
                    $usuario->hashPassword();

                    // Generar un token único
                    $usuario->crearToken();

                    // Enviar Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();


                    // Crear usuario
                    $resultado = $usuario->guardar();
                    if ($resultado) {
                        header("Location: /mensaje");
                    }
                }
            }
        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router)
    {
        $router->render("auth/mensaje");
    }

    public static function confirmar(Router $router)
    {
        $alertas = [];

        $token = s($_GET['token']);
        $usuario = Usuario::where("token", $token);
        // debuguear($usuario);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
        } else {
            $usuario->confirmado = "1";
            $usuario->token = null;

            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');
        }

        // Obtener alertas
        $alertas = Usuario::getAlertas();

        $router->render("auth/confirmar-cuenta", [
            "alertas" => $alertas
        ]);
    }
}
