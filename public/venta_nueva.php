<?php
require_once '../src/database.php';

// Obtener el ID del paciente para asociar la venta
$id_paciente = $_GET['id_paciente'] ?? null;
if (!$id_paciente) {
    header('Location: /pacientes.php');
    exit();
}

// Buscar el nombre del paciente para mostrarlo
$stmt_paciente = $pdo->prepare("SELECT nombres, apellido_paterno, apellido_materno FROM pacientes WHERE id_paciente = ?");
$stmt_paciente->execute([$id_paciente]);
$paciente = $stmt_paciente->fetch();
$nombre_completo = trim($paciente['nombres'] . ' ' . $paciente['apellido_paterno'] . ' ' . $paciente['apellido_materno']);

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <h2>Registrar Nueva Venta</h2>
    <p class="subtitulo-pagina">Paciente: <strong><?php echo htmlspecialchars($nombre_completo); ?></strong></p>
    
    <div class="formulario-container">
        <form action="/venta_crear.php" method="POST" class="formulario-grid">
            <input type="hidden" name="id_paciente" value="<?php echo htmlspecialchars($id_paciente); ?>">

            <div class="campo">
                <label for="numero_nota">Número de Nota:</label>
                <input type="text" id="numero_nota" name="numero_nota" required>
            </div>

            <div class="campo">
                <label for="fecha_venta">Fecha de Venta:</label>
                <input type="date" id="fecha_venta" name="fecha_venta" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="campo">
                <label for="costo_total">Monto Total:</label>
                <input type="number" step="0.01" id="costo_total" name="costo_total" required>
            </div>

            <div class="campo campo-full-width">
                <label for="observaciones_venta">Descripción / Observaciones:</label>
                <textarea id="observaciones_venta" name="observaciones_venta" rows="3" placeholder="Ej: Armazón Ray-Ban + Micas Policarbonato AR"></textarea>
            </div>

            <div class="campo">
                <label for="anticipo">Anticipo:</label>
                <input type="number" step="0.01" id="anticipo" name="anticipo">
            </div>

            <div class="campo">
                <label for="estado_pago">Estado del Pago:</label>
                <select id="estado_pago" name="estado_pago">
                    <option value="Pagado">Pagado</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="Abonos">Abonos</option>
                </select>
            </div>

            <div class="formulario-acciones campo-full-width">
                <button type="submit" class="boton boton--primario">Guardar Venta</button>
                <a href="/paciente_detalle.php?id=<?php echo htmlspecialchars($id_paciente); ?>" class="boton boton--secundario">Cancelar</a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>