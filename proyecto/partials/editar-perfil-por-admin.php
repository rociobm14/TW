<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Quéjate ¡no te calles!</title>
    <link rel="stylesheet" href="estiloProyecto.css">
    <link rel="icon" type="image/x-icon" href="logo.png">
</head>
<body>
    <main>
        <div class='listado'>
            <h3>Gestión de usuarios</h3>
            <h4>Editar usuario por Administrador</h4>
        </div>

    <?php

        // Verificar si se ha proporcionado el parámetro de ID del usuario en la URL
        if (isset($_GET['id'])) {
            $idUsuario = $_GET['id'];
        } else {?>
            <p class='error-formulario'>ERROR: No se pudo extraer el usuario a 
                editar de la BBDD.</p>
        <?php }
        
        $datosUsuarioDB = mysqli_query($db, "SELECT * FROM Usuarios WHERE id='" . mysqli_real_escape_string($db, $idUsuario) . "'");

        $datosUsuario = mysqli_fetch_assoc($datosUsuarioDB);
        
        if ($datosUsuarioDB && mysqli_num_rows($datosUsuarioDB) > 0){
                $_SESSION["datosUsuario"] = $datosUsuario;
        }

        function campoEsValido($campo)
        {
            global $db;
            switch ($campo){
                case "nombre":
                case "apellidos":
                case "direccion":
                    if (!empty($_POST[$campo])){
                        return true;
                    }
                    break;
                    
                case "email":
                    $emailRepetido = false;
                    // Buscar si el mail ya existe
                    $mailDB = mysqli_query($db, "SELECT * FROM Usuarios WHERE email = '" . mysqli_real_escape_string($db, $_POST[$campo]) . "'");
                    $mail = mysqli_fetch_assoc($mailDB);
                    if ($mailDB) {
                        if ($mailDB && mysqli_affected_rows($db) > 0 && ($mail["email"] != $_SESSION["datosUsuario"]["email"])) {
                            $emailRepetido = true;
                        }
                    }

                    if (isset($_POST[$campo]) && filter_var($_POST[$campo], FILTER_VALIDATE_EMAIL)
                        && !$emailRepetido){
                            return true;
                    }
                    break;
                    
                case "clave":

                    //En este caso, hay varias condiciones a cumplir: si la clave nueva está vacía, la confirmación también debe de estarlo
                    //no puede estar una puesta y la otra no. Además, ambas deben existir y no estar vacías y por supuesto coincidir
                    
                    if (empty($_POST['clave-nueva']) && empty($_POST['confirmar-clave-nueva'])){
                        return true;
                    }else if ((isset($_POST['clave-nueva']) &&!empty($_POST['clave-nueva'])) 
                            && (isset($_POST['confirmar-clave-nueva']) &&!empty($_POST['confirmar-clave-nueva']))
                            && ($_POST['clave-nueva'] == $_POST['confirmar-clave-nueva'])){
                        return true;
                        }
                    break;
        
                case "telefono":
                    $patronTelefono = "/^\d{9}$/"; //Formato español (9 números)
                    if (isset($_POST['telefono'])
                        && preg_match($patronTelefono, $_POST['telefono'])){
                        return true;
                    }
                    break;

                    /*
                case "imagen":
                    if (isset($_FILES['subir-imagen'])) {
                        $imagen_tipo = exif_imagetype($_FILES['subir-imagen']['tmp_name']);

                        if ($imagen_tipo === IMAGETYPE_JPEG || $imagen_tipo === IMAGETYPE_PNG) {
                            return true;
                        }
                    }
                    break;
                    */
            }
            return false;
        }

        function validarTodosLosCampos(){
            $fotoValida=campoEsValido("imagen");
            $nombreValido = campoEsValido("nombre");
            $apellidosValidos = campoEsValido("apellidos");
            $emailValido = campoEsValido("email");
            $direccionValida = campoEsValido("direccion");
            $claveValida = campoEsValido("clave");
            $telefonoValido = campoEsValido("telefono");
            //$imagenValida = campoEsValido("imagen");

            return $nombreValido && $apellidosValidos &&
            $emailValido && $direccionValida && 
            $claveValida && $telefonoValido;
            //$imagenValida
        }

        function hayErrores($campo){
            return isset($_POST['modificar-usuario']) && !campoEsValido($campo);
        }

        //Hacemos un update de los datos modificados en la base de datos, sólo en caso de que el valor nuevo sea distinto al de la BBDD
        function actualizar($campo){
            global $db;
            if ($_SESSION["$campo"] != $_SESSION["datosUsuario"]["$campo"]){
                $query = mysqli_query($db, "UPDATE Usuarios SET $campo = '" . mysqli_real_escape_string($db, $_SESSION["$campo"]) . "' WHERE id = '" . mysqli_real_escape_string($db, $_SESSION["datosUsuario"]["id"]) . "'");
                if ($query && mysqli_affected_rows($db) > 0) {
                    // La consulta se ejecutó correctamente y se actualizaron filas en la base de datos
                    $fecha = date('Y-m-d H:i:s');
                    $accion = "El administrador {$_SESSION['usuario']['email']} ha editado el perfil de {$_SESSION['datosUsuario']['email']}.";
                    $insercionLog = mysqli_query($db, "INSERT INTO Logs (fecha, accion) VALUES ('$fecha', '$accion')");
                    // Actualiza la variable de sesión con el nuevo valor
                    $_SESSION["datosUsuario"]["$campo"] = $_SESSION["$campo"];

                    if ($_SESSION["usuario"]["id"] == $_SESSION["datosUsuario"]["id"]){
                        $_SESSION["usuario"]["$campo"] = $_SESSION["$campo"];
                    }
            
                    // Otros pasos o redireccionamiento si es necesario
                } else {
                    // La consulta falló o no se realizaron cambios en la base de datos
                }
            }
        }

        //Realizamos las correspondientes actualizaciones de las variables en la BBDD
        function actualizarEnBD(){
            actualizar("nombre");
            actualizar("apellidos");
            actualizar("email");
            actualizar("direccion");
            actualizar("telefono");
            actualizar("clave");
            actualizar("rol");
            actualizar("estado");
        }

        //Actualización de variables de sesión con saneamiento de datos
        function actualizarVarSesion(){
            $_SESSION['nombre'] = htmlentities(strip_tags($_POST['nombre']));
            $_SESSION['apellidos'] = htmlentities(strip_tags($_POST['apellidos']));
            $_SESSION['email'] = htmlentities(strip_tags($_POST['email']));
            $_SESSION['direccion'] = htmlentities(strip_tags($_POST['direccion']));
            $_SESSION['telefono'] = htmlentities(strip_tags($_POST['telefono']));
            $_SESSION['rol'] = htmlentities(strip_tags($_POST['rol']));
            $_SESSION['estado'] = htmlentities(strip_tags($_POST['estado']));
            if (!empty($_POST['clave-nueva'])){
                $hash = password_hash(htmlentities(strip_tags($_POST['clave-nueva'])), PASSWORD_DEFAULT);
                $_SESSION['clave'] = $hash;
            } else {
                $_SESSION['clave'] = $_SESSION['datosUsuario']['clave'];
            }
        }

        function inicializarVarSesion($campo){
            global $enviadoCorrectamente;
            global $datosConfirmados;
            if (!$datosConfirmados && !$enviadoCorrectamente){
                $_SESSION["$campo"] = $_SESSION["datosUsuario"]["$campo"];
            }
                
        }

        function inicializarTodasVarSesion(){
            inicializarVarSesion("nombre");
            inicializarVarSesion("apellidos");
            inicializarVarSesion("email");
            inicializarVarSesion("direccion");
            inicializarVarSesion("telefono");
            inicializarVarSesion("clave");
            inicializarVarSesion("rol");
            inicializarVarSesion("estado");
        }

        $enviadoCorrectamente = false;
        if (isset($_POST["modificar-usuario"]) && validarTodosLosCampos()){
            $enviadoCorrectamente = true;
            actualizarVarSesion();
        }

        $datosConfirmados = false;

        if (isset($_POST['confirmar-datos'])){?>
            <span class = "confirmacion-datos">Se han modificado los datos del usuario.</span>
            <?php
                $datosConfirmados = true;
                actualizarEnBD();
            ?>
        <?php 
        }
        inicializarTodasVarSesion();
        ?>

        <div class="formulario-editar">
            <form action="#" method="POST">
                <div class="foto">
                    <label>Foto:
                        <img name ="imagen" src='<?php echo $_SESSION["datosUsuario"]["imagen"];?>' alt='Foto de <?php echo 
                        $_SESSION["datosUsuario"]["nombre"]?>' width="150" height="150">
                        <input type='file' name='subir-imagen' value='Seleccionar archivo' accept=".jpg, .png, .jpeg" 
                        <?php if ($enviadoCorrectamente || $datosConfirmados) echo "disabled"?>>
                    </label>
                </div>

                <label>Nombre:
                    <input type="text" name="nombre" value="<?php echo isset($_POST['nombre']) && !empty($_POST['nombre']) ? $_SESSION['nombre'] : $_SESSION['datosUsuario']['nombre']; ?>"
                    <?php if ($enviadoCorrectamente || $datosConfirmados) echo "disabled"; ?>>
                </label>


                <?php
                    if (hayErrores("nombre")){?>
                        <p class='error-formulario'>El nombre no puede estar vacío.</p>
                <?php } ?>

                
                <label>Apellidos:
                    <input type="text" name="apellidos" value="<?php echo isset($_POST['apellidos']) && !empty($_POST['apellidos'])? $_SESSION['apellidos'] : $_SESSION['datosUsuario']['apellidos']; ?>"
                    <?php if ($enviadoCorrectamente || $datosConfirmados) echo "disabled"; ?>>
                </label>

                <?php
                    if (hayErrores("apellidos")){?>
                        <p class='error-formulario'>Los apellidos no pueden estar vacíos.</p>
                <?php } ?>


                <label>Email:
                    <input type="email" name="email" value="<?php echo isset($_POST['email']) && !empty($_POST['email'])? $_SESSION['email'] : $_SESSION['datosUsuario']['email']; ?>"
                    <?php if ($enviadoCorrectamente || $datosConfirmados) echo "disabled"; ?>>
                </label>

                <?php
                    if (hayErrores("email")){?>
                        <p class='error-formulario'>El e-mail no tiene un formato válido o ya hay alguien registrado con ese e-mail.</p>
                <?php } ?>


                <?php 
                    if (!$enviadoCorrectamente && !$datosConfirmados){?>
                        <div class="clave">
                            <label>Clave:
                                <input type='password' placeholder="Nueva clave" name='clave-nueva'/>
                                <input type='password' placeholder="Confirmar nueva clave" name='confirmar-clave-nueva'/>
                            </label>
                        </div>

                    <?php 
                    }
                    if (hayErrores("clave")){?>
                        <p class='error-formulario'>Las claves no coinciden.</p>
                    <?php } ?>
            
                <label>Dirección:
                    <input type="text" name="direccion" value="<?php echo isset($_POST['direccion']) && !empty($_POST['direccion']) ? $_SESSION['direccion'] : $_SESSION['datosUsuario']['direccion']; ?>"
                    <?php if ($enviadoCorrectamente || $datosConfirmados) echo "disabled"; ?>>
                </label>

                <?php
                    if (hayErrores("direccion")){?>
                         <p class='error-formulario'>La dirección no puede estar vacía.</p>
                <?php } ?>

                <label>Teléfono:
                    <input type="text" name="telefono" value="<?php echo isset($_POST['telefono']) && !empty($_POST['telefono'])? $_SESSION['telefono'] : $_SESSION['datosUsuario']['telefono']; ?>"
                    <?php if ($enviadoCorrectamente || $datosConfirmados) echo "disabled"; ?>>
                </label>

                <?php
                    if (hayErrores("telefono")){?>
                        <p class='error-formulario'>El teléfono no tiene un formato válido.</p>
                <?php } ?>

                <label>Rol:
                    <select name='rol' <?php if ($enviadoCorrectamente || $datosConfirmados) echo "disabled"; ?>>
                    <option value='Administrador' <?php if (($_SESSION["datosUsuario"]["rol"] == "Administrador" && !$enviadoCorrectamente)) { echo "selected"; } else if (($_SESSION["rol"] == "Administrador") && ($enviadoCorrectamente || $datosConfirmados)){ echo "selected";} ?>> Administrador </option>
                    <option value='Colaborador' <?php if (($_SESSION["datosUsuario"]["rol"] == "Colaborador" && !$enviadoCorrectamente)) { echo "selected"; } else if (($_SESSION["rol"] == "Colaborador") && ($enviadoCorrectamente || $datosConfirmados)){ echo "selected";} ?>> Colaborador </option>
                    </select>
                </label>


                <label>Estado:
                    <select name='estado' <?php if ($enviadoCorrectamente || $datosConfirmados) echo "disabled"; ?>>
                        <option value='Activo' <?php if (($_SESSION["estado"] == "Activo")) { echo "selected"; }  else if (($_SESSION["estado"] == "Activo") && ($enviadoCorrectamente || $datosConfirmados)){ echo "selected";}?>> Activo </option>
                        <option value='Pendiente' <?php if (($_SESSION["estado"] == "Pendiente")) { echo "selected"; } else if (($_SESSION["estado"] == "Pendiente") && ($enviadoCorrectamente || $datosConfirmados)){ echo "selected";} ?>> Pendiente </option>
                    </select>
                </label>
                
                <?php 
                    if (!$enviadoCorrectamente && !$datosConfirmados){?>
                        <label>
                            <input type='submit' name='modificar-usuario' value='Modificar usuario'>
                        </label>
                    <?php
                    } else if (!$datosConfirmados){
                        ?>
                        <label>
                            <input type='submit' name='confirmar-datos' value='Confirmar datos'/>
                        </label>
                    <?php 
                    } 
                    ?>
                
            </form>
        </div>
    </main>
</body>
</html>