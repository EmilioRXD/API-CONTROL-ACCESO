<div class="mb-4">
    <h2>Gestión de Tarjetas</h2>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Listado de Tarjetas</h5>
    </div>
    <div class="card-body">
        <!-- Filtro de búsqueda -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" id="filtro" class="form-control" placeholder="Buscar tarjeta..." onkeyup="filtrarTabla()">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Tabla de tarjetas -->
        <?php if (count($tarjetas) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Serial</th>
                        <th>Estudiante</th>
                        <th>Fecha Emisión</th>
                        <th>Fecha Expiración</th>
                        <th>Estado</th>
                        <th class="actions-column">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tarjetas as $tarjeta): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tarjeta['id']); ?></td>
                        <td><?php echo htmlspecialchars($tarjeta['serial']); ?></td>
                        <td>
                            <?php 
                            $nombreEstudiante = '';
                            if (isset($tarjeta['nombre_estudiante']) && isset($tarjeta['apellido_estudiante'])) {
                                $nombreEstudiante = $tarjeta['nombre_estudiante'] . ' ' . $tarjeta['apellido_estudiante'];
                            } elseif (isset($tarjeta['estudiante_cedula'])) {
                                $nombreEstudiante = 'Cédula: ' . $tarjeta['estudiante_cedula'];
                            }
                            echo htmlspecialchars($nombreEstudiante);
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($tarjeta['fecha_emision']); ?></td>
                        <td><?php echo htmlspecialchars($tarjeta['fecha_expiracion']); ?></td>
                        <td>
                            <?php if (isset($tarjeta['activa']) && $tarjeta['activa']): ?>
                            <span class="badge bg-success">Activa</span>
                            <?php else: ?>
                            <span class="badge bg-danger">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo URL_BASE; ?>/public/tarjetas.php?action=view&id=<?php echo $tarjeta['id']; ?>" 
                               class="btn btn-sm btn-info" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo URL_BASE; ?>/public/tarjetas.php?action=edit&id=<?php echo $tarjeta['id']; ?>" 
                               class="btn btn-sm btn-warning" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <?php if (isset($tarjeta['activa'])): ?>
                                <?php if ($tarjeta['activa']): ?>
                                <a href="<?php echo URL_BASE; ?>/public/tarjetas.php?action=desactivar&id=<?php echo $tarjeta['id']; ?>" 
                                   class="btn btn-sm btn-secondary" title="Desactivar"
                                   onclick="return confirm('¿Está seguro que desea desactivar esta tarjeta?');">
                                    <i class="fas fa-lock"></i>
                                </a>
                                <?php else: ?>
                                <a href="<?php echo URL_BASE; ?>/public/tarjetas.php?action=activar&id=<?php echo $tarjeta['id']; ?>" 
                                   class="btn btn-sm btn-success" title="Activar"
                                   onclick="return confirm('¿Está seguro que desea activar esta tarjeta?');">
                                    <i class="fas fa-lock-open"></i>
                                </a>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <a href="<?php echo URL_BASE; ?>/public/tarjetas.php?action=delete&id=<?php echo $tarjeta['id']; ?>" 
                               class="btn btn-sm btn-danger" title="Eliminar"
                               onclick="return confirmarEliminacion('<?php echo $tarjeta['id']; ?>', 'tarjeta');">
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
            No hay tarjetas registradas.
        </div>
        <?php endif; ?>
    </div>
</div>
