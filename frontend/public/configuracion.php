<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../controllers/configuracion_controller.php';

// Verificar autenticación
requireAuth();

// Mostrar la barra de navegación
$showNavbar = true;

// Obtener la acción del query string
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Procesar la acción
switch ($action) {
    case 'index':
        // Obtener la configuración actual
        $configuracion = getConfiguracion();
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista del formulario de configuración
        require_once __DIR__ . '/../views/configuracion/form.php';
        break;
        
    case 'guardar_local':
        // Procesar datos del formulario para guardar configuración local
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitizar y validar datos
            $datos = [
                'api_url' => filter_input(INPUT_POST, 'api_url', FILTER_SANITIZE_URL),
                'timeout' => filter_input(INPUT_POST, 'timeout', FILTER_SANITIZE_NUMBER_INT)
            ];
            
            // Validar que los campos requeridos estén presentes
            if (empty($datos['api_url']) || empty($datos['timeout'])) {
                $error_message_local = 'Todos los campos son obligatorios.';
                
                // Obtener la configuración actual
                $configuracion = getConfiguracion();
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/configuracion/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
            
            // Intentar guardar la configuración
            $success = guardarConfiguracionLocal($datos);
            
            if ($success) {
                $success_message_local = 'Configuración local guardada correctamente.';
            } else {
                $error_message_local = 'Error al guardar la configuración local.';
            }
            
            // Obtener la configuración actualizada
            $configuracion = getConfiguracion();
            
            // Incluir la plantilla de cabecera
            require_once __DIR__ . '/../views/templates/header.php';
            
            // Incluir la vista del formulario con mensaje
            require_once __DIR__ . '/../views/configuracion/form.php';
        } else {
            // Si no es POST, redirigir al formulario
            header('Location: ' . URL_BASE . '/public/configuracion.php');
            exit;
        }
        break;
        
    case 'guardar_api':
        // Procesar datos del formulario para actualizar configuración en la API
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener todos los campos que comienzan con "api_"
            $datos = [];
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'api_') === 0) {
                    // Eliminar el prefijo "api_" y añadir al array de datos
                    $configKey = substr($key, 4);
                    $datos[$configKey] = $value;
                }
            }
            
            // Intentar actualizar la configuración en la API
            $result = actualizarConfiguracionAPI($datos);
            
            if ($result) {
                $success_message_api = 'Configuración del servidor actualizada correctamente.';
            } else {
                // Obtener error si está disponible
                $apiError = getApiError();
                $error_message_api = 'Error al actualizar la configuración del servidor.';
                
                if ($apiError && isset($apiError['response']['detail'])) {
                    $error_message_api .= ' ' . $apiError['response']['detail'];
                }
            }
            
            // Obtener la configuración actualizada
            $configuracion = getConfiguracion();
            
            // Incluir la plantilla de cabecera
            require_once __DIR__ . '/../views/templates/header.php';
            
            // Incluir la vista del formulario con mensaje
            require_once __DIR__ . '/../views/configuracion/form.php';
        } else {
            // Si no es POST, redirigir al formulario
            header('Location: ' . URL_BASE . '/public/configuracion.php');
            exit;
        }
        break;
        
    default:
        // Acción desconocida, redirigir a la configuración
        header('Location: ' . URL_BASE . '/public/configuracion.php');
        exit;
}

// Incluir la plantilla de pie de página
if ($action != 'guardar_local' && $action != 'guardar_api') {
    require_once __DIR__ . '/../views/templates/footer.php';
} else {
    require_once __DIR__ . '/../views/templates/footer.php';
}
?>
