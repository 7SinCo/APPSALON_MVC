<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController
{
    public static function index()
    {
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    public static function guardar()
    {
        // Almacena la cita y devuelve el ID
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();

        $id = $resultado["id"];

        // Almacena las citas y el/los servicio(s)
        $idServicios = explode(",", $_POST["servicios"]);

        // Almacena los servicios con el id de la cita
        foreach ($idServicios as $idServicio) {
            $args = [
                "citaId" => $id,
                "servicioId" => $idServicio
            ];
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();
        }

        echo json_encode(["resultado" => $resultado]);
    }

    public static function eliminar()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"];

            // More info
            // debuguear($_SERVER);

            $cita = Cita::find($id);
            $cita->eliminar();
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
    }
}
