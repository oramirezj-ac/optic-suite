<?php
require_once '../src/layouts/header.php';
require_once '../src/layouts/sidebar.php';
?>

<main class="contenido-principal">
    <h2>Registrar Nuevo Paciente</h2>
    <div class="calculadora-fecha">
        <h4>Calculadora de Fecha de Nacimiento (para expedientes antiguos)</h4>
        <div class="calculadora-inputs">
            <input type="number" id="edad_historica" placeholder="Edad del Paciente">
            <input type="number" id="ano_expediente" placeholder="Año del Expediente">
            <button type="button" id="btn_calcular_fecha" class="boton boton--secundario boton--sm">Calcular</button>
        </div>
    </div>
    <div class="formulario-container">
        <form action="/pacientes_crear.php" method="POST" class="formulario-grid">
            <div class="campo">
                <label for="nombres">Nombres:</label>
                <input type="text" id="nombres" name="nombres" required>
            </div>
            <div class="campo">
                <label for="apellido_paterno">Apellido Paterno:</label>
                <input type="text" id="apellido_paterno" name="apellido_paterno">
            </div>
            <div class="campo">
                <label for="apellido_materno">Apellido Materno:</label>
                <input type="text" id="apellido_materno" name="apellido_materno">
            </div>

            <div class="campo">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">
            </div>
            <div class="campo">
                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono">
            </div>
            <div class="campo">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email">
            </div>
            
            <div class="campo">
                <label for="dp_total">DP Total (mm):</label>
                <input type="text" id="dp_total" name="dp_total">
            </div>
            <div class="campo">
                <label for="dnp_od">DNP OD (mm):</label>
                <input type="text" id="dnp_od" name="dnp_od">
            </div>
            <div class="campo">
                <label for="dnp_oi">DNP OI (mm):</label>
                <input type="text" id="dnp_oi" name="dnp_oi">
            </div>

            <div class="campo campo-full-width">
                <label for="domicilio">Domicilio:</label>
                <input type="text" id="domicilio" name="domicilio">
            </div>

            <div class="campo campo-full-width">
                <label for="antecedentes_descripcion">Antecedentes Médicos (si aplica):</label>
                <textarea id="antecedentes_descripcion" name="antecedentes_descripcion" rows="3"></textarea>
            </div>

            <div class="formulario-acciones campo-full-width">
                <div class="acciones-grupo-izquierda">
                    <button type="submit" class="boton boton--primario">Guardar Paciente</button>
                    <a href="/pacientes.php" class="boton boton--secundario">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</main>

<?php require_once '../src/layouts/footer.php'; ?>