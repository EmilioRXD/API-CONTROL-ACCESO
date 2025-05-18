<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Controladores</h2>
    <a href="<?php echo URL_BASE; ?>/public/controladores.php?action=create" class="btn btn-success">
        <i class="fas fa-plus"></i> Nuevo Controlador
    </a>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Listado de Controladores</h5>
    </div>
    <div class="card-body">
        <!-- Filtros de búsqueda -->
        <div class="row mb-3">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" id="filtro" class="form-control" placeholder="Buscar controlador..." onkeyup="filtrarTabla()">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <form action="<?php echo URL_BASE; ?>/public/controladores.php" method="get" class="d-flex">
                    <select name="tipo" class="form-select">
                        <option value="">Todos los tipos</option>
                        <option value="READER" <?php echo isset($_GET['tipo']) && $_GET['tipo'] === 'READER' ? 'selected' : ''; ?>>Lectores</option>
                        <option value="WRITER" <?php echo isset($_GET['tipo']) && $_GET['tipo'] === 'WRITER' ? 'selected' : ''; ?>>Escritores</option>
                    </select>
                    <button type="submit" class="btn btn-primary ms-2">Filtrar</button>
                </form>
            </div>
        </div>
        
        <!-- Tabla de controladores -->
        <?php if (count($controladores) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>MAC</th>
                        <th>Tipo</th>
                        <th>Ubicación</th>
                        <th>Estado</th>
                        <th class="actions-column">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($controladores as $controlador): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($controlador['id']); ?></td>
                        <td><?php echo htmlspecialchars($controlador['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($controlador['mac']); ?></td>
                        <td>
                            <?php if ($controlador['tipo'] === 'READER'): ?>
                            <span class="badge bg-info">Lector</span>
                            <?php elseif ($controlador['tipo'] === 'WRITER'): ?>
                            <span class="badge bg-warning">Escritor</span>
                            <?php else: ?>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($controlador['tipo']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($controlador['ubicacion']); ?></td>
                        <td>
                            <?php if (isset($controlador['activo']) && $controlador['activo']): ?>
                            <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                            <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo URL_BASE; ?>/public/controladores.php?action=view&id=<?php echo $controlador['id']; ?>" 
                               class="btn btn-sm btn-info" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo URL_BASE; ?>/public/controladores.php?action=edit&id=<?php echo $controlador['id']; ?>" 
                               class="btn btn-sm btn-warning" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <?php if (isset($controlador['activo'])): ?>
                                <?php if ($controlador['activo']): ?>
                                <a href="<?php echo URL_BASE; ?>/public/controladores.php?action=desactivar&id=<?php echo $controlador['id']; ?>" 
                                   class="btn btn-sm btn-secondary" title="Desactivar"
                                   onclick="return confirm('¿Está seguro que desea desactivar este controlador?');">
                                    <i class="fas fa-power-off"></i>
                                </a>
                                <?php else: ?>
                                <a href="<?php echo URL_BASE; ?>/public/controladores.php?action=activar&id=<?php echo $controlador['id']; ?>" 
                                   class="btn btn-sm btn-success" title="Activar"
                                   onclick="return confirm('¿Está seguro que desea activar este controlador?');">
                                    <i class="fas fa-power-off"></i>
                                </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <a href="<?php echo URL_BASE; ?>/public/controladores.php?action=delete&id=<?php echo $controlador['id']; ?>" 
                               class="btn btn-sm btn-danger" title="Eliminar"
                               onclick="return confirmarEliminacion('<?php echo $controlador['id']; ?>', 'controlador');">
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
            No hay controladores registrados.
        </div>
        <?php endif; ?>
    </div>
</div>
