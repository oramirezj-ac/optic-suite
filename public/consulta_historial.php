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

// Buscar el historial de consultas de ese paciente
$stmt_consultas = $pdo->prepare("SELECT id_consulta, fecha_consulta, motivo_consulta FROM consultas WHERE id_paciente = ? ORDER BY fecha_consulta DESC");
$stmt_consultas->execute([$id_paciente]);
$consultas = $stmt_consultas->fetchAll();

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="pagina-header">
        <h2>Historial de: <?php echo htmlspecialchars($paciente['nombres'] . ' ' . $paciente['apellido_paterno'] . ' ' . $paciente['apellido_materno']); ?></h2>
        <a href="/consulta_nueva.php?id_paciente=<?php echo $id_paciente; ?>" class="boton boton-primario">+ Nueva Consulta</a>
    </div>

    <div class="tabla-container">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Motivo de la Consulta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultas as $consulta): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($consulta['fecha_consulta']); ?></td>
                        <td><?php echo htmlspecialchars($consulta['motivo_consulta']); ?></td>
                        <td>
                            <a href="/consulta_detalle.php?id_consulta=<?php echo $consulta['id_consulta']; ?>&id_paciente=<?php echo $id_paciente; ?>" class="boton boton-ver">Ver Detalles</a>
                            <a href="/consulta_editar.php?id_consulta=<?php echo $consulta['id_consulta']; ?>&id_paciente=<?php echo $id_paciente; ?>" class="boton boton-editar">Editar</a>
                            <a href="/consulta_confirmar_borrado.php?id_consulta=<?php echo $consulta['id_consulta']; ?>&id_paciente=<?php echo $id_paciente; ?>" class="boton boton-borrar">Borrar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>