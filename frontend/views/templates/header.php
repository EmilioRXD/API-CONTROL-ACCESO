<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Control de Acceso</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>/assets/css/styles.css">
    <!-- Data Tables CSS -->
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>/assets/css/data-tables.css">
    
    <!-- Variables JavaScript -->
    <script>
        // Variable global para la URL base
        var URL_BASE = '<?php echo URL_BASE; ?>';
    </script>
</head>
<body>
<?php if (isset($showNavbar) && $showNavbar): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo URL_BASE; ?>/public/dashboard.php">
            <img src="<?php echo URL_BASE; ?>/assets/img/universidad_logo.png" alt="Logo Universidad" height="40" class="d-inline-block align-text-middle me-2">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE; ?>/public/dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE; ?>/public/estudiantes.php">Estudiantes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE; ?>/public/tarjetas.php">Tarjetas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE; ?>/public/pagos.php">Pagos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE; ?>/public/controladores.php">Controladores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE; ?>/public/usuarios.php">Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE; ?>/public/registros.php">Registros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE; ?>/public/reportes.php">Reportes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE; ?>/public/configuracion.php">Configuración</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo URL_BASE; ?>/public/logout.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="container mt-4">
    <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['flash_type'] ?? 'info'; ?> alert-dismissible fade show">
        <?php echo $_SESSION['flash_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php 
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    endif; 
    ?>
