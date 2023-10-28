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
                <h3>Gestión de usuarios</h3>
                <h4>Listado de usuarios</h4>
            </div>

            <div class='accion-a-realizar'>
                <p>Indique la acción a realizar</p>
                <ul>
                    <li><a href="#">Listado</a></li>
                    <li><a href="add-usuario.php">Añadir nuevo usuario</a></li>
                </ul>
            </div>

            <?php
            $resultado = mysqli_query($db, 'SELECT * FROM Usuarios');

            // Verificar si se obtuvieron resultados
            if ($resultado) {
                // Iterar sobre los registros y mostrar la información
                while ($fila = mysqli_fetch_assoc($resultado)){?>
                <div class="datos-y-fotos-cada-usuario">
                    <div class="foto-cada-usuario">
                        
                        <img src='<?php echo $fila['imagen']; ?>' alt='Imagen de perfil de <?php echo 
                        $_SESSION['usuario']['nombre']; ?>' width='110' height='110'>
                        
                    </div>
                    
                    <div class="datos-cada-usuario">
                        <?php
                        echo '<ul>';
                            echo '<li>Usuario: ' . $fila['nombre'] . " " . $fila['apellidos'] . '</li>';
                            echo '<li>Email: ' . $fila['email'] . '</li>';
                            echo '<li>Dirección: ' . $fila['direccion'] . '</li>';
                            echo '<li>Teléfono: ' . $fila['telefono'] . '</li>';
                            echo '<li>Rol: ' . $fila['rol'] . '</li>';
                            echo '<li>Estado: ' . $fila['estado'] . '</li>';
                        echo '</ul>';?>
                    </div>

                    <div class="botones-editar-y-eliminar-cada-usuario">
                        <div class="boton-editar-perfil">
                            <a href="editar-perfil-por-admin.php?id=<?php echo $fila['id'] ?>" name="enlace-editar-usuario">Editar</a>
                        </div>
                        
                        <div class="boton-borrar-perfil">
                            <a href="borrar-perfil.php?id=<?php echo $fila['id'] ?>" name="enlace-borrar-usuario">Borrar</a>
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