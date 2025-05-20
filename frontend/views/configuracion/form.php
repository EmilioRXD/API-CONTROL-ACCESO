<div class="row mb-3">
    <div class="col-12 text-center">
        <h2>Configuración del Sistema</h2>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Configuración de Acceso y Pagos</h5>
            </div>
            <div class="card-body">

                <form action="<?php echo URL_BASE; ?>/public/configuracion.php?action=guardar_configuracion" method="post">
                    <?php if (isset($error_message_validacion) || isset($error_message_acceso)): ?>
                    <div class="alert alert-danger">
                        <?php echo isset($error_message_validacion) ? $error_message_validacion : $error_message_acceso; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($success_message_validacion) || isset($success_message_acceso)): ?>
                    <div class="alert alert-success">
                        <?php echo isset($success_message_validacion) ? $success_message_validacion : $success_message_acceso; ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Validación de cuotas -->
                    <div class="mb-4 border-bottom pb-3">
                        <div class="d-flex align-items-center mb-3">
                            <h6 class="mb-0 me-2">Validación de cuotas</h6>
                            <div class="position-relative d-inline-block" style="cursor: help;">
                                <i class="fas fa-info-circle text-primary" id="info-icon"></i>
                                <div class="tooltip-info position-absolute bg-white p-3 rounded shadow" 
                                     style="display: none; width: 300px; z-index: 100; top: -10px; left: 25px; border: 1px solid #ddd;">
                                    <h6 class="border-bottom pb-2 mb-2">Información sobre la validación de cuotas</h6>
                                    <ul class="ps-3 mb-0 small">
                                        <li class="mb-1"><strong>Activada:</strong> El sistema verificará el estado de pago de los estudiantes. Aquellos con cuotas vencidas no podrán acceder al campus.</li>
                                        <li><strong>Desactivada:</strong> No se verificará el estado de las cuotas. Todos los estudiantes podrán acceder al campus independientemente de sus pagos.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check form-switch">
                            <?php 
                            $bloqueo_checked = '';
                            foreach ($configuracion['api'] as $config) {
                                if ($config['parametro'] === 'BLOQUEO_ACCESO_VENCIDOS' && $config['valor'] === 'true') {
                                    $bloqueo_checked = 'checked';
                                    break;
                                }
                            }
                            ?>
                            <input type="checkbox" class="form-check-input" id="validacion_cuotas" name="BLOQUEO_ACCESO_VENCIDOS" value="true" <?php echo $bloqueo_checked; ?>>
                            <label class="form-check-label" for="validacion_cuotas">Activar validación de cuotas</label>
                            <input type="hidden" name="BLOQUEO_ACCESO_VENCIDOS_hidden" value="false">
                        </div>
                    </div>
                    
                    <!-- Período de gracia -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <h6 class="mb-0 me-2">Días de gracia para cuotas vencidas</h6>
                            <div class="position-relative d-inline-block" style="cursor: help;">
                                <i class="fas fa-info-circle text-primary" id="info-periodo-gracia"></i>
                                <div class="tooltip-info position-absolute bg-white p-3 rounded shadow" 
                                     style="display: none; width: 300px; z-index: 100; top: -10px; left: 25px; border: 1px solid #ddd;">
                                    <h6 class="border-bottom pb-2 mb-2">Información sobre el período de gracia</h6>
                                    <p class="small mb-0">Número de días que un estudiante puede seguir accediendo al campus después de que su cuota haya vencido. Una vez transcurrido este período, si la validación de cuotas está activada, se bloqueará el acceso hasta que la deuda sea regularizada.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="input-group">
                                <?php 
                                $periodo_valor = '5'; // Valor predeterminado
                                foreach ($configuracion['api'] as $config) {
                                    if ($config['parametro'] === 'PERIODO_GRACIA_DIAS') {
                                        $periodo_valor = htmlspecialchars($config['valor']);
                                        break;
                                    }
                                }
                                ?>
                                <input type="number" class="form-control" id="periodo_gracia" name="PERIODO_GRACIA_DIAS" 
                                       min="0" max="30" value="<?php echo $periodo_valor; ?>" required>
                                <span class="input-group-text">días</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script para manejar los tooltips -->
<script>
function setupTooltip(iconId) {
    const infoIcon = document.getElementById(iconId);
    if (infoIcon) {
        const tooltip = infoIcon.nextElementSibling;
        
        infoIcon.addEventListener('mouseenter', function() {
            tooltip.style.display = 'block';
        });
        
        infoIcon.addEventListener('mouseleave', function() {
            tooltip.style.display = 'none';
        });
        
        tooltip.addEventListener('mouseenter', function() {
            tooltip.style.display = 'block';
        });
        
        tooltip.addEventListener('mouseleave', function() {
            tooltip.style.display = 'none';
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    setupTooltip('info-icon');
    setupTooltip('info-periodo-gracia');
});
</script>
</div>
