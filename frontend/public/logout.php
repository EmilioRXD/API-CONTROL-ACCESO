<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';

// Cerrar sesión
logout();

// Establecer mensaje flash
$_SESSION['flash_message'] = 'Sesión cerrada correctamente.';
$_SESSION['flash_type'] = 'success';

// Redireccionar al login
header('Location: ' . URL_BASE . '/public/login.php');
exit;
?>
