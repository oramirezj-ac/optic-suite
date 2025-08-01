<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_paciente'];

    $stmt = $pdo->prepare("DELETE FROM pacientes WHERE id_paciente = ?");
    $stmt->execute([$id]);

    header("Location: /pacientes.php?status=deleted");
    exit();
}