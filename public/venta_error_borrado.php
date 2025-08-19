<?php
$id_venta = $_GET['id_venta'] ?? 0;
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
        <h2>No se Puede Eliminar la Venta</h2>
        <p>Esta venta no se puede eliminar porque tiene **pagos (anticipo o abonos) registrados**.</p>
        <p class="alerta-permanente">Primero debes eliminar todos los pagos asociados desde el historial de pagos.</p>
        
        <div class="confirmacion-acciones">
            <a href="/venta_pagos_detalle.php?id_venta=<?php echo $id_venta; ?>" class="boton boton--info">Ir al Historial de Pagos</a>
            <a href="/venta_detalle.php?id=<?php echo $id_venta; ?>" class="boton boton--secundario">Regresar</a>
        </div>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>