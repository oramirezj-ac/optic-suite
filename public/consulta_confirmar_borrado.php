<?php
require_once '../src/database.php';
$id_consulta = $_GET['id_consulta'] ?? null;
$id_paciente = $_GET['id_paciente'] ?? null;

if (!$id_consulta || !$id_paciente) {
    header('Location: /pacientes.php');
    exit();
}

$stmt = $pdo->prepare("SELECT fecha_consulta FROM consultas WHERE id_consulta = ?");
$stmt->execute([$id_consulta]);
$consulta = $stmt->fetch();

if (!$consulta) {
    header('Location: /pacientes.php');
    exit();
}

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
        <p>¿Estás seguro de que deseas eliminar la consulta del <strong><?php echo htmlspecialchars($consulta['fecha_consulta']); ?></strong>?</p>
        <p class="alerta-permanente">Esta acción es permanente y borrará todas las graduaciones asociadas.</p>
        
        <div class="confirmacion-acciones">
            <form action="/consulta_borrar.php" method="POST">
                <input type="hidden" name="id_consulta" value="<?php echo $id_consulta; ?>">
                <input type="hidden" name="id_paciente" value="<?php echo $id_paciente; ?>">
                <button type="submit" class="boton boton--peligro">Sí, Eliminar</button>
            </form>
            <a href="/consulta_historial.php?id=<?php echo $id_paciente; ?>" class="boton boton--secundario">Cancelar</a>
        </div>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>