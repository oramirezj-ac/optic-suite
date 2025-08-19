<?php

/**
 * EJEMPLO DE REFACTORING: paciente_buscar_mejorado.php
 * 
 * Esta es una versión mejorada del archivo original que demuestra:
 * - Separación de responsabilidades
 * - Manejo de errores
 * - Validación robusta  
 * - Código más seguro y mantenible
 */

require_once __DIR__ . '/../src/DatabaseImproved.php';
require_once __DIR__ . '/../src/helpers/Validator.php';

try {
    // Inicializar servicios
    $db = Database::getInstance();
    $validator = new Validator();
    
    // Obtener y validar parámetros de entrada
    $busqueda = trim($_GET['q'] ?? '');
    $resultados = [];
    $errores = [];
    
    // Validar término de búsqueda
    if (!empty($busqueda)) {
        $rules = ['q' => 'required|min:2|max:100'];
        
        if (!$validator->validate(['q' => $busqueda], $rules)) {
            $errores = $validator->getErrors();
        } else {
            // Realizar búsqueda de forma segura
            $sql = "SELECT 
                        id_paciente, 
                        nombres, 
                        apellido_paterno, 
                        apellido_materno, 
                        fecha_nacimiento 
                    FROM pacientes 
                    WHERE LOWER(CONCAT_WS(' ', nombres, apellido_paterno, apellido_materno)) LIKE LOWER(?)
                    ORDER BY apellido_paterno ASC, apellido_materno ASC
                    LIMIT 50"; // Limitar resultados para performance
            
            $resultados = $db->fetchAll($sql, ['%' . $busqueda . '%']);
        }
    }
    
    // Preparar datos para la vista
    $pageData = [
        'title' => 'Buscar Paciente',
        'busqueda' => Validator::escape($busqueda),
        'resultados' => $resultados,
        'errores' => $errores,
        'totalResultados' => count($resultados)
    ];
    
} catch (DatabaseException $e) {
    error_log("Database error in patient search: " . $e->getMessage());
    $pageData = [
        'title' => 'Error en Búsqueda',
        'error' => 'Error al realizar la búsqueda. Inténtalo nuevamente.'
    ];
} catch (Exception $e) {
    error_log("General error in patient search: " . $e->getMessage());
    $pageData = [
        'title' => 'Error',
        'error' => 'Ha ocurrido un error inesperado.'
    ];
}

// Incluir layout y vista
require_once __DIR__ . '/../src/layouts/header.php';
require_once __DIR__ . '/../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="pagina-header">
        <h2><?= $pageData['title'] ?></h2>
        <a href="/pacientes.php" class="btn btn--secondary btn--md">&larr; Regresar</a>
    </div>

    <?php if (isset($pageData['error'])): ?>
        <div class="alert alert--danger">
            <strong>Error:</strong> <?= Validator::escape($pageData['error']) ?>
        </div>
    <?php endif; ?>

    <div class="search-section">
        <p>Ingresa el nombre o apellido del paciente que deseas encontrar.</p>
        
        <div class="formulario-container">
            <form action="/examples/paciente_buscar_mejorado.php" method="GET" class="search-form">
                <div class="search-input-group">
                    <input 
                        type="search" 
                        name="q" 
                        placeholder="Buscar por nombre o apellido..." 
                        value="<?= $pageData['busqueda'] ?? '' ?>"
                        class="search-input <?= !empty($pageData['errores']['q']) ? 'is-invalid' : '' ?>"
                        minlength="2"
                        maxlength="100"
                        autocomplete="off"
                        aria-label="Término de búsqueda"
                    >
                    <button type="submit" class="btn btn--primary btn--md">
                        🔍 Buscar
                    </button>
                </div>
                
                <?php if (!empty($pageData['errores']['q'])): ?>
                    <div class="error-message">
                        <?= Validator::escape($pageData['errores']['q'][0]) ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if (!empty($pageData['busqueda']) && empty($pageData['errores'])): ?>
        <div class="results-section">
            <h3>
                Resultados para "<?= $pageData['busqueda'] ?>"
                <span class="results-count">(<?= $pageData['totalResultados'] ?> encontrados)</span>
            </h3>
            
            <?php if ($pageData['totalResultados'] > 0): ?>
                <div class="tabla-container">
                    <table class="tabla tabla--responsive" role="table">
                        <thead>
                            <tr>
                                <th scope="col">Nombres</th>
                                <th scope="col">Apellido Paterno</th>
                                <th scope="col">Apellido Materno</th>
                                <th scope="col">Edad</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pageData['resultados'] as $paciente): ?>
                                <tr>
                                    <td data-label="Nombres">
                                        <?= Validator::escape($paciente['nombres']) ?>
                                    </td>
                                    <td data-label="Apellido Paterno">
                                        <?= Validator::escape($paciente['apellido_paterno']) ?>
                                    </td>
                                    <td data-label="Apellido Materno">
                                        <?= Validator::escape($paciente['apellido_materno'] ?? 'N/A') ?>
                                    </td>
                                    <td data-label="Edad" class="edad-paciente">
                                        <?php
                                        if ($paciente['fecha_nacimiento']) {
                                            try {
                                                $fecha_nac = new DateTime($paciente['fecha_nacimiento']);
                                                $hoy = new DateTime();
                                                $edad = $hoy->diff($fecha_nac)->y;
                                                echo $edad . ' años';
                                            } catch (Exception $e) {
                                                echo 'N/A';
                                            }
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td data-label="Acciones">
                                        <a 
                                            href="/paciente_detalle.php?id=<?= (int)$paciente['id_paciente'] ?>" 
                                            class="btn btn--primary btn--sm"
                                            aria-label="Ver información de <?= Validator::escape($paciente['nombres']) ?>"
                                        >
                                            Ver Información
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($pageData['totalResultados'] >= 50): ?>
                    <div class="alert alert--info">
                        <strong>Nota:</strong> Se muestran los primeros 50 resultados. 
                        Si no encuentras lo que buscas, intenta ser más específico en tu búsqueda.
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state__icon">🔍</div>
                    <h4>No se encontraron resultados</h4>
                    <p>No hay pacientes que coincidan con "<?= $pageData['busqueda'] ?>".</p>
                    <div class="empty-state__actions">
                        <a href="/pacientes_nuevo.php" class="btn btn--primary">
                            + Registrar Nuevo Paciente
                        </a>
                        <a href="/pacientes.php" class="btn btn--secondary">
                            Ver Todos los Pacientes
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../src/layouts/footer.php'; ?>