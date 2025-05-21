<div class="row mb-3">
    <div class="col-12">
        <h2>Detalles del Estudiante</h2>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Información del Estudiante</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Cédula:</strong> <?php echo htmlspecialchars($estudiante['cedula']); ?></p>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($estudiante['nombre']); ?></p>
                <p><strong>Apellido:</strong> <?php echo htmlspecialchars($estudiante['apellido']); ?></p>
                <p><strong>Carrera:</strong> 
                    <?php 
                    if (isset($estudiante['carrera']) && isset($estudiante['carrera']['nombre'])) {
                        echo htmlspecialchars($estudiante['carrera']['nombre']);
                    } elseif (isset($estudiante['id_carrera'])) {
                        echo 'ID: ' . htmlspecialchars($estudiante['id_carrera']);
                    } else {
                        echo 'No asignada';
                    }
                    ?>
                </p>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="<?php echo URL_BASE; ?>/public/estudiantes.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="<?php echo URL_BASE; ?>/public/estudiantes.php?action=edit&cedula=<?php echo $estudiante['cedula']; ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="<?php echo URL_BASE; ?>/public/tarjetas.php?estudiante_cedula=<?php echo $estudiante['cedula']; ?>" class="btn btn-success">
                <i class="fas fa-id-card"></i> Ver Tarjetas
            </a>
            <a href="<?php echo URL_BASE; ?>/public/pagos.php?estudiante_cedula=<?php echo $estudiante['cedula']; ?>" class="btn btn-info">
                <i class="fas fa-money-bill-wave"></i> Ver Pagos
            </a>
        </div>
    </div>
</div>

<!-- Tarjetas del estudiante -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">Tarjetas Asignadas</h5>
    </div>
    <div class="card-body">
        <?php 
        // Obtener tarjetas del estudiante
        $api = new ApiClient();
        $tarjetas = $api->get('/tarjetas/estudiante/' . $estudiante['cedula']);
        
        if ($tarjetas && count($tarjetas) > 0):
        ?>
        <div class="table-responsive">
            <table id="tabla-tarjetas-estudiante" class="table paginated-table table-striped table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Serial</th>
                        <th>Fecha Emisión</th>
                        <th>Fecha Expiración</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tarjetas as $tarjeta): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tarjeta['id']); ?></td>
                        <td><?php echo htmlspecialchars($tarjeta['serial']); ?></td>
                        <td><?php echo htmlspecialchars($tarjeta['fecha_emision']); ?></td>
                        <td><?php echo htmlspecialchars($tarjeta['fecha_expiracion']); ?></td>
                        <td>
                            <?php if ($tarjeta['activa']): ?>
                            <span class="badge bg-success">Activa</span>
                            <?php else: ?>
                            <span class="badge bg-danger">Inactiva</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p>No hay tarjetas asignadas a este estudiante.</p>
        <a href="<?php echo URL_BASE; ?>/public/tarjetas.php?action=create&estudiante_cedula=<?php echo $estudiante['cedula']; ?>" class="btn btn-sm btn-success">
            <i class="fas fa-plus"></i> Asignar Tarjeta
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Pagos del estudiante -->
<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Historial de Pagos</h5>
    </div>
    <div class="card-body">
        <?php 
        // Obtener pagos del estudiante con la estructura correcta
        // La API devuelve: id, estudiante_cedula, nombre_estudiante, apellido_estudiante,
        // nombre_cuota, fecha_vencimiento, estado
        $pagos = $api->get('/pagos/estudiante/' . $estudiante['cedula']);
        
        if ($pagos && count($pagos) > 0):
        ?>
        <div class="table-responsive">
            <table id="tabla-pagos-estudiante" class="table paginated-table table-striped table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cuota</th>
                        <th>Fecha Vencimiento</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagos as $pago): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pago['id']); ?></td>
                        <td>
                            <?php 
                            if (isset($pago['nombre_cuota'])) {
                                echo htmlspecialchars($pago['nombre_cuota']);
                            } else {
                                echo 'No especificada';
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            if (isset($pago['fecha_vencimiento'])) {
                                echo date('d/m/Y', strtotime($pago['fecha_vencimiento']));
                            } else {
                                echo 'No especificada';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($pago['estado'] == 'PAGADO'): ?>
                            <span class="badge bg-success">Pagado</span>
                            <?php elseif ($pago['estado'] == 'PENDIENTE'): ?>
                            <span class="badge bg-warning">Pendiente</span>
                            <?php elseif ($pago['estado'] == 'VENCIDO'): ?>
                            <span class="badge bg-danger">Vencido</span>
                            <?php else: ?>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($pago['estado']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($pago['estado'] != 'PAGADO'): ?>
                            <a href="<?php echo URL_BASE; ?>/public/pagos.php?action=marcar_pagado&id=<?php echo $pago['id']; ?>" 
                               class="btn btn-sm btn-success" title="Marcar como pagado">
                                <i class="fas fa-check"></i>
                            </a>
                            <?php endif; ?>
                            <a href="<?php echo URL_BASE; ?>/public/pagos.php?action=view&id=<?php echo $pago['id']; ?>" 
                               class="btn btn-sm btn-info" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p>No hay pagos registrados para este estudiante.</p>
        <a href="<?php echo URL_BASE; ?>/public/pagos.php?action=create&estudiante_cedula=<?php echo $estudiante['cedula']; ?>" class="btn btn-sm btn-info">
            <i class="fas fa-plus"></i> Registrar Pago
        </a>
        <?php endif; ?>
    </div>
</div>
