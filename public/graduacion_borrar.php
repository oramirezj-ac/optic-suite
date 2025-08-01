<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_graduacion = $_POST['id_graduacion'];
    // Se reciben los IDs para poder redirigir de vuelta a la página correcta
    $id_consulta = $_POST['id_consulta'];
    $id_paciente = $_POST['id_paciente'];

    $stmt = $pdo->prepare("DELETE FROM graduaciones WHERE id_graduacion = ?");
    $stmt->execute([$id_graduacion]);

    header("Location: /consulta_detalle.php?id_consulta=" . $id_consulta . "&id_paciente=" . $id_paciente);
    exit();
}