<?php
    //Comprueba si el usuario está identificado
    if (!isset($_SESSION["usuario"])){
        header("Location:index.php");
        exit;
    }
?>