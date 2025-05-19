# Sistema de Control de Acceso

Este repositorio contiene un sistema completo de control de acceso con una API REST, base de datos MySQL, servidor MQTT para comunicación con controladores ESP32, y un servidor web Apache.

## Arquitectura del Sistema

El sistema está compuesto por los siguientes servicios:

1. **Backend (FastAPI)**: API REST que maneja toda la lógica de negocio
2. **MySQL**: Base de datos para almacenar información de estudiantes, tarjetas, pagos, etc.
3. **MQTT (Mosquitto)**: Broker MQTT para comunicación con controladores ESP32
4. **Apache**: Servidor web para servir la interfaz de usuario

### Puertos utilizados

- **Backend API**: 8090
- **MySQL**: 3306
- **MQTT**: 1883 (MQTT nativo), 9001 (WebSockets)
- **Apache**: 80

## Requisitos

- Docker Engine (versión 19.03.0+)
- Docker Compose (versión 1.27.0+)

## Instalación y Despliegue

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio>
cd API-CONTROL-ACCESO
```

### 2. Configuración (opcional)

Los valores predeterminados deberían funcionar correctamente, pero puede personalizar la configuración editando:

- **Variables de entorno**: Modificar los valores en el archivo `docker-compose.yml`
- **Configuración MQTT**: Archivos en `backend/mosquitto/config/`
- **Configuración Apache**: Archivos en `backend/apache-conf/`

### 3. Iniciar los servicios

En el directorio principal del proyecto, ejecute:

```bash
sudo docker compose up -d
```

Este comando:
- Construirá las imágenes necesarias
- Creará los contenedores
- Iniciará todos los servicios en modo desconectado (background)

La primera vez que ejecute este comando, puede tomar varios minutos mientras descarga las imágenes base y compila los componentes.

### 4. Verificar el estado de los servicios

```bash
sudo docker compose ps
```

Todos los servicios deberían estar en estado "Up". 

### 5. Acceder a los servicios

- **API Backend**: http://localhost:8090
- **Documentación API**: http://localhost:8090/docs
- **Página principal**: http://localhost

## Comandos útiles

### Ver logs de todos los servicios

```bash
sudo docker compose logs
```

### Ver logs de un servicio específico

```bash
sudo docker compose logs [servicio]
```

Ejemplo:
```bash
sudo docker compose logs backend
sudo docker compose logs mysql
sudo docker compose logs mqtt
sudo docker compose logs apache
```

### Seguir logs en tiempo real

```bash
sudo docker compose logs -f [servicio]
```

### Reiniciar un servicio específico

```bash
sudo docker compose restart [servicio]
```

### Detener todos los servicios

```bash
sudo docker compose down
```

### Detener y eliminar volúmenes (¡CUIDADO! Elimina datos)

```bash
sudo docker compose down -v
```

## Estructura del proyecto

```
API-CONTROL-ACCESO/
├── backend/                     # Código y configuración del backend
│   ├── apache-conf/             # Configuración del servidor Apache
│   ├── app/                     # Código de la aplicación FastAPI
│   ├── docker-entrypoint-initdb.d/ # Scripts de inicialización de MySQL
│   ├── mosquitto/               # Configuración del broker MQTT
│   └── www/                     # Archivos web estáticos
├── docker-compose.yml           # Configuración Docker Compose
└── README.md                    # Este archivo
```

## Despliegue en producción

Para desplegar este sistema en un servidor de producción:

1. Asegúrese de que el servidor tenga Docker y Docker Compose instalados
2. Clone este repositorio en el servidor
3. Modifique las variables de entorno en `docker-compose.yml` según sea necesario:
   - Cambie las contraseñas predeterminadas
   - Configure límites de recursos si es necesario
4. Configure un dominio y SSL:
   - Modifique la configuración de Apache para usar un certificado SSL
   - Actualice los servidores virtuales de Apache según sea necesario
5. Inicie los servicios con `sudo docker compose up -d`
6. Configure un sistema de respaldo regular para los datos de MySQL

## Solución de problemas

### Problemas de conexión a MySQL

Si la aplicación no puede conectarse a MySQL:

1. Verifique que MySQL esté activo: `sudo docker compose ps mysql`
2. Compruebe los logs: `sudo docker compose logs mysql`
3. Verifique que el usuario y contraseña en las variables de entorno sean correctos

### Problemas con el servidor MQTT

Si los controladores ESP32 no se conectan al broker MQTT:

1. Verifique que el puerto 1883 esté accesible desde la red
2. Compruebe los logs: `sudo docker compose logs mqtt`
3. Verifique la configuración MQTT en `backend/mosquitto/config/`

### Problemas con Apache

Si el servidor web no responde:

1. Verifique que Apache esté activo: `sudo docker compose ps apache`
2. Compruebe los logs: `sudo docker compose logs apache`
3. Verifique que el puerto 80 no esté siendo utilizado por otro proceso

## Mantenimiento

### Actualización del sistema

Para actualizar el sistema:

1. Detenga los servicios: `sudo docker compose down`
2. Actualice el código fuente: `git pull`
3. Reconstruya las imágenes: `sudo docker compose build`
4. Reinicie los servicios: `sudo docker compose up -d`

### Respaldos

Para respaldar la base de datos:

```bash
sudo docker compose exec mysql mysqldump -u root -p<password> control_acceso > backup.sql
```

## Desarrollo

Para desarrollar con este sistema:

1. Modifique el código del backend en el directorio `backend/app/`
2. Reconstruya la imagen del backend: `sudo docker compose build backend`
3. Reinicie el servicio: `sudo docker compose restart backend`

## Licencia

[Incluir información de licencia aquí]
