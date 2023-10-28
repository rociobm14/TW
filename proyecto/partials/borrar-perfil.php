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
            <h4>Borrar usuario</h4>
        </div>

    <?php

        // Verificar si se ha proporcionado el parámetro de ID  de usuario en la URL
        if (isset($_GET['id'])) {
            $idUsuario = $_GET['id'];
        } else {?>
            <p class='error-formulario'>ERROR: No se pudo extraer el usuario a 
                editar de la BBDD.</p>
        <?php }

        function borrar(){
            global $db;
            global $idUsuario;
              // Ejecutar la consulta DELETE
            $query = mysqli_query($db, "DELETE FROM Usuarios WHERE ID = '" . mysqli_real_escape_string($db, $idUsuario) . "'");
            
            if (!($query && mysqli_affected_rows($db) > 0)) {
                // La consulta falló o no se eliminó ninguna fila?>
                <p class='error-formulario'>ERROR: No se pudo borrar el usuario de la BBDD.</p> <?php
            }

            $fecha = date('Y-m-d H:i:s');
            $accion = "El usuario  {$_SESSION['usuario']['email']} ha borrado el perfil de {$_SESSION["datosUsuarioABorrar"]["email"]}.";
            $insercionLog = mysqli_query($db, "INSERT INTO Logs (fecha, accion) VALUES ('$fecha', '$accion')");
        }

        $enviadoCorrectamente = false;
        if (isset($_POST["borrar-usuario"])){
            $enviadoCorrectamente = true;
        }

        $datosConfirmados = false;

        if (isset($_POST['confirmar-borrado'])){?>
            <span class = "confirmacion-datos">Se han borrado los datos del usuario.</span>
            <?php
                $datosConfirmados = true;
                borrar();
            ?>
        <?php 
            }

        
        //Hacemos una consulta en la tabla de Usuarios que tenga el mismo ID
        $datosUsuarioABorrarDB = mysqli_query($db, "SELECT * FROM Usuarios WHERE id='" . mysqli_real_escape_string($db, $idUsuario) . "'");

        $datosUsuarioABorrar = mysqli_fetch_assoc($datosUsuarioABorrarDB);
        
        //Si se ha encontrado, se guardarán los datos del usuario en una variable de sesión, que la usaremos para obtener los datos en el formulario de la BBDD
        if ($datosUsuarioABorrarDB && mysqli_num_rows($datosUsuarioABorrarDB) > 0){
                $_SESSION["datosUsuarioABorrar"] = $datosUsuarioABorrar;
        }

        ?>

        <div class="formulario-editar">
            <form action="#" method="POST">
                <div class="foto">
                    <label>Foto:
                        <img name ="imagen" src='<?php echo $_SESSION["datosUsuarioABorrar"]["imagen"];?>' alt='Foto de <?php echo 
                        $_SESSION["datosUsuarioABorrar"]["nombre"]?>' width="150" height="150" disabled>
                    </label>
                </div>

                <label>Nombre:
                    <input type="text" name="nombre" value="<?php echo $_SESSION["datosUsuarioABorrar"]['nombre']; ?>" disabled/>
                </label>

                
                <label>Apellidos:
                    <input type="text" name="apellidos" value="<?php echo $_SESSION["datosUsuarioABorrar"]['apellidos']; ?>" disabled/>
                </label>


                <label>Email:
                    <input type="email" name="email" value="<?php echo $_SESSION["datosUsuarioABorrar"]['email']; ?>" disabled/>
                </label>
                  
                <label>Dirección:
                    <input type="text" name="direccion" value="<?php echo $_SESSION['datosUsuarioABorrar']['direccion']; ?>" disabled/>
                </label>

                <label>Teléfono:
                    <input type="text" name="telefono" value="<?php echo $_SESSION["datosUsuarioABorrar"]['telefono']; ?>" disabled/>
                </label>

                <label>Rol:
                    <input type='text' name='rol' value='<?php echo $_SESSION["datosUsuarioABorrar"]["rol"];?>' disabled/>
                </label>

                <label>Estado:
                    <input type='text' name='estado' value='<?php echo $_SESSION["datosUsuarioABorrar"]["estado"];?>' disabled/>
                </label>
                
                <?php
                    $soyAdmin = false;

                    //Condición para que un administrador no pueda borrarse a sí mismo
                    if ($_SESSION["usuario"]["id"] == $_SESSION["datosUsuarioABorrar"]["id"]){
                        $soyAdmin = true;?>
                        <p class='error-formulario'>ERROR: No te puedes borrar a ti mismo.</p>
                    <?php }
                
                    if (!$enviadoCorrectamente && !$datosConfirmados && !$soyAdmin){?>
                        <label>
                            <input type='submit' name='borrar-usuario' value='Borrar usuario'>
                        </label>
                    <?php
                    } else if (!$datosConfirmados && !$soyAdmin){
                        ?>
                        <label>
                            <input type='submit' name='confirmar-borrado' value='Confirmar borrado'/>
                        </label>
                    <?php 
                    } 
                    ?>
                
            </form>
        </div>
    </main>
</body>
</html>