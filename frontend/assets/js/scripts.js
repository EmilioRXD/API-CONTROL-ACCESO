// Scripts personalizados para el Sistema de Control de Acceso y Pagos

// Función para confirmar eliminación
function confirmarEliminacion(id, tipo) {
    return confirm(`¿Está seguro que desea eliminar este ${tipo}? Esta acción no se puede deshacer.`);
}

// Inicializar tooltips de Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-cerrar alertas después de 5 segundos
    window.setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});

// Validación de formularios
function validarFormularioEstudiante() {
    let cedula = document.getElementById('cedula').value;
    if (!/^\d+$/.test(cedula)) {
        alert('La cédula debe contener solo números');
        return false;
    }
    return true;
}

// Función para filtrar tablas
function filtrarTabla() {
    const input = document.getElementById('filtro');
    const filtro = input.value.toUpperCase();
    const tabla = document.querySelector('table');
    const filas = tabla.getElementsByTagName('tr');

    for (let i = 1; i < filas.length; i++) {
        let mostrar = false;
        const celdas = filas[i].getElementsByTagName('td');
        
        for (let j = 0; j < celdas.length; j++) {
            const texto = celdas[j].textContent || celdas[j].innerText;
            if (texto.toUpperCase().indexOf(filtro) > -1) {
                mostrar = true;
                break;
            }
        }
        
        filas[i].style.display = mostrar ? '' : 'none';
    }
}
