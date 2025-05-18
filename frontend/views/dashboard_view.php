<div class="row mb-4">
    <div class="col-12">
        <h2>Panel de Control</h2>
        <p>Bienvenido al Sistema de Control de Acceso y Pagos</p>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card dashboard-card">
            <div class="card-body text-center">
                <div class="dashboard-icon text-primary">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h5 class="card-title">Estudiantes</h5>
                <p class="card-text"><?php echo $total_estudiantes; ?> registrados</p>
                <a href="<?php echo URL_BASE; ?>/public/estudiantes.php" class="btn btn-sm btn-primary">Ver Estudiantes</a>
            </div>
        </div>
    </div>
    
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
                <div class="dashboard-icon text-info">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <h5 class="card-title">Pagos</h5>
                <p class="card-text"><?php echo $total_pagos; ?> registrados</p>
                <a href="<?php echo URL_BASE; ?>/public/pagos.php" class="btn btn-sm btn-info">Ver Pagos</a>
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
</div>

<div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Registros Recientes</h5>
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
                            <?php foreach ($registros_recientes as $registro): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($registro['nombre_estudiante'] . ' ' . $registro['apellido_estudiante']); ?></td>
                                <td>
                                    <?php if ($registro['tipo'] == 'ENTRADA'): ?>
                                    <span class="badge bg-success">Entrada</span>
                                    <?php else: ?>
                                    <span class="badge bg-danger">Salida</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($registro['fecha_hora']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">No hay registros recientes.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Pagos Pendientes</h5>
            </div>
            <div class="card-body">
                <?php if (count($pagos_pendientes) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Cuota</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pagos_pendientes as $pago): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pago['nombre_estudiante'] . ' ' . $pago['apellido_estudiante']); ?></td>
                                <td><?php echo htmlspecialchars($pago['nombre_cuota']); ?></td>
                                <td>
                                    <?php if ($pago['estado'] == 'PENDIENTE'): ?>
                                    <span class="badge bg-warning">Pendiente</span>
                                    <?php elseif ($pago['estado'] == 'VENCIDO'): ?>
                                    <span class="badge bg-danger">Vencido</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo URL_BASE; ?>/public/pagos.php?action=marcar_pagado&id=<?php echo $pago['id']; ?>" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i> Pagar
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted">No hay pagos pendientes.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
