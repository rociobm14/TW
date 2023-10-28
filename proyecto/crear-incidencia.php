<!DOCTYPE html>
<html>
<?php 
    session_start();
    include("partials/head-html.php");
?>
<body>
    <!---Comprobación de permisos -->
    <?php include("partials/comprobacion-login.php");?>

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
                <h3>Crear nueva incidencia</h3>
            </div>

            <?php include("partials/crear-incidencia.php");?>
        </main>
        <?php include("partials/sideMenu.php");?>
    </div>
    <!-- Footer de la web -->
    <?php include("partials/footer.php");?>

</body>
</html>