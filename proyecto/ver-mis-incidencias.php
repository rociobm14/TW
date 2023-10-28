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
                <h3>Ver mis incidencias</h3>
            </div>
            <?php

            $query = "SELECT COUNT(*) AS total FROM Incidencias WHERE idUsuario ='" . mysqli_real_escape_string($db, $_SESSION["usuario"]["id"]) . "'";
            $resultado = mysqli_query($db, $query);

            if ($resultado) {
                $fila = mysqli_fetch_assoc($resultado);
                $totalFilas = $fila['total'];

                if ($totalFilas == 0) {?>
                    <p class='error-formulario'>No tienes ninguna incidencia.</p>
               <?php }
            } else {
                echo "Error al ejecutar la consulta: " . mysqli_error($db);
            }


            $resultado = mysqli_query($db, "SELECT * FROM Incidencias WHERE idUsuario = '" . mysqli_real_escape_string($db, $_SESSION["usuario"]["id"]) . "'");
            $nombreCompletoDB = mysqli_query($db, "SELECT nombre, apellidos FROM Usuarios WHERE id = '" . mysqli_real_escape_string($db, $_SESSION["usuario"]["id"]) . "'");

            $nombreCompleto = mysqli_fetch_assoc($nombreCompletoDB);

            // Verificar si se obtuvieron resultados
            if ($resultado) {
                // Iterar sobre los registros y mostrar la información
                while ($fila = mysqli_fetch_assoc($resultado)){?>
                <div class="datos-y-fotos-cada-usuario">
                    <div class="datos-cada-incidencia">
                        <?php
                        echo '<ul>';
                            echo "<div class='titulo-incidencia'>";
                                echo $fila['titulo'];
                            echo "</div>";
                            echo "<div class='resto-datos-incidencia'>";
                                echo '<li>Lugar: ' . $fila['lugar'] . '</li>';
                                echo '<li>Fecha: ' . $fila['fecha'] . '</li>';
                                echo '<li>Creado por: ' . $nombreCompleto["nombre"] . " " . $nombreCompleto["apellidos"] . '</li>';
                                echo '<li>Palabras clave: ' . $fila['palabrasClave'] . '</li>';
                                echo '<li>Estado: ' . $fila['estado'] . '</li>';
                                echo '<li>Valoraciones: Pos: ' . $fila['positivas'] . ' Neg: ' . $fila['negativas'] . '</li>';
                                echo "<div class='descripcion-incidencia'>";
                                    echo $fila['descripcion'];
                                echo "</div>";
                                $query = "SELECT rutaImagen, idImagen FROM FotosIncidencias WHERE idIncidencia ='" . mysqli_real_escape_string($db, $fila['id']) . "'";
                                $imagenesDB = mysqli_query($db, $query);
                                while ($row = mysqli_fetch_assoc($imagenesDB)) {
                                    $idImagen = $row['idImagen'];
                                    $rutaImagen = $row['rutaImagen'];
                                        echo "<img src='$rutaImagen' alt='Imagen borrada' height=20% width=20%>";?>
                                    <?php
                                }
                            echo "</div>";
                        echo '</ul>';
                        
                        $query = "SELECT * FROM Comentarios WHERE idIncidencia ='" . mysqli_real_escape_string($db, $fila['id']) . "'";
                            $comentariosDB = mysqli_query($db, $query);

                            while ($row = mysqli_fetch_assoc($comentariosDB)) {
                                $nombreComentarioQuery = "SELECT nombre, apellidos FROM Usuarios WHERE id ='" . mysqli_real_escape_string($db, $row['idUsuario']) . "'";
                                $nombreComentarioDB = mysqli_query($db, $nombreComentarioQuery);
                                $nombreComentario = mysqli_fetch_assoc($nombreComentarioDB);
                                
                                echo '<div class="comentario">';
                                    echo '<span class="fecha">' . $row["fecha"] . '</span> ';
                                    echo '<span class="nombre">' . $nombreComentario["nombre"] . ' ' . $nombreComentario["apellidos"] . '</span><br>';
                                    echo '<span class="contenido">' . $row["comentario"] . '</span>';
                                    if (($_SESSION["usuario"]["rol"] == "Administrador") || ($row["idUsuario"] == $_SESSION["usuario"]["id"]) || ($fila["idUsuario"] == $_SESSION["usuario"]["id"])){
                                        echo "<a href='borrar-comentario.php?id=" . $row['id'] . "' name='enlace-borrar-incidencia'>Borrar</a>";
                                    }
                                echo '</div>';
                            }?>
                    </div>
                    

                    <div class="botones-editar-y-eliminar-cada-usuario">
                        <div class="boton-editar-perfil">
                            <a href="editar-incidencia.php?id=<?php echo $fila['id'] ?>" name="enlace-editar-incidencia">Editar</a>
                        </div>
                        
                        <div class="boton-borrar-perfil">
                            <a href="borrar-incidencia.php?id=<?php echo $fila['id'] ?>" name="enlace-borrar-incidencia">Borrar</a>
                        </div>
                    </div>
                </div>
                <?php }
            } else {
                echo 'Error al ejecutar la consulta: ' . mysqli_error($db);
            }
            ?>
        </main>
        <?php include("partials/sideMenu.php");?>
    </div>

    <!-- Footer de la web -->
    <?php include("partials/footer.php");?>

</body>
</html>