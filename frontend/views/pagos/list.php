<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestión de Pagos</h2>
    <a href="<?php echo URL_BASE; ?>/public/pagos.php?action=create" class="btn btn-success">
        <i class="fas fa-plus"></i> Registrar Nuevo Pago
    </a>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Listado de Pagos</h5>
    </div>
    <div class="card-body">
        <!-- Filtros de búsqueda -->
        <div class="row mb-3">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" id="filtro" class="form-control" placeholder="Buscar pago..." onkeyup="filtrarTabla()">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <form action="<?php echo URL_BASE; ?>/public/pagos.php" method="get" class="d-flex">
                    <select name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="PENDIENTE" <?php echo isset($_GET['estado']) && $_GET['estado'] === 'PENDIENTE' ? 'selected' : ''; ?>>Pendientes</option>
                        <option value="PAGADO" <?php echo isset($_GET['estado']) && $_GET['estado'] === 'PAGADO' ? 'selected' : ''; ?>>Pagados</option>
                        <option value="VENCIDO" <?php echo isset($_GET['estado']) && $_GET['estado'] === 'VENCIDO' ? 'selected' : ''; ?>>Vencidos</option>
                    </select>
                    <button type="submit" class="btn btn-primary ms-2">Filtrar</button>
                </form>
            </div>
        </div>
        
        <!-- Tabla de pagos -->
        <?php if (count($pagos) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Estudiante</th>
                        <th>Cuota</th>
                        <th>Fecha Creación</th>
                        <th>Estado</th>
                        <th class="actions-column">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagos as $pago): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pago['id']); ?></td>
                        <td>
                            <?php 
                            $nombreEstudiante = '';
                            if (isset($pago['nombre_estudiante']) && isset($pago['apellido_estudiante'])) {
                                $nombreEstudiante = $pago['nombre_estudiante'] . ' ' . $pago['apellido_estudiante'];
                            } elseif (isset($pago['estudiante_cedula'])) {
                                $nombreEstudiante = 'Cédula: ' . $pago['estudiante_cedula'];
                            }
                            echo htmlspecialchars($nombreEstudiante);
                            ?>
                        </td>
                        <td>
                            <?php 
                            if (isset($pago['nombre_cuota'])) {
                                echo htmlspecialchars($pago['nombre_cuota']);
                                
                                // Si hay fecha de vencimiento, mostrarla
                                if (isset($pago['fecha_vencimiento'])) {
                                    $fecha_formateada = date('d/m/Y', strtotime($pago['fecha_vencimiento']));
                                    echo ' (Vence: ' . $fecha_formateada . ')';
                                }
                            } else {
                                echo 'No especificada';
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($pago['fecha_creacion']); ?></td>
                        <td>
                            <?php if ($pago['estado'] === 'PAGADO'): ?>
                            <span class="badge bg-success">Pagado</span>
                            <?php elseif ($pago['estado'] === 'PENDIENTE'): ?>
                            <span class="badge bg-warning">Pendiente</span>
                            <?php elseif ($pago['estado'] === 'VENCIDO'): ?>
                            <span class="badge bg-danger">Vencido</span>
                            <?php else: ?>
                            <span class="badge bg-secondary"><?php echo htmlspecialchars($pago['estado']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo URL_BASE; ?>/public/pagos.php?action=view&id=<?php echo $pago['id']; ?>" 
                               class="btn btn-sm btn-info" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            <?php if ($pago['estado'] !== 'PAGADO'): ?>
                            <a href="<?php echo URL_BASE; ?>/public/pagos.php?action=marcar_pagado&id=<?php echo $pago['id']; ?>" 
                               class="btn btn-sm btn-success" title="Marcar como pagado"
                               onclick="return confirm('¿Está seguro que desea marcar este pago como pagado?');">
                                <i class="fas fa-check"></i>
                            </a>
                            <?php endif; ?>
                            
                            <?php if ($pago['estado'] === 'PENDIENTE'): ?>
                            <a href="<?php echo URL_BASE; ?>/public/pagos.php?action=marcar_vencido&id=<?php echo $pago['id']; ?>" 
                               class="btn btn-sm btn-warning" title="Marcar como vencido"
                               onclick="return confirm('¿Está seguro que desea marcar este pago como vencido?');">
                                <i class="fas fa-exclamation-triangle"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            No hay pagos registrados.
        </div>
        <?php endif; ?>
    </div>
</div>
