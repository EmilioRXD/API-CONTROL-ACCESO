<div class="row mb-3">
    <div class="col-12">
        <h2>Detalles del Usuario</h2>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Información del Usuario</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($usuario['id']); ?></p>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?></p>
                <p><strong>Apellido:</strong> <?php echo htmlspecialchars($usuario['apellido']); ?></p>
                <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($usuario['correo_electronico']); ?></p>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="<?php echo URL_BASE; ?>/public/usuarios.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="<?php echo URL_BASE; ?>/public/usuarios.php?action=edit&id=<?php echo $usuario['id']; ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            
            <?php if (isset($usuario['activo'])): ?>
                <?php if ($usuario['activo']): ?>
                <a href="<?php echo URL_BASE; ?>/public/usuarios.php?action=desactivar&id=<?php echo $usuario['id']; ?>" 
                   class="btn btn-secondary" 
                   onclick="return confirm('¿Está seguro que desea desactivar este usuario?');">
                    <i class="fas fa-lock"></i> Desactivar
                </a>
                <?php else: ?>
                <a href="<?php echo URL_BASE; ?>/public/usuarios.php?action=activar&id=<?php echo $usuario['id']; ?>" 
                   class="btn btn-success" 
                   onclick="return confirm('¿Está seguro que desea activar este usuario?');">
                    <i class="fas fa-lock-open"></i> Activar
                </a>
                <?php endif; ?>
            <?php endif; ?>
            
            <a href="<?php echo URL_BASE; ?>/public/usuarios.php?action=delete&id=<?php echo $usuario['id']; ?>" 
               class="btn btn-danger" 
               onclick="return confirmarEliminacion('<?php echo $usuario['id']; ?>', 'usuario');">
                <i class="fas fa-trash"></i> Eliminar
            </a>
        </div>
    </div>
</div>
