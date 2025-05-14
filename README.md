# Backend del Sistema de Control de Acceso y Pagos

Este backend implementa una API RESTful usando FastAPI para gestionar un sistema de control de acceso y pagos para estudiantes.

## Requisitos previos

- Python 3.8+
- MySQL (a través de XAMPP)
- Virtualenv (recomendado)

## Estructura del proyecto

```
backend/
│
├── app/
│   ├── auth/            # Autenticación JWT
│   ├── database/        # Configuración de la base de datos
│   ├── models/          # Modelos ORM
│   ├── routers/         # Rutas API
│   ├── schemas/         # Schemas Pydantic
│   └── main.py          # Punto de entrada principal
│
├── .env                 # Variables de entorno
├── requirements.txt     # Dependencias
└── run.py               # Script para ejecutar el servidor
```

## Instalación

1. Clone el repositorio:

```bash
git clone <URL_del_repositorio>
cd <nombre_del_repositorio>/backend
```

2. Cree un entorno virtual e instale las dependencias:

```bash
python -m venv venv
source venv/bin/activate  # En Windows: venv\Scripts\activate
pip install -r requirements.txt
```

3. Configure la base de datos en XAMPP:
   - Inicie los servicios de Apache y MySQL en XAMPP
   - Cree una base de datos llamada `tesis` en phpMyAdmin
   - Importe el archivo DataBase.sql

4. Configure el archivo .env según sea necesario (por defecto está configurado para localhost con root sin contraseña)

## Uso

Ejecute el servidor de desarrollo:

```bash
python run.py
```

El servidor se iniciará en `http://localhost:8000`.

La documentación interactiva de la API estará disponible en:
- Swagger UI: `http://localhost:8000/docs`
- ReDoc: `http://localhost:8000/redoc`

## Endpoints principales

### Autenticación
- POST `/token`: Obtener token JWT
- POST `/usuarios/`: Crear usuario

### Entidades
- `/carreras`: CRUD para carreras
- `/estudiantes`: CRUD para estudiantes
- `/tarjetas`: CRUD para tarjetas
- `/pagos`: CRUD para pagos
- `/controladores`: CRUD para controladores
- `/registros`: CRUD para registros de acceso

### Validaciones (para ESP32)
- POST `/validaciones/tarjeta`: Validar acceso de tarjeta
- GET `/validaciones/informe-accesos`: Generar informes de acceso

## Seguridad

Todos los endpoints exceptuando `/validaciones/tarjeta` requieren autenticación JWT.
Para solicitudes desde el ESP32, se valida el controlador por su dirección MAC.
