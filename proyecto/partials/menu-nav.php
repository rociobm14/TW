<?php 
    if (!isset($_SESSION["usuario"])){
        $rol = "Visitante";
    } else {
        $rol = $_SESSION["usuario"]["rol"];
    }
?>

<nav>
    <ul class="<?php echo $rol; ?>">
        <?php //Para cualquier persona (ya sea loggeada o anónima), solo se les ofrecerá una vista de la página ver incidencias ?>
        <li><a href="ver-incidencias.php" class="<?php echo isActivePage('ver-incidencias.php'); ?>">Ver incidencias</a></li>
        <?php
        
            //Una vez iniciada la sesión, a cualquier usuario se le mostrará, además de lo anterior, la página de crear una incidencia y la de 
            //ver sus incidencias
        if (isset($_SESSION["usuario"])){
            ?>
            <li><a href="crear-incidencia.php" class="<?php echo isActivePage('crear-incidencia.php'); ?>">Nueva incidencia</a></li>
            <li><a href="ver-mis-incidencias.php" class="<?php echo isActivePage('ver-mis-incidencias.php'); ?>">Mis incidencias</a></li>
            <?php

            //Pero en caso de que dicho usuario sea un Administrador, además de lo anterior se le mostrará la página de gestionar los usuarios,
            //la de ver los distintos eventos que ocurren en la página y la de la gestión de la base de datos
            if (strcmp($_SESSION["usuario"]["rol"], "Administrador") == 0){
                ?>
                <li><a href="gestion-usuarios.php" class="<?php echo isActivePage('gestion-usuarios.php'); ?>">Gestión de usuarios</a></li>
                <li><a href="ver-log.php" class="<?php echo isActivePage('ver-log.php'); ?>">Ver log</a></li>
                <li><a href="gestion-bbdd.php" class="<?php echo isActivePage('gestion-bbdd.php'); ?>">Gestión de BBDD</a></li>
                <?php
            }
        }
        ?>
    </ul>
</nav>

<?php
function isActivePage($pageName) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    if ($currentPage === $pageName) {
        return 'active';
    } else {
        return '';
    }
}
?>

