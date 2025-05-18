<div class="row mb-3">
    <div class="col-12">
        <h2><?php echo $accion === 'create' ? 'Nuevo Controlador' : 'Editar Controlador'; ?></h2>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Formulario de Controlador</h5>
    </div>
    <div class="card-body">
        <form action="<?php echo URL_BASE; ?>/public/controladores.php?action=<?php echo $accion === 'create' ? 'store' : 'update&id=' . $controlador['id']; ?>" 
              method="post" id="controladorForm">
            
            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" 
                       value="<?php echo isset($controlador['nombre']) ? htmlspecialchars($controlador['nombre']) : ''; ?>" required>
                <small class="form-text text-muted">Nombre descriptivo del controlador</small>
            </div>
            
            <div class="mb-3">
                <label for="mac" class="form-label">Dirección MAC</label>
                <input type="text" class="form-control" id="mac" name="mac" 
                       value="<?php echo isset($controlador['mac']) ? htmlspecialchars($controlador['mac']) : ''; ?>" 
                       pattern="^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$" required>
                <small class="form-text text-muted">Formato: XX:XX:XX:XX:XX:XX</small>
            </div>
            
            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo</label>
                <select class="form-select" id="tipo" name="tipo" required>
                    <option value="">Seleccionar tipo</option>
                    <option value="READER" <?php echo (isset($controlador['tipo']) && $controlador['tipo'] === 'READER') ? 'selected' : ''; ?>>Lector</option>
                    <option value="WRITER" <?php echo (isset($controlador['tipo']) && $controlador['tipo'] === 'WRITER') ? 'selected' : ''; ?>>Escritor</option>
                </select>
                <small class="form-text text-muted">Seleccione el tipo de controlador</small>
            </div>
            
            <div class="mb-3">
                <label for="ubicacion" class="form-label">Ubicación</label>
                <input type="text" class="form-control" id="ubicacion" name="ubicacion" 
                       value="<?php echo isset($controlador['ubicacion']) ? htmlspecialchars($controlador['ubicacion']) : ''; ?>" required>
                <small class="form-text text-muted">Ubicación física del controlador</small>
            </div>
            
            <?php if ($accion === 'edit' && isset($controlador['activo'])): ?>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="activo" name="activo" value="1" 
                       <?php echo isset($controlador['activo']) && $controlador['activo'] ? 'checked' : ''; ?>>
                <label class="form-check-label" for="activo">Controlador activo</label>
            </div>
            <?php endif; ?>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo URL_BASE; ?>/public/controladores.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <?php echo $accion === 'create' ? 'Guardar' : 'Actualizar'; ?>
                </button>
            </div>
        </form>
    </div>
</div>
