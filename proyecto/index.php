<!DOCTYPE html>
<html>
<?php 
    session_start();
    include("partials/head-html.php");
?>
<body>

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

    <!-- Página de bienvenida -->    
    <div class="contenedor">
        <?php
            include("bienvenida.php");
            include("partials/sideMenu.php");
        ?>
    </div>

    <!-- Footer de la web -->
    <?php include("partials/footer.php");?>

</body>
</html>