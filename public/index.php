<?php
require_once '../src/database.php';

// 1. Total de pacientes
$total_pacientes = $pdo->query("SELECT COUNT(*) FROM pacientes")->fetchColumn();

// 2. Fecha actual en español
$formatter = new IntlDateFormatter('es_MX', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
$fecha_actual = $formatter->format(time());

// --- NUEVO: Consulta para el total GLOBAL de ventas ---
$sql_ventas_global = "SELECT SUM(costo_total) as total_global FROM ventas";
$ventas_global = $pdo->query($sql_ventas_global)->fetchColumn();
if ($ventas_global === null) {
    $ventas_global = 0;
}

// 3. (Placeholder) Consultas de hoy
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
        </div>

    <h3>Métricas Generales</h3>
    <div class="dashboard-widgets">
        <div class="widget">
            <h3>Pacientes Registrados</h3>
            <p class="widget__numero"><?php echo $total_pacientes; ?></p>
        </div>
        <div class="widget">
            <h3>Ventas Globales</h3>
            <p class="widget__numero">$<?php echo number_format($ventas_global, 2); ?></p>
            <a href="/ventas_resumen.php" class="boton boton--info boton--md">Ver Resumen de Ventas</a>
        </div>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>