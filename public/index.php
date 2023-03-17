<?php

require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\APIController;
use Controllers\citaController;
use Controllers\adminController;
use Controllers\loginController;
use Controllers\servicioController;

$router = new Router();

// Iniciar sesión
$router->get('/', [loginController::class, 'login']);
$router->post('/', [loginController::class, 'login']);

// Cerrar sesión
$router->get('/logout', [loginController::class, 'logout']);

// Recuperar password
$router->get('/olvide', [loginController::class, 'olvide']);
$router->post('/olvide', [loginController::class, 'olvide']);
$router->get('/recuperar', [loginController::class, 'recuperar']);
$router->post('/recuperar', [loginController::class, 'recuperar']);

// Crear cuenta
$router->get('/crear-cuenta', [loginController::class, 'crear']);
$router->post('/crear-cuenta', [loginController::class, 'crear']);

// Confirmar cuenta
$router->get('/confirmar-cuenta', [loginController::class, 'confirmar']);
$router->get('/mensaje', [loginController::class, 'mensaje']);

// AREA PRIVADA
$router->get('/cita', [citaController::class, 'index']);
$router->get('/admin', [adminController::class, 'index']);

// API de Citas
$router->get('/api/servicios', [APIController::class, 'index']);
$router->post('/api/citas', [APIController::class, 'guardar']);
$router->post('/api/eliminar', [APIController::class, 'eliminar']);

// CRUD de Servicios
$router->get('/servicios', [servicioController::class, 'index']);
$router->get('/servicios/crear', [servicioController::class, 'crear']);
$router->post('/servicios/crear', [servicioController::class, 'crear']);
$router->get('/servicios/actualizar', [servicioController::class, 'actualizar']);
$router->post('/servicios/actualizar', [servicioController::class, 'actualizar']);
$router->post('/servicios/eliminar', [servicioController::class, 'eliminar']);



// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
