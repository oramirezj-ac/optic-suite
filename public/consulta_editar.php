<?php
require_once '../src/database.php';
$id_consulta = $_GET['id_consulta'] ?? null;
$id_paciente = $_GET['id_paciente'] ?? null;

if (!$id_consulta || !$id_paciente) {
    header('Location: /pacientes.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM consultas WHERE id_consulta = ?");
$stmt->execute([$id_consulta]);
$consulta = $stmt->fetch();

if (!$consulta) {
    header('Location: /pacientes.php');
    exit();
}

// Lista de opciones para el select de Agudeza Visual
$opciones_av = ["20/200", "20/100", "20/70", "20/50", "20/40", "20/30", "20/25", "20/20", "20/15"];

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <h2>Editar Consulta</h2>
    <div class="formulario-container">
        <form action="/consulta_actualizar.php" method="POST" class="formulario-grid">
            <input type="hidden" name="id_consulta" value="<?php echo htmlspecialchars($consulta['id_consulta']); ?>">
            <input type="hidden" name="id_paciente" value="<?php echo htmlspecialchars($id_paciente); ?>">

            <div class="campo">
                <label for="fecha_consulta">Fecha de Consulta:</label>
                <input type="date" name="fecha_consulta" value="<?php echo htmlspecialchars($consulta['fecha_consulta']); ?>" required>
            </div>

            <div class="campo campo-full-width">
                <label>Agudeza Visual</label>
                <div class="agudeza-visual-grupo">
                    <div class="av-campo">
                        <label for="av_ao">A.O.</label>
                        <select id="av_ao" name="av_ao">
                            <?php foreach ($opciones_av as $opcion): ?>
                                <option value="<?php echo $opcion; ?>" <?php echo ($consulta['av_ao'] == $opcion) ? 'selected' : ''; ?>><?php echo $opcion; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="av-campo">
                        <label for="av_od">O.D.</label>
                        <select id="av_od" name="av_od">
                            <?php foreach ($opciones_av as $opcion): ?>
                                <option value="<?php echo $opcion; ?>" <?php echo ($consulta['av_od'] == $opcion) ? 'selected' : ''; ?>><?php echo $opcion; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="av-campo">
                        <label for="av_oi">O.I.</label>
                        <select id="av_oi" name="av_oi">
                            <?php foreach ($opciones_av as $opcion): ?>
                                <option value="<?php echo $opcion; ?>" <?php echo ($consulta['av_oi'] == $opcion) ? 'selected' : ''; ?>><?php echo $opcion; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="campo campo-full-width">
                <label for="motivo_consulta">Motivo de la Consulta:</label>
                <textarea name="motivo_consulta" rows="3"><?php echo htmlspecialchars($consulta['motivo_consulta']); ?></textarea>
            </div>

            <div class="campo campo-full-width">
                <label for="notas_adicionales">Notas Adicionales:</label>
                <textarea name="notas_adicionales" rows="3"><?php echo htmlspecialchars($consulta['notas_adicionales']); ?></textarea>
            </div>

            <div class="formulario-acciones campo-full-width">
                <div class="acciones-grupo-izquierda">
                    <button type="submit" class="boton boton--primario">Actualizar Consulta</button>
                    <a href="/consulta_historial.php?id=<?php echo $id_paciente; ?>" class="boton boton--secundario">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>