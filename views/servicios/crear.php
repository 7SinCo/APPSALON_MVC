<h1 class="nombre-pagina">Crear servicio</h1>
<p class="descripcion-pagina">Llena todos los campos para añadir un nuevo servicio</p>

<?php include_once __DIR__ . "/../templates/barra.php" ?>
<?php include_once __DIR__ . "/../templates/alertas.php" ?>

<form class="formulario" action="/servicios/crear" method="POST">
    <?php include_once __DIR__ . "/formulario.php" ?>

    <input class="boton" type="submit" value="Guardar servicio">
</form>