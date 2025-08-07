<?php
require_once '../src/database.php';

// --- Consultas para el Panel de Inicio ---
// 1. Total de pacientes (ya lo teníamos)
$total_pacientes = $pdo->query("SELECT COUNT(*) FROM pacientes")->fetchColumn();

// 2. Fecha actual en español
$formatter = new IntlDateFormatter('es_MX', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
$fecha_actual = $formatter->format(time());


// 3. (Placeholder) Consultas de hoy - A futuro se conectará a una agenda
$consultas_hoy = 0; 

require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="pagina-header">
        <div>
            <h2>Panel de Inicio</h2>
            <p class="subtitulo-pagina"><strong><?php echo ucfirst($fecha_actual); ?></strong></p>
        </div>
    </div>

    <h3>Tareas Pendientes</h3>
    <div class="dashboard-widgets">
        <div class="widget widget-accion">
            <h4>Órdenes en Laboratorio</h4>
            <p class="widget__numero">0</p> </div>
        <div class="widget widget-accion">
            <h4>Listos para Entregar</h4>
            <p class="widget__numero">0</p> </div>
        <div class="widget widget-accion">
            <h4>Consultas de Hoy</h4>
            <p class="widget__numero"><?php echo $consultas_hoy; ?></p>
        </div>
    </div>

    <h3>Métricas Generales</h3>
    <div class="dashboard-widgets">
        <div class="widget">
            <h3>Pacientes Registrados</h3>
            <p class="widget__numero"><?php echo $total_pacientes; ?></p>
        </div>
        <div class="widget">
            <h3>Ventas del Mes</h3>
            <p class="widget__numero">$ 0.00</p> </div>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>