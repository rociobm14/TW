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
            <h4>Añadir nuevo usuario por Administrador</h4>
        </div>
    <?php
        //Por defecto, establecemos que el usuario creado tenga como Rol ser Colaborador
        //y que su estado sea Pendiente
        if (!isset($_SESSION["rol"])){
            $_SESSION["rol"] = "Colaborador";
        }

        if (!isset($_SESSION["estado"])){
            $_SESSION["estado"] = "Pendiente";
        }
    
        //Comprobamos si cada campo es válido
        function campoEsValido($campo)
        {
            global $db;
            switch ($campo){
                //Para el caso del nombre, apellidos y dirección, se requiere que el campo no esté vacío, por lo que
                //lo metemos todo dentro de un mismo case.
                case "nombre":
                case "apellidos":
                case "direccion":
                    if (!empty($_POST[$campo])){
                        return true;
                    }
                    break;

                //En el caso del email debe comprobar 2 cosas: que el email registrado en el nuevo usuario no esté ya 
                //registrado en otro usuario de la BBDD, y que tenga un formato correcto, lo cual lo conseguimos con
                //la función filter_var
                case "email":
                    $emailRepetido = false;
                    // Buscar si el mail ya existe
                    $mailDB = mysqli_query($db, "SELECT * FROM Usuarios WHERE email = '" . mysqli_real_escape_string($db, $_POST[$campo]) . "'");
                    if ($mailDB) {
                        if ($mailDB && mysqli_affected_rows($db) > 0) {
                            $emailRepetido = true;
                        }
                    }

                    if (isset($_POST[$campo]) && filter_var($_POST[$campo], FILTER_VALIDATE_EMAIL)
                        && !$emailRepetido){
                            return true;
                    }
                    break;
                
                //Para la clave, se debe de comprobar que se introduzca la clave nueva 2 veces, y que ningún
                //campo de la clave esté vacío, además de que coincidan.
                case "clave":
                    if ((isset($_POST['clave-nueva']) &&!empty($_POST['clave-nueva'])) 
                            && (isset($_POST['confirmar-clave-nueva']) &&!empty($_POST['confirmar-clave-nueva']))
                            && ($_POST['clave-nueva'] == $_POST['confirmar-clave-nueva'])){
                        return true;
                        }
                    break;
                
                //Para el teléfono hemos usado una expresión regular que sólo admita números de teléfono
                //con formato español, 9 dígitos.
                case "telefono":
                    $patronTelefono = "/^\d{9}$/"; //Formato español (9 números)
                    if (isset($_POST['telefono'])
                        && preg_match($patronTelefono, $_POST['telefono'])){
                        return true;
                    }
                    break;
                
                //Para las imágenes se deben comprobar que existan y que sean de formato png o jpeg
                case "imagen":
                    if (isset($_FILES['subir-imagen'])) {
                        $imagen_tipo = exif_imagetype($_FILES['subir-imagen']['tmp_name']);

                        if ($imagen_tipo === IMAGETYPE_JPEG || $imagen_tipo === IMAGETYPE_PNG) {
                            return true;
                        }
                    }
                    break;
            }
            return false;
        }

        //Funcion que se encarga de validar todos los campos
        //Devolverá true si todos los campos son válidos.
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

        //Función que comprueba si hay errores en un campo.
        //Devolverá error si, al haberle dado al botón de añadir usuario,
        //encuentra que el campo a evaluar no es válido al no cumplir
        //alguna de las condiciones anteriores.
        function hayErrores($campo){
            return isset($_POST['add-usuario']) && !campoEsValido($campo);
        }

        //Función que inserta el usuario en la base de datos
        //Insertaremos los valores de las variables de sesión utilizadas para
        //guardar los datos introducidos en el formulario
        function insertarEnBD(){
            global $db;
            // Obtener el valor del último ID para que cada usuario tenga el suyo propio
            $idDB = mysqli_query($db, "SELECT MAX(ID) AS ultimoID FROM Usuarios");
            if ($idDB) {
                $id = mysqli_fetch_assoc($idDB);

                $ultimoID = $id['ultimoID'];

                $id = $ultimoID+1;
            }
                //Insertar todos los datos del usuario en la tabla con el nuevo ID asignado
                $nombre = $_SESSION["nombre"];
                $apellidos = $_SESSION["apellidos"];
                $email = $_SESSION["email"];
                $direccion = $_SESSION["direccion"];
                $telefono = $_SESSION["telefono"];
                $clave = $_SESSION["clave"];
                $rol = $_SESSION["rol"];
                $estado = $_SESSION["estado"];
                $imagen = "./img/web/avatar-estandar.png";
                
                $insercion = mysqli_query($db, "INSERT INTO Usuarios (id, nombre, apellidos, email, direccion, 
                telefono, clave, rol, estado, imagen) 
                VALUES ('$id', '$nombre', '$apellidos', '$email', '$direccion', '$telefono', '$clave', '$rol', '$estado', '$imagen')");

                $fecha = date('Y-m-d H:i:s');
                $accion = "El usuario  {$email} ha sido registrado en el sistema.";
                $insercionLog = mysqli_query($db, "INSERT INTO Logs (fecha, accion) VALUES ('$fecha', '$accion')");


        }

        //Actualización de variables de sesión con saneamiento de datos, con los datos que se
        //han introducido en el formulario
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
            }
        }

        $enviadoCorrectamente = false;
        //Si se le ha dado a añadir usuario, y todos los campos son válidos, los datos se han enviado correctamente
        //y se actializarán las variables de sesión para guardar los datos introducidos en el formulario
        if (isset($_POST["add-usuario"]) && validarTodosLosCampos()){
            $enviadoCorrectamente = true;
            actualizarVarSesion();
        }

        $datosConfirmados = false;

        //Si se le ha dado al botón de confirmar, ya se habrá añadido un usuario y por tanto se hará una inserción en la
        //base de datos con la función creada anteriormente
        if (isset($_POST['confirmar-datos'])){?>
            <span class = "confirmacion-datos">Se ha añadido un nuevo usuario.</span>
            <?php
                $datosConfirmados = true;
                insertarEnBD();
        }
        ?>

        <div class="formulario-editar">
            <form action="#" method="POST">
                <div class="foto">
                    <label>Foto:
                        <?php if (!$enviadoCorrectamente && !$datosConfirmados){?>
                            <input type='file' name='subir-imagen' value='Seleccionar archivo' accept=".jpg, .png, .jpeg"/>
                        <?php 
                        //<img name="imagen" src="<?php echo $_SESSION['imagen']; 
                        } else if ($enviadoCorrectamente || $datosConfirmados){?>
                            <img name="imagen" src="./img/web/avatar-estandar.png" 
                            alt="Foto de <?php echo $_SESSION['nombre']; ?>" width="150" height="150"/>
                        <?php 
                        }
                        ?>
                    </label>
                </div>
                
                <?php //si existe el nombre y no está vacío, valdrá lo que hayamos puesto en el formulario.
                //si se han confirmado los datos  o se ha enviado correctamente aparecerá deshabilitado
                //lo mismo se aplicará para el resto de campos.?>
                <label>Nombre:
                    <input type="text" name="nombre" value="<?php echo isset($_POST['nombre']) && !empty($_POST['nombre']) ? $_POST['nombre'] : ""; 
                    if ($datosConfirmados) echo $_SESSION["nombre"]; ?>"
                    <?php if ($enviadoCorrectamente || $datosConfirmados) echo "disabled"; ?>>
                </label>


                <?php
                    if (hayErrores("nombre")){?>
                        <p class='error-formulario'>El nombre no puede estar vacío.</p>
                <?php } ?>

                
                <label>Apellidos:
                    <input type="text" name="apellidos" value="<?php echo isset($_POST['apellidos']) && !empty($_POST['apellidos'])? $_POST['apellidos'] : ""; 
                    if ($datosConfirmados) echo $_SESSION["apellidos"]; ?>"
                    <?php if ($enviadoCorrectamente || $datosConfirmados) echo "disabled"; ?>>
                </label>

                <?php
                    if (hayErrores("apellidos")){?>
                        <p class='error-formulario'>Los apellidos no pueden estar vacíos.</p>
                <?php } ?>


                <label>Email:
                    <input type="email" name="email" value="<?php echo isset($_POST['email']) && !empty($_POST['email'])? $_POST['email'] : ""; 
                    if ($datosConfirmados) echo $_SESSION["email"]; ?>"
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
                    <input type="text" name="direccion" value="<?php echo isset($_POST['direccion']) && !empty($_POST['direccion']) ? $_POST['direccion'] : ""; 
                    if ($datosConfirmados) echo $_SESSION["direccion"]; ?>"
                    <?php if ($enviadoCorrectamente || $datosConfirmados) echo "disabled"; ?>>
                </label>

                <?php
                    if (hayErrores("direccion")){?>
                         <p class='error-formulario'>La dirección no puede estar vacía.</p>
                <?php } ?>

                <label>Teléfono:
                    <input type="text" name="telefono" value="<?php echo isset($_POST['telefono']) && !empty($_POST['telefono'])? $_POST['telefono'] : ""; 
                    if ($datosConfirmados) echo $_SESSION["telefono"]; ?>"
                    <?php if ($enviadoCorrectamente || $datosConfirmados) echo "disabled"; ?>>
                </label>

                <?php
                    if (hayErrores("telefono")){?>
                        <p class='error-formulario'>El teléfono no tiene un formato válido.</p>
                <?php } ?>
                
                <?php //Para el Rol y el Estado, vamos comprobando si se ha enviado o se ha confirmado, para que aparezca deshabilitado, y además seleccionado en función de lo que se elija. ?>
                <label>Rol:
                    <select name='rol' <?php if ($enviadoCorrectamente || $datosConfirmados) echo "disabled"; ?>>
                    <option value='Administrador' <?php if (($_SESSION["rol"] == "Administrador") && ($enviadoCorrectamente || $datosConfirmados)){ echo "selected";} ?>> Administrador </option>
                    <option value='Colaborador' <?php if (!$enviadoCorrectamente && !$datosConfirmados) {echo "selected";} else if (($_SESSION["rol"] == "Colaborador") && ($enviadoCorrectamente || $datosConfirmados)){ echo "selected";}?>> Colaborador </option>
                    </select>
                </label>


                <label>Estado:
                    <select name='estado' <?php if ($enviadoCorrectamente || $datosConfirmados) echo "disabled"; ?>>
                    <option value='Activo' <?php if (($_SESSION["estado"] == "Activo") && ($enviadoCorrectamente || $datosConfirmados)){ echo "selected";} ?>> Activo </option>
                    <option value='Pendiente' <?php if (!$enviadoCorrectamente && !$datosConfirmados) {echo "selected";} else if (($_SESSION["estado"] == "Pendiente") && ($enviadoCorrectamente || $datosConfirmados)){ echo "selected";} ?>> Pendiente </option>
                    </select>
                </label>
                
                <?php
                    //Si no se han enviado los datos ni se han confirmado, significa que aparecerá el botón de añadir usuario
                    if (!$enviadoCorrectamente && !$datosConfirmados){?>
                        <label>
                            <input type='submit' name='add-usuario' value='Añadir usuario'>
                        </label>
                    <?php

                    //Si lo único que ocurre es que no se han confirmado, significará que aún deben confirmarse, por lo que aparecerá
                    //el botón de confirmar datos
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