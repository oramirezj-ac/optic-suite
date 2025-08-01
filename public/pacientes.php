<?php
require_once '../src/database.php';

// --- Lógica del Filtro Alfabético ---
$abecedario = range('A', 'Z');
$letra_seleccionada = $_GET['letra'] ?? null;

// --- Consulta a la Base de Datos (Ahora es dinámica) ---
$sql_todos = "SELECT id_paciente, nombres, apellido_paterno, apellido_materno, telefono, dp_total FROM pacientes";
$params = [];

if ($letra_seleccionada) {
    $sql_todos .= " WHERE apellido_paterno LIKE ?";
    $params[] = $letra_seleccionada . '%';
}

$sql_todos .= " ORDER BY apellido_paterno ASC, apellido_materno ASC";

$stmt_todos = $pdo->prepare($sql_todos);
$stmt_todos->execute($params);
$pacientes_todos = $stmt_todos->fetchAll();


require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="pagina-header">
        <h2>Gestión de Pacientes</h2>
        <a href="/pacientes_nuevo.php" class="boton boton-primario">+ Agregar Nuevo Paciente</a>
    </div>

    <h3>Filtrar por Apellido</h3>
    <div class="filtro-alfabetico">
        <a href="/pacientes.php" class="<?php echo !$letra_seleccionada ? 'activo' : ''; ?>">Todos</a>
        <?php foreach ($abecedario as $letra): ?>
            <a href="/pacientes.php?letra=<?php echo $letra; ?>" class="<?php echo ($letra_seleccionada == $letra) ? 'activo' : ''; ?>">
                <?php echo $letra; ?>
            </a>
        <?php endforeach; ?>
    </div>


    <h3>
        <?php 
        if ($letra_seleccionada) {
            echo 'Listado de apellidos que inician con "' . htmlspecialchars($letra_seleccionada) . '"';
        } else {
            echo 'Listado Completo';
        }
        ?>
    </h3>
    <div class="tabla-container">
        <table class="tabla">
            <thead>
                <tr>
                    <th>Nombres</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                    <th>Teléfono</th>
                    <th>DP Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pacientes_todos as $paciente): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($paciente['nombres']); ?></td>
                        <td><?php echo htmlspecialchars($paciente['apellido_paterno']); ?></td>
                        <td><?php echo htmlspecialchars($paciente['apellido_materno']); ?></td>
                        <td><?php echo htmlspecialchars($paciente['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($paciente['dp_total']); ?></td>
                        <td>
                            <a href="/consulta_historial.php?id=<?php echo $paciente['id_paciente']; ?>" class="boton boton-ver">Ver Consultas</a>
                            <a href="/pacientes_editar.php?id=<?php echo $paciente['id_paciente']; ?>" class="boton boton-editar">Editar</a>
                            <a href="/paciente_confirmar_borrado.php?id=<?php echo $paciente['id_paciente']; ?>" class="boton boton-borrar">Borrar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                 <?php if (empty($pacientes_todos)): ?>
                    <tr>
                        <td colspan="6">No se encontraron pacientes con ese criterio.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>