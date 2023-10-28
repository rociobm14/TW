<?php
    //Conexión a la BBDD

    $db = mysqli_connect("localhost", "gonzalorocio2223", "W5A7ruon", "gonzalorocio2223");

    if (!$db){?>
        <p>Error en la conexión: no se pudo conectar a la base de datos.</p>
        <p>Código de error: " <?php. mysqli_connect_errorno();?></p>
    <?php
    }
?>