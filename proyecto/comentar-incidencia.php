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
                <h3>Comentar incidencia</h3>
            </div>   
            <div class="enviar-comentario">    
            <?php
            $enviadoCorrectamente = false;
            $datosConfirmados = false;
            if (isset($_POST["enviar-comentario"]) && campoValido("comentario")) {
                $enviadoCorrectamente = true;
                $_SESSION["comentario"] = $_POST["comentario"];
            }

            if (isset($_POST["confirmar-comentario"])){
                $datosConfirmados = true;
            }

            function campoValido($campo){
                return isset($_POST["$campo"]) && !empty($_POST["$campo"]);
            }


            // Verificar si se ha proporcionado el parámetro de ID en la URL
                if (isset($_GET['id'])) {
                    $idIncidencia = $_GET['id'];
                } else {?>
                    <p class='error-formulario'>ERROR: No se pudo extraer el ID 
                        de la incidencia de la BBDD.</p>
            <?php } 
            
                if ($datosConfirmados){
                    $contadorComentarioDB = mysqli_query($db, "SELECT MAX(id) AS max_id FROM Comentarios;");

                    $contadorComentario = mysqli_fetch_assoc($contadorComentarioDB);

                    $siguienteComentario = $contadorComentario["max_id"]+1;

                    $fecha = date('Y-m-d H:i:s');
                    $comentario = $_SESSION["comentario"];
                    $idUsuario = $_SESSION["usuario"]["id"];
                    $insercion = mysqli_query($db, "INSERT INTO Comentarios (fecha, comentario, idUsuario, idIncidencia, id) 
                    VALUES ('$fecha', '$comentario', '$idUsuario', '$idIncidencia', '$siguienteComentario')");?>
                    <span class = "confirmacion-datos">Se ha añadido el comentario a la incidencia.</span><?php 
                    $fecha = date('Y-m-d H:i:s');
                    $accion = "El usuario {$_SESSION['usuario']['email']} ha comentado en una incidencia.";
                    $insercionLog = mysqli_query($db, "INSERT INTO Logs (fecha, accion) VALUES ('$fecha', '$accion')");
                }
            if (!$datosConfirmados){?>
                <form action="#" method="POST" class="comentario-form">
                <label for="nuevo-comentario">Nuevo comentario de la incidencia:</label>
                <textarea <?php echo $enviadoCorrectamente ? "disabled" : "";?> id="nuevo-comentario" name="comentario"
                rows="10" cols="50"><?php echo campoValido("comentario") ? $_SESSION['comentario'] : ""; ?></textarea>
                
                <?php 
                    if (!campoValido("comentario") && isset($_POST["enviar-comentario"])){?>
                        <p class='error-formulario'>El comentario no puede estar vacío.</p>
            <?php }
            
                if (!$enviadoCorrectamente){ ?>
                    <div class="boton-enviar-comentario">
                    <input type="submit" value="Enviar" name="enviar-comentario">
                </div>
                    
                <?php }
                
                if ($enviadoCorrectamente && !$datosConfirmados){ ?>
                    <div class="boton-confirmar-comentario">
                        <input type="submit" value="Confirmar comentario" name="confirmar-comentario">
                    </div>
            <?php } ?>
            </div>

                
            </form>
            <?php }
            ?>
        </main>
        <?php include("partials/sideMenu.php");?>
    </div>

    <!-- Footer de la web -->
    <?php include("partials/footer.php");?>

</body>
</html>