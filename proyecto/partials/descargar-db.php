<?php
//Comenzamos con la configuración de la base de datos
$host = 'localhost';
$user = 'gonzalorocio2223';
$password = 'W5A7ruon';
$database = 'gonzalorocio2223';

//Nos conectamos a la base de datos
$db = mysqli_connect($host, $user, $password, $database);

//Verificamos si hay algún error de conexión
if (mysqli_connect_errno()) {
    echo 'Error al conectar con la base de datos: ' . mysqli_connect_error();
    exit;
}

//Obtenemos el nombre de todas las tablas en la base de datos
$tables = array();
$result = mysqli_query($db, 'SHOW TABLES');
while ($row = mysqli_fetch_row($result)) {
    $tables[] = $row[0];
}

//Creamos un archivo temporal para almacenar todo el contenido de la BBDD
$tempFile = tempnam(sys_get_temp_dir(), 'Backup-BBDD');
$fileHandle = fopen($tempFile, 'w');

//Obtenemos las sentencias SQL para borrar y crear todas las tablas
foreach ($tables as $table) {
    //Borramos la tabla
    $dropTableSQL = "DROP TABLE `" . mysqli_real_escape_string($db, $table) . "`;";
    fwrite($fileHandle, $dropTableSQL . "\n\n");

    //Creamos la tabla
    $createTableSQL = '';
    $result = mysqli_query($db, "SHOW CREATE TABLE `" . mysqli_real_escape_string($db, $table) . "`");
    if ($row = mysqli_fetch_row($result)) {
        $createTableSQL = $row[1] . ";";
    }
    fwrite($fileHandle, $createTableSQL . "\n\n");
}

//Recorremos todas las tablas y generamos las consultas para exportar su contenido
foreach ($tables as $table) {
    $query = "SELECT * FROM `" . mysqli_real_escape_string($db, $table) . "`";
    $result = mysqli_query($db, $query);

    //Vamos escribiendo las tablas en el archivo
    while ($row = mysqli_fetch_assoc($result)) {
        $values = array_map(function ($value) use ($db) {
            return "'" . mysqli_real_escape_string($db, $value) . "'";
        }, $row);
        $sql = "INSERT INTO `" . mysqli_real_escape_string($db, $table) . "` (`" . implode('`, `', array_keys($row)) . "`) VALUES (" . implode(", ", $values) . ");";
        fwrite($fileHandle, $sql . "\n");
    }

    //Separamos cada tabla
    fwrite($fileHandle, "\n");
}

// Cerrar el archivo
fclose($fileHandle);

// Configuración de la descarga del archivo
$filename = 'Backup-BBDD';
$fileSize = filesize($tempFile);

// Enviar las cabeceras para descargar el archivo
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . $fileSize);
header('Pragma: no-cache');
header('Expires: 0');

    $fecha = date('Y-m-d H:i:s');
    $accion = "Un administrador ha descargado una copia de la base de datos.";
    $insercionLog = mysqli_query($db, "INSERT INTO Logs (fecha, accion) VALUES ('$fecha', '$accion')");

// Leer el contenido del archivo temporal y enviarlo al cliente
readfile($tempFile);

// Eliminar el archivo temporal
unlink($tempFile);

// Cerrar la conexión a la base de datos
mysqli_close($db);
?>