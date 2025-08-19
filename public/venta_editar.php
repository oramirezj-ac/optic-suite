<?php
require_once '../src/database.php';
$id_venta = $_GET['id'] ?? null;
if (!$id_venta) {
    header('Location: /ventas.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM ventas WHERE id_venta = ?");
$stmt->execute([$id_venta]);
$venta = $stmt->fetch();

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <h2>Editar Venta #<?php echo htmlspecialchars($venta['numero_nota']); ?></h2>
    <div class="formulario-container">
        <form action="/venta_actualizar.php" method="POST" class="formulario-grid">
            <input type="hidden" name="id_venta" value="<?php echo htmlspecialchars($venta['id_venta']); ?>">
            <input type="hidden" name="id_paciente" value="<?php echo htmlspecialchars($venta['id_paciente']); ?>">

            <div class="campo">
                <label for="numero_nota">Número de Nota:</label>
                <input type="text" id="numero_nota" name="numero_nota" value="<?php echo htmlspecialchars($venta['numero_nota']); ?>" required>
            </div>

            <div class="campo">
                <label for="fecha_venta">Fecha de Venta:</label>
                <input type="date" id="fecha_venta" name="fecha_venta" value="<?php echo htmlspecialchars($venta['fecha_venta']); ?>" required>
            </div>
            
            <div class="campo">
                <label for="costo_total">Monto Total:</label>
                <input type="number" step="0.01" id="costo_total" name="costo_total" value="<?php echo htmlspecialchars($venta['costo_total']); ?>" required>
            </div>

            <div class="campo campo-full-width">
                <label for="observaciones_venta">Descripción / Observaciones:</label>
                <textarea id="observaciones_venta" name="observaciones_venta" rows="3"><?php echo htmlspecialchars($venta['observaciones_venta']); ?></textarea>
            </div>

            <div class="campo">
                <label for="anticipo">Anticipo:</label>
                <input type="number" step="0.01" id="anticipo" name="anticipo" value="<?php echo htmlspecialchars($venta['anticipo']); ?>">
            </div>

            <div class="campo">
                <label for="estado_pago">Estado del Pago:</label>
                <select id="estado_pago" name="estado_pago">
                    <option value="Pagado" <?php echo ($venta['estado_pago'] == 'Pagado') ? 'selected' : ''; ?>>Pagado</option>
                    <option value="Pendiente" <?php echo ($venta['estado_pago'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="Abonos" <?php echo ($venta['estado_pago'] == 'Abonos') ? 'selected' : ''; ?>>Abonos</option>
                </select>
            </div>

            <div class="formulario-acciones campo-full-width">
                <button type="submit" class="boton boton--primario">Actualizar Venta</button>
                <a href="/venta_detalle.php?id=<?php echo htmlspecialchars($venta['id_venta']); ?>" class="boton boton--secundario">Cancelar</a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>