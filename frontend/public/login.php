<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';

// Si ya está autenticado, redirigir al dashboard
if (isAuthenticated()) {
    header('Location: ' . URL_BASE . '/public/dashboard.php');
    exit;
}

$error_message = null;

// Comprobar si hay un mensaje de error de autenticación
if (isset($_SESSION['auth_message'])) {
    $error_message = $_SESSION['auth_message'];
    unset($_SESSION['auth_message']);
} elseif (isset($_GET['session_expired']) && $_GET['session_expired'] == '1') {
    $error_message = 'Su sesión ha expirado o no tiene autorización. Por favor, inicie sesión nuevamente.';
}

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_EMAIL);
    $contrasena = filter_input(INPUT_POST, 'contrasena', FILTER_SANITIZE_STRING);
    
    if ($usuario && $contrasena) {
        if (doLogin($usuario, $contrasena)) {
            // Redireccionar al dashboard tras login exitoso
            header('Location: ' . URL_BASE . '/public/dashboard.php');
            exit;
        } else {
            // Error de autenticación
            $error_message = 'Credenciales incorrectas. Por favor, inténtelo de nuevo.';
            
            // Obtener detalles del error si están disponibles
            $apiError = getApiError();
            if ($apiError && isset($apiError['response']['detail'])) {
                $error_message = 'Error: ' . $apiError['response']['detail'];
            }
        }
    } else {
        $error_message = 'Por favor, complete todos los campos.';
    }
}

// No mostrar la barra de navegación en la página de login
$showNavbar = false;

// Incluir la plantilla de cabecera
require_once __DIR__ . '/../views/templates/header.php';

// Incluir la vista del formulario de login
require_once __DIR__ . '/../views/login_form.php';

// Incluir la plantilla de pie de página
require_once __DIR__ . '/../views/templates/footer.php';
?>
