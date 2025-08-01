<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar todos los datos del formulario de edición
    $id_graduacion = $_POST['id_graduacion'];
    $id_consulta = $_POST['id_consulta'];
    $tipo_graduacion = $_POST['tipo_graduacion'];
    $od_esfera = $_POST['od_esfera'] ?? null;
    $od_cilindro = $_POST['od_cilindro'] ?? null;
    $od_eje = $_POST['od_eje'] ?? null;
    $od_add = !empty($_POST['od_add']) ? $_POST['od_add'] : 0;
    $oi_esfera = $_POST['oi_esfera'] ?? null;
    $oi_cilindro = $_POST['oi_cilindro'] ?? null;
    $oi_eje = $_POST['oi_eje'] ?? null;
    $oi_add = !empty($_POST['oi_add']) ? $_POST['oi_add'] : 0;
    
    // Consulta SQL de actualización completa
    $sql = "UPDATE graduaciones SET 
                tipo_graduacion = ?, 
                od_esfera = ?, 
                od_cilindro = ?, 
                od_eje = ?, 
                od_add = ?, 
                oi_esfera = ?, 
                oi_cilindro = ?, 
                oi_eje = ?, 
                oi_add = ?
            WHERE id_graduacion = ?";
    
    $stmt = $pdo->prepare($sql);
    
    // Ejecutar la consulta con todas las variables en el orden correcto
    $stmt->execute([
        $tipo_graduacion, 
        $od_esfera, 
        $od_cilindro, 
        $od_eje, 
        $od_add, 
        $oi_esfera, 
        $oi_cilindro, 
        $oi_eje, 
        $oi_add,
        $id_graduacion
    ]);
    
    // Obtener el id_paciente para poder redirigir correctamente
    $stmt_paciente = $pdo->prepare("SELECT id_paciente FROM consultas WHERE id_consulta = ?");
    $stmt_paciente->execute([$id_consulta]);
    $paciente = $stmt_paciente->fetch();
    $id_paciente = $paciente['id_paciente'];

    // Redirigir de vuelta a la página de detalle con un mensaje de éxito
    header("Location: /consulta_detalle.php?id_consulta=" . $id_consulta . "&id_paciente=" . $id_paciente . "&status=updated");
    exit();
}