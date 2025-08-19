<?php
require_once '../src/database.php';

// Obtener el ID del paciente desde la URL
$id_paciente = $_GET['id'] ?? null;
if (!$id_paciente) {
    header('Location: /pacientes.php');
    exit();
}

// 1. Buscar la información principal del paciente
$stmt_paciente = $pdo->prepare("SELECT * FROM pacientes WHERE id_paciente = ?");
$stmt_paciente->execute([$id_paciente]);
$paciente = $stmt_paciente->fetch();

if (!$paciente) {
    header('Location: /pacientes.php');
    exit();
}
$nombre_completo = trim($paciente['nombres'] . ' ' . $paciente['apellido_paterno'] . ' ' . $paciente['apellido_materno']);

// 2. Buscar la última graduación de tipo "Final"
$sql_graduacion = "
    SELECT g.*, c.fecha_consulta 
    FROM graduaciones g
    JOIN consultas c ON g.id_consulta = c.id_consulta
    WHERE c.id_paciente = ? AND g.tipo_graduacion = 'Final'
    ORDER BY c.fecha_consulta DESC
    LIMIT 1
";
$stmt_graduacion = $pdo->prepare($sql_graduacion);
$stmt_graduacion->execute([$id_paciente]);
$ultima_graduacion = $stmt_graduacion->fetch();

// 3. Buscar la última venta del paciente
$stmt_venta = $pdo->prepare("SELECT * FROM ventas WHERE id_paciente = ? ORDER BY fecha_venta DESC LIMIT 1");
$stmt_venta->execute([$id_paciente]);
$ultima_venta = $stmt_venta->fetch();

// Formateador de fecha
$formatter = new IntlDateFormatter('es_MX', IntlDateFormatter::FULL, IntlDateFormatter::NONE);

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="pagina-header">
        <h2>Detalle del Paciente</h2>
        <a href="/pacientes.php" class="boton boton--secundario">&larr; Regresar a la Lista</a>
    </div>

    <div class="paciente-titulo">
        <h3><?php echo htmlspecialchars($nombre_completo); ?></h3>
    </div>

    <div class="detalle-paciente-container">
        <div class="detalle-acciones">
            <div class="acciones-grupo-izquierda">
                <a href="/consulta_nueva.php?id_paciente=<?php echo $id_paciente; ?>" class="boton boton--primario boton--md">+ Consulta Nueva</a>
                <a href="/consulta_historial.php?id=<?php echo $id_paciente; ?>" class="boton boton--secundario boton--md">Ver Historial</a>
                <a href="/pacientes_editar.php?id=<?php echo $id_paciente; ?>" class="boton boton--secundario boton--md">Editar Paciente</a>
                <a href="/paciente_confirmar_borrado.php?id=<?php echo $id_paciente; ?>" class="boton boton--peligro boton--md">Eliminar Paciente</a>
            </div>
            <div class="acciones-grupo-derecha">
                <a href="/venta_nueva.php?id_paciente=<?php echo $id_paciente; ?>" class="boton boton--venta boton--md">+ Nueva Venta</a>
            </div>
        </div>

        <div class="detalle-seccion">
            <h3>Datos Personales</h3>
            <div class="detalle-grid">
                <div>
                    <strong>Edad:</strong>
                    <span>
                         <?php
                            if ($paciente['fecha_nacimiento']) {
                                $fecha_nac = new DateTime($paciente['fecha_nacimiento']);
                                $hoy = new DateTime();
                                $edad = $hoy->diff($fecha_nac)->y;
                                echo $edad . ' años';
                            } else {
                                echo 'N/A';
                            }
                        ?>
                    </span>
                </div>
                <div>
                    <strong>Teléfono:</strong>
                    <span><?php echo htmlspecialchars($paciente['telefono']); ?></span>
                </div>
                <div>
                    <strong>DP Total:</strong>
                    <span><?php echo htmlspecialchars($paciente['dp_total']); ?> mm</span>
                </div>
            </div>
        </div>
        
        <div class="detalle-seccion">
            <h3>Última Venta</h3>
            <?php if ($ultima_venta): ?>
                <div class="detalle-grid">
                    <div>
                        <strong># Nota:</strong>
                        <span><?php echo htmlspecialchars($ultima_venta['numero_nota']); ?></span>
                    </div>
                    <div>
                        <strong>Fecha:</strong>
                        <span><?php echo ucfirst($formatter->format(strtotime($ultima_venta['fecha_venta']))); ?></span>
                    </div>
                    <div>
                        <strong>Monto:</strong>
                        <span>$<?php echo number_format($ultima_venta['costo_total'], 2); ?></span>
                    </div>
                </div>
                <div class="detalle-venta-acciones">
                    <a href="/venta_detalle.php?id=<?php echo $ultima_venta['id_venta']; ?>" class="boton boton--info boton--sm">Ver Detalle de la Venta</a>
                </div>
            <?php else: ?>
                <p>No se encontraron ventas para este paciente.</p>
            <?php endif; ?>
        </div>

        <div class="detalle-seccion seccion-graduacion">
            <h3>Última Graduación</h3>
            <?php if ($ultima_graduacion): ?>
                <p>Fecha de la consulta: <strong><?php echo ucfirst($formatter->format(strtotime($ultima_graduacion['fecha_consulta']))); ?></strong></p>
                <div class="graduacion-formula-container">
                    <div class="graduacion-formula">
                        <span class="graduacion-ojo-label">OD</span>
                        <span class="valor"><?php echo htmlspecialchars($ultima_graduacion['od_esfera']); ?></span>
                        <span class="simbolo">=</span>
                        <span class="valor"><?php echo htmlspecialchars($ultima_graduacion['od_cilindro']); ?></span>
                        <span class="simbolo">x</span>
                        <span class="valor"><?php echo htmlspecialchars($ultima_graduacion['od_eje']); ?></span>
                        <span class="simbolo">°</span>
                        <span class="valor valor-add"><?php echo htmlspecialchars($ultima_graduacion['od_add']); ?></span>
                    </div>
                    <div class="graduacion-formula">
                        <span class="graduacion-ojo-label">OI</span>
                        <span class="valor"><?php echo htmlspecialchars($ultima_graduacion['oi_esfera']); ?></span>
                        <span class="simbolo">=</span>
                        <span class="valor"><?php echo htmlspecialchars($ultima_graduacion['oi_cilindro']); ?></span>
                        <span class="simbolo">x</span>
                        <span class="valor"><?php echo htmlspecialchars($ultima_graduacion['oi_eje']); ?></span>
                        <span class="simbolo">°</span>
                        <span class="valor valor-add"><?php echo htmlspecialchars($ultima_graduacion['oi_add']); ?></span>
                    </div>
                </div>
            <?php else: ?>
                <p>No se encontró una graduación final para este paciente.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>