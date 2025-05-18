<div class="row mb-3">
    <div class="col-12">
        <h2>Detalles del Registro de Acceso</h2>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Información del Registro</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($registro['id']); ?></p>
                <p><strong>ID Tarjeta:</strong> <?php echo htmlspecialchars($registro['id_tarjeta']); ?></p>
                <p><strong>Ubicación del Controlador:</strong> <?php echo htmlspecialchars($registro['ubicacion_controlador']); ?></p>
                <p><strong>Fecha y Hora:</strong> 
                    <?php 
                    if (isset($registro['fecha_hora'])) {
                        $fecha = new DateTime($registro['fecha_hora']);
                        echo $fecha->format('d/m/Y H:i:s');
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </p>
                <p><strong>Tipo de Acceso:</strong> 
                    <?php if (isset($registro['acceso_permitido']) && $registro['acceso_permitido']): ?>
                    <span class="badge bg-success">Permitido</span>
                    <?php else: ?>
                    <span class="badge bg-danger">Denegado</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        
        <!-- Información adicional de la tarjeta y el estudiante si está disponible -->
        <?php if (isset($tarjeta) && $tarjeta): ?>
        <div class="mt-4">
            <h5>Información de la Tarjeta</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 200px;">Serial</th>
                            <td><?php echo isset($tarjeta['serial']) ? htmlspecialchars($tarjeta['serial']) : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <th>Estado</th>
                            <td>
                                <?php if (isset($tarjeta['activa']) && $tarjeta['activa']): ?>
                                <span class="badge bg-success">Activa</span>
                                <?php else: ?>
                                <span class="badge bg-danger">Inactiva</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Fecha de Emisión</th>
                            <td><?php echo isset($tarjeta['fecha_emision']) ? htmlspecialchars($tarjeta['fecha_emision']) : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <th>Fecha de Expiración</th>
                            <td><?php echo isset($tarjeta['fecha_expiracion']) ? htmlspecialchars($tarjeta['fecha_expiracion']) : 'N/A'; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (isset($estudiante) && $estudiante): ?>
        <div class="mt-4">
            <h5>Información del Estudiante</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 200px;">Cédula</th>
                            <td><?php echo isset($estudiante['cedula']) ? htmlspecialchars($estudiante['cedula']) : 'N/A'; ?></td>
                        </tr>
                        <tr>
                            <th>Nombre</th>
                            <td>
                                <?php 
                                $nombreCompleto = '';
                                if (isset($estudiante['nombre'])) {
                                    $nombreCompleto .= $estudiante['nombre'];
                                }
                                if (isset($estudiante['apellido'])) {
                                    $nombreCompleto .= ' ' . $estudiante['apellido'];
                                }
                                echo !empty($nombreCompleto) ? htmlspecialchars($nombreCompleto) : 'N/A'; 
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Carrera</th>
                            <td>
                                <?php 
                                if (isset($estudiante['carrera']) && isset($estudiante['carrera']['nombre'])) {
                                    echo htmlspecialchars($estudiante['carrera']['nombre']);
                                } elseif (isset($estudiante['id_carrera'])) {
                                    echo 'ID: ' . htmlspecialchars($estudiante['id_carrera']);
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="<?php echo URL_BASE; ?>/public/registros.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            
            <?php if (isset($tarjeta) && $tarjeta): ?>
            <a href="<?php echo URL_BASE; ?>/public/tarjetas.php?action=view&id=<?php echo $tarjeta['id']; ?>" class="btn btn-info">
                <i class="fas fa-id-card"></i> Ver Tarjeta
            </a>
            <?php endif; ?>
            
            <?php if (isset($estudiante) && $estudiante): ?>
            <a href="<?php echo URL_BASE; ?>/public/estudiantes.php?action=view&cedula=<?php echo $estudiante['cedula']; ?>" class="btn btn-primary">
                <i class="fas fa-user-graduate"></i> Ver Estudiante
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
