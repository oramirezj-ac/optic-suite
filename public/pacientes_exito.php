<?php
require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <div class="confirmacion-container">
        <div class="confirmacion-icono">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
        </div>
        <h2>¡Paciente Registrado Exitosamente!</h2>
        <p>El registro del paciente ha sido guardado en la base de datos.</p>
        <div class="confirmacion-acciones">
            <a href="/pacientes_nuevo.php" class="boton boton-primario">Registrar Nuevo Paciente</a>
            <a href="/pacientes.php" class="boton boton-secundario">Pacientes</a>
            <a href="/index.php" class="boton boton-secundario">Regresar al Inicio</a>
        </div>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>