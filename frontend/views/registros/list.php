<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Registros de Acceso</h2>
    <div class="export-buttons">
        <button onclick="exportTableToPDF('registros')" class="btn btn-success">
            <i class="fas fa-file-pdf"></i> Exportar PDF
        </button>
        <a href="<?php echo URL_BASE; ?>/public/registros.php?action=exportar&formato=excel<?php echo $url_params; ?>" class="btn btn-primary ml-2" target="_blank">
            <i class="fas fa-file-excel"></i> Exportar Excel
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Filtros de Búsqueda</h5>
    </div>
    <div class="card-body">
        <form action="<?php echo URL_BASE; ?>/public/registros.php" method="get" class="row g-3">
            <div class="col-md-3">
                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                       value="<?php echo isset($_GET['fecha_inicio']) ? htmlspecialchars($_GET['fecha_inicio']) : ''; ?>">
            </div>
            
            <div class="col-md-3">
                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                       value="<?php echo isset($_GET['fecha_fin']) ? htmlspecialchars($_GET['fecha_fin']) : ''; ?>">
            </div>
            
            <div class="col-md-3">
                <label for="acceso_permitido" class="form-label">Tipo de Acceso</label>
                <select class="form-select" id="acceso_permitido" name="acceso_permitido">
                    <option value="">Todos</option>
                    <option value="1" <?php echo (isset($_GET['acceso_permitido']) && $_GET['acceso_permitido'] === '1') ? 'selected' : ''; ?>>Permitido</option>
                    <option value="0" <?php echo (isset($_GET['acceso_permitido']) && $_GET['acceso_permitido'] === '0') ? 'selected' : ''; ?>>Denegado</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="ubicacion_controlador" class="form-label">Ubicación</label>
                <select class="form-select" id="ubicacion_controlador" name="ubicacion_controlador">
                    <option value="">Todas</option>
                    <?php foreach ($ubicaciones as $ubicacion): ?>
                    <option value="<?php echo htmlspecialchars($ubicacion); ?>" 
                            <?php echo (isset($_GET['ubicacion_controlador']) && $_GET['ubicacion_controlador'] === $ubicacion) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($ubicacion); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <a href="<?php echo URL_BASE; ?>/public/registros.php" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Limpiar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Listado de Registros</h5>
        
        <!-- Botonera de exportación -->
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-download"></i> Exportar
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="<?php echo URL_BASE; ?>/public/registros.php?action=exportar&formato=pdf<?php echo $url_params; ?>" target="_blank">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?php echo URL_BASE; ?>/public/registros.php?action=exportar&formato=excel<?php echo $url_params; ?>" target="_blank">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <!-- Tabla de registros -->
        <?php if (count($registros) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover data-table" id="registrosTable">
                <thead>
                    <tr>
                        <th data-column="id">ID</th>
                        <th data-column="id_tarjeta">ID Tarjeta</th>
                        <th data-column="ubicacion_controlador">Ubicación</th>
                        <th data-column="fecha_hora">Fecha/Hora</th>
                        <th data-column="acceso_permitido">Acceso</th>
                        <th class="text-center">Detalles</th>
                    </tr>
                    <!-- La fila de filtros se agregará mediante JavaScript -->
                </thead>
                <tbody>
                    <?php foreach ($registros as $registro): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($registro['id']); ?></td>
                        <td><?php echo htmlspecialchars($registro['id_tarjeta']); ?></td>
                        <td><?php echo htmlspecialchars($registro['ubicacion_controlador']); ?></td>
                        <td><?php 
                            if (isset($registro['fecha_hora'])) {
                                $fecha = new DateTime($registro['fecha_hora']);
                                echo $fecha->format('d/m/Y H:i:s');
                            } else {
                                echo 'N/A';
                            }
                        ?></td>
                        <td>
                            <?php if (isset($registro['acceso_permitido']) && $registro['acceso_permitido']): ?>
                            <span class="badge bg-success">Permitido</span>
                            <?php else: ?>
                            <span class="badge bg-danger">Denegado</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo URL_BASE; ?>/public/registros.php?action=view&id=<?php echo $registro['id']; ?>" 
                               class="btn btn-sm btn-info" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginación simple -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                <span class="text-muted">Mostrando <?php echo count($registros); ?> registros</span>
            </div>
            <div>
                <?php if ($skip > 0): ?>
                <a href="<?php echo URL_BASE; ?>/public/registros.php?skip=<?php echo max(0, $skip - $limit); ?>&limit=<?php echo $limit . $url_params; ?>" class="btn btn-sm btn-primary me-2">
                    <i class="fas fa-chevron-left"></i> Anterior
                </a>
                <?php endif; ?>
                
                <?php if (count($registros) >= $limit): ?>
                <a href="<?php echo URL_BASE; ?>/public/registros.php?skip=<?php echo $skip + $limit; ?>&limit=<?php echo $limit . $url_params; ?>" class="btn btn-sm btn-primary">
                    Siguiente <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            No hay registros que coincidan con los criterios de búsqueda.
        </div>
        <?php endif; ?>
    </div>
</div>
