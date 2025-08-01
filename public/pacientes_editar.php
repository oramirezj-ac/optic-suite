<?php
require_once '../src/database.php';
$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: /pacientes.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM pacientes WHERE id_paciente = ?");
$stmt->execute([$id]);
$paciente = $stmt->fetch();

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <h2>Editar Paciente: <?php echo htmlspecialchars($paciente['nombres'] . ' ' . $paciente['apellido_paterno'] . ' ' . $paciente['apellido_materno']); ?></h2>
    <div class="formulario-container">
        <form action="/pacientes_actualizar.php" method="POST" class="formulario-grid">
            <input type="hidden" name="id_paciente" value="<?php echo htmlspecialchars($paciente['id_paciente']); ?>">

             <div class="campo">
                <label for="nombres">Nombres:</label>
                <input type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($paciente['nombres']); ?>" required>
            </div>
            <div class="campo">
                <label for="apellido_paterno">Apellido Paterno:</label>
                <input type="text" id="apellido_paterno" name="apellido_paterno" value="<?php echo htmlspecialchars($paciente['apellido_paterno']); ?>">
            </div>
            <div class="campo">
                <label for="apellido_materno">Apellido Materno:</label>
                <input type="text" id="apellido_materno" name="apellido_materno" value="<?php echo htmlspecialchars($paciente['apellido_materno']); ?>">
            </div>

            <div class="campo">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($paciente['fecha_nacimiento']); ?>">
            </div>
            <div class="campo">
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($paciente['telefono']); ?>">
            </div>
            <div class="campo">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($paciente['email']); ?>">
            </div>
            
            <div class="campo">
                <label for="dp_total">DP Total (mm):</label>
                <input type="text" id="dp_total" name="dp_total" value="<?php echo htmlspecialchars($paciente['dp_total']); ?>">
            </div>
            <div class="campo">
                <label for="dnp_od">DNP OD (mm):</label>
                <input type="text" id="dnp_od" name="dnp_od" value="<?php echo htmlspecialchars($paciente['dnp_od']); ?>">
            </div>
            <div class="campo">
                <label for="dnp_oi">DNP OI (mm):</label>
                <input type="text" id="dnp_oi" name="dnp_oi" value="<?php echo htmlspecialchars($paciente['dnp_oi']); ?>">
            </div>

            <div class="campo campo-full-width">
                <label for="domicilio">Domicilio:</label>
                <input type="text" id="domicilio" name="domicilio" value="<?php echo htmlspecialchars($paciente['domicilio']); ?>">
            </div>

            <div class="campo campo-full-width">
                <label for="antecedentes_descripcion">Antecedentes Médicos:</label>
                <textarea id="antecedentes_descripcion" name="antecedentes_descripcion" rows="3"><?php echo htmlspecialchars($paciente['antecedentes_descripcion']); ?></textarea>
            </div>
            
            <div class="formulario-acciones campo-full-width">
                <div class="acciones-grupo-izquierda">
                    <button type="submit" class="boton boton-primario">Guardar Cambios</button>
                    <a href="/pacientes.php" class="boton boton-secundario">Cancelar</a>
                </div>
                <a href="/paciente_confirmar_borrado.php?id=<?php echo htmlspecialchars($paciente['id_paciente']); ?>" class="boton boton-borrar">Eliminar Paciente</a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>