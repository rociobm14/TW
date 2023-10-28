<?php
if (true){
?>
        <aside>
            <?php
                if (!isset($_SESSION["usuario"])){
                    if ($errorUsuarioClave){?>
                        <p>El usuario/contraseña es incorrecto.</p>
                    <?php
                    }
                    ?>
                    
                    <div class='login'>
                        <form action='index.php' method='POST'>
                            <div class='form-group'>
                                <label for='usuario'>Email:</label>
                                <input type='text' name='usuario' id='usuario'>
                            </div>

                            <div class='form-group'>
                                <label for='clave'>Clave:</label>
                                <input type='password' name='clave' id='clave'>
                            </div>
                        
                            <div class=boton-login>
                                <input type='submit' name='iniciar-sesion' value='Login'>
                            </div>
                        </form>
                    </div>
                <?php
                } else if (isset($_SESSION["usuario"])){
                ?>                        
                    <p>Bienvenido/a <?php echo $_SESSION['usuario']['nombre']; ?>.</p>
                    <div class='rol'> <?php echo $_SESSION['usuario']['rol']; ?>.</div>
                    
                    <div class='foto-login'>
                        <img src='<?php echo $_SESSION['usuario']['imagen']; ?>' alt='Imagen de perfil de <?php echo 
                        $_SESSION['usuario']['nombre']; ?>' width='150' height='150'>
                    </div>
                    
                    <div class= 'botones-editar-logout'>
                    <div class='boton-editar'>
                    <a href="editar-perfil.php"><input type='submit' name='editar-datos' value='Editar'></a>
                    </div>
                      
                    <form action='#' method='POST'>      
                            <div class=boton-logout>
                                <input type='submit' name='cerrar-sesion' value='Logout'>
                            </div>
                        </div>
                    </form>
                <?php    
                } ?>
            
            <div class="numero-likes">
                <h3>Número de likes</h3>
                <?php
                $query = "SELECT COUNT(*) AS likes FROM Valoraciones WHERE tipo = 'Like'";
                $resultado = mysqli_query($db, $query);

                if ($resultado) {
                    $fila = mysqli_fetch_assoc($resultado);
                    $numLikes = $fila['likes'];
                } else {
                    echo "Error al ejecutar la consulta: " . mysqli_error($db);
                }

                echo "<p class='fondo-estadisticas'>El número de likes es $numLikes. </p>";
                ?>
            </div>

            <div class="numero-dislikes">
                <h3>Número de dislikes</h3>
                <?php
                $query = "SELECT COUNT(*) AS dislikes FROM Valoraciones WHERE tipo = 'Dislike'";
                $resultado = mysqli_query($db, $query);

                if ($resultado) {
                    $fila = mysqli_fetch_assoc($resultado);
                    $numDislikes = $fila['dislikes'];
                } else {
                    echo "Error al ejecutar la consulta: " . mysqli_error($db);
                }

                echo "<p class='fondo-estadisticas'>El número de dislikes es $numDislikes. </p>";
                ?>
            </div>

            <div class="numero-incidencias">
                <h3>Número de incidencias</h3>
                <?php
                $query = "SELECT COUNT(*) AS total FROM Incidencias";
                $resultado = mysqli_query($db, $query);

                if ($resultado) {
                    $fila = mysqli_fetch_assoc($resultado);
                    $numIncidencias = $fila['total'];
                } else {
                    echo "Error al ejecutar la consulta: " . mysqli_error($db);
                }

                echo "<p class='fondo-estadisticas'>El número de incidencias es $numIncidencias. </p>";
                ?>

            </div>
        </aside>
    </div>
    <?php 
    }
    ?>