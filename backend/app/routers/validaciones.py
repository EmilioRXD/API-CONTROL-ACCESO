from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from datetime import datetime, date, timedelta
from app.database.database import get_db
from app.models.models import Tarjeta, Controlador, Registro, Pago, Estudiante, Cuota, Configuracion
from app.schemas.schemas import ValidacionTarjeta, RespuestaValidacion
from sqlalchemy import and_, func, or_
from app.utils.configuracion_utils import get_period_grace_days, is_blocking_access_for_overdue
from app.utils.date_utils import is_within_grace_period

router = APIRouter(
    prefix="/validaciones",
    tags=["validaciones"]
)

@router.post("/tarjeta", response_model=RespuestaValidacion)
def validar_acceso_tarjeta(validacion: ValidacionTarjeta, db: Session = Depends(get_db)):
    # Obtener la tarjeta por su serial
    tarjeta = db.query(Tarjeta).filter(Tarjeta.serial == validacion.serial).first()
    if not tarjeta:
        # No intentamos registrar en la base de datos para tarjetas desconocidas
        # ya que no hay un id_tarjeta válido para referenciar
        return RespuestaValidacion(
            acceso_permitido=False,
            mensaje="Tarjeta no encontrada en el sistema"
        )
    
    # Verificar si la tarjeta está activa
    if not tarjeta.activa:
        # Registrar intento fallido
        controlador = db.query(Controlador).filter(Controlador.mac == validacion.mac_controlador).first()
        if controlador:
            registro = Registro(
                id_tarjeta=tarjeta.id,
                id_controlador=controlador.id,
                fecha_hora=datetime.now(),
                acceso_permitido=False
            )
            db.add(registro)
            db.commit()
            db.refresh(registro)
        
        return RespuestaValidacion(
            acceso_permitido=False,
            mensaje="Tarjeta inactiva"
        )
    
    # Verificar fecha de expiración
    if tarjeta.fecha_expiracion < datetime.now().date():
        # Registrar intento fallido
        controlador = db.query(Controlador).filter(Controlador.mac == validacion.mac_controlador).first()
        if controlador:
            registro = Registro(
                id_tarjeta=tarjeta.id,
                id_controlador=controlador.id,
                fecha_hora=datetime.now(),
                acceso_permitido=False
            )
            db.add(registro)
            db.commit()
            db.refresh(registro)
        
        return RespuestaValidacion(
            acceso_permitido=False,
            mensaje="Tarjeta expirada"
        )
    
    # Obtener el período de gracia de la configuración
    periodo_gracia_dias = get_period_grace_days(db)
    bloquear_por_vencidos = is_blocking_access_for_overdue(db)
    
    # Verificar estado de pagos del estudiante
    estudiante_cedula = tarjeta.estudiante_cedula
    
    # Obtener todos los pagos pendientes o vencidos del estudiante con sus cuotas
    pagos_query = db.query(Pago, Cuota).filter(
        and_(
            Pago.estudiante_cedula == estudiante_cedula,
            Pago.estado.in_(["PENDIENTE", "VENCIDO"])
        )
    ).join(Cuota, Pago.cuota_id == Cuota.id)
    
    pagos_info = pagos_query.all()
    
    # Verificar si hay cuotas vencidas fuera del período de gracia
    cuotas_vencidas_fuera_gracia = False
    pagos_vencidos = 0
    pagos_pendientes = 0
    mensaje_denegacion = ""
    
    hoy = date.today()
    
    for pago, cuota in pagos_info:
        if pago.estado == "VENCIDO":
            pagos_vencidos += 1
            # Si hay fecha de pago y está fuera del período de gracia
            if cuota.fecha_pago and not is_within_grace_period(cuota.fecha_pago, periodo_gracia_dias):
                cuotas_vencidas_fuera_gracia = True
        elif pago.estado == "PENDIENTE":
            pagos_pendientes += 1
            # Si la cuota tiene fecha y está vencida y fuera del período de gracia
            if cuota.fecha_pago and cuota.fecha_pago < hoy:
                if not is_within_grace_period(cuota.fecha_pago, periodo_gracia_dias):
                    cuotas_vencidas_fuera_gracia = True
    
    # Lógica de control de acceso según reglas
    permitir_acceso = True
    
    if cuotas_vencidas_fuera_gracia and bloquear_por_vencidos:
        permitir_acceso = False
        mensaje_denegacion = f"Cuotas vencidas fuera del período de gracia de {periodo_gracia_dias} días"
    elif pagos_vencidos > 0:
        mensaje_denegacion = f"Estudiante tiene {pagos_vencidos} pagos vencidos"
        # Aún permite acceso si no está fuera del período de gracia
    elif pagos_pendientes > 0:
        mensaje_denegacion = f"Estudiante tiene {pagos_pendientes} pagos pendientes"
        # Permite acceso porque son solo pendientes
    
    if not permitir_acceso:
        # Registrar intento fallido
        controlador = db.query(Controlador).filter(Controlador.mac == validacion.mac_controlador).first()
        if controlador:
            registro = Registro(
                id_tarjeta=tarjeta.id,
                id_controlador=controlador.id,
                fecha_hora=datetime.now(),
                acceso_permitido=False
            )
            db.add(registro)
            db.commit()
            db.refresh(registro)
        
        return RespuestaValidacion(
            acceso_permitido=False,
            mensaje=mensaje_denegacion
        )
    
    # Verificar si el controlador existe y es un LECTOR
    controlador = db.query(Controlador).filter(
        and_(
            Controlador.mac == validacion.mac_controlador,
            Controlador.funcion == "LECTOR"
        )
    ).first()
    
    if not controlador:
        # Verificar si existe pero no es LECTOR
        existe_controlador = db.query(Controlador).filter(Controlador.mac == validacion.mac_controlador).first()
        
        if existe_controlador:
            mensaje = "Este controlador no tiene permiso para validar tarjetas (no es LECTOR)"
        else:
            mensaje = "Controlador no registrado en el sistema"
            
        return RespuestaValidacion(
            acceso_permitido=False,
            mensaje=mensaje
        )
    
    # Si todo está bien, registrar el acceso y permitir
    registro = Registro(
        id_tarjeta=tarjeta.id,
        id_controlador=controlador.id,
        fecha_hora=datetime.now(),
        acceso_permitido=True
    )
    db.add(registro)
    db.commit()
    db.refresh(registro)
    
    # Obtener información del estudiante para incluir en el mensaje
    estudiante = db.query(Estudiante).filter(Estudiante.cedula == estudiante_cedula).first()
    nombre_completo = f"{estudiante.nombre} {estudiante.apellido}" if estudiante else "Estudiante"
    
    return RespuestaValidacion(
        acceso_permitido=True,
        mensaje=f"Acceso permitido para {nombre_completo}"
    )

@router.get("/informe-accesos")
def generar_informe_accesos(
    fecha_inicio: str = None, 
    fecha_fin: str = None, 
    id_controlador: int = None,
    db: Session = Depends(get_db)
):
    """
    Genera un informe de accesos con filtros opcionales.
    """
    query = db.query(
        Registro, 
        Tarjeta, 
        Estudiante, 
        Controlador
    ).join(
        Tarjeta, 
        Registro.id_tarjeta == Tarjeta.id
    ).join(
        Estudiante, 
        Tarjeta.estudiante_cedula == Estudiante.cedula
    ).join(
        Controlador, 
        Registro.id_controlador == Controlador.id
    )
    
    # Aplicar filtros
    if fecha_inicio:
        try:
            fecha_inicio_obj = datetime.strptime(fecha_inicio, "%Y-%m-%d")
            query = query.filter(Registro.fecha_hora >= fecha_inicio_obj)
        except ValueError:
            raise HTTPException(status_code=400, detail="Formato de fecha de inicio inválido. Use YYYY-MM-DD")
    
    if fecha_fin:
        try:
            fecha_fin_obj = datetime.strptime(fecha_fin, "%Y-%m-%d")
            # Ajustar al final del día
            fecha_fin_obj = datetime(fecha_fin_obj.year, fecha_fin_obj.month, fecha_fin_obj.day, 23, 59, 59)
            query = query.filter(Registro.fecha_hora <= fecha_fin_obj)
        except ValueError:
            raise HTTPException(status_code=400, detail="Formato de fecha de fin inválido. Use YYYY-MM-DD")
    
    if id_controlador:
        query = query.filter(Registro.id_controlador == id_controlador)
    
    # Ordenar por fecha (más reciente primero)
    query = query.order_by(Registro.fecha_hora.desc())
    
    # Ejecutar la consulta
    resultados = query.all()
    
    # Formatear los resultados
    informe = []
    for registro, tarjeta, estudiante, controlador in resultados:
        informe.append({
            "id_registro": registro.id,
            "fecha_hora": registro.fecha_hora.strftime("%Y-%m-%d %H:%M:%S"),
            "estudiante": {
                "cedula": estudiante.cedula,
                "nombre": estudiante.nombre,
                "apellido": estudiante.apellido
            },
            "tarjeta": {
                "id": tarjeta.id,
                "serial": tarjeta.serial
            },
            "controlador": {
                "id": controlador.id,
                "ubicacion": controlador.ubicacion,
                "tipo_acceso": controlador.tipo_acceso
            },
            "acceso_permitido": registro.acceso_permitido
        })
    
    # Estadísticas básicas
    total_registros = len(informe)
    accesos_permitidos = sum(1 for r in resultados if r[0].acceso_permitido)
    accesos_denegados = total_registros - accesos_permitidos
    
    # Datos para la respuesta
    respuesta = {
        "total_registros": total_registros,
        "accesos_permitidos": accesos_permitidos,
        "accesos_denegados": accesos_denegados,
        "porcentaje_permitidos": (accesos_permitidos / total_registros * 100) if total_registros > 0 else 0,
        "registros": informe
    }
    
    return respuesta
