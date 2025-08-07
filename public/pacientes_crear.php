<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombres = $_POST['nombres'] ?? '';
    $apellido_paterno = $_POST['apellido_paterno'] ?? '';
    $apellido_materno = $_POST['apellido_materno'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? null;
    $telefono = $_POST['telefono'] ?? '';
    $domicilio = $_POST['domicilio'] ?? '';
    $email = $_POST['email'] ?? '';
    $antecedentes_descripcion = $_POST['antecedentes_descripcion'] ?? '';
    $dp_total = $_POST['dp_total'] ?? '';
    $dnp_od = $_POST['dnp_od'] ?? '';
    $dnp_oi = $_POST['dnp_oi'] ?? '';

    $sql = "INSERT INTO pacientes (nombres, apellido_paterno, apellido_materno, fecha_nacimiento, telefono, domicilio, email, antecedentes_descripcion, dp_total, dnp_od, dnp_oi) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        $nombres, 
        $apellido_paterno, 
        $apellido_materno, 
        $fecha_nacimiento, 
        $telefono, 
        $domicilio, 
        $email, 
        $antecedentes_descripcion,
        $dp_total,
        $dnp_od,
        $dnp_oi
    ]);

    // Obtenemos el ID del paciente recién creado
    $id_nuevo_paciente = $pdo->lastInsertId();

    // Redirigimos a la página de éxito, pasando el nuevo ID
    header("Location: /pacientes_exito.php?id=" . $id_nuevo_paciente);
    exit();
}