<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar datos del formulario
    $id_consulta = $_POST['id_consulta'];
    $id_paciente = $_POST['id_paciente']; // Para la redirección
    $tipo_graduacion = $_POST['tipo_graduacion'];
    $od_esfera = $_POST['od_esfera'] ?? null;
    $od_cilindro = !empty($_POST['od_cilindro']) ? $_POST['od_cilindro'] : 0;
    $od_eje = !empty($_POST['od_eje']) ? $_POST['od_eje'] : 0;
    $od_add = !empty($_POST['od_add']) ? $_POST['od_add'] : 0;
    $oi_esfera = $_POST['oi_esfera'] ?? null;
    $oi_cilindro = !empty($_POST['oi_cilindro']) ? $_POST['oi_cilindro'] : 0;
    $oi_eje = !empty($_POST['oi_eje']) ? $_POST['oi_eje'] : 0;
    $oi_add = !empty($_POST['oi_add']) ? $_POST['oi_add'] : 0;
    
    // Preparar y ejecutar la consulta SQL
    $sql = "INSERT INTO graduaciones (id_consulta, tipo_graduacion, od_esfera, od_cilindro, od_eje, od_add, oi_esfera, oi_cilindro, oi_eje, oi_add) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        $id_consulta, $tipo_graduacion, $od_esfera, $od_cilindro, $od_eje, $od_add, $oi_esfera, $oi_cilindro, $oi_eje, $oi_add
    ]);

    // Redirigir de vuelta a la página de detalle de la consulta
    header("Location: /consulta_detalle.php?id_consulta=" . $id_consulta . "&id_paciente=" . $id_paciente . "&status=success");
    exit();
}