<?php
require_once '../src/database.php';
$id_orden = $_GET['id'] ?? null;
if (!$id_orden) {
    header('Location: /ventas.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM ordenes_laboratorio WHERE id_orden_lab = ?");
$stmt->execute([$id_orden]);
$orden = $stmt->fetch();

if (!$orden) {
    header('Location: /ventas.php');
    exit();
}

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <h2>Editar Orden de Laboratorio</h2>
    <div class="formulario-container">
        <form action="/orden_actualizar.php" method="POST" class="formulario-grid">
            <input type="hidden" name="id_orden" value="<?php echo htmlspecialchars($orden['id_orden_lab']); ?>">
            <input type="hidden" name="id_venta" value="<?php echo htmlspecialchars($orden['id_venta']); ?>">

            <div class="campo">
                <label for="nombre_laboratorio">Laboratorio:</label>
                <input type="text" id="nombre_laboratorio" name="nombre_laboratorio" value="<?php echo htmlspecialchars($orden['nombre_laboratorio']); ?>" required>
            </div>

            <div class="campo">
                <label for="numero_orden_externa"># Orden Externa:</label>
                <input type="text" id="numero_orden_externa" name="numero_orden_externa" value="<?php echo htmlspecialchars($orden['numero_orden_externa']); ?>">
            </div>

            <div class="campo">
                <label for="fecha_envio">Fecha de Envío:</label>
                <input type="date" id="fecha_envio" name="fecha_envio" value="<?php echo htmlspecialchars($orden['fecha_envio']); ?>" required>
            </div>

             <div class="campo">
                <label for="fecha_recepcion">Fecha de Recepción:</label>
                <input type="date" id="fecha_recepcion" name="fecha_recepcion" value="<?php echo htmlspecialchars($orden['fecha_recepcion']); ?>">
            </div>
            
            <div class="campo">
                <label for="costo_laboratorio">Costo de Laboratorio:</label>
                <input type="number" step="0.01" id="costo_laboratorio" name="costo_laboratorio" value="<?php echo htmlspecialchars($orden['costo_laboratorio']); ?>">
            </div>

            <div class="campo">
                <label for="estado_orden">Estado:</label>
                <select id="estado_orden" name="estado_orden">
                    <option value="Enviada" <?php echo ($orden['estado_orden'] == 'Enviada') ? 'selected' : ''; ?>>Enviada</option>
                    <option value="Recibida" <?php echo ($orden['estado_orden'] == 'Recibida') ? 'selected' : ''; ?>>Recibida</option>
                    <option value="Entregada al Cliente" <?php echo ($orden['estado_orden'] == 'Entregada al Cliente') ? 'selected' : ''; ?>>Entregada al Cliente</option>
                </select>
            </div>

            <div class="formulario-acciones campo-full-width">
                <button type="submit" class="boton boton--primario">Actualizar Orden</button>
                <a href="/venta_detalle.php?id=<?php echo htmlspecialchars($orden['id_venta']); ?>" class="boton boton--secundario">Cancelar</a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>