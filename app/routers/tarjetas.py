from fastapi import APIRouter, Depends, HTTPException, status, BackgroundTasks
from sqlalchemy.orm import Session
from typing import List, Optional
from datetime import date, timedelta
import os
import json
from dotenv import load_dotenv

from app.database.database import get_db
from app.models.models import Tarjeta as TarjetaModel, Estudiante as EstudianteModel, Controlador as ControladorModel
from app.schemas.schemas import Tarjeta, TarjetaCreate, AsignacionTarjeta, RespuestaAsignacion, TarjetaDetalle
from app.auth.auth import get_current_active_user
from app.utils.mqtt_service import get_mqtt_service

# Cargar variables de entorno
load_dotenv()
MQTT_BROKER_HOST = os.getenv("MQTT_BROKER_HOST", "localhost")
MQTT_BROKER_PORT = int(os.getenv("MQTT_BROKER_PORT", "1883"))
MQTT_TIMEOUT = int(os.getenv("MQTT_TIMEOUT", "30"))

router = APIRouter(
    prefix="/tarjetas",
    tags=["tarjetas"],
    dependencies=[Depends(get_current_active_user)]
)

@router.get("/", response_model=List[TarjetaDetalle])
def listar_tarjetas(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    # Consultar tarjetas junto con información del estudiante
    result = db.query(
        TarjetaModel,
        EstudianteModel.nombre.label('nombre_estudiante'),
        EstudianteModel.apellido.label('apellido_estudiante')
    ).join(
        EstudianteModel, 
        TarjetaModel.estudiante_cedula == EstudianteModel.cedula
    ).offset(skip).limit(limit).all()
    
    # Formateamos la respuesta para incluir todos los campos requeridos
    response = []
    for row in result:
        tarjeta = row[0]  # El objeto TarjetaModel
        nombre = row[1]   # El nombre del estudiante
        apellido = row[2] # El apellido del estudiante
        
        # Crear un diccionario con todos los atributos necesarios
        tarjeta_dict = {
            "id": tarjeta.id,
            "serial": tarjeta.serial,
            "estudiante_cedula": tarjeta.estudiante_cedula,
            "fecha_emision": tarjeta.fecha_emision,
            "fecha_expiracion": tarjeta.fecha_expiracion,
            "activa": tarjeta.activa,
            "nombre_estudiante": nombre,
            "apellido_estudiante": apellido
        }
        response.append(tarjeta_dict)
    
    return response

@router.post("/", response_model=RespuestaAsignacion, status_code=status.HTTP_201_CREATED)
async def crear_tarjeta(
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
    
    # Verificar si ya tiene una tarjeta activa
    tarjeta_activa = db.query(TarjetaModel).filter(
        TarjetaModel.estudiante_cedula == asignacion.estudiante_cedula,
        TarjetaModel.activa == True
    ).first()
    
    if tarjeta_activa:
        raise HTTPException(status_code=400, detail="El estudiante ya tiene una tarjeta activa")
    
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

@router.get("/{tarjeta_id}", response_model=Tarjeta)
def obtener_tarjeta(tarjeta_id: int, db: Session = Depends(get_db)):
    db_tarjeta = db.query(TarjetaModel).filter(TarjetaModel.id == tarjeta_id).first()
    if db_tarjeta is None:
        raise HTTPException(status_code=404, detail="Tarjeta no encontrada")
    return db_tarjeta

@router.get("/estudiante/{cedula}", response_model=List[Tarjeta])
def obtener_tarjetas_por_estudiante(cedula: int, db: Session = Depends(get_db)):
    # Verificar si el estudiante existe
    estudiante = db.query(EstudianteModel).filter(EstudianteModel.cedula == cedula).first()
    if not estudiante:
        raise HTTPException(status_code=404, detail="Estudiante no encontrado")
    
    tarjetas = db.query(TarjetaModel).filter(TarjetaModel.estudiante_cedula == cedula).all()
    return tarjetas

@router.put("/{tarjeta_id}", response_model=Tarjeta)
def actualizar_tarjeta(tarjeta_id: int, tarjeta: TarjetaCreate, db: Session = Depends(get_db)):
    db_tarjeta = db.query(TarjetaModel).filter(TarjetaModel.id == tarjeta_id).first()
    if db_tarjeta is None:
        raise HTTPException(status_code=404, detail="Tarjeta no encontrada")
    
    # Verificar si el estudiante existe
    estudiante = db.query(EstudianteModel).filter(EstudianteModel.cedula == tarjeta.estudiante_cedula).first()
    if not estudiante:
        raise HTTPException(status_code=404, detail="Estudiante no encontrado")
    
    # Actualizar los campos
    db_tarjeta.serial = tarjeta.serial
    db_tarjeta.estudiante_cedula = tarjeta.estudiante_cedula
    db_tarjeta.fecha_emision = tarjeta.fecha_emision
    db_tarjeta.fecha_expiracion = tarjeta.fecha_expiracion
    db_tarjeta.activa = tarjeta.activa
    
    db.commit()
    db.refresh(db_tarjeta)
    return db_tarjeta

@router.patch("/{tarjeta_id}/desactivar", response_model=Tarjeta)
def desactivar_tarjeta(tarjeta_id: int, db: Session = Depends(get_db)):
    db_tarjeta = db.query(TarjetaModel).filter(TarjetaModel.id == tarjeta_id).first()
    if db_tarjeta is None:
        raise HTTPException(status_code=404, detail="Tarjeta no encontrada")
    
    db_tarjeta.activa = False
    db.commit()
    db.refresh(db_tarjeta)
    return db_tarjeta

@router.patch("/{tarjeta_id}/activar", response_model=Tarjeta)
def activar_tarjeta(tarjeta_id: int, db: Session = Depends(get_db)):
    db_tarjeta = db.query(TarjetaModel).filter(TarjetaModel.id == tarjeta_id).first()
    if db_tarjeta is None:
        raise HTTPException(status_code=404, detail="Tarjeta no encontrada")
    
    # Verificar si el estudiante ya tiene otra tarjeta activa
    tarjeta_activa = db.query(TarjetaModel).filter(
        TarjetaModel.estudiante_cedula == db_tarjeta.estudiante_cedula,
        TarjetaModel.activa == True,
        TarjetaModel.id != tarjeta_id
    ).first()
    
    if tarjeta_activa:
        raise HTTPException(status_code=400, detail="El estudiante ya tiene otra tarjeta activa")
    
    db_tarjeta.activa = True
    db.commit()
    db.refresh(db_tarjeta)
    return db_tarjeta

@router.delete("/{tarjeta_id}", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_tarjeta(tarjeta_id: int, db: Session = Depends(get_db)):
    db_tarjeta = db.query(TarjetaModel).filter(TarjetaModel.id == tarjeta_id).first()
    if db_tarjeta is None:
        raise HTTPException(status_code=404, detail="Tarjeta no encontrada")
    
    db.delete(db_tarjeta)
    db.commit()
    return {"message": "Tarjeta eliminada"}


def init_mqtt_service():
    """Inicializa el servicio MQTT si no está ya inicializado."""
    mqtt_service = get_mqtt_service(MQTT_BROKER_HOST, MQTT_BROKER_PORT)
    if not mqtt_service.connected:
        mqtt_service.connect()
    return mqtt_service

# Se eliminó el endpoint duplicado mqtt/asignar ya que su funcionalidad ahora está en el endpoint principal
