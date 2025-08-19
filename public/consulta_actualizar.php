<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_consulta = $_POST['id_consulta'];
    $id_paciente = $_POST['id_paciente'];
    $fecha_consulta = $_POST['fecha_consulta'];
    $av_ao = $_POST['av_ao'] ?? '';
    $av_od = $_POST['av_od'] ?? '';
    $av_oi = $_POST['av_oi'] ?? '';
    $motivo_consulta = $_POST['motivo_consulta'] ?? '';
    $notas_adicionales = $_POST['notas_adicionales'] ?? '';

    $sql = "UPDATE consultas SET 
                fecha_consulta = ?, 
                av_ao = ?, 
                av_od = ?, 
                av_oi = ?,  
                motivo_consulta = ?, 
                notas_adicionales = ?
            WHERE id_consulta = ?";
            
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        $fecha_consulta,
        $av_ao,
        $av_od,
        $av_oi,
        $motivo_consulta,
        $notas_adicionales,
        $id_consulta
    ]);

    header("Location: /consulta_detalle.php?id_consulta=" . $id_consulta . "&id_paciente=" . $id_paciente . "&status=updated");
    exit();
}