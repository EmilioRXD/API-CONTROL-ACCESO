<?php
require_once __DIR__ . '/../config/config.php';

/**
 * Cliente para interactuar con la API REST
 */
class ApiClient {
    private $apiUrl;
    private $jwt;
    
    /**
     * Constructor
     * 
     * @param string $jwt Token JWT opcional
     */
    public function __construct($jwt = null) {
        $this->apiUrl = API_BASE_URL;
        $this->jwt = $jwt ?? getJWTFromSession();
    }
    
    /**
     * Realiza login y obtiene un JWT
     * 
     * @param string $email Correo electrónico
     * @param string $password Contraseña
     * @return array|false Respuesta con token o false si falla
     */
    public function login($email, $password) {
        $data = [
            'username' => $email,
            'password' => $password
        ];
        
        $response = $this->makeRequest('POST', API_TOKEN_ENDPOINT, $data, false);
        
        if ($response && isset($response['access_token'])) {
            return $response;
        }
        
        return false;
    }
    
    /**
     * Realiza peticiones a la API
     * 
     * @param string $method Método HTTP (GET, POST, PUT, DELETE, PATCH)
     * @param string $endpoint Endpoint de la API
     * @param array $data Datos a enviar (opcional)
     * @param bool $requireAuth Indica si requiere autorización JWT
     * @return array|false Respuesta de la API o false en caso de error
     */
    public function makeRequest($method, $endpoint, $data = null, $requireAuth = true) {
        // Inicializar cURL
        $ch = curl_init();
        
        // URL completa
        $url = $this->apiUrl . $endpoint;
        
        // Headers básicos
        $headers = ['Content-Type: application/json'];
        
        // Añadir token JWT si se requiere autenticación
        if ($requireAuth) {
            if (!$this->jwt) {
                return false; // No hay token, no se puede hacer la petición
            }
            $headers[] = 'Authorization: Bearer ' . $this->jwt;
        }
        
        // Configurar opciones de cURL según el método
        switch ($method) {
            case 'GET':
                if ($data) {
                    // Guardar los parámetros originales para depuración
                    file_put_contents(__DIR__ . '/../debug_params.txt', "Endpoint: {$endpoint}\nParámetros originales: " . print_r($data, true));
                    
                    // Procesamos los datos para manejar correctamente tipos de datos
                    $processedData = [];
                    foreach ($data as $key => $value) {
                        // Para booleanos, enviarlos como 1 o 0 (integers)
                        if (is_bool($value) || $value === 'true' || $value === 'false' || $value === true || $value === false) {
                            // Asegurarnos que tenemos un valor booleano 
                            $boolValue = is_bool($value) ? $value : ($value === 'true' || $value === true);
                            // Convertir a integer 1 o 0
                            $processedData[$key] = $boolValue ? 1 : 0;
                        }
                        // Para fechas y otros valores, los pasamos tal cual
                        else {
                            $processedData[$key] = $value;
                        }
                    }
                    
                    // Guardar los parámetros procesados para depuración
                    file_put_contents(__DIR__ . '/../debug_params.txt', file_get_contents(__DIR__ . '/../debug_params.txt') . "\nParámetros procesados: " . print_r($processedData, true), FILE_APPEND);
                    
                    $url .= '?' . http_build_query($processedData);
                    
                    // Guardar la URL completa para depuración
                    file_put_contents(__DIR__ . '/../debug_params.txt', file_get_contents(__DIR__ . '/../debug_params.txt') . "\nURL completa: {$url}", FILE_APPEND);
                }
                break;
                
            case 'POST':
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                if ($data) {
                    // Convertir a JSON si no es para login (que usa form-data)
                    $postData = $endpoint === API_TOKEN_ENDPOINT ? 
                                http_build_query($data) : json_encode($data);
                    
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                    
                    // Para login usamos form-data en lugar de JSON
                    if ($endpoint === API_TOKEN_ENDPOINT) {
                        $headers = ['Content-Type: application/x-www-form-urlencoded'];
                    }
                }
                break;
                
            default:
                return false; // Método no soportado
        }
        
        // Configuración general de cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, API_TIMEOUT);
        
        // Ejecutar la petición
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Cerrar cURL
        curl_close($ch);
        
        // Verificar respuesta
        if ($response === false) {
            return false;
        }
        
        // Decodificar respuesta JSON
        $responseData = json_decode($response, true);
        
        // Evaluar código HTTP
        if ($httpCode >= 200 && $httpCode < 300) {
            return $responseData;
        } 
        
        // Guardar error para depuración
        $_SESSION['api_error'] = [
            'code' => $httpCode,
            'response' => $responseData
        ];
        
        // Detectar errores de autenticación (401 Unauthorized, 403 Forbidden)
        if ($requireAuth && ($httpCode == 401 || $httpCode == 403)) {
            // Eliminar el token actual ya que es inválido o ha expirado
            if (isset($_SESSION[SESSION_JWT_KEY])) {
                unset($_SESSION[SESSION_JWT_KEY]);
            }
            
            // Guardar mensaje de error para mostrar en login
            $_SESSION['auth_message'] = 'Su sesión ha expirado o no tiene autorización. Por favor, inicie sesión nuevamente.';
            
            // Siempre redirigir al login para errores de autenticación
            if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
                // Guardar la página actual para redirigir después del login
                $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
                
                // Forzar redireccionamiento al login
                header('Location: ' . URL_BASE . '/public/login.php?session_expired=1');
                exit;
            }
        }
        
        return false;
    }
    
    // Métodos útiles para cada tipo de solicitud
    
    /**
     * Realiza una petición GET
     */
    public function get($endpoint, $params = null) {
        return $this->makeRequest('GET', $endpoint, $params);
    }
    
    /**
     * Realiza una petición POST
     */
    public function post($endpoint, $data) {
        return $this->makeRequest('POST', $endpoint, $data);
    }
    
    /**
     * Realiza una petición PUT
     */
    public function put($endpoint, $data) {
        return $this->makeRequest('PUT', $endpoint, $data);
    }
    
    /**
     * Realiza una petición PATCH
     */
    public function patch($endpoint, $data) {
        return $this->makeRequest('PATCH', $endpoint, $data);
    }
    
    /**
     * Realiza una petición DELETE
     */
    public function delete($endpoint) {
        return $this->makeRequest('DELETE', $endpoint);
    }
}
