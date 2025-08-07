document.addEventListener('DOMContentLoaded', function() {
    // Selector del botón para calcular
    const btnCalcular = document.getElementById('btn_calcular_fecha');

    if (btnCalcular) {
        btnCalcular.addEventListener('click', function() {
            // Obtener los valores de los campos de la calculadora
            const edad = parseInt(document.getElementById('edad_historica').value);
            const ano = parseInt(document.getElementById('ano_expediente').value);
            
            // El campo principal de fecha de nacimiento
            const campoFechaNacimiento = document.getElementById('fecha_nacimiento');

            // Validar que se hayan ingresado ambos datos
            if (!isNaN(edad) && !isNaN(ano)) {
                const anoNacimiento = ano - edad;
                // Formatear la fecha como YYYY-MM-DD y asignarla al campo principal
                campoFechaNacimiento.value = `${anoNacimiento}-01-01`;
            } else {
                alert('Por favor, ingresa la edad y el año del expediente.');
            }
        });
    }
});