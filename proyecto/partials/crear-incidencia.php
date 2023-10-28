<?php
    function campoValido($campo){
        return isset($_POST["$campo"]) && !empty($_POST["$campo"]);
    }

    function hayErrores($campo){
        return isset($_POST['enviar-incidencia']) && !campoValido($campo);
    }

    //Función que comprueba si el campo es válido, y en ese caso, será válido el valor que hay en el formulario
    function compruebaCampo($campo){
        echo campoValido("$campo") ? $_POST["$campo"] : '';
    }

    function validarTodosLosCampos(){
        $titulo = campoValido("titulo");
        $descripcion = campoValido("descripcion");
        $lugar = campoValido("lugar");
        $palabrasclave = campoValido("palabras-clave");
        
        return $titulo && $descripcion && $lugar
        && $palabrasclave;
    }

    //Actualización de variables de sesión con saneamiento de datos, con los valores introducidos en el formulario
    function actualizarVarSesion(){
        $_SESSION['titulo'] = htmlentities(strip_tags($_POST['titulo']));
        $_SESSION['lugar'] = htmlentities(strip_tags($_POST['lugar']));
        $_SESSION['descripcion'] = htmlentities(strip_tags($_POST['descripcion']));
        $_SESSION['palabrasClave'] = htmlentities(strip_tags($_POST['palabras-clave']));
    }

    $enviadoCorrectamente = false;
    if (isset($_POST["enviar-incidencia"]) && validarTodosLosCampos()){
        $enviadoCorrectamente = true;
        actualizarVarSesion();
    }

    $datosConfirmados = false;
    if (isset($_POST["confirmar-datos"])){
        global $db;
        $idDB = mysqli_query($db, "SELECT MAX(id) AS ultimoID FROM Incidencias");
        if ($idDB) {
            $id = mysqli_fetch_assoc($idDB);

            $ultimoID = $id['ultimoID'];
            $id = $ultimoID+1;
        }

        $titulo = $_SESSION['titulo'];
        $lugar = $_SESSION['lugar'];
        $fecha = date('Y-m-d H:i:s');
        $palabrasclave = $_SESSION["palabrasClave"];
        $estado = "Pendiente";
        $descripcion = $_SESSION['descripcion'];
        $idUsuario = $_SESSION["usuario"]["id"];

        //Insertamos los datos en la BBDD
        $insercion = mysqli_query($db, "INSERT INTO Incidencias (titulo, lugar, fecha, palabrasClave, 
        estado, positivas, negativas, descripcion, id, idUsuario) 
        VALUES ('$titulo', '$lugar', '$fecha', '$palabrasclave', '$estado', '0', '0', '$descripcion', '$id', '$idUsuario')");
        $datosConfirmados = true;

        $fecha = date('Y-m-d H:i:s');
        $accion = "El usuario  {$_SESSION['usuario']['email']} ha creado una nueva incidencia.";
        $insercionLog = mysqli_query($db, "INSERT INTO Logs (fecha, accion) VALUES ('$fecha', '$accion')");

        header("Location: editar-incidencia.php?id=$id");
    }
?>

<form action="#" method="POST">
    <div class="formulario-incidencia">
        <h4>Datos principales:</h4>
        <label>
            Título:
            <input type="text" name="titulo" value="<?php compruebaCampo("titulo"); ?>"
            <?php echo $enviadoCorrectamente ? "disabled" : ""?>>
        </label>

        <?php
            if (hayErrores("titulo")){?>
                <p class='error-formulario'>El título no puede estar vacío.</p>
        <?php } ?>
        
        <label>
            Descripción:
            <textarea <?php echo $enviadoCorrectamente ? "disabled" : ""?> name="descripcion" 
            rows="10" cols="50"><?php compruebaCampo("descripcion");?></textarea>
        </label>


        <?php
            if (hayErrores("descripcion")){?>
                <p class='error-formulario'>La descripción no puede estar vacía.</p>
        <?php } ?>

        <label>
            Lugar:
            <input type="text" name="lugar" value="<?php compruebaCampo("lugar"); ?>"
            <?php echo $enviadoCorrectamente ? "disabled" : ""?>>
        </label>

        <?php
            if (hayErrores("lugar")){?>
                <p class='error-formulario'>El lugar no puede estar vacío.</p>
        <?php } ?>
        
        <label>
            Palabras clave:
            <input type="text" name="palabras-clave" value="<?php compruebaCampo("palabras-clave"); ?>"
            <?php echo $enviadoCorrectamente ? "disabled" : ""?>>
        </label>

        <?php
            if (hayErrores("palabras-clave")){?>
                <p class='error-formulario'>Las palabras clave no pueden estar vacías.</p>
        <?php } 
        
        if (!$enviadoCorrectamente && !$datosConfirmados){?>
            <div class="boton-crear-incidencia">
                <input type="submit" value="Enviar datos" name="enviar-incidencia"/>
            </div>
        <?php
        } else if (!$datosConfirmados){
            ?>
            <label>
                <input type='submit' name='confirmar-datos' value='Confirmar datos'/>
            </label>
        <?php 
        } 
        ?>
        
        
    </div>
</form>