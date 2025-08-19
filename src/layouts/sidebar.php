<?php
// Obtenemos la URL actual para saber qué enlace resaltar
$currentPage = $_SERVER['REQUEST_URI'];
?>
<aside class="sidebar">
    <nav class="sidebar__nav">
        <a href="/index.php" 
           class="<?php echo ($currentPage == '/index.php') ? 'activo' : ''; ?>">
           Dashboard
        </a>
        <a href="/pacientes.php" 
           class="<?php echo (str_starts_with($currentPage, '/paciente') || str_starts_with($currentPage, '/consulta')) ? 'activo' : ''; ?>">
           Pacientes
        </a>
        <a href="/ventas.php"
           class="<?php echo (str_starts_with($currentPage, '/venta')) ? 'activo' : ''; ?>">
           Ventas
        </a>
        <a href="#">Catálogos</a>
    </nav>
</aside>