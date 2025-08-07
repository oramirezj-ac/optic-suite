<?php
require_once '../src/database.php';

// Obtener el ID del paciente desde la URL
$id_paciente = $_GET['id'] ?? null;
if (!$id_paciente) {
    header('Location: /pacientes.php');
    exit();
}

// Buscar la información del paciente
$stmt_paciente = $pdo->prepare("SELECT nombres, apellido_paterno, apellido_materno FROM pacientes WHERE id_paciente = ?");
$stmt_paciente->execute([$id_paciente]);
$paciente = $stmt_paciente->fetch();
$nombre_completo = trim($paciente['nombres'] . ' ' . $paciente['apellido_paterno'] . ' ' . $paciente['apellido_materno']);

// --- NUEVA CONSULTA SQL CON JOIN ---
// Busca todas las consultas y, si existe, une los datos de su graduación de tipo "Final"
$sql = "SELECT
            c.id_consulta, c.fecha_consulta, c.motivo_consulta,
            g.od_esfera, g.od_cilindro, g.od_eje, g.od_add,
            g.oi_esfera, g.oi_cilindro, g.oi_eje, g.oi_add
        FROM consultas c
        LEFT JOIN graduaciones g ON c.id_consulta = g.id_consulta AND g.tipo_graduacion = 'Final'
        WHERE c.id_paciente = ?
        ORDER BY c.fecha_consulta DESC";

$stmt_consultas = $pdo->prepare($sql);
$stmt_consultas->execute([$id_paciente]);
$consultas = $stmt_consultas->fetchAll();

// Formateador de fecha
$formatter = new IntlDateFormatter('es_MX', IntlDateFormatter::FULL, IntlDateFormatter::NONE);

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="pagina-header">
        <h2>Historial de: <?php echo htmlspecialchars($nombre_completo); ?></h2>
        <div class="acciones-grupo-izquierda">
            <a href="/paciente_detalle.php?id=<?php echo $id_paciente; ?>" class="boton boton--secundario">Regresar</a>
            <a href="/consulta_nueva.php?id_paciente=<?php echo $id_paciente; ?>" class="boton boton--primario">+ Nueva Consulta</a>
        </div>
    </div>

    <div class="tabla-container">
        <table class="tabla">
            <thead>
                <tr>
                    <th class="th-fecha">Fecha</th>
                    <th class="th-motivo">Motivo de la Consulta</th>
                    <th class="th-graduacion">Graduación Final</th>
                    <th class="th-acciones">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultas as $consulta): ?>
                    <tr>
                        <td class="td-fecha">
                            <?php echo ucfirst($formatter->format(strtotime($consulta['fecha_consulta']))); ?>
                        </td>
                        <td class="td-motivo">
                            <?php echo htmlspecialchars($consulta['motivo_consulta']); ?>
                        </td>
                        <td>
                            <?php if ($consulta['od_esfera'] !== null): // Si se encontró una graduación ?>
                                <div class="graduacion-formula-container-sm">
                                    <div class="graduacion-formula">
                                        <span class="graduacion-ojo-label">OD</span>
                                        <span class="valor"><?php echo htmlspecialchars($consulta['od_esfera']); ?></span>
                                        <span class="simbolo">=</span>
                                        <span class="valor"><?php echo htmlspecialchars($consulta['od_cilindro']); ?></span>
                                        <span class="simbolo">x</span>
                                        <span class="valor"><?php echo htmlspecialchars($consulta['od_eje']); ?></span>
                                        <span class="simbolo">°</span>
                                        <span class="valor valor-add"><?php echo htmlspecialchars($consulta['od_add']); ?></span>
                                    </div>
                                    <div class="graduacion-formula">
                                        <span class="graduacion-ojo-label">OI</span>
                                        <span class="valor"><?php echo htmlspecialchars($consulta['oi_esfera']); ?></span>
                                        <span class="simbolo">=</span>
                                        <span class="valor"><?php echo htmlspecialchars($consulta['oi_cilindro']); ?></span>
                                        <span class="simbolo">x</span>
                                        <span class="valor"><?php echo htmlspecialchars($consulta['oi_eje']); ?></span>
                                        <span class="simbolo">°</span>
                                        <span class="valor valor-add"><?php echo htmlspecialchars($consulta['oi_add']); ?></span>
                                    </div>
                                </div>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td class="td-acciones">
                            <a href="/consulta_detalle.php?id_consulta=<?php echo $consulta['id_consulta']; ?>&id_paciente=<?php echo $id_paciente; ?>" class="boton boton--sm boton--info">Ver/Editar</a>
                            <a href="/consulta_confirmar_borrado.php?id_consulta=<?php echo $consulta['id_consulta']; ?>&id_paciente=<?php echo $id_paciente; ?>" class="boton boton--sm boton--peligro">Borrar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                 <?php if (empty($consultas)): ?>
                    <tr>
                        <td colspan="4">No hay consultas registradas para este paciente.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>