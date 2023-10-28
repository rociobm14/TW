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

    <!-- Página de "Ver incidencia" -->    

    <div class="contenedor">
    <main>    
    <div class='listado'>
        <h3>Listado de incidencias</h3>
    </div>
            
    <div class='incidencias'>
        <form action='ver-incidencias.php' method='POST'>
            <h4>Criterios de búsqueda</h4>
            <div class='ordenar-por'>
                <h5>Ordenar por:</h5>
                <label>
                    <input type='radio' name='antiguedad' value='antiguedad'/>
                        Antigüedad (primero las más recientes)
                </label>
    
                <label>
                    <input type='radio' name='antiguedad' value='numero-positivos'/>
                        Número de positivos (de más a menos)
                 </label>
    
                <label>
                    <input type='radio' name='antiguedad' value='numero-positivos-netos'/>
                        Número de positivos netos (de más a menos)
                </label>
            </div>
    
            <div class='incidencias-que-contengan'>
                <h5>Incidencias que contengan: </h5>
                <label>
                    Texto de búsqueda: 
                    <input type='text' id='texto-busqueda'>
                </label>
    
                <label>
                    Lugar:
                    <input type='text' id='lugar'>
                </label>
            </div>
                            
            <div class='estado'>
                <h5> Estado </h5>
                <div class='opciones-estado'>
                    <label>
                        <input type='checkbox'name='Pendiente' value='pendiente'/> Pendiente
                    </label>
    
                    <label>
                        <input type='checkbox' name='Comprobada' value='comprobada'/> Comprobada
                    </label>
                            
                    <label>
                        <input type='checkbox' name='Tramitada' value='tramitada' /> Tramitada
                    </label>
                           
                    <label>
                        <input type='checkbox' name='Irresoluble' value='irresoluble' /> Irresoluble
                    </label>
                            
                    <label>
                        <input type='checkbox' name='Resuelta' value='resuelta'/> Resuelta
                    </label>
                </div>
            </div>
                        
            <div class='boton-aplicar-criterios'>
                <input type='submit' name='aplicar-criterios' value='Aplicar criterios de búsqueda'>
            </div>

            <?php
                $resultado = mysqli_query($db, "SELECT * FROM Incidencias");

                // Verificar si se obtuvieron resultados
                if ($resultado) { 
                    // Iterar sobre los registros y mostrar la información
                    while ($fila = mysqli_fetch_assoc($resultado)){?>
                        <div class="datos-cada-incidencia">
                            <?php
                            $nombreCompletoDB = mysqli_query($db, "SELECT nombre, apellidos FROM Usuarios WHERE id = '" . mysqli_real_escape_string($db, $fila["idUsuario"]) . "'");
                            $nombreCompleto = mysqli_fetch_assoc($nombreCompletoDB);
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
                                    if (isset($_SESSION["usuario"])){
                                        if (($_SESSION["usuario"]["rol"] == "Administrador") || ($row["idUsuario"] == $_SESSION["usuario"]["id"]) || ($fila["idUsuario"] == $_SESSION["usuario"]["id"])){
                                            echo "<a href='borrar-comentario.php?id=" . $row['id'] . "' name='enlace-borrar-incidencia'>Borrar</a>";
                                        }
                                    }
                                echo '</div>';
                            }
                             ?>
                            
                        </div>
                            <div class="todos-botones-incidencia">
                                <?php if (isset($_SESSION["usuario"])){
                                    if ($_SESSION["usuario"]["rol"] == "Administrador" || $fila["idUsuario"] == $_SESSION["usuario"]["id"]){?>
                                    <div class="botones-incidencia">
                                        <a href="editar-incidencia.php?id=<?php echo $fila['id'] ?>" name="enlace-editar-incidencia">Editar</a>
                                    </div>
                                    
                                    <div class="botones-incidencia">
                                        <a href="borrar-incidencia.php?id=<?php echo $fila['id'] ?>" name="enlace-borrar-incidencia">Borrar</a>
                                    </div>
                            <?php } 
                            ?>

                                <div class="botones-incidencia">
                                    <a href="comentar-incidencia.php?id=<?php echo $fila['id'] ?>" name="enlace-comentar-incidencia">Comentar</a>
                                </div>

                                <div class="botones-incidencia">
                                    <a href="likes.php?id=<?php echo $fila['id'] ?>" name="enlace-dar-like-incidencia">Like</a>
                                </div>

                                <div class="botones-incidencia">
                                    <a href="dislikes.php?id=<?php echo $fila['id'] ?>" name="enlace-dar-dislike-incidencia">Dislike</a>
                                </div>
                            <?php }?>
                            </div>
                        
                    <?php } 
                }?>
            
        </form>
    </div>            
</main>
        <?php include("partials/sideMenu.php");?>
    </div>

    <!-- Footer de la web -->
    <?php include("partials/footer.php");?>

</body>
</html>