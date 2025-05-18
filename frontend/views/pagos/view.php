<div class="row mb-3">
    <div class="col-12">
        <h2>Detalles del Pago</h2>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Información del Pago</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($pago['id']); ?></p>
                <p><strong>Estudiante:</strong> 
                    <?php 
                    $nombreEstudiante = '';
                    if (isset($pago['nombre_estudiante']) && isset($pago['apellido_estudiante'])) {
                        $nombreEstudiante = $pago['nombre_estudiante'] . ' ' . $pago['apellido_estudiante'];
                    } elseif (isset($pago['estudiante_cedula'])) {
                        $nombreEstudiante = 'Cédula: ' . $pago['estudiante_cedula'];
                    }
                    echo htmlspecialchars($nombreEstudiante);
                    ?>
                </p>
                <p><strong>Cuota:</strong> 
                    <?php 
                    if (isset($pago['nombre_cuota'])) {
                        echo htmlspecialchars($pago['nombre_cuota']);
                    } else {
                        echo 'No especificada';
                    }
                    ?>
                </p>
                <?php if (isset($pago['fecha_vencimiento']) && $pago['fecha_vencimiento']): ?>
                <p><strong>Fecha de Vencimiento:</strong> <?php echo date('d/m/Y', strtotime($pago['fecha_vencimiento'])); ?></p>
                <?php endif; ?>
                <p><strong>Fecha de Creación:</strong> <?php echo htmlspecialchars($pago['fecha_creacion']); ?></p>
                <p><strong>Estado:</strong> 
                    <?php if ($pago['estado'] === 'PAGADO'): ?>
                    <span class="badge bg-success">Pagado</span>
                    <?php elseif ($pago['estado'] === 'PENDIENTE'): ?>
                    <span class="badge bg-warning">Pendiente</span>
                    <?php elseif ($pago['estado'] === 'VENCIDO'): ?>
                    <span class="badge bg-danger">Vencido</span>
                    <?php else: ?>
                    <span class="badge bg-secondary"><?php echo htmlspecialchars($pago['estado']); ?></span>
                    <?php endif; ?>
                </p>
                <?php if (isset($pago['fecha_pago']) && $pago['fecha_pago']): ?>
                <p><strong>Fecha de Pago:</strong> <?php echo htmlspecialchars($pago['fecha_pago']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="<?php echo URL_BASE; ?>/public/pagos.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            
            <?php if ($pago['estado'] !== 'PAGADO'): ?>
            <a href="<?php echo URL_BASE; ?>/public/pagos.php?action=marcar_pagado&id=<?php echo $pago['id']; ?>" 
               class="btn btn-success" 
               onclick="return confirm('¿Está seguro que desea marcar este pago como pagado?');">
                <i class="fas fa-check"></i> Marcar como Pagado
            </a>
            <?php endif; ?>
            
            <?php if ($pago['estado'] === 'PENDIENTE'): ?>
            <a href="<?php echo URL_BASE; ?>/public/pagos.php?action=marcar_vencido&id=<?php echo $pago['id']; ?>" 
               class="btn btn-warning" 
               onclick="return confirm('¿Está seguro que desea marcar este pago como vencido?');">
                <i class="fas fa-exclamation-triangle"></i> Marcar como Vencido
            </a>
            <?php endif; ?>
            
            <a href="<?php echo URL_BASE; ?>/public/estudiantes.php?action=view&cedula=<?php echo $pago['estudiante_cedula']; ?>" 
               class="btn btn-info">
                <i class="fas fa-user-graduate"></i> Ver Estudiante
            </a>
            
            <a href="<?php echo URL_BASE; ?>/public/reportes.php?action=generar_comprobante&pago_id=<?php echo $pago['id']; ?>" 
               class="btn btn-primary" target="_blank">
                <i class="fas fa-file-pdf"></i> Generar Comprobante
            </a>
        </div>
    </div>
</div>
