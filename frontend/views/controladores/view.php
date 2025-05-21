<div class="row mb-3">
    <div class="col-12">
        <h2>Detalles del Controlador</h2>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Información del Controlador</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($controlador['id']); ?></p>
                <p><strong>Dirección MAC:</strong> <?php echo htmlspecialchars($controlador['mac']); ?></p>
                <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($controlador['ubicacion']); ?></p>
                <p><strong>Función:</strong> 
                    <?php if (isset($controlador['funcion'])): ?>
                    <span class="badge bg-<?php echo strtolower($controlador['funcion']) === 'lector' ? 'info' : 'warning'; ?>">
                        <?php echo htmlspecialchars($controlador['funcion']); ?>
                    </span>
                    <?php else: ?>
                    <span class="badge bg-secondary">No definida</span>
                    <?php endif; ?>
                </p>
                <p><strong>Tipo de Acceso:</strong> 
                    <?php if (isset($controlador['tipo_acceso'])): ?>
                    <span class="badge bg-primary"><?php echo htmlspecialchars($controlador['tipo_acceso']); ?></span>
                    <?php else: ?>
                    <span class="badge bg-secondary">No definido</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="<?php echo URL_BASE; ?>/public/controladores.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="<?php echo URL_BASE; ?>/public/controladores.php?action=edit&id=<?php echo $controlador['id']; ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            
            <a href="<?php echo URL_BASE; ?>/public/controladores.php?action=delete&id=<?php echo $controlador['id']; ?>" 
               class="btn btn-danger" 
               onclick="return confirmarEliminacion('<?php echo $controlador['id']; ?>', 'controlador');">
                <i class="fas fa-trash"></i> Eliminar
            </a>
        </div>
    </div>
</div>

<!-- La sección de historial de registros de acceso ha sido eliminada según lo solicitado -->
