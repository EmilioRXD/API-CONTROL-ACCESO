from fastapi import FastAPI, Depends
from fastapi.middleware.cors import CORSMiddleware
from app.database.database import engine, Base
from app.auth.auth import get_current_active_user
import time
import logging

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Importar los routers
from app.routers import auth, carreras, estudiantes, usuarios, configuracion
from app.routers import tarjetas, cuotas, controladores, pagos, registros, validaciones

# Importar MQTT service
from app.utils.mqtt_service import get_mqtt_service, MQTTService
from app.routers.tarjetas import MQTT_BROKER_HOST, MQTT_BROKER_PORT

# Global MQTT service instance
mqtt_service = get_mqtt_service(MQTT_BROKER_HOST, MQTT_BROKER_PORT)

# Crear tablas en la base de datos
Base.metadata.create_all(bind=engine)

app = FastAPI(
    title="Sistema de Control de Acceso y Pagos",
    description="API para el sistema de control de acceso y pagos de estudiantes",
    version="1.0.0"
)

# Initialize MQTT service at startup
@app.on_event("startup")
async def init_mqtt():
    global mqtt_service
    if mqtt_service is None:
        mqtt_service = get_mqtt_service(MQTT_BROKER_HOST, MQTT_BROKER_PORT)
    if not mqtt_service.connected:
        mqtt_service.connect()
    logger.info(f"MQTT Service initialized. Connection status: {'connected' if mqtt_service.connected else 'disconnected'}")

# Monitor MQTT connection
@app.on_event("startup")
async def start_mqtt_monitor():
    global mqtt_service
    if mqtt_service:
        import threading
        def monitor_mqtt():
            while True:
                if not mqtt_service.connected:
                    logger.info("MQTT connection lost, attempting to reconnect...")
                    mqtt_service.connect()
                time.sleep(5)  # Check every 5 seconds
        threading.Thread(target=monitor_mqtt, daemon=True).start()

# Configurar CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Permite todas las origins en desarrollo
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Incluir los routers
app.include_router(auth.router)
app.include_router(carreras.router)
app.include_router(estudiantes.router)
app.include_router(usuarios.router)
app.include_router(configuracion.router)
app.include_router(tarjetas.router)
app.include_router(cuotas.router)
app.include_router(controladores.router)
app.include_router(pagos.router)
app.include_router(registros.router)
app.include_router(validaciones.router)

@app.get("/", tags=["root"])
async def root():
    return {"message": "Bienvenido a la API del Sistema de Control de Acceso y Pagos"}

@app.get("/status", tags=["status"])
async def status():
    return {"status": "online"}

@app.get("/mqtt/status", tags=["mqtt"])
async def mqtt_status():
    """Check MQTT connection status."""
    global mqtt_service
    if mqtt_service:
        return {
            "connected": mqtt_service.connected,
            "broker_host": mqtt_service.broker_host,
            "broker_port": mqtt_service.broker_port,
            "client_id": mqtt_service.client_id
        }
    return {"connected": False, "message": "MQTT service not initialized"}

@app.get("/protected", tags=["test"])
async def protected_route(current_user = Depends(get_current_active_user)):
    return {"message": "Ruta protegida accedida correctamente", "user": current_user.correo_electronico}
