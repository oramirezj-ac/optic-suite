<?php
require_once '../src/database.php';
$id_orden = $_GET['id'] ?? null;
if (!$id_orden) {
    header('Location: /ventas.php');
    exit();
}

// Buscar datos para la redirección
$stmt = $pdo->prepare("SELECT id_venta FROM ordenes_laboratorio WHERE id_orden_lab = ?");
$stmt->execute([$id_orden]);
$orden = $stmt->fetch();
$id_venta = $orden['id_venta'];

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="confirmacion-container">
        <h2>Confirmar Eliminación</h2>
        <p>¿Estás seguro de que deseas eliminar esta orden de laboratorio?</p>
        <p class="alerta-permanente">Esta acción es permanente.</p>
        <div class="confirmacion-acciones">
            <form action="/orden_borrar.php" method="POST">
                <input type="hidden" name="id_orden" value="<?php echo $id_orden; ?>">
                <input type="hidden" name="id_venta" value="<?php echo $id_venta; ?>">
                <button type="submit" class="boton boton--peligro">Sí, Eliminar</button>
            </form>
            <a href="/venta_detalle.php?id=<?php echo $id_venta; ?>" class="boton boton--secundario">Cancelar</a>
        </div>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>