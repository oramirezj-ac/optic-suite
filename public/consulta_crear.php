<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar los datos del formulario (con los nuevos campos AV)
    $id_paciente = $_POST['id_paciente'];
    $fecha_consulta = $_POST['fecha_consulta'];
    $av_ao = $_POST['av_ao'] ?? '';
    $av_od = $_POST['av_od'] ?? '';
    $av_oi = $_POST['av_oi'] ?? '';
    $motivo_consulta = $_POST['motivo_consulta'] ?? '';
    $notas_adicionales = $_POST['notas_adicionales'] ?? '';

    // Preparar y ejecutar la consulta SQL actualizada
    $sql = "INSERT INTO consultas (id_paciente, fecha_consulta, av_ao, av_od, av_oi, motivo_consulta, notas_adicionales) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        $id_paciente, 
        $fecha_consulta, 
        $av_ao,
        $av_od,
        $av_oi, 
        $motivo_consulta, 
        $notas_adicionales
    ]);

    // Redirigir de vuelta al historial del paciente
    header("Location: /consulta_historial.php?id=" . $id_paciente . "&status=success");
    exit();
}