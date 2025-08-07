<?php
require_once '../src/database.php';

// Variable para guardar los resultados
$resultados = [];
// Variable para guardar el término de búsqueda
$busqueda = $_GET['q'] ?? '';

// Si se envió un término de búsqueda, ejecutar la consulta
if (!empty($busqueda)) {
    $sql = "SELECT id_paciente, nombres, apellido_paterno, apellido_materno, fecha_nacimiento 
            FROM pacientes 
            WHERE LOWER(CONCAT_WS(' ', nombres, apellido_paterno, apellido_materno)) LIKE LOWER(?)
            ORDER BY apellido_paterno ASC, apellido_materno ASC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['%' . $busqueda . '%']);
    $resultados = $stmt->fetchAll();
}

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="pagina-header">
        <h2>Buscar Paciente</h2>
        <a href="/pacientes.php" class="boton boton--secundario boton--md">&larr; Regresar</a>
    </div>
    <p>Ingresa el nombre o apellido del paciente que deseas encontrar.</p>

    <div class="formulario-container">
        <form action="/paciente_buscar.php" method="GET" class="formulario-busqueda">
            <input type="search" name="q" placeholder="Buscar por nombre o apellido..." value="<?php echo htmlspecialchars($busqueda); ?>">
            <button type="submit" class="boton boton--primario boton--md">Buscar</button>
        </form>
    </div>

    <?php if (!empty($busqueda)): ?>
        <h3>Resultados para "<?php echo htmlspecialchars($busqueda); ?>"</h3>
        <div class="tabla-container">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Nombres</th>
                        <th>Apellido Paterno</th>
                        <th>Apellido Materno</th>
                        <th>Edad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($resultados) > 0): ?>
                        <?php foreach ($resultados as $paciente): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($paciente['nombres']); ?></td>
                                <td><?php echo htmlspecialchars($paciente['apellido_paterno']); ?></td>
                                <td><?php echo htmlspecialchars($paciente['apellido_materno']); ?></td>
                                <td>
                                    <?php
                                        if ($paciente['fecha_nacimiento']) {
                                            $fecha_nac = new DateTime($paciente['fecha_nacimiento']);
                                            $hoy = new DateTime();
                                            $edad = $hoy->diff($fecha_nac)->y;
                                            echo $edad . ' años';
                                        } else { echo 'N/A'; }
                                    ?> 
                                <td>
                                    <a href="/paciente_detalle.php?id=<?php echo $paciente['id_paciente']; ?>" class="boton boton--primario boton--sm">Revisar Información</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No se encontraron pacientes que coincidan con tu búsqueda.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php require_once '../src/layouts/footer.php'; ?>