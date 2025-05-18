<div class="row mb-3">
    <div class="col-12">
        <h2><?php echo $accion === 'create' ? 'Nuevo Estudiante' : 'Editar Estudiante'; ?></h2>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Formulario de Estudiante</h5>
    </div>
    <div class="card-body">
        <form action="<?php echo URL_BASE; ?>/public/estudiantes.php?action=<?php echo $accion === 'create' ? 'store' : 'update&cedula=' . $estudiante['cedula']; ?>" 
              method="post" id="estudianteForm" onsubmit="return validarFormularioEstudiante()">
            
            <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>
            
            <div class="mb-3">
                <label for="cedula" class="form-label">Cédula</label>
                <input type="text" class="form-control" id="cedula" name="cedula" value="<?php echo isset($estudiante['cedula']) ? htmlspecialchars($estudiante['cedula']) : ''; ?>" 
                       <?php echo $accion === 'edit' ? 'readonly' : 'required'; ?>>
                <small class="form-text text-muted">Número de cédula sin puntos ni guiones</small>
            </div>
            
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo isset($estudiante['nombre']) ? htmlspecialchars($estudiante['nombre']) : ''; ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo isset($estudiante['apellido']) ? htmlspecialchars($estudiante['apellido']) : ''; ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="id_carrera" class="form-label">Carrera</label>
                <select class="form-select" id="id_carrera" name="id_carrera" required>
                    <option value="">Seleccionar carrera</option>
                    <?php foreach ($carreras as $carrera): ?>
                    <option value="<?php echo $carrera['id']; ?>" 
                            <?php echo (isset($estudiante['id_carrera']) && $estudiante['id_carrera'] == $carrera['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($carrera['nombre']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo URL_BASE; ?>/public/estudiantes.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <?php echo $accion === 'create' ? 'Guardar' : 'Actualizar'; ?>
                </button>
            </div>
        </form>
    </div>
</div>
