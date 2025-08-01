<?php
require_once '../src/database.php';

// Obtener el ID del paciente desde la URL
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: /pacientes.php');
    exit();
}

// Buscar el nombre completo del paciente para mostrarlo
$stmt = $pdo->prepare("SELECT nombres, apellido_paterno, apellido_materno FROM pacientes WHERE id_paciente = ?");
$stmt->execute([$id]);
$paciente = $stmt->fetch();

if (!$paciente) {
    header('Location: /pacientes.php');
    exit();
}

$nombre_completo = trim($paciente['nombres'] . ' ' . $paciente['apellido_paterno'] . ' ' . $paciente['apellido_materno']);

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="confirmacion-container">
        <div class="confirmacion-icono-alerta">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
            </svg>
        </div>
        <h2>Confirmar Eliminación</h2>
        <p>¿Estás seguro de que deseas eliminar el registro de <strong><?php echo htmlspecialchars($nombre_completo); ?></strong>?</p>
        <p class="alerta-permanente">Esta acción es permanente y no se puede deshacer.</p>
        
        <div class="confirmacion-acciones">
            <form action="/pacientes_borrar.php" method="POST">
                <input type="hidden" name="id_paciente" value="<?php echo $id; ?>">
                <button type="submit" class="boton boton-borrar">Sí, Eliminar</button>
            </form>
            <a href="/pacientes.php" class="boton boton-secundario">Cancelar</a>
        </div>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>