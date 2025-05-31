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

- Python 3.8+
- MySQL Server
- MQTT Broker (Mosquitto)
- Apache Web Server

## Instalación y Despliegue

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio>
cd API-CONTROL-ACCESO
```

### 2. Configuración

Los valores predeterminados deberían funcionar correctamente, pero puede personalizar la configuración editando:

- **Variables de entorno**: Crear un archivo `.env` basado en el ejemplo
- **Configuración MQTT**: Archivos en `backend/mosquitto/config/`
- **Configuración Apache**: Archivos en `backend/apache-conf/`

### 3. Instalar dependencias

```bash
pip install -r requirements.txt
```

### 4. Iniciar los servicios

Iniciar los servicios individualmente según su sistema operativo:
- Iniciar MySQL
- Iniciar el broker MQTT
- Iniciar Apache
- Iniciar la aplicación FastAPI

### 5. Acceder a los servicios

- **API Backend**: http://localhost:8090
- **Documentación API**: http://localhost:8090/docs
- **Página principal**: http://localhost



## Estructura del proyecto

```
API-CONTROL-ACCESO/
├── backend/                     # Código y configuración del backend
│   ├── apache-conf/             # Configuración del servidor Apache
│   ├── app/                     # Código de la aplicación FastAPI
│   ├── docker-entrypoint-initdb.d/ # Scripts de inicialización de MySQL
│   ├── mosquitto/               # Configuración del broker MQTT
│   └── www/                     # Archivos web estáticos
└── README.md                    # Este archivo
```

## Despliegue en producción

Para desplegar este sistema en un servidor de producción:

1. Configure el servidor con los requisitos mencionados anteriormente
2. Clone este repositorio en el servidor
3. Configure las variables de entorno según sea necesario
4. Configure un dominio y SSL:
   - Modifique la configuración de Apache para usar un certificado SSL
   - Actualice los servidores virtuales de Apache según sea necesario
5. Inicie los servicios
6. Configure un sistema de respaldo regular para los datos de MySQL

## Solución de problemas

### Problemas de conexión a MySQL

Si la aplicación no puede conectarse a MySQL:

1. Verifique que MySQL esté activo
2. Compruebe los logs del servicio MySQL
3. Verifique que el usuario y contraseña sean correctos

### Problemas con el servidor MQTT

Si los controladores ESP32 no se conectan al broker MQTT:

1. Verifique que el puerto 1883 esté accesible desde la red
2. Compruebe los logs del servicio MQTT
3. Verifique la configuración MQTT en `backend/mosquitto/config/`

### Problemas con Apache

Si el servidor web no responde:

1. Verifique que Apache esté activo
2. Compruebe los logs del servicio Apache
3. Verifique que el puerto 80 no esté siendo utilizado por otro proceso

## Mantenimiento

### Actualización del sistema

Para actualizar el sistema:

1. Detenga los servicios
2. Actualice el código fuente: `git pull`
3. Actualice las dependencias: `pip install -r requirements.txt`
4. Reinicie los servicios

### Respaldos

Para respaldar la base de datos:

```bash
mysqldump -u root -p<password> control_acceso > backup.sql
```

## Desarrollo

Para desarrollar con este sistema:

1. Modifique el código del backend en el directorio `backend/app/`
2. Reinicie el servicio correspondiente según los cambios realizados