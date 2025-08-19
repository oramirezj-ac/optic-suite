<?php
require_once '../src/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_venta = $_POST['id_venta'];

    // 1. Contar cuántos abonos tiene esta venta
    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM abonos WHERE id_venta = ?");
    $stmt_count->execute([$id_venta]);
    $numero_de_abonos = $stmt_count->fetchColumn();

    // 2. Decidir si borrar o mostrar error
    if ($numero_de_abonos > 0) {
        // Si hay abonos, redirigir a una página de error
        header("Location: /venta_error_borrado.php?id_venta=" . $id_venta);
        exit();
    } else {
        // Si no hay abonos, proceder con el borrado
        $stmt_delete = $pdo->prepare("DELETE FROM ventas WHERE id_venta = ?");
        $stmt_delete->execute([$id_venta]);

        header("Location: /ventas.php?status=deleted");
        exit();
    }
}