<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Usuarios</h2>
    <a href="<?php echo URL_BASE; ?>/public/usuarios.php?action=create" class="btn btn-success">
        <i class="fas fa-plus"></i> Nuevo Usuario
    </a>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Listado de Usuarios</h5>
    </div>
    <div class="card-body">
        <!-- Filtro de búsqueda -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" id="filtro" class="form-control" placeholder="Buscar usuario..." onkeyup="filtrarTabla()">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Tabla de usuarios -->
        <?php if (count($usuarios) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Correo Electrónico</th>
                        <th>Estado</th>
                        <th class="actions-column">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['apellido']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['correo_electronico']); ?></td>
                        <td>
                            <?php if (isset($usuario['activo']) && $usuario['activo']): ?>
                            <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                            <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo URL_BASE; ?>/public/usuarios.php?action=view&id=<?php echo $usuario['id']; ?>" 
                               class="btn btn-sm btn-info" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo URL_BASE; ?>/public/usuarios.php?action=edit&id=<?php echo $usuario['id']; ?>" 
                               class="btn btn-sm btn-warning" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <?php if (isset($usuario['activo'])): ?>
                                <?php if ($usuario['activo']): ?>
                                <a href="<?php echo URL_BASE; ?>/public/usuarios.php?action=desactivar&id=<?php echo $usuario['id']; ?>" 
                                   class="btn btn-sm btn-secondary" title="Desactivar"
                                   onclick="return confirm('¿Está seguro que desea desactivar este usuario?');">
                                    <i class="fas fa-lock"></i>
                                </a>
                                <?php else: ?>
                                <a href="<?php echo URL_BASE; ?>/public/usuarios.php?action=activar&id=<?php echo $usuario['id']; ?>" 
                                   class="btn btn-sm btn-success" title="Activar"
                                   onclick="return confirm('¿Está seguro que desea activar este usuario?');">
                                    <i class="fas fa-lock-open"></i>
                                </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <a href="<?php echo URL_BASE; ?>/public/usuarios.php?action=delete&id=<?php echo $usuario['id']; ?>" 
                               class="btn btn-sm btn-danger" title="Eliminar"
                               onclick="return confirmarEliminacion('<?php echo $usuario['id']; ?>', 'usuario');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            No hay usuarios registrados.
        </div>
        <?php endif; ?>
    </div>
</div>
