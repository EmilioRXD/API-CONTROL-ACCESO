from fastapi import APIRouter, Depends, HTTPException, status, BackgroundTasks
from sqlalchemy.orm import Session
from datetime import date, timedelta
import os
import json
from typing import Optional
from dotenv import load_dotenv

from app.database.database import get_db
from app.models.models import Tarjeta as TarjetaModel, Controlador as ControladorModel, Estudiante as EstudianteModel
from app.schemas.schemas import AsignacionTarjeta, RespuestaAsignacion
from app.auth.auth import get_current_active_user
from app.utils.mqtt_service import get_mqtt_service

# Cargar variables de entorno
load_dotenv()
MQTT_BROKER_HOST = "0.0.0.0"
MQTT_BROKER_PORT = 1883
MQTT_TIMEOUT = int(os.getenv("MQTT_TIMEOUT", "30"))

router = APIRouter(
    prefix="/tarjetas/mqtt",
    tags=["tarjetas-mqtt"],
    dependencies=[Depends(get_current_active_user)]
)

def init_mqtt_service():
    """Inicializa el servicio MQTT si no está ya inicializado."""
    mqtt_service = get_mqtt_service(MQTT_BROKER_HOST, MQTT_BROKER_PORT)
    if not mqtt_service.connected:
        mqtt_service.connect()
    return mqtt_service

@router.post("/asignar", response_model=RespuestaAsignacion)
async def asignar_tarjeta_mqtt(
    asignacion: AsignacionTarjeta,
    background_tasks: BackgroundTasks,
    db: Session = Depends(get_db)
):
    """
    Solicita la asignación de una tarjeta a un estudiante a través de un ESP32 con función ESCRITOR.
    Este endpoint mantiene la solicitud abierta mientras espera la respuesta del ESP32 o hasta que
    se agote el tiempo de espera.
    """
    # Verificar si el estudiante existe
    estudiante = db.query(EstudianteModel).filter(EstudianteModel.cedula == asignacion.estudiante_cedula).first()
    if not estudiante:
        raise HTTPException(status_code=404, detail="Estudiante no encontrado")
    
    # Verificar si el controlador existe y es un ESCRITOR
    escritor = db.query(ControladorModel).filter(
        ControladorModel.mac == asignacion.mac_escritor,
        ControladorModel.funcion == "ESCRITOR"
    ).first()
    
    if not escritor:
        # Verificar si existe pero no es ESCRITOR
        existe_controlador = db.query(ControladorModel).filter(
            ControladorModel.mac == asignacion.mac_escritor
        ).first()
        
        if existe_controlador:
            raise HTTPException(
                status_code=400, 
                detail="Este controlador no tiene permiso para escribir tarjetas (no es ESCRITOR)"
            )
        else:
            raise HTTPException(
                status_code=404, 
                detail="Controlador no registrado en el sistema"
            )
    
    # Inicializar servicio MQTT
    mqtt_service = init_mqtt_service()
    if not mqtt_service or not mqtt_service.connected:
        raise HTTPException(
            status_code=503, 
            detail="No se pudo establecer conexión con el broker MQTT"
        )
    
    # Establecer fechas por defecto si no se proporcionaron
    fecha_emision = asignacion.fecha_emision or date.today()
    fecha_expiracion = asignacion.fecha_expiracion or (date.today() + timedelta(days=365))  # 1 año por defecto
    
    # Solicitar asignación de tarjeta al ESP32
    response = mqtt_service.request_card_assignment(
        asignacion.mac_escritor,
        asignacion.estudiante_cedula,
        MQTT_TIMEOUT
    )
    
    if not response:
        # Timeout o error en la comunicación
        return RespuestaAsignacion(
            success=False,
            mensaje=f"No se recibió respuesta del dispositivo escritor en {MQTT_TIMEOUT} segundos"
        )
    
    # Procesar respuesta del ESP32
    try:
        if isinstance(response, str):
            response_data = json.loads(response)
        else:
            response_data = response
        
        status = response_data.get("status")
        serial = response_data.get("serial")
        student_id = response_data.get("student_id")
        
        if status == "success" and serial and str(student_id) == str(asignacion.estudiante_cedula):
            # Registrar la nueva tarjeta en la base de datos
            db_tarjeta = TarjetaModel(
                serial=serial,
                estudiante_cedula=asignacion.estudiante_cedula,
                fecha_emision=fecha_emision,
                fecha_expiracion=fecha_expiracion,
                activa=True
            )
            db.add(db_tarjeta)
            db.commit()
            db.refresh(db_tarjeta)
            
            # Programar limpieza de solicitudes expiradas
            background_tasks.add_task(mqtt_service.cleanup_expired_requests)
            
            return RespuestaAsignacion(
                success=True,
                mensaje="Tarjeta asignada correctamente",
                serial=serial,
                tarjeta_id=db_tarjeta.id
            )
        else:
            return RespuestaAsignacion(
                success=False,
                mensaje=f"Error en la asignación: {response_data.get('message', 'Error desconocido')}"
            )
    
    except Exception as e:
        return RespuestaAsignacion(
            success=False,
            mensaje=f"Error al procesar la respuesta: {str(e)}"
        )

# Se eliminó la función de listar escritores disponibles
