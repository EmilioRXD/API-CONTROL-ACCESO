<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4>Iniciar Sesión</h4>
            </div>
            <div class="text-center mt-3 mb-3">
                <img src="<?php echo URL_BASE; ?>/assets/img/universidad_logo.png" alt="Logo Universidad" height="80" class="img-fluid">
            </div>
            <div class="card-body">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario (Email)</label>
                        <input type="email" class="form-control" id="usuario" name="usuario" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
