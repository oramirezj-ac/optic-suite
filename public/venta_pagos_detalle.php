<?php
require_once '../src/database.php';

$id_venta = $_GET['id_venta'] ?? null;
if (!$id_venta) {
    header('Location: /ventas.php');
    exit();
}

// Buscar datos de la venta y paciente
$sql_venta = "SELECT v.*, p.nombres, p.apellido_paterno, p.apellido_materno 
              FROM ventas v
              JOIN pacientes p ON v.id_paciente = p.id_paciente
              WHERE v.id_venta = ?";
$stmt_venta = $pdo->prepare($sql_venta);
$stmt_venta->execute([$id_venta]);
$venta = $stmt_venta->fetch();

if (!$venta) {
    header('Location: /ventas.php');
    exit();
}
$nombre_completo = trim($venta['nombres'] . ' ' . $venta['apellido_paterno'] . ' ' . $venta['apellido_materno']);

// Buscar todos los abonos de esta venta
$stmt_abonos = $pdo->prepare("SELECT * FROM abonos WHERE id_venta = ? ORDER BY fecha_abono ASC");
$stmt_abonos->execute([$id_venta]);
$abonos = $stmt_abonos->fetchAll();

$total_abonado = 0;
foreach ($abonos as $abono) {
    $total_abonado += $abono['monto_abono'];
}
$saldo_actual = $venta['costo_total'] - $total_abonado;

// --- Formateador de fecha ---
$formatter = new IntlDateFormatter('es_MX', IntlDateFormatter::FULL, IntlDateFormatter::NONE);

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="pagina-header">
        <h2>Historial de Pagos</h2>
        <a href="/venta_detalle.php?id=<?php echo $id_venta; ?>" class="boton boton--secundario boton--md">&larr; Regresar al Detalle</a>
    </div>

    <div class="paciente-titulo">
        <p class="subtitulo-pagina">Venta #<strong><?php echo htmlspecialchars($venta['numero_nota']); ?></strong></p>
        <h3><?php echo htmlspecialchars($nombre_completo); ?></h3>
    </div>

    <div class="detalle-venta-container">
        <div class="detalle-seccion">
            <h3>Resumen Financiero</h3>
            <div class="detalle-grid">
                <div><strong>Monto Total:</strong> <span>$<?php echo number_format($venta['costo_total'], 2); ?></span></div>
                <div><strong>Total Abonado:</strong> <span>$<?php echo number_format($total_abonado, 2); ?></span></div>
                <div><strong>Saldo Pendiente:</strong> <span>$<?php echo number_format($saldo_actual, 2); ?></span></div>
            </div>
        </div>
        
        <div class="detalle-seccion">
            <div class="abonos-container">
                <div class="abonos-lista">
                    <h3>Pagos Registrados</h3>
                    <table class="tabla">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Concepto/Método</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($abonos as $abono): ?>
                            <tr>
                                <td><?php echo ucfirst($formatter->format(strtotime($abono['fecha_abono']))); ?></td>
                                <td>$<?php echo number_format($abono['monto_abono'], 2); ?></td>
                                <td><?php echo htmlspecialchars($abono['metodo_pago']); ?></td>
                                <td>
                                    <form action="/abono_borrar.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este pago?');">
                                        <input type="hidden" name="id_abono" value="<?php echo $abono['id_abono']; ?>">
                                        <input type="hidden" name="id_venta" value="<?php echo $id_venta; ?>">
                                        <button type="submit" class="boton boton--sm boton--peligro">Borrar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($abonos)): ?>
                            <tr><td colspan="4">No se han registrado pagos.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="abonos-form">
                    <h4>Registrar Nuevo Abono</h4>
                    <form action="/abono_crear.php" method="POST" class="formulario-vertical">
                        <input type="hidden" name="id_venta" value="<?php echo $id_venta; ?>">
                        
                        <div class="campo">
                            <label for="monto_abono">Monto a abonar:</label>
                            <input type="number" step="0.01" id="monto_abono" name="monto_abono" required>
                        </div>

                        <div class="campo">
                            <label for="fecha_abono">Fecha del Abono:</label>
                            <input type="date" id="fecha_abono" name="fecha_abono" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="campo">
                            <label for="metodo_pago">Concepto:</label>
                            <input type="text" id="metodo_pago" name="metodo_pago" placeholder="Ej: Abono en efectivo" value="Abono">
                        </div>

                        <button type="submit" class="boton boton--primario boton--md">Agregar Abono</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>