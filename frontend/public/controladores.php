<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../controllers/controladores_controller.php';

// Verificar autenticación
requireAuth();

// Mostrar la barra de navegación
$showNavbar = true;

// Obtener la acción del query string
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Procesar la acción
switch ($action) {
    case 'index':
        // Obtener la función de filtro si existe
        $funcion = isset($_GET['funcion']) ? $_GET['funcion'] : null;
        
        // Listar controladores
        $controladores = getControladores(0, 100, $funcion);
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista de listado de controladores
        require_once __DIR__ . '/../views/controladores/list.php';
        break;
        
    case 'create':
        // Inicializar controlador vacío
        $controlador = [];
        $accion = 'create';
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista del formulario
        require_once __DIR__ . '/../views/controladores/form.php';
        break;
        
    case 'store':
        // Procesar datos del formulario para crear controlador
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitizar y validar datos
            $datos = [
                'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
                'mac' => filter_input(INPUT_POST, 'mac', FILTER_SANITIZE_STRING),
                'tipo' => filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING),
                'ubicacion' => filter_input(INPUT_POST, 'ubicacion', FILTER_SANITIZE_STRING)
            ];
            
            // Validación básica
            $error = false;
            $error_message = '';
            
            if (empty($datos['nombre'])) {
                $error = true;
                $error_message .= 'El nombre es requerido. ';
            }
            
            if (empty($datos['mac'])) {
                $error = true;
                $error_message .= 'La dirección MAC es requerida. ';
            } elseif (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $datos['mac'])) {
                $error = true;
                $error_message .= 'El formato de la dirección MAC no es válido (XX:XX:XX:XX:XX:XX). ';
            }
            
            if (empty($datos['tipo'])) {
                $error = true;
                $error_message .= 'El tipo es requerido. ';
            } elseif (!in_array($datos['tipo'], ['READER', 'WRITER'])) {
                $error = true;
                $error_message .= 'El tipo debe ser READER o WRITER. ';
            }
            
            if (empty($datos['ubicacion'])) {
                $error = true;
                $error_message .= 'La ubicación es requerida. ';
            }
            
            // Si hay errores, volver al formulario
            if ($error) {
                $controlador = $datos;
                $accion = 'create';
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/controladores/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
            
            // Intentar crear el controlador
            $result = crearControlador($datos);
            
            if ($result) {
                // Establecer mensaje flash
                $_SESSION['flash_message'] = 'Controlador creado correctamente.';
                $_SESSION['flash_type'] = 'success';
                
                // Redireccionar al listado
                header('Location: ' . URL_BASE . '/public/controladores.php');
                exit;
            } else {
                // Obtener error si está disponible
                $apiError = getApiError();
                $error_message = 'Error al crear el controlador.';
                
                if ($apiError && isset($apiError['response']['detail'])) {
                    $error_message = 'Error: ' . $apiError['response']['detail'];
                }
                
                // Recargar formulario con datos y error
                $controlador = $datos;
                $accion = 'create';
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/controladores/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
        } else {
            // Si no es POST, redirigir al formulario de creación
            header('Location: ' . URL_BASE . '/public/controladores.php?action=create');
            exit;
        }
        
    case 'edit':
        // Obtener ID del controlador a editar
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de controlador no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/controladores.php');
            exit;
        }
        
        // Obtener datos del controlador
        $controlador = getControlador($id);
        
        if (!$controlador) {
            $_SESSION['flash_message'] = 'Controlador no encontrado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/controladores.php');
            exit;
        }
        
        $accion = 'edit';
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista del formulario
        require_once __DIR__ . '/../views/controladores/form.php';
        break;
        
    case 'update':
        // Procesar datos del formulario para actualizar controlador
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener ID del controlador a actualizar
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            
            if (!$id) {
                $_SESSION['flash_message'] = 'ID de controlador no especificado.';
                $_SESSION['flash_type'] = 'danger';
                header('Location: ' . URL_BASE . '/public/controladores.php');
                exit;
            }
            
            // Sanitizar y validar datos
            $datos = [
                'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
                'mac' => filter_input(INPUT_POST, 'mac', FILTER_SANITIZE_STRING),
                'tipo' => filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING),
                'ubicacion' => filter_input(INPUT_POST, 'ubicacion', FILTER_SANITIZE_STRING)
            ];
            
            // Verificar estado activo (solo si viene en el formulario)
            if (isset($_POST['activo'])) {
                $datos['activo'] = (bool)$_POST['activo'];
            }
            
            // Validación básica
            $error = false;
            $error_message = '';
            
            if (empty($datos['nombre'])) {
                $error = true;
                $error_message .= 'El nombre es requerido. ';
            }
            
            if (empty($datos['mac'])) {
                $error = true;
                $error_message .= 'La dirección MAC es requerida. ';
            } elseif (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $datos['mac'])) {
                $error = true;
                $error_message .= 'El formato de la dirección MAC no es válido (XX:XX:XX:XX:XX:XX). ';
            }
            
            if (empty($datos['tipo'])) {
                $error = true;
                $error_message .= 'El tipo es requerido. ';
            } elseif (!in_array($datos['tipo'], ['READER', 'WRITER'])) {
                $error = true;
                $error_message .= 'El tipo debe ser READER o WRITER. ';
            }
            
            if (empty($datos['ubicacion'])) {
                $error = true;
                $error_message .= 'La ubicación es requerida. ';
            }
            
            // Si hay errores, volver al formulario
            if ($error) {
                $controlador = $datos;
                $controlador['id'] = $id;
                $accion = 'edit';
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/controladores/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
            
            // Intentar actualizar el controlador
            $result = actualizarControlador($id, $datos);
            
            if ($result) {
                // Establecer mensaje flash
                $_SESSION['flash_message'] = 'Controlador actualizado correctamente.';
                $_SESSION['flash_type'] = 'success';
                
                // Redireccionar al listado
                header('Location: ' . URL_BASE . '/public/controladores.php');
                exit;
            } else {
                // Obtener error si está disponible
                $apiError = getApiError();
                $error_message = 'Error al actualizar el controlador.';
                
                if ($apiError && isset($apiError['response']['detail'])) {
                    $error_message = 'Error: ' . $apiError['response']['detail'];
                }
                
                // Recargar formulario con datos y error
                $controlador = $datos;
                $controlador['id'] = $id;
                $accion = 'edit';
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/controladores/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
        } else {
            // Si no es POST, redirigir al listado
            header('Location: ' . URL_BASE . '/public/controladores.php');
            exit;
        }
        
    case 'view':
        // Ver detalles de un controlador
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de controlador no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/controladores.php');
            exit;
        }
        
        // Obtener datos del controlador
        $controlador = getControlador($id);
        
        if (!$controlador) {
            $_SESSION['flash_message'] = 'Controlador no encontrado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/controladores.php');
            exit;
        }
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista detallada del controlador
        require_once __DIR__ . '/../views/controladores/view.php';
        break;
        
    case 'delete':
        // Eliminar controlador
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de controlador no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/controladores.php');
            exit;
        }
        
        // Intentar eliminar el controlador
        $success = eliminarControlador($id);
        
        if ($success) {
            $_SESSION['flash_message'] = 'Controlador eliminado correctamente.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $apiError = getApiError();
            $_SESSION['flash_message'] = 'Error al eliminar el controlador.';
            
            if ($apiError && isset($apiError['response']['detail'])) {
                $_SESSION['flash_message'] .= ' ' . $apiError['response']['detail'];
            }
            
            $_SESSION['flash_type'] = 'danger';
        }
        
        // Redireccionar al listado
        header('Location: ' . URL_BASE . '/public/controladores.php');
        exit;
        
    case 'activar':
    case 'desactivar':
        // Activar o desactivar controlador
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de controlador no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/controladores.php');
            exit;
        }
        
        $activar = $action === 'activar';
        
        // Intentar cambiar el estado del controlador
        $result = $activar ? activarControlador($id) : desactivarControlador($id);
        
        if ($result) {
            $_SESSION['flash_message'] = 'Controlador ' . ($activar ? 'activado' : 'desactivado') . ' correctamente.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $apiError = getApiError();
            $_SESSION['flash_message'] = 'Error al ' . ($activar ? 'activar' : 'desactivar') . ' el controlador.';
            
            if ($apiError && isset($apiError['response']['detail'])) {
                $_SESSION['flash_message'] .= ' ' . $apiError['response']['detail'];
            }
            
            $_SESSION['flash_type'] = 'danger';
        }
        
        // Redireccionar al listado
        header('Location: ' . URL_BASE . '/public/controladores.php');
        exit;
        
    default:
        // Acción desconocida, redirigir al listado
        header('Location: ' . URL_BASE . '/public/controladores.php');
        exit;
}

// Incluir la plantilla de pie de página
if (!in_array($action, ['store', 'update', 'delete', 'activar', 'desactivar'])) {
    require_once __DIR__ . '/../views/templates/footer.php';
}
?>
