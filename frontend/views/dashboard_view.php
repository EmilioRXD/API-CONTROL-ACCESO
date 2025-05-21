<div class="row mb-4">
    <div class="col-12">
        <h2>Panel de Control</h2>
        <p>Bienvenido al Sistema de Control de Acceso</p>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <div class="dashboard-icon text-success">
                    <i class="fas fa-id-card"></i>
                </div>
                <h5 class="card-title">Tarjetas</h5>
                <p class="card-text"><?php echo $total_tarjetas; ?> asignadas</p>
                <a href="<?php echo URL_BASE; ?>/public/tarjetas.php" class="btn btn-sm btn-success">Ver Tarjetas</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <div class="dashboard-icon text-warning">
                    <i class="fas fa-microchip"></i>
                </div>
                <h5 class="card-title">Controladores</h5>
                <p class="card-text"><?php echo $total_controladores; ?> activos</p>
                <a href="<?php echo URL_BASE; ?>/public/controladores.php" class="btn btn-sm btn-warning">Ver Controladores</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <div class="dashboard-icon text-primary">
                    <i class="fas fa-door-open"></i>
                </div>
                <h5 class="card-title">Accesos</h5>
                <p class="card-text"><?php echo $total_accesos; ?> registrados</p>
                <a href="<?php echo URL_BASE; ?>/public/registros.php" class="btn btn-sm btn-primary">Ver Accesos</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <div class="dashboard-icon text-danger">
                    <i class="fas fa-door-closed"></i>
                </div>
                <h5 class="card-title">Accesos Denegados</h5>
                <p class="card-text"><?php echo $total_accesos_denegados; ?> registrados</p>
                <a href="<?php echo URL_BASE; ?>/public/registros.php?tipo=denegados" class="btn btn-sm btn-danger">Ver Denegados</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Primera fila de paneles -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Registros de Hoy (<?php echo date('d/m/Y'); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (count($registros_recientes) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Tipo</th>
                                <th>Fecha/Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($registros_recientes, 0, 10) as $registro): ?>
                            <tr>
                                <td>
                                    <?php 
                                    if (isset($registro['info_estudiante'])) {
                                        echo htmlspecialchars($registro['info_estudiante']['nombre'] . ' ' . $registro['info_estudiante']['apellido']);
                                    } else {
                                        echo 'Tarjeta #' . htmlspecialchars($registro['id_tarjeta']);
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($registro['acceso_permitido']): ?>
                                    <span class="badge bg-success">Entrada</span>
                                    <?php else: ?>
                                    <span class="badge bg-danger">Denegado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    // Convertir formato ISO a un formato más legible
                                    $fecha_hora = new DateTime($registro['fecha_hora']);
                                    echo htmlspecialchars($fecha_hora->format('d/m/Y H:i:s')); 
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">No hay registros de acceso para el día de hoy.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Accesos Denegados</h5>
            </div>
            <div class="card-body">
                <?php if (count($accesos_denegados) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Controlador</th>
                                <th>Fecha/Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($accesos_denegados, 0, 10) as $denegado): ?>
                            <tr>
                                <td>
                                    <?php 
                                    if (isset($denegado['info_estudiante'])) {
                                        echo htmlspecialchars($denegado['info_estudiante']['nombre'] . ' ' . $denegado['info_estudiante']['apellido']);
                                    } else {
                                        echo 'Tarjeta #' . htmlspecialchars($denegado['id_tarjeta']);
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    if (isset($denegado['info_controlador']) && isset($denegado['info_controlador']['ubicacion'])) {
                                        echo htmlspecialchars($denegado['info_controlador']['ubicacion']);
                                    } else {
                                        echo 'Controlador #' . htmlspecialchars($denegado['id_controlador']);
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    // Convertir formato ISO a un formato más legible
                                    $fecha_hora = new DateTime($denegado['fecha_hora']);
                                    echo htmlspecialchars($fecha_hora->format('d/m/Y H:i:s')); 
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">No hay accesos denegados registrados.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Segunda fila con estadísticas -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Estadísticas de Acceso</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-center">
                    <div class="text-center p-3">
                        <div class="fs-1 fw-bold text-primary"><?php echo number_format(($total_accesos + $total_accesos_denegados) > 0 ? ($total_accesos * 100 / ($total_accesos + $total_accesos_denegados)) : 0, 1); ?>%</div>
                        <div>Tasa de éxito</div>
                    </div>
                    <div class="text-center p-3">
                        <div class="fs-1 fw-bold text-danger"><?php echo number_format(($total_accesos + $total_accesos_denegados) > 0 ? ($total_accesos_denegados * 100 / ($total_accesos + $total_accesos_denegados)) : 0, 1); ?>%</div>
                        <div>Tasa de rechazo</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
