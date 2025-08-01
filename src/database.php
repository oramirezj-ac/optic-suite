<?php
// Define las credenciales de la base de datos
$db_host = 'localhost';
$db_name = 'optica_san_gabriel_db';
$db_user = 'admin_osg';
$db_pass = 'admin_osg';

try {
    // Crea una nueva conexión a la base de datos usando PDO
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);

    // Configura PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Si la conexión falla, muestra un mensaje de error y termina el script
    die("Error de conexión a la base de datos: " . $e->getMessage());
}