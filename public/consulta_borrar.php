<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_consulta = $_POST['id_consulta'];
    $id_paciente = $_POST['id_paciente'];

    $stmt = $pdo->prepare("DELETE FROM consultas WHERE id_consulta = ?");
    $stmt->execute([$id_consulta]);

    header("Location: /consulta_historial.php?id=" . $id_paciente . "&status=deleted");
    exit();
}