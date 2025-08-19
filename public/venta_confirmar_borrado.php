<?php
require_once '../src/database.php';
$id_venta = $_GET['id'] ?? null;
if (!$id_venta) {
    header('Location: /ventas.php');
    exit();
}

$stmt = $pdo->prepare("SELECT numero_nota FROM ventas WHERE id_venta = ?");
$stmt->execute([$id_venta]);
$venta = $stmt->fetch();

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="confirmacion-container">
        <h2>Confirmar Eliminación</h2>
        <p>¿Estás seguro de que deseas eliminar la venta con número de nota <strong>#<?php echo htmlspecialchars($venta['numero_nota']); ?></strong>?</p>
        <p class="alerta-permanente">Esta acción es permanente y no se puede deshacer.</p>
        
        <div class="confirmacion-acciones">
            <form action="/venta_borrar.php" method="POST">
                <input type="hidden" name="id_venta" value="<?php echo $id_venta; ?>">
                <button type="submit" class="boton boton--peligro">Sí, Eliminar</button>
            </form>
            <a href="/venta_detalle.php?id=<?php echo $id_venta; ?>" class="boton boton--secundario">Cancelar</a>
        </div>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>