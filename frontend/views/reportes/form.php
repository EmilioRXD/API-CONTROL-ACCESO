<div class="row mb-3">
    <div class="col-12">
        <h2>Generación de Reportes</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Tipo de Reporte</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo URL_BASE; ?>/public/reportes.php" method="get" id="reporteForm">
                    <input type="hidden" name="action" value="generar">
                    
                    <div class="mb-3">
                        <label for="tipo_reporte" class="form-label">Seleccione el tipo de reporte</label>
                        <select class="form-select" id="tipo_reporte" name="tipo_reporte" required>
                            <option value="">Seleccionar...</option>
                            <option value="estudiantes">Estudiantes</option>
                            <option value="estudiantes_tarjetas">Estudiantes con Tarjetas</option>
                            <option value="tarjetas">Tarjetas</option>
                            <option value="pagos">Pagos</option>
                            <option value="registros">Registros de Acceso</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="formato" class="form-label">Formato</label>
                        <select class="form-select" id="formato" name="formato" required>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel (CSV)</option>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary" id="btnContinuar">
                            <i class="fas fa-arrow-right"></i> Continuar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8" id="filtrosContainer" style="display: none;">
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Filtros del Reporte</h5>
            </div>
            <div class="card-body">
                <!-- Filtros para estudiantes -->
                <div id="filtros_estudiantes" class="filtros-reporte" style="display: none;">
                    <div class="mb-3">
                        <label for="carrera_id" class="form-label">Carrera</label>
                        <select class="form-select" id="carrera_id" name="carrera_id">
                            <option value="">Todas</option>
                            <?php foreach ($carreras as $carrera): ?>
                            <option value="<?php echo $carrera['id']; ?>"><?php echo htmlspecialchars($carrera['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="limit" class="form-label">Límite de registros</label>
                        <input type="number" class="form-control" id="limit_estudiantes" name="limit" min="1" value="100">
                    </div>
                </div>
                
                <!-- Filtros para estudiantes con tarjetas -->
                <div id="filtros_estudiantes_tarjetas" class="filtros-reporte" style="display: none;">
                    <div class="mb-3">
                        <label for="carrera_id_tarjetas" class="form-label">Carrera</label>
                        <select class="form-select" id="carrera_id_tarjetas" name="carrera_id">
                            <option value="">Todas</option>
                            <?php foreach ($carreras as $carrera): ?>
                            <option value="<?php echo $carrera['id']; ?>"><?php echo htmlspecialchars($carrera['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tiene_tarjeta" class="form-label">Estado de Tarjeta</label>
                        <select class="form-select" id="tiene_tarjeta" name="tiene_tarjeta">
                            <option value="">Todos</option>
                            <option value="1">Con Tarjeta</option>
                            <option value="0">Sin Tarjeta</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="limit_estudiantes_tarjetas" class="form-label">Límite de registros</label>
                        <input type="number" class="form-control" id="limit_estudiantes_tarjetas" name="limit" min="1" value="100">
                    </div>
                </div>
                
                <!-- Filtros para tarjetas -->
                <div id="filtros_tarjetas" class="filtros-reporte" style="display: none;">
                    <div class="mb-3">
                        <label for="activa" class="form-label">Estado</label>
                        <select class="form-select" id="activa" name="activa">
                            <option value="">Todos</option>
                            <option value="1">Activas</option>
                            <option value="0">Inactivas</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="limit" class="form-label">Límite de registros</label>
                        <input type="number" class="form-control" id="limit_tarjetas" name="limit" min="1" value="100">
                    </div>
                </div>
                
                <!-- Filtros para pagos -->
                <div id="filtros_pagos" class="filtros-reporte" style="display: none;">
                    <div class="mb-3">
                        <label for="estado_pago" class="form-label">Estado</label>
                        <select class="form-select" id="estado_pago" name="estado">
                            <option value="">Todos</option>
                            <option value="PENDIENTE">Pendientes</option>
                            <option value="PAGADO">Pagados</option>
                            <option value="VENCIDO">Vencidos</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_inicio_pago" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio_pago" name="fecha_inicio">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_fin_pago" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin_pago" name="fecha_fin">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="limit" class="form-label">Límite de registros</label>
                        <input type="number" class="form-control" id="limit_pagos" name="limit" min="1" value="100">
                    </div>
                </div>
                
                <!-- Filtros para registros -->
                <div id="filtros_registros" class="filtros-reporte" style="display: none;">
                    <div class="mb-3">
                        <label for="tipo_registro" class="form-label">Tipo</label>
                        <select class="form-select" id="tipo_registro" name="tipo">
                            <option value="">Todos</option>
                            <option value="ENTRADA">Entrada</option>
                            <option value="SALIDA">Salida</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="controlador_id" class="form-label">Controlador</label>
                        <select class="form-select" id="controlador_id" name="controlador_id">
                            <option value="">Todos</option>
                            <?php foreach ($controladores as $controlador): ?>
                            <option value="<?php echo $controlador['id']; ?>"><?php echo htmlspecialchars($controlador['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_inicio_registro" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio_registro" name="fecha_inicio">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_fin_registro" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin_registro" name="fecha_fin">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="limit" class="form-label">Límite de registros</label>
                        <input type="number" class="form-control" id="limit_registros" name="limit" min="1" value="100">
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-success" form="reporteForm">
                        <i class="fas fa-file-download"></i> Generar Reporte
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/ocultar filtros según el tipo de reporte
    document.getElementById('btnContinuar').addEventListener('click', function() {
        const tipoReporte = document.getElementById('tipo_reporte').value;
        if (!tipoReporte) {
            alert('Por favor, seleccione un tipo de reporte.');
            return;
        }
        
        // Ocultar todos los filtros
        document.querySelectorAll('.filtros-reporte').forEach(function(el) {
            el.style.display = 'none';
        });
        
        // Mostrar filtros correspondientes al tipo de reporte
        document.getElementById('filtros_' + tipoReporte).style.display = 'block';
        document.getElementById('filtrosContainer').style.display = 'block';
    });
});
</script>
