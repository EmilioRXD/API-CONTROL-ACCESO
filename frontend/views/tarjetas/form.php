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
                <input type="text" class="form-control" id="estudiante_cedula" name="estudiante_cedula" 
                       value="<?php echo isset($tarjeta['estudiante_cedula']) ? htmlspecialchars($tarjeta['estudiante_cedula']) : ''; ?>" required>
                <small class="form-text text-muted">Ingrese la cédula del estudiante al que se asignará la tarjeta</small>
            </div>
            
            <div class="mb-3">
                <label for="mac_escritor" class="form-label">Dispositivo Escritor</label>
                <select class="form-select" id="mac_escritor" name="mac_escritor" required>
                    <option value="">Seleccione un dispositivo escritor</option>
                    <?php foreach ($escritores as $escritor): ?>
                    <option value="<?php echo htmlspecialchars($escritor['mac']); ?>"
                            <?php echo (isset($tarjeta['mac_escritor']) && $tarjeta['mac_escritor'] == $escritor['mac']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($escritor['nombre'] . ' (' . $escritor['mac'] . ')'); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">Seleccione el dispositivo que escribirá la tarjeta</small>
            </div>
            <?php else: ?>
            <div class="mb-3">
                <label class="form-label">Estudiante</label>
                <input type="text" class="form-control" readonly
                       value="<?php 
                              echo isset($tarjeta['nombre_estudiante']) && isset($tarjeta['apellido_estudiante']) ? 
                                   htmlspecialchars($tarjeta['nombre_estudiante'] . ' ' . $tarjeta['apellido_estudiante']) : 
                                   'Cédula: ' . htmlspecialchars($tarjeta['estudiante_cedula']); 
                              ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Serial de Tarjeta</label>
                <input type="text" class="form-control" readonly
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
                <button type="submit" class="btn btn-primary">
                    <?php echo $accion === 'create' ? 'Asignar Tarjeta' : 'Actualizar Tarjeta'; ?>
                </button>
            </div>
        </form>
    </div>
</div>
