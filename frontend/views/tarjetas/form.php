<div class="row mb-3">
    <div class="col-12">
        <h2><?php echo $accion === 'create' ? 'Asignar Nueva Tarjeta' : 'Editar Tarjeta'; ?></h2>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Formulario de Tarjeta</h5>
    </div>
    <div class="card-body">
        <form action="<?php echo URL_BASE; ?>/public/tarjetas.php?action=<?php echo $accion === 'create' ? 'store' : 'update&id=' . $tarjeta['id']; ?>" 
              method="post" id="tarjetaForm">
            
            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($accion === 'create'): ?>
            <div class="mb-3">
                <label for="estudiante_cedula" class="form-label">Cédula del Estudiante</label>
                <?php if (isset($tarjeta['estudiante_cedula']) && !empty($tarjeta['estudiante_cedula'])): ?>
                    <input type="text" class="form-control bg-light text-muted" id="estudiante_cedula" 
                           value="<?php echo htmlspecialchars($tarjeta['estudiante_cedula']); ?>" 
                           readonly disabled>
                    <input type="hidden" name="estudiante_cedula" value="<?php echo htmlspecialchars($tarjeta['estudiante_cedula']); ?>">
                <?php else: ?>
                    <input type="text" class="form-control" id="estudiante_cedula" name="estudiante_cedula" 
                           value="" required>
                    <small class="form-text text-muted">
                        Ingrese la cédula del estudiante al que se asignará la tarjeta
                    </small>
                <?php endif; ?>
            </div>
            
            <div class="mb-3">
                <label for="mac_escritor" class="form-label">Dispositivo Escritor</label>
                <!-- Selector de dispositivos escritores -->
                <select class="form-select" id="mac_escritor" name="mac_escritor" required>
                    <option value="">Seleccione un dispositivo escritor</option>
                    <?php 
                    // Obtener controladores disponibles desde la API
                    $controladores = getControladores();
                    
                    // Filtrar solo los controladores con función ESCRITOR
                    $escritores = array_filter($controladores, function($controlador) {
                        return isset($controlador['funcion']) && $controlador['funcion'] === 'ESCRITOR';
                    });
                    
                    // Mostrar opciones de controladores
                    foreach ($escritores as $controlador): 
                    ?>
                        <option value="<?php echo htmlspecialchars($controlador['mac']); ?>">
                            <?php echo htmlspecialchars($controlador['ubicacion']); ?>
                        </option>
                    <?php endforeach; ?>
                    
                    <?php if (empty($escritores)): ?>
                        <option value="" disabled>No hay dispositivos escritores disponibles</option>
                    <?php endif; ?>
                </select>
                <small class="form-text text-muted">Seleccione el dispositivo que escribirá la tarjeta</small>
            </div>
            <?php else: ?>
            <div class="mb-3">
                <label class="form-label">Estudiante</label>
                <input type="text" class="form-control bg-light text-muted" readonly disabled
                       value="<?php 
                              echo isset($tarjeta['nombre_estudiante']) && isset($tarjeta['apellido_estudiante']) ? 
                                   htmlspecialchars($tarjeta['nombre_estudiante'] . ' ' . $tarjeta['apellido_estudiante']) : 
                                   'Cédula: ' . htmlspecialchars($tarjeta['estudiante_cedula']); 
                              ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Serial de Tarjeta</label>
                <input type="text" class="form-control bg-light text-muted" readonly disabled
                       value="<?php echo htmlspecialchars($tarjeta['serial']); ?>">
            </div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="fecha_emision" class="form-label">Fecha de Emisión</label>
                <input type="date" class="form-control" id="fecha_emision" name="fecha_emision" 
                       value="<?php echo isset($tarjeta['fecha_emision']) ? htmlspecialchars($tarjeta['fecha_emision']) : date('Y-m-d'); ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="fecha_expiracion" class="form-label">Fecha de Expiración</label>
                <input type="date" class="form-control" id="fecha_expiracion" name="fecha_expiracion" 
                       value="<?php echo isset($tarjeta['fecha_expiracion']) ? htmlspecialchars($tarjeta['fecha_expiracion']) : date('Y-m-d', strtotime('+1 year')); ?>" required>
            </div>
            
            <?php if ($accion === 'edit' && isset($tarjeta['activa'])): ?>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="activa" name="activa" value="1" 
                           <?php echo isset($tarjeta['activa']) && $tarjeta['activa'] ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="activa">
                        Tarjeta activa
                    </label>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo URL_BASE; ?>/public/tarjetas.php" class="btn btn-secondary">Cancelar</a>
                <?php if ($accion === 'create'): ?>
                <button type="button" id="btnAsignar" class="btn btn-primary">Asignar Tarjeta</button>
                <?php else: ?>
                <button type="submit" class="btn btn-primary">Actualizar Tarjeta</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Modal de proceso de asignación de tarjeta -->
<div class="modal fade" id="asignacionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="asignacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="asignacionModalLabel">Asignando tarjeta</h5>
            </div>
            <div class="modal-body text-center">
                <div id="procesando" class="mb-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Procesando...</span>
                    </div>
                    <h5>Acerque la tarjeta al dispositivo escritor</h5>
                    <p class="text-muted">Esperando respuesta del servidor...</p>
                    <div class="progress mt-3">
                        <div id="contador-progress" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                    </div>
                    <p class="mt-2">Tiempo restante: <span id="contador">30</span> segundos</p>
                </div>
                
                <div id="exito" class="d-none">
                    <div class="mb-3 text-success">
                        <i class="fas fa-check-circle fa-5x"></i>
                    </div>
                    <h4 class="text-success">¡Tarjeta asignada correctamente!</h4>
                    <p>La tarjeta ha sido asignada al estudiante exitosamente.</p>
                </div>
                
                <div id="error" class="d-none">
                    <div class="mb-3 text-danger">
                        <i class="fas fa-times-circle fa-5x"></i>
                    </div>
                    <h4 class="text-danger">Error en la asignación</h4>
                    <p id="error-mensaje">No se pudo completar la asignación de la tarjeta.</p>
                </div>
            </div>
            <div class="modal-footer">
                <div id="footer-procesando">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
                <div id="footer-completado" class="d-none">
                    <a href="<?php echo URL_BASE; ?>/public/estudiantes.php?action=view&cedula=" class="btn btn-primary" id="btn-perfil-estudiante">Ver perfil del estudiante</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para manejar la asignación de tarjeta con el modal y contador -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario
    const form = document.getElementById('tarjetaForm');
    const btnAsignar = document.getElementById('btnAsignar');
    
    if (!btnAsignar) return;
    
    // Elementos del modal
    const modal = new bootstrap.Modal(document.getElementById('asignacionModal'));
    const procesando = document.getElementById('procesando');
    const exito = document.getElementById('exito');
    const error = document.getElementById('error');
    const errorMensaje = document.getElementById('error-mensaje');
    const footerProcesando = document.getElementById('footer-procesando');
    const footerCompletado = document.getElementById('footer-completado');
    const contadorElement = document.getElementById('contador');
    const contadorProgress = document.getElementById('contador-progress');
    
    // Variables para el contador
    let tiempoRestante = 30;
    let intervalId = null;
    let peticionEnviada = false;
    let tiempoAgotado = false; // Nueva variable para controlar si el tiempo se ha agotado
    
    // Función para iniciar el contador
    function iniciarContador() {
        tiempoRestante = 30;
        contadorElement.textContent = tiempoRestante;
        contadorProgress.style.width = '100%';
        
        // Mostrar y ocultar los elementos correspondientes
        procesando.classList.remove('d-none');
        exito.classList.add('d-none');
        error.classList.add('d-none');
        footerProcesando.classList.remove('d-none');
        footerCompletado.classList.add('d-none');
        
        // Iniciar el temporizador
        intervalId = setInterval(function() {
            tiempoRestante--;
            contadorElement.textContent = tiempoRestante;
            const porcentaje = (tiempoRestante / 30) * 100;
            contadorProgress.style.width = porcentaje + '%';
            
            if (tiempoRestante <= 0) {
                clearInterval(intervalId);
                // Marcar que el tiempo se ha agotado
                tiempoAgotado = true;
                
                // Mostrar solo mensaje de error y ocultar cualquier otro mensaje
                procesando.classList.add('d-none');
                exito.classList.add('d-none');
                error.classList.remove('d-none');
                errorMensaje.textContent = 'Tiempo de espera agotado. La operación no pudo completarse.';
                footerProcesando.classList.add('d-none');
                footerCompletado.classList.remove('d-none');
            }
        }, 1000);
    }
    
    // Función para mostrar el mensaje de éxito
    function mostrarExito() {
        clearInterval(intervalId);
        procesando.classList.add('d-none');
        exito.classList.remove('d-none');
        footerProcesando.classList.add('d-none');
        footerCompletado.classList.remove('d-none');
        
        // Actualizar el enlace al perfil del estudiante con la cédula
        const cedulaInput = document.getElementById('estudiante_cedula');
        const btnPerfilEstudiante = document.getElementById('btn-perfil-estudiante');
        
        if (cedulaInput && btnPerfilEstudiante) {
            // Si el input está visible, tomar el valor directamente
            let cedula = cedulaInput.value;
            
            // Si el input está como hidden (cuando viene pre-llenado)
            if (cedulaInput.type === 'hidden') {
                const hiddenCedula = document.querySelector('input[name="estudiante_cedula"]');
                if (hiddenCedula) {
                    cedula = hiddenCedula.value;
                }
            }
            
            if (cedula) {
                // Actualizar el href del botón para incluir la cédula del estudiante
                const currentHref = btnPerfilEstudiante.getAttribute('href');
                btnPerfilEstudiante.setAttribute('href', currentHref + cedula);
            }
        }
    }
    
    // Función para mostrar el mensaje de error
    function mostrarError(mensaje) {
        clearInterval(intervalId);
        procesando.classList.add('d-none');
        error.classList.remove('d-none');
        errorMensaje.textContent = mensaje || 'No se pudo completar la asignación de la tarjeta.';
        footerProcesando.classList.add('d-none');
        footerCompletado.classList.remove('d-none');
    }
    
    // Evento click del botón asignar
    btnAsignar.addEventListener('click', function() {
        // Validar el formulario antes de mostrar el modal
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Mostrar el modal y comenzar el contador
        modal.show();
        iniciarContador();
        peticionEnviada = false;
        tiempoAgotado = false; // Reiniciar la variable de tiempo agotado
        
        // Enviar el formulario mediante AJAX
        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            peticionEnviada = true;
            
            // Si el tiempo se ha agotado, no hacemos nada con la respuesta
            if (tiempoAgotado) {
                return;
            }
            
            // Verificar el tipo de contenido de la respuesta
            const contentType = response.headers.get('content-type');
            
            if (response.ok && (response.status === 200 || response.status === 201)) {
                // La operación fue exitosa
                mostrarExito();
                return;
            } else {
                // Error en la respuesta
                if (contentType && contentType.includes('application/json')) {
                    // Si es JSON, intentamos extraer el mensaje de error
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || 'Error en la operación');
                    });
                } else {
                    // Si no es JSON, simplemente informamos del error
                    throw new Error('La operación no pudo completarse');
                }
            }
        })
        .catch(err => {
            // Si el tiempo se ha agotado, no mostramos ningún otro error
            if (!tiempoAgotado) {
                // Procesar el error
                mostrarError(err.message);
            }
        });
    });
});
</script>
