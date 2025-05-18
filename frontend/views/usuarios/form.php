<div class="row mb-3">
    <div class="col-12">
        <h2><?php echo $accion === 'create' ? 'Nuevo Usuario' : 'Editar Usuario'; ?></h2>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Formulario de Usuario</h5>
    </div>
    <div class="card-body">
        <form action="<?php echo URL_BASE; ?>/public/usuarios.php?action=<?php echo $accion === 'create' ? 'store' : 'update&id=' . $usuario['id']; ?>" 
              method="post" id="usuarioForm">
            
            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo isset($usuario['nombre']) ? htmlspecialchars($usuario['nombre']) : ''; ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo isset($usuario['apellido']) ? htmlspecialchars($usuario['apellido']) : ''; ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="correo_electronico" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="correo_electronico" name="correo_electronico" 
                       value="<?php echo isset($usuario['correo_electronico']) ? htmlspecialchars($usuario['correo_electronico']) : ''; ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="contraseña" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="contraseña" name="contraseña" 
                       <?php echo $accion === 'create' ? 'required' : ''; ?>>
                <?php if ($accion === 'edit'): ?>
                <small class="form-text text-muted">Dejar en blanco para mantener la contraseña actual</small>
                <?php endif; ?>
            </div>
            
            <?php if ($accion === 'edit' && isset($usuario['activo'])): ?>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" 
                           <?php echo $usuario['activo'] ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="activo">
                        Usuario activo
                    </label>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo URL_BASE; ?>/public/usuarios.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <?php echo $accion === 'create' ? 'Guardar' : 'Actualizar'; ?>
                </button>
            </div>
        </form>
    </div>
</div>
