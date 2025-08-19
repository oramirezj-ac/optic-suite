<?php
require_once '../src/database.php';

// Obtener IDs desde la URL
$id_consulta = $_GET['id_consulta'] ?? null;
$id_paciente = $_GET['id_paciente'] ?? null;
if (!$id_consulta || !$id_paciente) {
    header('Location: /pacientes.php');
    exit();
}

// Buscar datos de la consulta
$stmt_consulta = $pdo->prepare("SELECT * FROM consultas WHERE id_consulta = ?");
$stmt_consulta->execute([$id_consulta]);
$consulta = $stmt_consulta->fetch();

if (!$consulta) {
    header('Location: /pacientes.php');
    exit();
}

// Buscar el nombre completo del paciente
$stmt_paciente = $pdo->prepare("SELECT nombres, apellido_paterno, apellido_materno FROM pacientes WHERE id_paciente = ?");
$stmt_paciente->execute([$id_paciente]);
$paciente = $stmt_paciente->fetch();
$nombre_completo = trim($paciente['nombres'] . ' ' . $paciente['apellido_paterno'] . ' ' . $paciente['apellido_materno']);

// Buscar graduaciones existentes para esta consulta
$stmt_graduaciones = $pdo->prepare("SELECT * FROM graduaciones WHERE id_consulta = ? ORDER BY tipo_graduacion");
$stmt_graduaciones->execute([$id_consulta]);
$graduaciones = $stmt_graduaciones->fetchAll();

// Formateador de fecha
$formatter = new IntlDateFormatter('es_MX', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
$fecha_formateada = $formatter->format(strtotime($consulta['fecha_consulta']));

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="pagina-header">
        <h2>Detalle de Consulta</h2>
        <div class="acciones-grupo-izquierda">
            <a href="/consulta_editar.php?id_consulta=<?php echo $id_consulta; ?>&id_paciente=<?php echo $id_paciente; ?>" class="boton boton--secundario boton--md">Editar Consulta</a>
            <a href="/consulta_confirmar_borrado.php?id_consulta=<?php echo $id_consulta; ?>&id_paciente=<?php echo $id_paciente; ?>" class="boton boton--peligro boton--md">Eliminar Consulta</a>
            <a href="/consulta_historial.php?id=<?php echo $id_paciente; ?>" class="boton boton--secundario boton--md">&larr; Regresar al Historial</a>
        </div>
    </div>

    <div class="pagina-header">
        <h2><?php echo ucfirst(htmlspecialchars($fecha_formateada)); ?></h2>
    </div>
    <div class="paciente-titulo">
        <h3><?php echo htmlspecialchars($nombre_completo); ?></h3>
    </div>

    <h3>Graduaciones Registradas</h3>
    <div class="lista-graduaciones">
        <?php if (empty($graduaciones)): ?>
            <p>No hay graduaciones registradas para esta consulta.</p>
        <?php else: ?>
            <?php foreach ($graduaciones as $graduacion): ?>
                <div class="graduacion-fila">
                    <div class="graduacion-columna-tipo">
                        <strong><?php echo htmlspecialchars($graduacion['tipo_graduacion']); ?></strong>
                    </div>
                    <div class="graduacion-columna-formulas">
                        <div class="graduacion-formula">
                            <span class="graduacion-ojo-label">OD</span>
                            <span class="valor"><?php echo htmlspecialchars($graduacion['od_esfera']); ?></span>
                            <span class="simbolo">=</span>
                            <span class="valor"><?php echo htmlspecialchars($graduacion['od_cilindro']); ?></span>
                            <span class="simbolo">x</span>
                            <span class="valor"><?php echo htmlspecialchars($graduacion['od_eje']); ?></span>
                            <span class="simbolo">°</span>
                            <span class="valor valor-add"><?php echo htmlspecialchars($graduacion['od_add']); ?></span>
                        </div>
                        <div class="graduacion-formula">
                            <span class="graduacion-ojo-label">OI</span>
                            <span class="valor"><?php echo htmlspecialchars($graduacion['oi_esfera']); ?></span>
                            <span class="simbolo">=</span>
                            <span class="valor"><?php echo htmlspecialchars($graduacion['oi_cilindro']); ?></span>
                            <span class="simbolo">x</span>
                            <span class="valor"><?php echo htmlspecialchars($graduacion['oi_eje']); ?></span>
                            <span class="simbolo">°</span>
                            <span class="valor valor-add"><?php echo htmlspecialchars($graduacion['oi_add']); ?></span>
                        </div>
                    </div>
                    <div class="graduacion-columna-acciones">
                        <a href="/graduacion_editar.php?id_graduacion=<?php echo $graduacion['id_graduacion']; ?>" class="boton boton--sm boton--exito">Editar</a>
                        <a href="/graduacion_confirmar_borrado.php?id=<?php echo $graduacion['id_graduacion']; ?>" class="boton boton--sm boton--peligro">Borrar</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <div class="formulario-container">
        <h3>Registrar Nueva Graduación</h3>
        <form action="/graduacion_crear.php" method="POST" class="formulario-graduacion-captura">
            <input type="hidden" name="id_consulta" value="<?php echo htmlspecialchars($id_consulta); ?>">
            <input type="hidden" name="id_paciente" value="<?php echo htmlspecialchars($id_paciente); ?>">
            <div class="campo-full-width campo-tipo-graduacion">
                <label for="tipo_graduacion">Tipo de Graduación:</label>
                <select id="tipo_graduacion" name="tipo_graduacion">
                    <option value="Final">Final</option>
                    <option value="Lensometro">Lensómetro</option>
                    <option value="Autorefractometro">Autorefractómetro</option>
                    <option value="Externa">Externa</option>
                </select>
            </div>
            <label class="graduacion-ojo-label">OD</label>
            <div class="graduacion-valores">
                <input type="number" step="0.25" name="od_esfera" placeholder="Esfera" class="valor">
                <input type="number" step="0.25" name="od_cilindro" placeholder="Cilindro" class="valor">
                <input type="number" name="od_eje" min="0" max="180" placeholder="Eje" class="valor">
                <input type="number" step="0.25" name="od_add" min="0" max="3.00" placeholder="Add" class="valor">
            </div>
            <label class="graduacion-ojo-label">OI</label>
            <div class="graduacion-valores">
                <input type="number" step="0.25" name="oi_esfera" placeholder="Esfera" class="valor">
                <input type="number" step="0.25" name="oi_cilindro" placeholder="Cilindro" class="valor">
                <input type="number" name="oi_eje" min="0" max="180" placeholder="Eje" class="valor">
                <input type="number" step="0.25" name="oi_add" min="0" max="3.00" placeholder="Add" class="valor">
            </div>
            <div class="campo-full-width">
                <button type="submit" class="boton boton--primario">Guardar Graduación</button>
            </div>
        </form>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>