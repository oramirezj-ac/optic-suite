<?php
// Obtener el ID del paciente para asociar la consulta
$id_paciente = $_GET['id_paciente'] ?? null;
if (!$id_paciente) {
    header('Location: /pacientes.php');
    exit();
}

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <h2>Registrar Nueva Consulta</h2>
    <p>Estás agregando una consulta para el paciente #<?php echo htmlspecialchars($id_paciente); ?>.</p>
    
    <div class="formulario-container">
        <form action="/consulta_crear.php" method="POST" class="formulario-grid">
            <input type="hidden" name="id_paciente" value="<?php echo htmlspecialchars($id_paciente); ?>">

            <div class="campo">
                <label for="fecha_consulta">Fecha de Consulta:</label>
                <input type="date" id="fecha_consulta" name="fecha_consulta" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="campo campo-full-width">
                <label>Agudeza Visual</label>
                <div class="agudeza-visual-grupo">
                    <div class="av-campo">
                        <label for="av_ao">A.O.</label>
                        <select id="av_ao" name="av_ao">
                            <option value="20/200">20/200</option>
                            <option value="20/100">20/100</option>
                            <option value="20/70">20/70</option>
                            <option value="20/50">20/50</option>
                            <option value="20/40">20/40</option>
                            <option value="20/30">20/30</option>
                            <option value="20/25">20/25</option>
                            <option value="20/20" selected>20/20</option>
                            <option value="20/15">20/15</option>
                        </select>
                    </div>
                    <div class="av-campo">
                        <label for="av_od">O.D.</label>
                        <select id="av_od" name="av_od">
                            <option value="20/200">20/200</option>
                            <option value="20/100">20/100</option>
                            <option value="20/70">20/70</option>
                            <option value="20/50">20/50</option>
                            <option value="20/40">20/40</option>
                            <option value="20/30">20/30</option>
                            <option value="20/25">20/25</option>
                            <option value="20/20" selected>20/20</option>
                            <option value="20/15">20/15</option>
                        </select>
                    </div>
                    <div class="av-campo">
                        <label for="av_oi">O.I.</label>
                        <select id="av_oi" name="av_oi">
                            <option value="20/200">20/200</option>
                            <option value="20/100">20/100</option>
                            <option value="20/70">20/70</option>
                            <option value="20/50">20/50</option>
                            <option value="20/40">20/40</option>
                            <option value="20/30">20/30</option>
                            <option value="20/25">20/25</option>
                            <option value="20/20" selected>20/20</option>
                            <option value="20/15">20/15</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="campo campo-full-width">
                <label for="motivo_consulta">Motivo de la Consulta:</label>
                <textarea id="motivo_consulta" name="motivo_consulta" rows="3"></textarea>
            </div>

            <div class="campo campo-full-width">
                <label for="notas_adicionales">Notas Adicionales:</label>
                <textarea id="notas_adicionales" name="notas_adicionales" rows="3"></textarea>
            </div>

            <div class="formulario-acciones campo-full-width">
                <button type="submit" class="boton boton--primario">Guardar Consulta</button>
                <a href="/consulta_historial.php?id=<?php echo htmlspecialchars($id_paciente); ?>" class="boton boton--secundario">Cancelar</a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>