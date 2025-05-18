<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Estudiantes</h2>
    <a href="<?php echo URL_BASE; ?>/public/estudiantes.php?action=create" class="btn btn-success">
        <i class="fas fa-plus"></i> Nuevo Estudiante
    </a>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Listado de Estudiantes</h5>
    </div>
    <div class="card-body">
        <!-- Filtro de búsqueda -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" id="filtro" class="form-control" placeholder="Buscar estudiante..." onkeyup="filtrarTabla()">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Tabla de estudiantes -->
        <?php if (count($estudiantes) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Carrera</th>
                        <th class="actions-column">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estudiantes as $estudiante): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($estudiante['cedula']); ?></td>
                        <td><?php echo htmlspecialchars($estudiante['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($estudiante['apellido']); ?></td>
                        <td>
                            <?php 
                            // Dar prioridad al nuevo campo nombre_carrera que devuelve la API
                            if (isset($estudiante['nombre_carrera'])) {
                                echo htmlspecialchars($estudiante['nombre_carrera']);
                            } elseif (isset($estudiante['carrera']) && isset($estudiante['carrera']['nombre'])) {
                                echo htmlspecialchars($estudiante['carrera']['nombre']);
                            } elseif (isset($estudiante['id_carrera'])) {
                                echo 'ID: ' . htmlspecialchars($estudiante['id_carrera']);
                            } else {
                                echo 'No asignada';
                            }
                            ?>
                        </td>
                        <td>
                            <a href="<?php echo URL_BASE; ?>/public/estudiantes.php?action=view&cedula=<?php echo $estudiante['cedula']; ?>" 
                               class="btn btn-sm btn-info" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?php echo URL_BASE; ?>/public/estudiantes.php?action=edit&cedula=<?php echo $estudiante['cedula']; ?>" 
                               class="btn btn-sm btn-warning" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?php echo URL_BASE; ?>/public/estudiantes.php?action=delete&cedula=<?php echo $estudiante['cedula']; ?>" 
                               class="btn btn-sm btn-danger" title="Eliminar"
                               onclick="return confirmarEliminacion('<?php echo $estudiante['cedula']; ?>', 'estudiante');">
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
            No hay estudiantes registrados.
        </div>
        <?php endif; ?>
    </div>
</div>
