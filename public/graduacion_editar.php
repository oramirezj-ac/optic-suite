<?php
require_once '../src/database.php';

// Obtener el ID de la graduación desde la URL
$id_graduacion = $_GET['id_graduacion'] ?? null;
if (!$id_graduacion) {
    header('Location: /pacientes.php');
    exit();
}

// Buscar los datos de la graduación para pre-llenar el formulario
$stmt = $pdo->prepare("SELECT * FROM graduaciones WHERE id_graduacion = ?");
$stmt->execute([$id_graduacion]);
$graduacion = $stmt->fetch();

if (!$graduacion) {
    header('Location: /pacientes.php');
    exit();
}

// NUEVO: Buscar el id_paciente para el enlace de "Cancelar"
$stmt_paciente = $pdo->prepare("SELECT id_paciente FROM consultas WHERE id_consulta = ?");
$stmt_paciente->execute([$graduacion['id_consulta']]);
$paciente = $stmt_paciente->fetch();
$id_paciente = $paciente['id_paciente'];


require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <h2>Editar Graduación</h2>
    <div class="formulario-container">
        <form action="/graduacion_actualizar.php" method="POST" class="formulario-graduacion-captura">
            <input type="hidden" name="id_graduacion" value="<?php echo htmlspecialchars($graduacion['id_graduacion']); ?>">
            <input type="hidden" name="id_consulta" value="<?php echo htmlspecialchars($graduacion['id_consulta']); ?>">

            <div class="campo-full-width">
                <label for="tipo_graduacion">Tipo de Graduación:</label>
                <select id="tipo_graduacion" name="tipo_graduacion">
                    <option value="Final" <?php echo ($graduacion['tipo_graduacion'] == 'Final') ? 'selected' : ''; ?>>Final</option>
                    <option value="Lensometro" <?php echo ($graduacion['tipo_graduacion'] == 'Lensometro') ? 'selected' : ''; ?>>Lensómetro</option>
                    <option value="Autorefractometro" <?php echo ($graduacion['tipo_graduacion'] == 'Autorefractometro') ? 'selected' : ''; ?>>Autorefractómetro</option>
                    <option value="Externa" <?php echo ($graduacion['tipo_graduacion'] == 'Externa') ? 'selected' : ''; ?>>Externa</option>
                </select>
            </div>

            <label class="graduacion-ojo-label">OD</label>
            <div class="graduacion-valores">
                <input type="number" step="0.25" name="od_esfera" placeholder="Esfera" class="valor" value="<?php echo htmlspecialchars($graduacion['od_esfera']); ?>">
                <input type="number" step="0.25" name="od_cilindro" placeholder="Cilindro" class="valor" value="<?php echo htmlspecialchars($graduacion['od_cilindro']); ?>">
                <input type="number" name="od_eje" min="0" max="180" placeholder="Eje" class="valor" value="<?php echo htmlspecialchars($graduacion['od_eje']); ?>">
                <input type="number" step="0.25" name="od_add" min="0" max="3.00" placeholder="Add" class="valor" value="<?php echo htmlspecialchars($graduacion['od_add']); ?>">
            </div>

            <label class="graduacion-ojo-label">OI</label>
            <div class="graduacion-valores">
                <input type="number" step="0.25" name="oi_esfera" placeholder="Esfera" class="valor" value="<?php echo htmlspecialchars($graduacion['oi_esfera']); ?>">
                <input type="number" step="0.25" name="oi_cilindro" placeholder="Cilindro" class="valor" value="<?php echo htmlspecialchars($graduacion['oi_cilindro']); ?>">
                <input type="number" name="oi_eje" min="0" max="180" placeholder="Eje" class="valor" value="<?php echo htmlspecialchars($graduacion['oi_eje']); ?>">
                <input type="number" step="0.25" name="oi_add" min="0" max="3.00" placeholder="Add" class="valor" value="<?php echo htmlspecialchars($graduacion['oi_add']); ?>">
            </div>

            <div class="formulario-acciones campo-full-width">
                <div class="acciones-grupo-izquierda">
                    <button type="submit" class="boton boton-primario">Actualizar Graduación</button>
                    <a href="/consulta_detalle.php?id_consulta=<?php echo $graduacion['id_consulta']; ?>&id_paciente=<?php echo $id_paciente; ?>" class="boton boton-secundario">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>