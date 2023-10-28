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
                <h3>Valoraciones de la incidencia</h3>
            </div>
            <?php
                // Verificar si se ha proporcionado el parámetro de ID en la URL
                if (isset($_GET['id'])) {
                    $idIncidencia = $_GET['id'];
                } else {?>
                    <p class='error-formulario'>ERROR: No se pudo extraer el ID 
                        de la incidencia de la BBDD.</p>
            <?php }    
                $query = "SELECT COUNT(*) AS count FROM Valoraciones WHERE idUsuario = " . $_SESSION["usuario"]["id"] . " AND idIncidencia = $idIncidencia";
                $resultado = mysqli_query($db, $query);
                
                if ($resultado) {
                    $fila = mysqli_fetch_assoc($resultado);
                    $count = $fila['count'];
                
                    if ($count > 0) {
                        // Ya existe una valoración para el usuario e incidencia específicos?>
                        <p class='error-formulario'>ERROR: Ya consta que has votado en esta incidencia. No puedes 
                        volver a votar.</p><?php
                    } else {
                        // No existe una valoración para el usuario e incidencia específicos
                        $idUsuario = $_SESSION["usuario"]["id"];
                        $insercion = mysqli_query($db, "INSERT INTO Valoraciones (idUsuario, idIncidencia, tipo) 
                        VALUES ('$idUsuario', '$idIncidencia', 'Dislike')");?>
                        <span class = "confirmacion-datos">Se ha añadido el dislike a la incidencia.</span><?php
                        $fecha = date('Y-m-d H:i:s');
                        $accion = "El usuario {$_SESSION['usuario']['email']} ha dado dislike a una incidencia.";
                        $insercionLog = mysqli_query($db, "INSERT INTO Logs (fecha, accion) VALUES ('$fecha', '$accion')");

                        $query = "SELECT negativas FROM Incidencias WHERE id = $idIncidencia";
                        $resultado = mysqli_query($db, $query);

                        if ($resultado) {
                            $fila = mysqli_fetch_assoc($resultado);
                            $negativas = $fila['negativas'];
                        } else {
                            echo "Error al ejecutar la consulta: " . mysqli_error($db);
}

                        $query = mysqli_query($db, "UPDATE Incidencias SET negativas = '" . mysqli_real_escape_string($db, $negativas+1) . "' WHERE id = '" . mysqli_real_escape_string($db, $idIncidencia) . "'");
                    }
                } else {
                    // Error al ejecutar la consulta
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