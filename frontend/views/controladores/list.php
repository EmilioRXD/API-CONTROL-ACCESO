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
                    <input type="text" id="filtro" class="form-control" placeholder="Buscar controlador..." data-table-filter="tabla-controladores">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <form action="<?php echo URL_BASE; ?>/public/controladores.php" method="get" class="d-flex">
                    <select name="funcion" class="form-select">
                        <option value="">Todas las funciones</option>
                        <option value="LECTOR" <?php echo isset($_GET['funcion']) && $_GET['funcion'] === 'LECTOR' ? 'selected' : ''; ?>>Lectores</option>
                        <option value="ESCRITOR" <?php echo isset($_GET['funcion']) && $_GET['funcion'] === 'ESCRITOR' ? 'selected' : ''; ?>>Escritores</option>
                    </select>
                    <button type="submit" class="btn btn-primary ms-2">Filtrar</button>
                </form>
            </div>
        </div>
        
        <!-- Tabla de controladores -->
        <?php if (count($controladores) > 0): ?>
        <div class="table-responsive">
            <table id="tabla-controladores" class="table paginated-table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>MAC</th>
                        <th>Ubicación</th>
                        <th>Función</th>
                        <th>Tipo de Acceso</th>
                        <th class="actions-column">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($controladores as $controlador): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($controlador['id']); ?></td>
                        <td><?php echo htmlspecialchars($controlador['mac']); ?></td>
                        <td><?php echo htmlspecialchars($controlador['ubicacion']); ?></td>
                        <td>
                            <?php if (isset($controlador['funcion'])): ?>
                            <span class="badge bg-<?php echo strtolower($controlador['funcion']) === 'lector' ? 'info' : 'warning'; ?>">
                                <?php echo htmlspecialchars($controlador['funcion']); ?>
                            </span>
                            <?php else: ?>
                            <span class="badge bg-secondary">No definida</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($controlador['tipo_acceso'])): ?>
                            <span class="badge bg-primary"><?php echo htmlspecialchars($controlador['tipo_acceso']); ?></span>
                            <?php else: ?>
                            <span class="badge bg-secondary">No definido</span>
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
