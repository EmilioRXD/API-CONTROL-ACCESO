<div class="row mb-3">
    <div class="col-12">
        <h2>Detalles del Controlador</h2>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Información del Controlador</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($controlador['id']); ?></p>
                <p><strong>Nombre:</strong> <?php echo htmlspecialchars($controlador['nombre']); ?></p>
                <p><strong>Dirección MAC:</strong> <?php echo htmlspecialchars($controlador['mac']); ?></p>
                <p><strong>Tipo:</strong> 
                    <?php if ($controlador['tipo'] === 'READER'): ?>
                    <span class="badge bg-info">Lector</span>
                    <?php elseif ($controlador['tipo'] === 'WRITER'): ?>
                    <span class="badge bg-warning">Escritor</span>
                    <?php else: ?>
                    <span class="badge bg-secondary"><?php echo htmlspecialchars($controlador['tipo']); ?></span>
                    <?php endif; ?>
                </p>
                <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($controlador['ubicacion']); ?></p>
                <p><strong>Estado:</strong> 
                    <?php if (isset($controlador['activo']) && $controlador['activo']): ?>
                    <span class="badge bg-success">Activo</span>
                    <?php else: ?>
                    <span class="badge bg-danger">Inactivo</span>
                    <?php endif; ?>
                </p>
                <?php if (isset($controlador['ultimo_ping'])): ?>
                <p><strong>Último ping:</strong> <?php echo htmlspecialchars($controlador['ultimo_ping']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="<?php echo URL_BASE; ?>/public/controladores.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="<?php echo URL_BASE; ?>/public/controladores.php?action=edit&id=<?php echo $controlador['id']; ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            
            <?php if (isset($controlador['activo'])): ?>
                <?php if ($controlador['activo']): ?>
                <a href="<?php echo URL_BASE; ?>/public/controladores.php?action=desactivar&id=<?php echo $controlador['id']; ?>" 
                   class="btn btn-secondary" 
                   onclick="return confirm('¿Está seguro que desea desactivar este controlador?');">
                    <i class="fas fa-power-off"></i> Desactivar
                </a>
                <?php else: ?>
                <a href="<?php echo URL_BASE; ?>/public/controladores.php?action=activar&id=<?php echo $controlador['id']; ?>" 
                   class="btn btn-success" 
                   onclick="return confirm('¿Está seguro que desea activar este controlador?');">
                    <i class="fas fa-power-off"></i> Activar
                </a>
                <?php endif; ?>
            <?php endif; ?>
            
            <a href="<?php echo URL_BASE; ?>/public/controladores.php?action=delete&id=<?php echo $controlador['id']; ?>" 
               class="btn btn-danger" 
               onclick="return confirmarEliminacion('<?php echo $controlador['id']; ?>', 'controlador');">
                <i class="fas fa-trash"></i> Eliminar
            </a>
        </div>
    </div>
</div>

<!-- Historial de registros del controlador -->
<?php if ($controlador['tipo'] === 'READER'): ?>
<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Últimos Registros de Acceso</h5>
    </div>
    <div class="card-body">
        <?php 
        // Obtener registros de acceso de este controlador
        $api = new ApiClient();
        $registros = $api->get('/registros/', ['controlador_id' => $controlador['id'], 'limit' => 10]);
        
        if ($registros && count($registros) > 0):
        ?>
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Estudiante</th>
                        <th>Tarjeta</th>
                        <th>Tipo</th>
                        <th>Fecha/Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registros as $registro): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($registro['id']); ?></td>
                        <td>
                            <?php 
                            echo isset($registro['nombre_estudiante']) && isset($registro['apellido_estudiante']) ? 
                                 htmlspecialchars($registro['nombre_estudiante'] . ' ' . $registro['apellido_estudiante']) : 
                                 (isset($registro['estudiante_cedula']) ? 'Cédula: ' . htmlspecialchars($registro['estudiante_cedula']) : 'N/A'); 
                            ?>
                        </td>
                        <td><?php echo isset($registro['tarjeta_serial']) ? htmlspecialchars($registro['tarjeta_serial']) : 'N/A'; ?></td>
                        <td>
                            <?php if (isset($registro['tipo']) && $registro['tipo'] == 'ENTRADA'): ?>
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
        <p class="text-muted">No hay registros de acceso para este controlador.</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
