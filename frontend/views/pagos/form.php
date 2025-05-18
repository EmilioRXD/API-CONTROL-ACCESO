<div class="row mb-3">
    <div class="col-12">
        <h2>Registrar Nuevo Pago</h2>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Formulario de Pago</h5>
    </div>
    <div class="card-body">
        <form action="<?php echo URL_BASE; ?>/public/pagos.php?action=store" method="post" id="pagoForm">
            
            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="estudiante_cedula" class="form-label">Cédula del Estudiante</label>
                <input type="text" class="form-control" id="estudiante_cedula" name="estudiante_cedula" 
                       value="<?php echo isset($pago['estudiante_cedula']) ? htmlspecialchars($pago['estudiante_cedula']) : ''; ?>" required>
                <small class="form-text text-muted">Ingrese la cédula del estudiante para el que se registrará el pago</small>
            </div>
            
            <div class="mb-3">
                <label for="cuota_id" class="form-label">Cuota</label>
                <select class="form-select" id="cuota_id" name="cuota_id" required>
                    <option value="">Seleccione una cuota</option>
                    <?php if (empty($cuotas)): ?>
                    <option value="" disabled>No hay cuotas disponibles</option>
                    <?php else: ?>
                    <?php foreach ($cuotas as $cuota): ?>
                    <option value="<?php echo $cuota['id']; ?>"
                            <?php echo (isset($pago['cuota_id']) && $pago['cuota_id'] == $cuota['id']) ? 'selected' : ''; ?>
                            data-fecha="<?php echo isset($cuota['fecha_pago']) ? $cuota['fecha_pago'] : ''; ?>">
                        <?php 
                        // Usar el nombre_cuota según la estructura real de datos
                        $descripcion = isset($cuota['nombre_cuota']) ? htmlspecialchars($cuota['nombre_cuota']) : 'Cuota';
                        
                        // Agregar fecha de pago si está disponible
                        if (isset($cuota['fecha_pago'])) {
                            $fecha_formateada = date('d/m/Y', strtotime($cuota['fecha_pago']));
                            $descripcion .= ' (Fecha: ' . $fecha_formateada . ')';
                        }
                        
                        echo $descripcion;
                        ?>
                    </option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <small class="form-text text-muted">Seleccione la cuota que se va a registrar</small>
                <div id="cuota-detalle" class="mt-2 p-2 border rounded" style="display: none;">
                    <p class="mb-1"><strong>Detalle de la cuota seleccionada:</strong></p>
                    <p class="mb-1" id="cuota-fecha">Fecha de pago: <span></span></p>
                </div>
            </div>
            
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const cuotaSelect = document.getElementById('cuota_id');
                const cuotaDetalle = document.getElementById('cuota-detalle');
                const cuotaFechaSpan = document.querySelector('#cuota-fecha span');
                
                cuotaSelect.addEventListener('change', function() {
                    if (this.value) {
                        const selectedOption = this.options[this.selectedIndex];
                        const fecha = selectedOption.dataset.fecha;
                        
                        if (fecha) {
                            // Formatear la fecha para mostrarla
                            const fechaObj = new Date(fecha);
                            const dia = fechaObj.getDate().toString().padStart(2, '0');
                            const mes = (fechaObj.getMonth() + 1).toString().padStart(2, '0');
                            const anio = fechaObj.getFullYear();
                            const fechaFormateada = `${dia}/${mes}/${anio}`;
                            
                            cuotaFechaSpan.textContent = fechaFormateada;
                        } else {
                            cuotaFechaSpan.textContent = 'No especificada';
                        }
                        
                        cuotaDetalle.style.display = 'block';
                    } else {
                        cuotaDetalle.style.display = 'none';
                    }
                });
                
                // Disparar el evento change si hay una opción ya seleccionada
                if (cuotaSelect.value) {
                    cuotaSelect.dispatchEvent(new Event('change'));
                }
            });
            </script>
            
            <div class="mb-3">
                <label for="estado" class="form-label">Estado Inicial</label>
                <select class="form-select" id="estado" name="estado" required>
                    <option value="PENDIENTE" <?php echo (isset($pago['estado']) && $pago['estado'] === 'PENDIENTE') ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="PAGADO" <?php echo (isset($pago['estado']) && $pago['estado'] === 'PAGADO') ? 'selected' : ''; ?>>Pagado</option>
                    <option value="VENCIDO" <?php echo (isset($pago['estado']) && $pago['estado'] === 'VENCIDO') ? 'selected' : ''; ?>>Vencido</option>
                </select>
                <small class="form-text text-muted">Seleccione el estado inicial del pago</small>
                <div class="form-text">
                    <span class="badge bg-warning text-dark">PENDIENTE</span>: Pago registrado pero no completado.<br>
                    <span class="badge bg-success">PAGADO</span>: Pago completado satisfactoriamente.<br>
                    <span class="badge bg-danger">VENCIDO</span>: Pago que superó su fecha límite sin ser completado.
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo URL_BASE; ?>/public/pagos.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Registrar Pago
                </button>
            </div>
        </form>
    </div>
</div>
