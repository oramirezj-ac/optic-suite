<?php
require_once '../src/database.php';

// Obtener el ID de la venta para asociar la orden
$id_venta = $_GET['id_venta'] ?? null;
if (!$id_venta) {
    header('Location: /ventas.php');
    exit();
}

// --- NUEVO: Buscar el número de nota de la venta ---
$stmt_venta = $pdo->prepare("SELECT numero_nota FROM ventas WHERE id_venta = ?");
$stmt_venta->execute([$id_venta]);
$venta = $stmt_venta->fetch();
$numero_nota = $venta['numero_nota'] ?? $id_venta; // Muestra el # de nota, o el ID si no lo encuentra

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="pagina-header">
        <h2>Registrar Nueva Orden de Laboratorio</h2>
        <a href="/venta_detalle.php?id=<?php echo htmlspecialchars($id_venta); ?>" class="boton boton--secundario boton--md">&larr; Regresar a la Venta</a>
    </div>
    <p class="subtitulo-pagina">Asociada a la Nota de Venta #<strong><?php echo htmlspecialchars($numero_nota); ?></strong></p>
    
    <div class="formulario-container">
        <form action="/orden_crear.php" method="POST" class="formulario-grid">
            <input type="hidden" name="id_venta" value="<?php echo htmlspecialchars($id_venta); ?>">

            <div class="campo">
                <label for="nombre_laboratorio">Laboratorio:</label>
                <input type="text" id="nombre_laboratorio" name="nombre_laboratorio" required>
            </div>

            <div class="campo">
                <label for="numero_orden_externa"># Orden Externa:</label>
                <input type="text" id="numero_orden_externa" name="numero_orden_externa">
            </div>

            <div class="campo">
                <label for="fecha_envio">Fecha de Envío:</label>
                <input type="date" id="fecha_envio" name="fecha_envio" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="campo">
                <label for="costo_laboratorio">Costo de Laboratorio:</label>
                <input type="number" step="0.01" id="costo_laboratorio" name="costo_laboratorio">
            </div>

            <div class="campo">
                <label for="estado_orden">Estado:</label>
                <select id="estado_orden" name="estado_orden">
                    <option value="Enviada">Enviada</option>
                    <option value="Recibida">Recibida</option>
                    <option value="Entregada al Cliente">Entregada al Cliente</option>
                </select>
            </div>

            <div class="formulario-acciones campo-full-width">
                <button type="submit" class="boton boton--primario">Guardar Orden</button>
                <a href="/venta_detalle.php?id=<?php echo htmlspecialchars($id_venta); ?>" class="boton boton--secundario">Cancelar</a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>