<?php
require_once '../src/database.php';

// Obtener el ID de la graduación desde la URL
$id_graduacion = $_GET['id'] ?? null;
if (!$id_graduacion) {
    header('Location: /pacientes.php');
    exit();
}

// Buscar los datos de la graduación para mostrarlos
$stmt = $pdo->prepare("SELECT * FROM graduaciones WHERE id_graduacion = ?");
$stmt->execute([$id_graduacion]);
$graduacion = $stmt->fetch();

if (!$graduacion) {
    header('Location: /pacientes.php');
    exit();
}

// Obtener el id_paciente para el botón de cancelar
$stmt_paciente = $pdo->prepare("SELECT id_paciente FROM consultas WHERE id_consulta = ?");
$stmt_paciente->execute([$graduacion['id_consulta']]);
$paciente = $stmt_paciente->fetch();
$id_paciente = $paciente['id_paciente'];

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
        <h2>Confirmar Eliminación de Graduación</h2>
        <p>¿Estás seguro de que deseas eliminar la graduación de tipo <strong><?php echo htmlspecialchars($graduacion['tipo_graduacion']); ?></strong>?</p>
        <p class="alerta-permanente">Esta acción es permanente y no se puede deshacer.</p>
        
        <div class="confirmacion-acciones">
            <form action="/graduacion_borrar.php" method="POST">
                <input type="hidden" name="id_graduacion" value="<?php echo $id_graduacion; ?>">
                <input type="hidden" name="id_consulta" value="<?php echo $graduacion['id_consulta']; ?>">
                <input type="hidden" name="id_paciente" value="<?php echo $id_paciente; ?>">
                <button type="submit" class="boton boton-borrar">Sí, Eliminar</button>
            </form>
            <a href="/consulta_detalle.php?id_consulta=<?php echo $graduacion['id_consulta']; ?>&id_paciente=<?php echo $id_paciente; ?>" class="boton boton-secundario">Cancelar</a>
        </div>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>