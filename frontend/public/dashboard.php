<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../controllers/dashboard_controller.php';

// Verificar autenticación
requireAuth();

// Obtener datos para el dashboard
$dashboardData = getDashboardData();

// Extraer variables para la vista
extract($dashboardData);

// Mostrar la barra de navegación
$showNavbar = true;

// Incluir la plantilla de cabecera
require_once __DIR__ . '/../views/templates/header.php';

// Incluir la vista del dashboard
require_once __DIR__ . '/../views/dashboard_view.php';

// Incluir la plantilla de pie de página
require_once __DIR__ . '/../views/templates/footer.php';
?>
