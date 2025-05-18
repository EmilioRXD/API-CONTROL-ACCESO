<div class="row mb-3">
    <div class="col-12">
        <h2>Configuración del Sistema</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Configuración Local</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo URL_BASE; ?>/public/configuracion.php?action=guardar_local" method="post">
                    <?php if (isset($error_message_local)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message_local; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($success_message_local)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message_local; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="api_url" class="form-label">URL Base de la API</label>
                        <input type="url" class="form-control" id="api_url" name="api_url" 
                               value="<?php echo isset($configuracion['local']['api_url']) ? htmlspecialchars($configuracion['local']['api_url']) : API_BASE_URL; ?>" required>
                        <small class="form-text text-muted">Ejemplo: http://localhost:8000</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="timeout" class="form-label">Tiempo de espera (segundos)</label>
                        <input type="number" class="form-control" id="timeout" name="timeout" min="1" max="120" 
                               value="<?php echo isset($configuracion['local']['timeout']) ? htmlspecialchars($configuracion['local']['timeout']) : API_TIMEOUT; ?>" required>
                        <small class="form-text text-muted">Tiempo máximo de espera para las peticiones a la API</small>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Configuración Local
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Configuración del Servidor</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo URL_BASE; ?>/public/configuracion.php?action=guardar_api" method="post">
                    <?php if (isset($error_message_api)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message_api; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($success_message_api)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message_api; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($configuracion['api']) && !empty($configuracion['api'])): ?>
                        <?php foreach ($configuracion['api'] as $key => $value): ?>
                            <?php if (is_bool($value)): ?>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="api_<?php echo $key; ?>" name="api_<?php echo $key; ?>" value="1" <?php echo $value ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="api_<?php echo $key; ?>"><?php echo ucfirst(str_replace('_', ' ', $key)); ?></label>
                            </div>
                            <?php elseif (is_numeric($value)): ?>
                            <div class="mb-3">
                                <label for="api_<?php echo $key; ?>" class="form-label"><?php echo ucfirst(str_replace('_', ' ', $key)); ?></label>
                                <input type="number" class="form-control" id="api_<?php echo $key; ?>" name="api_<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>">
                            </div>
                            <?php elseif (is_string($value)): ?>
                            <div class="mb-3">
                                <label for="api_<?php echo $key; ?>" class="form-label"><?php echo ucfirst(str_replace('_', ' ', $key)); ?></label>
                                <input type="text" class="form-control" id="api_<?php echo $key; ?>" name="api_<?php echo $key; ?>" value="<?php echo htmlspecialchars($value); ?>">
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-save"></i> Guardar Configuración del Servidor
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No hay configuraciones disponibles en el servidor o no se pudo conectar con la API.
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>
