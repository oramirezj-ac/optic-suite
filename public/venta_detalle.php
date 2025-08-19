<?php
require_once '../src/database.php';

// Obtener el ID de la venta desde la URL
$id_venta = $_GET['id'] ?? null;
if (!$id_venta) {
    header('Location: /ventas.php');
    exit();
}

// Buscar los datos de la venta y unir con la tabla de pacientes
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

// Buscar las órdenes de laboratorio de esta venta
$stmt_ordenes = $pdo->prepare("SELECT * FROM ordenes_laboratorio WHERE id_venta = ? ORDER BY fecha_envio DESC");
$stmt_ordenes->execute([$id_venta]);
$ordenes = $stmt_ordenes->fetchAll();

// Buscar abonos y calcular el total abonado
$stmt_abonos = $pdo->prepare("SELECT * FROM abonos WHERE id_venta = ? ORDER BY fecha_abono ASC");
$stmt_abonos->execute([$id_venta]);
$abonos = $stmt_abonos->fetchAll();

$total_abonado = 0;
foreach ($abonos as $abono) {
    $total_abonado += $abono['monto_abono'];
}
$saldo_actual = $venta['costo_total'] - $total_abonado;

// Formateador de fecha
$formatter = new IntlDateFormatter('es_MX', IntlDateFormatter::FULL, IntlDateFormatter::NONE);

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="pagina-header">
        <h2>Detalle de Venta #<?php echo htmlspecialchars($venta['numero_nota']); ?></h2>
        <div class="acciones-grupo-izquierda">
             <a href="/venta_editar.php?id=<?php echo $venta['id_venta']; ?>" class="boton boton--secundario boton--md">Editar Venta</a>
             <a href="/venta_confirmar_borrado.php?id=<?php echo $venta['id_venta']; ?>" class="boton boton--peligro boton--md">Eliminar Venta</a>
             <a href="/paciente_detalle.php?id=<?php echo $venta['id_paciente']; ?>" class="boton boton--secundario boton--md">&larr; Regresar al Paciente</a>
        </div>
    </div>
    
    <div class="paciente-titulo">
        <p class="subtitulo-pagina">Paciente</p>
        <h3><?php echo htmlspecialchars($nombre_completo); ?></h3>
    </div>

    <div class="detalle-venta-container">
        <div class="detalle-seccion">
            <h3>Información de la Venta</h3>
            <div class="detalle-grid">
                <div><strong>Fecha:</strong> <span><?php echo ucfirst($formatter->format(strtotime($venta['fecha_venta']))); ?></span></div>
                <div><strong>Monto Total:</strong> <span>$<?php echo number_format($venta['costo_total'], 2); ?></span></div>
                <div><strong>Total Abonado:</strong> <span>$<?php echo number_format($total_abonado, 2); ?></span></div>
                <div><strong>Saldo Pendiente:</strong> <span>$<?php echo number_format($saldo_actual, 2); ?></span></div>
                <div><strong>Estado:</strong> <span><?php echo htmlspecialchars($venta['estado_pago']); ?></span></div>
            </div>
        </div>

        <div class="detalle-seccion">
            <div class="pagina-header">
                <h3>Historial de Pagos</h3>
                <a href="/venta_pagos_detalle.php?id_venta=<?php echo $id_venta; ?>" class="boton boton--info boton--sm">Gestionar Pagos</a>
            </div>
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Concepto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($abonos as $abono): ?>
                    <tr>
                        <td><?php echo ucfirst($formatter->format(strtotime($abono['fecha_abono']))); ?></td>
                        <td>$<?php echo number_format($abono['monto_abono'], 2); ?></td>
                        <td><?php echo htmlspecialchars($abono['metodo_pago']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($abonos)): ?>
                    <tr><td colspan="3">No se han registrado pagos.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="detalle-seccion">
            <div class="pagina-header">
                <h3>Órdenes de Laboratorio</h3>
                <a href="/orden_nueva.php?id_venta=<?php echo $venta['id_venta']; ?>" class="boton boton--exito boton--sm">+ Agregar Orden</a>
            </div>
            <div class="tabla-container">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th>Laboratorio</th>
                            <th># Orden Externa</th>
                            <th>Fecha Envío</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ordenes)): ?>
                            <tr>
                                <td colspan="5">No hay órdenes de laboratorio asociadas a esta venta.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ordenes as $orden): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($orden['nombre_laboratorio']); ?></td>
                                    <td><?php echo htmlspecialchars($orden['numero_orden_externa']); ?></td>
                                    <td><?php echo ucfirst($formatter->format(strtotime($orden['fecha_envio']))); ?></td>
                                    <td><?php echo htmlspecialchars($orden['estado_orden']); ?></td>
                                    <td>
                                        <a href="/orden_editar.php?id=<?php echo $orden['id_orden_lab']; ?>" class="boton boton--sm boton--exito">Editar</a>
                                        <a href="/orden_confirmar_borrado.php?id=<?php echo $orden['id_orden_lab']; ?>" class="boton boton--sm boton--peligro">Borrar</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>