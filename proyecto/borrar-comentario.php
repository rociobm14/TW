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

    <!-- Página de "Editar incidencia" -->  
    <div class="contenedor">
        <main>
            <div class='listado'>
                <h3>Borrar comentario</h3>
            </div>
            <?php
                // Verificar si se ha proporcionado el parámetro de ID en la URL
                if (isset($_GET['id'])) {
                    $idComentario = $_GET['id'];
                } else {?>
                    <p class='error-formulario'>ERROR: No se pudo extraer el ID 
                        de la incidencia de la BBDD.</p>
            <?php }
            
            
            $eliminaComentarioDB = mysqli_query($db, "DELETE FROM Comentarios WHERE id = '" . mysqli_real_escape_string($db, $idComentario) . "'");

            if ($eliminaComentarioDB){?>
                <span class = "confirmacion-datos">Se ha eliminado el comentario de la incidencia.</span>
            <?php 
                $fecha = date('Y-m-d H:i:s');
                $accion = "El usuario {$_SESSION['usuario']['email']} ha borrado un comentario de una incidencia.";
                $insercionLog = mysqli_query($db, "INSERT INTO Logs (fecha, accion) VALUES ('$fecha', '$accion')");
            }
            ?>
        </main>
        <?php include("partials/sideMenu.php");?>
    </div>

    <!-- Footer de la web -->
    <?php include("partials/footer.php");?>

</body>
</html>