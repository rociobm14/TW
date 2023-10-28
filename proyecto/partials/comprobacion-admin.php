<?php
    //Comprueba si el usuario está identificado como Administrador
    if (strcmp($_SESSION["usuario"]["rol"], "Administrador") != 0){
        header("Location: index.php");
        exit;
    }
?>