<?php
    // Verificar si se ha proporcionado el parámetro de ID de la incidencia en la URL
    if (isset($_GET['id'])) {
        $idIncidencia = $_GET['id'];
    } else {?>
        <p class='error-formulario'>ERROR: No se pudo extraer el ID 
            de la incidencia a editar de la BBDD.</p>
    <?php }

    $borrado = false;
    //Si se le da al botón de confirmar, significa que la se ha borrado la incidencia, por lo que
    //se nos imprimirá el mensaje de que se ha borrado
    if (isset($_POST["confirmar-borrado-incidencia"])){
        $borrado = true;
    }

    if ($borrado){?>
        <span class = "confirmacion-datos">Se ha borrado la incidencia.</span>
    <?php }

    //Realizamos una consulta de la incidencia que coincida con el ID proporcionado en la URL

    $incidenciaDB = mysqli_query($db, "SELECT * FROM Incidencias WHERE id='" . mysqli_real_escape_string($db, $idIncidencia) . "'");

    $incidencia = mysqli_fetch_assoc($incidenciaDB);
    
    //Si la hemos encontrado, guardaremos en una variable de sesión esa incidencia, y en el formulario jugaremos con esa variable de sesión
    //para que pueda imprimir los datos de la BBDD
    if ($incidenciaDB && mysqli_num_rows($incidenciaDB) > 0){
        $_SESSION["incidencia"] = $incidencia;
    }

    //Verificamos que el usuario tiene permisos para borrar la incidencia
    if ($_SESSION["usuario"]["rol"] == "Colaborador" && $_SESSION["usuario"]["id"] != $_SESSION["incidencia"]["idUsuario"]){
        header("Location: ./index.php");
    }
?>
    <div class="formulario-editar-incidencia">
    <form action="#" method="POST">
        <div class="formulario-incidencia">
            <h4>Estado:</h4>
            <div class='opciones-estado-incidencia'>
                <label>
                    <input type='radio' name='estado' value='Pendiente' <?php echo $_SESSION["incidencia"]["estado"] == 'Pendiente' ? "checked" : "";?> disabled/> Pendiente
                </label>
        
                <label>
                    <input type='radio' name='estado' value='Comprobada' <?php echo $_SESSION["incidencia"]["estado"] == 'Comprobada' ? "checked" : "";?> disabled/> Comprobada
                </label>
                                
                <label>
                    <input type='radio' name='estado' value='Tramitada' <?php echo $_SESSION["incidencia"]["estado"] == 'Tramitada' ? "checked" : "";?> disabled/> Tramitada
                </label>
                            
                <label>
                    <input type='radio' name='estado' value='Irresoluble' <?php echo $_SESSION["incidencia"]["estado"] == 'Irresoluble' ? "checked" : "";?> disabled/> Irresoluble
                </label>
                                
                <label>
                    <input type='radio' name='estado' value='Resuelta'<?php echo $_SESSION["incidencia"]["estado"] == 'Resuelta' ? "checked" : "";?> disabled/> Resuelta
                </label>
            </div>
        </div>
    </form>

    <form action="#" method="POST">
        <div class="formulario-incidencia">
            <h4>Datos principales:</h4>
            <label>
                Título:
                <input type="text" name="titulo" value="<?php echo $_SESSION["incidencia"]["titulo"]; ?>" disabled/>
            </label>
            
            <label>
                Descripción:
                <textarea disabled name="descripcion" 
                rows="10" cols="50"><?php echo $_SESSION["incidencia"]["descripcion"];?></textarea>
            </label>

            <label>
                Lugar:
                <input type="text" name="lugar" value="<?php echo $_SESSION['incidencia']['lugar']; ?>" disabled/>
            </label>
            
            <label>
                Palabras clave:
                <input type="text" name="palabras-clave" value="<?php echo $_SESSION['incidencia']['palabrasClave']; ?>" disabled/>
            </label>
        </div>
    </form>

    <form action="#" method="POST" enctype="multipart/form-data">
        <div class="formulario-incidencia">
            <h4>Fotografías adjuntas:</h4>
                <div class="imagenes-incidencia">
                <?php
                    $idIncidenciaActual = $_SESSION["incidencia"]["id"];
                    $query = "SELECT rutaImagen,idImagen FROM FotosIncidencias WHERE idIncidencia ='" . mysqli_real_escape_string($db, $idIncidenciaActual) . "'";
                    $resultado = mysqli_query($db, $query);
                    
                    if ($resultado) {
                        // Iterar sobre los resultados y mostrar las imágenes
                        while ($row = mysqli_fetch_assoc($resultado)) {
                            $idImagen = $row['idImagen'];
                            $rutaImagen = $row['rutaImagen'];
                                echo "<img src='$rutaImagen' alt='Imagen borrada' height=20% width=20%>";
                        }
                    } else {
                        echo "Error al ejecutar la consulta: " . mysqli_error($db);
                    }?>

                    <?php if (!$borrado){ ?>
                    <input type='submit' name='confirmar-borrado-incidencia' value='Confirmar borrado'/>

                    <?php }
                        if (isset($_POST["confirmar-borrado-incidencia"])){
                            global $db;
                              // Ejecutar la consulta DELETE que borrará todo el contenido de los 3 formularios de la incidencia
                            $eliminaIncidencias = mysqli_query($db, "DELETE FROM Incidencias WHERE id = '" . mysqli_real_escape_string($db, $idIncidencia) . "'");
                            $eliminaFotos = mysqli_query($db, "DELETE FROM FotosIncidencias WHERE idIncidencia = '" . mysqli_real_escape_string($db, $idIncidencia) . "'");
                            $eliminaValoraciones = mysqli_query($db, "DELETE FROM Valoraciones WHERE idIncidencia = '" . mysqli_real_escape_string($db, $idIncidencia) . "'");


                            $fecha = date('Y-m-d H:i:s');
                            $accion = "El usuario {$_SESSION['usuario']['email']} ha borrado una incidencia.";
                            $insercionLog = mysqli_query($db, "INSERT INTO Logs (fecha, accion) VALUES ('$fecha', '$accion')");
                        }
                    ?>
                </div>
        </div>
    </form>
</div>