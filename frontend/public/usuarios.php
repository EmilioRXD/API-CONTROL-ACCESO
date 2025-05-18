<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../controllers/usuarios_controller.php';

// Verificar autenticación
requireAuth();

// Mostrar la barra de navegación
$showNavbar = true;

// Obtener la acción del query string
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Procesar la acción
switch ($action) {
    case 'index':
        // Listar usuarios
        $usuarios = getUsuarios();
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista de listado de usuarios
        require_once __DIR__ . '/../views/usuarios/list.php';
        break;
        
    case 'create':
        // Inicializar usuario vacío
        $usuario = [];
        $accion = 'create';
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista del formulario
        require_once __DIR__ . '/../views/usuarios/form.php';
        break;
        
    case 'store':
        // Procesar datos del formulario para crear usuario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitizar y validar datos
            $datos = [
                'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
                'apellido' => filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING),
                'correo_electronico' => filter_input(INPUT_POST, 'correo_electronico', FILTER_SANITIZE_EMAIL),
                'contraseña' => filter_input(INPUT_POST, 'contraseña', FILTER_UNSAFE_RAW)
            ];
            
            // Validar que todos los campos requeridos estén presentes
            if (empty($datos['nombre']) || empty($datos['apellido']) || 
                empty($datos['correo_electronico']) || empty($datos['contraseña'])) {
                
                $error_message = 'Todos los campos son obligatorios.';
                $usuario = $datos;
                $accion = 'create';
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/usuarios/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
            
            // Intentar crear el usuario
            $result = crearUsuario($datos);
            
            if ($result) {
                // Establecer mensaje flash
                $_SESSION['flash_message'] = 'Usuario creado correctamente.';
                $_SESSION['flash_type'] = 'success';
                
                // Redireccionar al listado
                header('Location: ' . URL_BASE . '/public/usuarios.php');
                exit;
            } else {
                // Obtener error si está disponible
                $apiError = getApiError();
                $error_message = 'Error al crear el usuario.';
                
                if ($apiError && isset($apiError['response']['detail'])) {
                    $error_message = 'Error: ' . $apiError['response']['detail'];
                }
                
                // Recargar formulario con datos y error
                $usuario = $datos;
                $accion = 'create';
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/usuarios/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
        } else {
            // Si no es POST, redirigir al formulario de creación
            header('Location: ' . URL_BASE . '/public/usuarios.php?action=create');
            exit;
        }
        
    case 'edit':
        // Obtener ID del usuario a editar
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de usuario no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/usuarios.php');
            exit;
        }
        
        // Obtener datos del usuario
        $usuario = getUsuario($id);
        
        if (!$usuario) {
            $_SESSION['flash_message'] = 'Usuario no encontrado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/usuarios.php');
            exit;
        }
        
        $accion = 'edit';
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista del formulario
        require_once __DIR__ . '/../views/usuarios/form.php';
        break;
        
    case 'update':
        // Procesar datos del formulario para actualizar usuario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtener ID del usuario a actualizar
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            
            if (!$id) {
                $_SESSION['flash_message'] = 'ID de usuario no especificado.';
                $_SESSION['flash_type'] = 'danger';
                header('Location: ' . URL_BASE . '/public/usuarios.php');
                exit;
            }
            
            // Sanitizar y validar datos
            $datos = [
                'nombre' => filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING),
                'apellido' => filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING),
                'correo_electronico' => filter_input(INPUT_POST, 'correo_electronico', FILTER_SANITIZE_EMAIL),
            ];
            
            // Verificar si se ha proporcionado una nueva contraseña
            $contraseña = filter_input(INPUT_POST, 'contraseña', FILTER_UNSAFE_RAW);
            if (!empty($contraseña)) {
                $datos['contraseña'] = $contraseña;
            }
            
            // Verificar estado activo (solo si viene en el formulario)
            if (isset($_POST['activo'])) {
                $datos['activo'] = (bool)$_POST['activo'];
            }
            
            // Validar que los campos requeridos estén presentes
            if (empty($datos['nombre']) || empty($datos['apellido']) || empty($datos['correo_electronico'])) {
                $error_message = 'Los campos Nombre, Apellido y Correo Electrónico son obligatorios.';
                $usuario = $datos;
                $usuario['id'] = $id;
                $accion = 'edit';
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/usuarios/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
            
            // Intentar actualizar el usuario
            $result = actualizarUsuario($id, $datos);
            
            if ($result) {
                // Establecer mensaje flash
                $_SESSION['flash_message'] = 'Usuario actualizado correctamente.';
                $_SESSION['flash_type'] = 'success';
                
                // Redireccionar al listado
                header('Location: ' . URL_BASE . '/public/usuarios.php');
                exit;
            } else {
                // Obtener error si está disponible
                $apiError = getApiError();
                $error_message = 'Error al actualizar el usuario.';
                
                if ($apiError && isset($apiError['response']['detail'])) {
                    $error_message = 'Error: ' . $apiError['response']['detail'];
                }
                
                // Recargar formulario con datos y error
                $usuario = $datos;
                $usuario['id'] = $id;
                $accion = 'edit';
                
                // Incluir la plantilla de cabecera
                require_once __DIR__ . '/../views/templates/header.php';
                
                // Incluir la vista del formulario con error
                require_once __DIR__ . '/../views/usuarios/form.php';
                
                // Incluir la plantilla de pie de página
                require_once __DIR__ . '/../views/templates/footer.php';
                exit;
            }
        } else {
            // Si no es POST, redirigir al listado
            header('Location: ' . URL_BASE . '/public/usuarios.php');
            exit;
        }
        
    case 'view':
        // Ver detalles de un usuario
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de usuario no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/usuarios.php');
            exit;
        }
        
        // Obtener datos del usuario
        $usuario = getUsuario($id);
        
        if (!$usuario) {
            $_SESSION['flash_message'] = 'Usuario no encontrado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/usuarios.php');
            exit;
        }
        
        // Incluir la plantilla de cabecera
        require_once __DIR__ . '/../views/templates/header.php';
        
        // Incluir la vista detallada del usuario
        require_once __DIR__ . '/../views/usuarios/view.php';
        break;
        
    case 'delete':
        // Eliminar usuario
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de usuario no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/usuarios.php');
            exit;
        }
        
        // Intentar eliminar el usuario
        $success = eliminarUsuario($id);
        
        if ($success) {
            $_SESSION['flash_message'] = 'Usuario eliminado correctamente.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $apiError = getApiError();
            $_SESSION['flash_message'] = 'Error al eliminar el usuario.';
            
            if ($apiError && isset($apiError['response']['detail'])) {
                $_SESSION['flash_message'] .= ' ' . $apiError['response']['detail'];
            }
            
            $_SESSION['flash_type'] = 'danger';
        }
        
        // Redireccionar al listado
        header('Location: ' . URL_BASE . '/public/usuarios.php');
        exit;
        
    case 'activar':
    case 'desactivar':
        // Activar o desactivar usuario
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id) {
            $_SESSION['flash_message'] = 'ID de usuario no especificado.';
            $_SESSION['flash_type'] = 'danger';
            header('Location: ' . URL_BASE . '/public/usuarios.php');
            exit;
        }
        
        $activar = $action === 'activar';
        
        // Intentar cambiar el estado del usuario
        $result = cambiarEstadoUsuario($id, $activar);
        
        if ($result) {
            $_SESSION['flash_message'] = 'Usuario ' . ($activar ? 'activado' : 'desactivado') . ' correctamente.';
            $_SESSION['flash_type'] = 'success';
        } else {
            $apiError = getApiError();
            $_SESSION['flash_message'] = 'Error al ' . ($activar ? 'activar' : 'desactivar') . ' el usuario.';
            
            if ($apiError && isset($apiError['response']['detail'])) {
                $_SESSION['flash_message'] .= ' ' . $apiError['response']['detail'];
            }
            
            $_SESSION['flash_type'] = 'danger';
        }
        
        // Redireccionar al listado
        header('Location: ' . URL_BASE . '/public/usuarios.php');
        exit;
        
    default:
        // Acción desconocida, redirigir al listado
        header('Location: ' . URL_BASE . '/public/usuarios.php');
        exit;
}

// Incluir la plantilla de pie de página
if (!in_array($action, ['store', 'update', 'delete', 'activar', 'desactivar'])) {
    require_once __DIR__ . '/../views/templates/footer.php';
}
?>
