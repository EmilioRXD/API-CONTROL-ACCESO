<div class="row mb-3">
    <div class="col-12">
        <h2>Detalles de la Tarjeta</h2>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Información de la Tarjeta</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($tarjeta['id']); ?></p>
                <p><strong>Serial:</strong> <?php echo htmlspecialchars($tarjeta['serial']); ?></p>
                <p><strong>Estudiante:</strong> 
                    <?php 
                    $nombreEstudiante = '';
                    if (isset($tarjeta['nombre_estudiante']) && isset($tarjeta['apellido_estudiante'])) {
                        $nombreEstudiante = $tarjeta['nombre_estudiante'] . ' ' . $tarjeta['apellido_estudiante'];
                    } elseif (isset($tarjeta['estudiante_cedula'])) {
                        $nombreEstudiante = 'Cédula: ' . $tarjeta['estudiante_cedula'];
                    }
                    echo htmlspecialchars($nombreEstudiante);
                    ?>
                </p>
                <p><strong>Fecha de Emisión:</strong> <?php echo htmlspecialchars($tarjeta['fecha_emision']); ?></p>
                <p><strong>Fecha de Expiración:</strong> <?php echo htmlspecialchars($tarjeta['fecha_expiracion']); ?></p>
                <p><strong>Estado:</strong> 
                    <?php if (isset($tarjeta['activa']) && $tarjeta['activa']): ?>
                    <span class="badge bg-success">Activa</span>
                    <?php else: ?>
                    <span class="badge bg-danger">Inactiva</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="<?php echo URL_BASE; ?>/public/tarjetas.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="<?php echo URL_BASE; ?>/public/tarjetas.php?action=edit&id=<?php echo $tarjeta['id']; ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
            
            <?php if (isset($tarjeta['activa'])): ?>
                <?php if ($tarjeta['activa']): ?>
                <a href="<?php echo URL_BASE; ?>/public/tarjetas.php?action=desactivar&id=<?php echo $tarjeta['id']; ?>" 
                   class="btn btn-secondary" 
                   onclick="return confirm('¿Está seguro que desea desactivar esta tarjeta?');">
                    <i class="fas fa-lock"></i> Desactivar
                </a>
                <?php else: ?>
                <a href="<?php echo URL_BASE; ?>/public/tarjetas.php?action=activar&id=<?php echo $tarjeta['id']; ?>" 
                   class="btn btn-success" 
                   onclick="return confirm('¿Está seguro que desea activar esta tarjeta?');">
                    <i class="fas fa-lock-open"></i> Activar
                </a>
                <?php endif; ?>
            <?php endif; ?>
            
            <a href="<?php echo URL_BASE; ?>/public/tarjetas.php?action=delete&id=<?php echo $tarjeta['id']; ?>" 
               class="btn btn-danger" 
               onclick="return confirmarEliminacion('<?php echo $tarjeta['id']; ?>', 'tarjeta');">
                <i class="fas fa-trash"></i> Eliminar
            </a>
            
            <a href="<?php echo URL_BASE; ?>/public/estudiantes.php?action=view&cedula=<?php echo $tarjeta['estudiante_cedula']; ?>" 
               class="btn btn-info">
                <i class="fas fa-user-graduate"></i> Ver Estudiante
            </a>
        </div>
    </div>
</div>

<!-- Historial de accesos con esta tarjeta -->

<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">Historial de Accesos</h5>
    </div>
    <div class="card-body">
        <?php 
        // Obtener registros de acceso de esta tarjeta
        $api = new ApiClient();
        $registros = $api->get('/registros/tarjeta/' . $tarjeta['id']);
        
        if ($registros && ((is_array($registros) && count($registros) > 0) || is_object($registros))):
            // Definir las columnas de la tabla
            $columns = [
                [
                    'title' => 'ID',
                    'field' => 'id'
                ],
                [
                    'title' => 'Estado',
                    'render' => function($registro) {
                        $estado = isset($registro['acceso_permitido']) && $registro['acceso_permitido'] ? 
                            '<span class="badge bg-success">Permitido</span>' : 
                            '<span class="badge bg-danger">Denegado</span>';
                        return $estado;
                    }
                ],
                [
                    'title' => 'Controlador',
                    'render' => function($registro) {
                        return isset($registro['ubicacion_controlador']) ? 
                            htmlspecialchars($registro['ubicacion_controlador']) : 
                            (isset($registro['id_controlador']) ? 'ID: ' . htmlspecialchars($registro['id_controlador']) : 'N/A');
                    }
                ],
                [
                    'title' => 'Tipo',
                    'render' => function($registro) {
                        return isset($registro['tipo_acceso_controlador']) ? 
                            htmlspecialchars($registro['tipo_acceso_controlador']) : 'N/A';
                    }
                ],
                [
                    'title' => 'Fecha/Hora',
                    'render' => function($registro) {
                        if (!empty($registro['fecha_hora'])) {
                            $fecha_hora = new DateTime($registro['fecha_hora']);
                            return htmlspecialchars($fecha_hora->format('d/m/Y H:i:s')); 
                        } else {
                            return 'N/A';
                        }
                    }
                ]
            ];
            
            // Renderizar la tabla estándar sin paginación
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped" id="historial-accesos-table">';
            echo '<thead><tr>';
            foreach ($columns as $column) {
                echo '<th>' . $column['title'] . '</th>';
            }
            echo '</tr></thead><tbody>';
            
            foreach ($registros as $registro) {
                echo '<tr>';
                foreach ($columns as $column) {
                    echo '<td>';
                    if (isset($column['render']) && is_callable($column['render'])) {
                        echo $column['render']($registro);
                    } elseif (isset($column['field']) && isset($registro[$column['field']])) {
                        echo htmlspecialchars($registro[$column['field']]);
                    } else {
                        echo 'N/A';
                    }
                    echo '</td>';
                }
                echo '</tr>';
            }
            
            echo '</tbody></table></div>';
        else:
        ?>
        <p class="text-muted">No hay registros de acceso para esta tarjeta.</p>
        <?php endif; ?>
    </div>
</div>
