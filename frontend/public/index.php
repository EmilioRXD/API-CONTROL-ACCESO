<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';

// Redireccionar al dashboard si está autenticado, de lo contrario al login
if (isAuthenticated()) {
    header('Location: ' . URL_BASE . '/public/dashboard.php');
} else {
    header('Location: ' . URL_BASE . '/public/login.php');
}
exit;
