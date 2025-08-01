<?php require_once '../src/layouts/header.php'; ?>

<?php require_once '../src/layouts/sidebar.php'; ?>

<main class="contenido-principal">
    <h2>Dashboard</h2>
    <p>Resumen general de la óptica.</p>

    <div class="dashboard-widgets">
        <div class="widget">
            <h3>Pacientes Registrados</h3>
            <p class="widget__numero">1,234</p>
        </div>
        <div class="widget">
            <h3>Ventas del Mes</h3>
            <p class="widget__numero">$ 45,678</p>
        </div>
        <div class="widget">
            <h3>Consultas Hoy</h3>
            <p class="widget__numero">5</p>
        </div>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>