<?php
require_once '../src/database.php';

// Consulta para obtener todas las ventas y el nombre del paciente asociado
$sql = "SELECT v.id_venta, v.numero_nota, v.fecha_venta, v.costo_total,
               p.nombres, p.apellido_paterno, p.apellido_materno
        FROM ventas v
        JOIN pacientes p ON v.id_paciente = p.id_paciente
        ORDER BY CAST(v.numero_nota AS UNSIGNED) DESC"; // Aseguramos que el número de nota se ordene numéricamente

$stmt = $pdo->query($sql);
$ventas = $stmt->fetchAll();

// Formateador de fecha
$formatter = new IntlDateFormatter('es_MX', IntlDateFormatter::FULL, IntlDateFormatter::NONE);

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="pagina-header">
        <h2>Gestión de Ventas</h2>
        </div>

    <div class="tabla-container">
        <table class="tabla">
            <thead>
                <tr>
                    <th># Nota</th>
                    <th>Paciente</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($venta['numero_nota']); ?></td>
                        <td><?php echo htmlspecialchars(trim($venta['nombres'] . ' ' . $venta['apellido_paterno'] . ' ' . $venta['apellido_materno'])); ?></td>
                        <td><?php echo ucfirst($formatter->format(strtotime($venta['fecha_venta']))); ?></td>
                        <td>$<?php echo number_format($venta['costo_total'], 2); ?></td>
                        <td>
                            <a href="/venta_detalle.php?id=<?php echo $venta['id_venta']; ?>" class="boton boton--sm boton--info">Ver Detalles</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                 <?php if (empty($ventas)): ?>
                    <tr>
                        <td colspan="5">Aún no se han registrado ventas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>