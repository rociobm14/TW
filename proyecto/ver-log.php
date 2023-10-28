<!DOCTYPE html>
<html>
<?php 
    session_start();
    include("partials/head-html.php");
?>
<body>
    <!---Comprobación de permisos -->
    <?php include("partials/comprobacion-login.php");?>
    <?php include("partials/comprobacion-admin.php");?>

    <!---Conexión con la base de datos -->
    <?php include("partials/conexion-bbdd.php");?>

	<!-- Header de la web -->   
    <?php include("partials/header.php");?>

    <!-- Configuración del login -->   
    <?php include("partials/login.php");?>

     <!-- Menú de navegación -->   
     <?php include("partials/menu-nav.php");?>
    
    <?php
        //Herramientas de desarrollo
        include("partials/herramientas-desarrollo.php");
    ?>

    <!-- Página de "Ver mis incidencias" -->    

    <div class="contenedor">
        <main>
            <div class='listado'>
                <h3>Eventos del sistema</h3>
            </div>

            <?php
$query = "SELECT * FROM Logs";
$resultado = mysqli_query($db, $query);

if ($resultado) {
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $fecha = $fila['fecha'];
        $accion = $fila['accion'];
        ?>

        <div class="log-entry">
            <div class="fecha"><?php echo $fecha; ?></div>
            <div class="accion">INFO: <?php echo $accion; ?></div>
        </div>

        <?php
    }
} else {
    echo "Error al ejecutar la consulta: " . mysqli_error($db);
}
?>
        </main>
        <?php include("partials/sideMenu.php");?>
    </div>

    <!-- Footer de la web -->
    <?php include("partials/footer.php");?>

</body>
</html>