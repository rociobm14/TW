<?php
    function campoValido($campo){
        return isset($_POST["$campo"]) && !empty($_POST["$campo"]);
    }

    //Comprobamos si hay errores en el formulario
    function hayErrores($campo, $formulario){
        switch ($formulario){
            case 0:
                return isset($_POST['modificar-estado-incidencia']) && !campoValido($campo);
                break;
            
            case 1:
                return isset($_POST['enviar-incidencia']) && !campoValido($campo);
                break;

            case 2:
                break;
        }
        
    }

    //Comprobamos si todos los campos son válidos
    function validarTodosLosCampos($campo){
        switch($campo){
            case "estado":
                $estado = campoValido("estado");
                return $estado;
                break;

            case "datos":
                $titulo = campoValido("titulo");
                $descripcion = campoValido("descripcion");
                $lugar = campoValido("lugar");
                $palabrasclave = campoValido("palabras-clave");
        
                return $titulo && $descripcion && $lugar
                && $palabrasclave;
                break;

            case "imagen":
                break;   
        }
    }

    //Actualización de variables de sesión con saneamiento de datos
    function actualizarVarSesion($campo){
        switch ($campo){
            case "estado":
                $_SESSION['estado-incidencia'] = htmlentities(strip_tags($_POST['estado']));
                break;
            case "datos":
                $_SESSION['titulo'] = htmlentities(strip_tags($_POST['titulo']));
                $_SESSION['lugar'] = htmlentities(strip_tags($_POST['lugar']));
                $_SESSION['descripcion'] = htmlentities(strip_tags($_POST['descripcion']));
                $_SESSION['palabrasClave'] = htmlentities(strip_tags($_POST['palabras-clave']));
                break;
            case "imagen":
                break;    
        }
    }

    //Inicializamos las variables de sesión necesarias para el correcto 
    //funcionamiento del formulario
    function inicializarVarSesion($formulario){
        global $enviadoCorrectamente;
        global $datosConfirmados;
        switch ($formulario){
            case 0:
                if (!$datosConfirmados[0] && !$enviadoCorrectamente[0]){
                    $_SESSION["estado-incidencia"] = $_SESSION["incidencia"]["estado"];
                }
                break;

            case 1:
                if (!$datosConfirmados[1] && !$enviadoCorrectamente[1]){
                    $_SESSION["titulo"] = $_SESSION["incidencia"]["titulo"];
                    $_SESSION["descripcion"] = $_SESSION["incidencia"]["descripcion"];
                    $_SESSION["lugar"] = $_SESSION["incidencia"]["lugar"];
                    $_SESSION["palabrasClave"] = $_SESSION["incidencia"]["palabrasClave"];
                }
                break;

            case 2:
                break;    
        }
            
    }

    //Inicializamos todas las variables de sesión
    function inicializarTodasVarSesion($formulario){
        switch ($formulario){
            case 0:
                inicializarVarSesion(0);
                break;
            case 1:
                inicializarVarSesion(1);
                break;
            
            case 2:
                inicializarVarSesion(2);
                break;
        }
    }

    //Actualizamos con UPDATE en la base de datos los cambios que se produzcan en
    //la incidencia por parte del usuario
    function actualizar($campo, $formulario){
        global $db;
        switch($formulario){
            case 0:
                if ($_SESSION["estado-incidencia"] != $_SESSION["incidencia"]["$campo"]){
                    $query = mysqli_query($db, "UPDATE Incidencias SET $campo = '" . mysqli_real_escape_string($db, $_SESSION["estado-incidencia"]) . "' WHERE id = '" . mysqli_real_escape_string($db, $_SESSION["incidencia"]["id"]) . "'");
                    if ($query && mysqli_affected_rows($db) > 0) {
                        // La consulta se ejecutó correctamente y se actualizaron filas en la base de datos
                        $fecha = date('Y-m-d H:i:s');
                        $accion = "El usuario {$_SESSION['usuario']['email']} ha modificado el estado de una incidencia.";
                        $insercionLog = mysqli_query($db, "INSERT INTO Logs (fecha, accion) VALUES ('$fecha', '$accion')");
                        // Actualiza la variable de sesión con el nuevo valor
                        $_SESSION["incidencia"]["$campo"] = $_SESSION["estado-incidencia"];
        
                        if ($_SESSION["usuario"]["id"] == $_SESSION["incidencia"]["idUsuario"]){
                            $_SESSION["incidencia"]["$campo"] = $_SESSION["estado-incidencia"];
                        }
                    } else {
                        // La consulta falló o no se realizaron cambios en la base de datos
                    }
                }

                break;

            case 1:
                if ($_SESSION["$campo"] != $_SESSION["incidencia"]["$campo"]){
                    $query = mysqli_query($db, "UPDATE Incidencias SET $campo = '" . mysqli_real_escape_string($db, $_SESSION["$campo"]) . "' WHERE id = '" . mysqli_real_escape_string($db, $_SESSION["incidencia"]["id"]) . "'");
                    if ($query && mysqli_affected_rows($db) > 0) {
                        // La consulta se ejecutó correctamente y se actualizaron filas en la base de datos
                        $fecha = date('Y-m-d H:i:s');
                        $accion = "El usuario {$_SESSION['usuario']['email']} ha modificado los datos principales de una incidencia.";
                        $insercionLog = mysqli_query($db, "INSERT INTO Logs (fecha, accion) VALUES ('$fecha', '$accion')");
                        // Actualiza la variable de sesión con el nuevo valor
                        $_SESSION["incidencia"]["$campo"] = $_SESSION["$campo"];
        
                        if ($_SESSION["usuario"]["id"] == $_SESSION["incidencia"]["idUsuario"]){
                            $_SESSION["incidencia"]["$campo"] = $_SESSION["$campo"];
                        }
                    } else {
                        // La consulta falló o no se realizaron cambios en la base de datos
                    }
                }
                break;

            case 2:
                break;    
        }
    }

    //Posiciones 0, 1 y 2 (correspondiente a cada formulario)
    $enviadoCorrectamente = array(false, false, false);
    $camposDesactivados = array(false, false, false);
    $datosConfirmados = array(false, false, false);

    //Lógica de lo que hace cada botón en este formulario
    if (isset($_POST["modificar-estado-incidencia"]) && validarTodosLosCampos("estado")){
        $enviadoCorrectamente[0] = true;
        actualizarVarSesion("estado");
        $camposDesactivados[0] = true;
    }

    if (isset($_POST["confirmar-estado-incidencia"])){
        $datosConfirmados[0] = true;
        actualizar("estado", 0);
    }

    if (isset($_POST["modificar-datos-incidencia"]) && validarTodosLosCampos("datos")){
        $enviadoCorrectamente[1] = true;
        actualizarVarSesion("datos");
        $camposDesactivados[1] = true;
    }


    if (isset($_POST["confirmar-modificacion-datos"])){
        $datosConfirmados[1] = true;
        actualizar("titulo", 1);
        actualizar("descripcion", 1);
        actualizar("lugar", 1);
        actualizar("palabrasClave", 1);
    }

    //Comprobamos si el usuario actual es Administrador
    function esAdmin(){
        if ($_SESSION["usuario"]["rol"] == "Administrador")
            return true;
        
        return false;
    }

    function getEstado($estado){
        return $_SESSION["estado-incidencia"] == $estado;
    }

    // Verificar si se ha proporcionado el parámetro de ID en la URL
    if (isset($_GET['id'])) {
        $idIncidencia = $_GET['id'];
    } else {?>
        <p class='error-formulario'>ERROR: No se pudo extraer el ID 
            de la incidencia a editar de la BBDD.</p>
    <?php }


    $incidenciaDB = mysqli_query($db, "SELECT * FROM Incidencias WHERE id='" . mysqli_real_escape_string($db, $idIncidencia) . "'");

    $incidencia = mysqli_fetch_assoc($incidenciaDB);
        
    if ($incidenciaDB && mysqli_num_rows($incidenciaDB) > 0){
        $_SESSION["incidencia"] = $incidencia;
    }

    inicializarTodasVarSesion(0);
    inicializarTodasVarSesion(1);

    //Verificamos que el usuario tiene permisos para editar la incidencia
    if ($_SESSION["usuario"]["rol"] == "Colaborador" && $_SESSION["usuario"]["id"] != $_SESSION["incidencia"]["idUsuario"]){
        header("Location: ./index.php");
    }
?>
<?php //Mostramos los formularios con las configuraciones oportunas de PHP

    if (!$camposDesactivados[1] && !$camposDesactivados[2]){?>
    <div class="formulario-editar-incidencia">
    <form action="#" method="POST">
        <div class="formulario-incidencia">
            <h4>Estado:</h4>
            <div class='opciones-estado-incidencia'>
                <label>
                    <input type='radio' name='estado' value='Pendiente' <?php echo getEstado("Pendiente") ? "checked" : ""; echo !esAdmin() || $camposDesactivados[0] ? " disabled" : ""; ?>/> Pendiente
                </label>
        
                <label>
                    <input type='radio' name='estado' value='Comprobada' <?php echo getEstado("Comprobada") ? "checked" : ""; echo !esAdmin() || $camposDesactivados[0] ? " disabled" : "";?>/> Comprobada
                </label>
                                
                <label>
                    <input type='radio' name='estado' value='Tramitada' <?php echo getEstado("Tramitada") ? "checked" : ""; echo !esAdmin() || $camposDesactivados[0] ? " disabled" : "";?>/> Tramitada
                </label>
                            
                <label>
                    <input type='radio' name='estado' value='Irresoluble' <?php echo getEstado("Irresoluble") ? "checked" : ""; echo !esAdmin() || $camposDesactivados[0] ? " disabled" : "";?>/> Irresoluble
                </label>
                                
                <label>
                    <input type='radio' name='estado' value='Resuelta'<?php echo getEstado("Resuelta") ? "checked" : ""; echo !esAdmin() || $camposDesactivados[0] ? " disabled" : "";?>/> Resuelta
                </label>
            </div>

            <?php
                if (hayErrores("estado", 0)){?>
                    <p class='error-formulario'>Debes seleccionar un estado.</p>
            <?php } ?>

            <?php if (esAdmin()){
                if (!$enviadoCorrectamente[0]){?> 
                    <label>
                        <input type='submit' name='modificar-estado-incidencia' value='Modificar estado'/>
                    </label>
               <?php }
                else if ($enviadoCorrectamente[0] && !$datosConfirmados[0]){?>          
                    <label>
                        <input type='submit' name='confirmar-estado-incidencia' value='Confirmar estado'/>
                    </label>
                    
                <?php }
                ?>

            <?php } ?>
            
        </div>
    </form>
<?php } ?>


<?php if (!$camposDesactivados[0] && !$camposDesactivados[2]){ ?>
    <form action="#" method="POST">
        <div class="formulario-incidencia">
            <h4>Datos principales:</h4>
            <label>
                Título:
                <input type="text" name="titulo" value="<?php echo campoValido("titulo") ? $_SESSION['titulo'] : $_SESSION['incidencia']['titulo']; ?>"
                <?php echo $enviadoCorrectamente[1] ? "disabled" : ""?>>
            </label>

            <?php //Comprobamos errores
                if (hayErrores("titulo", 1)){?>
                    <p class='error-formulario'>El título no puede estar vacío.</p>
            <?php } ?>
            
            <label>
                Descripción:
                <textarea <?php echo $enviadoCorrectamente[1] ? "disabled" : ""?> name="descripcion" 
                rows="10" cols="50"><?php echo campoValido("descripcion") ? $_SESSION['descripcion'] : $_SESSION['incidencia']['descripcion']; ?></textarea>
            </label>


            <?php
                if (hayErrores("descripcion", 1)){?>
                    <p class='error-formulario'>La descripción no puede estar vacía.</p>
            <?php } ?>

            <label>
                Lugar:
                <input type="text" name="lugar" value="<?php echo campoValido("lugar") ? $_SESSION['lugar'] : $_SESSION['incidencia']['lugar']; ?>"
                <?php echo $enviadoCorrectamente[1] ? "disabled" : ""?>>
            </label>

            <?php
                if (hayErrores("lugar", 1)){?>
                    <p class='error-formulario'>El lugar no puede estar vacío.</p>
            <?php } ?>
            
            <label>
                Palabras clave:
                <input type="text" name="palabras-clave" value="<?php echo campoValido("palabras-clave") ? $_SESSION['palabrasClave'] : $_SESSION['incidencia']['palabrasClave']; ?>"
                <?php echo $enviadoCorrectamente[1] ? "disabled" : ""?>>
            </label>

            <?php
                if (hayErrores("palabras-clave", 1)){?>
                    <p class='error-formulario'>Las palabras clave no pueden estar vacías.</p>
            <?php } 
            
            if (!$enviadoCorrectamente[1]){?>
                <div class="boton-crear-incidencia">
                    <input type="submit" value="Modificar datos" name="modificar-datos-incidencia"/>
                </div>
            <?php
            } else if ($enviadoCorrectamente[1] && !$datosConfirmados[1]){
                ?>
                <label>
                    <input type='submit' name='confirmar-modificacion-datos' value='Confirmar datos'/>
                </label>
            <?php 
            } 
            ?>
        </div>
    </form>

<?php }
 
    //Configuramos la nomenclatura de los nombres de archivo de las imágenes,
    //que todas siguen la misma convección
    $apareceFoto = true;
    if (isset($_POST['borrar-foto'])) {
        $apareceFoto = false;
    }
    if (isset($_FILES['subir-imagen'])) {
        // Obtener información del archivo
        $archivo = $_FILES['subir-imagen'];
        $nombreArchivo = $archivo['name'];
        $tipoArchivo = $archivo['type'];
        $rutaTemporal = $archivo['tmp_name'];

        $contadorFotoDB = mysqli_query($db, "SELECT COUNT(*) AS total FROM FotosIncidencias WHERE idIncidencia ='" . mysqli_real_escape_string($db, $_SESSION["incidencia"]["id"]) . "'");

        $contadorFoto = mysqli_fetch_assoc($contadorFotoDB);

        $siguienteImagen = $contadorFoto["total"]+1;
        
        // Generar nuevo nombre de archivo
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $nuevoNombreSinExtension = $_SESSION["incidencia"]["id"] . '-' . $siguienteImagen;
        $nuevoNombre = $nuevoNombreSinExtension . '.' . $extension;
        
        // Ruta de destino
        $carpetaDestino = './img/incidencias/';
        $rutaDestino = $carpetaDestino . $nuevoNombre;
        
        // Mover archivo a la carpeta de destino
        if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
            $idIncidenciaFoto = $_SESSION["incidencia"]["id"];
            $insercionFoto = mysqli_query($db, "INSERT INTO FotosIncidencias (rutaImagen, idImagen, idIncidencia) VALUES ('$rutaDestino', '$nuevoNombreSinExtension','$idIncidenciaFoto')");
            
            $fecha = date('Y-m-d H:i:s');
            $accion = "El usuario {$_SESSION['usuario']['email']} ha subido una imagen a una incidencia.";
            $insercionLog = mysqli_query($db, "INSERT INTO Logs (fecha, accion) VALUES ('$fecha', '$accion')");
        } else {
            
        }
    }
?>

<?php if (!$camposDesactivados[0] && !$camposDesactivados[1]){ ?>
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
                                if ($apareceFoto){?>
                                <div class="cada-imagen-incidencia"><?php
                                    echo "<input type='submit' value='Borrar foto' name='borrar-foto'/>";
                                    echo "<input type='hidden' name='idImagen' value='$idImagen'>";?>
                                </div>
                            <?php }
                        }
                    } else {
                        echo "Error al ejecutar la consulta: " . mysqli_error($db);
                    }
                    
                ?>
                </div>
                <input type='file' name='subir-imagen' value='Seleccionar archivo' accept=".jpg, .png, .jpeg"/>
                <input type="submit" value="Añadir fotografia" name="aniadir-foto"/>
        </div>
    </form>
</div>
<?php }

    if (isset($_POST['borrar-foto'])) {
        $idImagen = $_POST['idImagen'];

        // Obtener la ruta de la imagen a eliminar de la base de datos
        $query = "SELECT idImagen, rutaImagen FROM FotosIncidencias WHERE idImagen ='" . mysqli_real_escape_string($db, $idImagen) . "'";
        $resultado = mysqli_query($db, $query);
        
        if ($resultado) {
            $row = mysqli_fetch_assoc($resultado);
            $rutaImagen = $row['rutaImagen'];
    
            // Eliminar la imagen del sistema de archivos
            if (unlink($rutaImagen)) {
                
                // Borrar la entrada de la base de datos
                $query = "DELETE FROM FotosIncidencias WHERE idImagen ='" . mysqli_real_escape_string($db, $idImagen) . "'";
                $resultado = mysqli_query($db, $query);
    
                if ($resultado) {
                    $fecha = date('Y-m-d H:i:s');
                    $accion = "El usuario {$_SESSION['usuario']['email']} ha eliminado una foto de una incidencia.";
                    $insercionLog = mysqli_query($db, "INSERT INTO Logs (fecha, accion) VALUES ('$fecha', '$accion')");
                } else {
                    echo "Error al borrar la foto de la base de datos: " . mysqli_error($db);
                }
            } else {
                echo "Error al borrar el archivo del sistema de archivos.";
            }
        } else {
            echo "Error al ejecutar la consulta: " . mysqli_error($db);
        }
    }
?>