from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session, joinedload
from typing import List, Optional
from datetime import datetime, date
from app.database.database import get_db
from app.models.models import Pago as PagoModel, Estudiante as EstudianteModel, Cuota as CuotaModel
from app.schemas.schemas import Pago, PagoCreate, PagoDetalle, EstadoEnum, PagoListado
from pydantic import BaseModel
from app.auth.auth import get_current_active_user

router = APIRouter(
    prefix="/pagos",
    tags=["pagos"],
    dependencies=[Depends(get_current_active_user)]
)

@router.get("/", response_model=List[PagoListado])
def listar_pagos(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    # Consulta con joins para obtener los datos necesarios
    pagos = db.query(PagoModel, 
                    EstudianteModel.cedula, 
                    EstudianteModel.nombre.label('nombre_estudiante'),
                    EstudianteModel.apellido.label('apellido_estudiante'),
                    CuotaModel.nombre_cuota,
                    CuotaModel.fecha_pago.label('fecha_vencimiento'))\
            .join(EstudianteModel, PagoModel.estudiante_cedula == EstudianteModel.cedula)\
            .join(CuotaModel, PagoModel.cuota_id == CuotaModel.id)\
            .offset(skip).limit(limit).all()
    
    # Formatear los resultados
    result = []
    for pago in pagos:
        pago_dict = {
            "id": pago[0].id,
            "estudiante_cedula": pago[1],
            "nombre_estudiante": pago[2],
            "apellido_estudiante": pago[3],
            "nombre_cuota": pago[4],
            "fecha_vencimiento": pago[5],
            "estado": pago[0].estado
        }
        result.append(pago_dict)
    
    return result

@router.post("/", response_model=Pago, status_code=status.HTTP_201_CREATED)
def crear_pago(pago: PagoCreate, db: Session = Depends(get_db)):
    # Verificar si el estudiante existe
    estudiante = db.query(EstudianteModel).filter(EstudianteModel.cedula == pago.estudiante_cedula).first()
    if not estudiante:
        raise HTTPException(status_code=404, detail="Estudiante no encontrado")
    
    # Verificar si la cuota existe
    cuota = db.query(CuotaModel).filter(CuotaModel.id == pago.cuota_id).first()
    if not cuota:
        raise HTTPException(status_code=404, detail="Cuota no encontrada")
    
    # Verificar si ya existe un pago para este estudiante y cuota
    pago_existente = db.query(PagoModel).filter(
        PagoModel.estudiante_cedula == pago.estudiante_cedula,
        PagoModel.cuota_id == pago.cuota_id
    ).first()
    
    if pago_existente:
        raise HTTPException(status_code=400, detail="Ya existe un registro de pago para este estudiante y cuota")
    
    # Obtener la cuota
    cuota = db.query(CuotaModel).filter(CuotaModel.id == pago.cuota_id).first()
    
    db_pago = PagoModel(
        estudiante_cedula=pago.estudiante_cedula,
        cuota_id=pago.cuota_id,
        estado=pago.estado
    )
    
    # Si el estado es PAGADO, actualizar la fecha_pago de la cuota
    if pago.estado == EstadoEnum.PAGADO:
        cuota.fecha_pago = date.today()
    db.add(db_pago)
    db.commit()
    db.refresh(db_pago)
    return db_pago

@router.get("/{pago_id}", response_model=PagoDetalle)
def obtener_pago(pago_id: int, db: Session = Depends(get_db)):
    db_pago = db.query(PagoModel).filter(PagoModel.id == pago_id).first()
    if db_pago is None:
        raise HTTPException(status_code=404, detail="Pago no encontrado")
    return db_pago

@router.get("/estudiante/{cedula}", response_model=List[PagoListado])
def obtener_pagos_por_estudiante(cedula: int, db: Session = Depends(get_db)):
    # Verificar si el estudiante existe
    estudiante = db.query(EstudianteModel).filter(EstudianteModel.cedula == cedula).first()
    if not estudiante:
        raise HTTPException(status_code=404, detail="Estudiante no encontrado")
    
    # Consulta con joins para obtener los datos necesarios
    pagos = db.query(PagoModel, 
                    EstudianteModel.cedula, 
                    EstudianteModel.nombre.label('nombre_estudiante'),
                    EstudianteModel.apellido.label('apellido_estudiante'),
                    CuotaModel.nombre_cuota,
                    CuotaModel.fecha_pago.label('fecha_vencimiento'))\
            .join(EstudianteModel, PagoModel.estudiante_cedula == EstudianteModel.cedula)\
            .join(CuotaModel, PagoModel.cuota_id == CuotaModel.id)\
            .filter(PagoModel.estudiante_cedula == cedula).all()
    
    # Formatear los resultados
    result = []
    for pago in pagos:
        pago_dict = {
            "id": pago[0].id,
            "estudiante_cedula": pago[1],
            "nombre_estudiante": pago[2],
            "apellido_estudiante": pago[3],
            "nombre_cuota": pago[4],
            "fecha_vencimiento": pago[5],
            "estado": pago[0].estado
        }
        result.append(pago_dict)
    
    return result

@router.get("/cuota/{cuota_id}", response_model=List[Pago])
def obtener_pagos_por_cuota(cuota_id: int, db: Session = Depends(get_db)):
    # Verificar si la cuota existe
    cuota = db.query(CuotaModel).filter(CuotaModel.id == cuota_id).first()
    if not cuota:
        raise HTTPException(status_code=404, detail="Cuota no encontrada")
    
    pagos = db.query(PagoModel).filter(PagoModel.cuota_id == cuota_id).all()
    return pagos

@router.put("/{pago_id}", response_model=Pago)
def actualizar_pago(pago_id: int, pago: PagoCreate, db: Session = Depends(get_db)):
    db_pago = db.query(PagoModel).filter(PagoModel.id == pago_id).first()
    if db_pago is None:
        raise HTTPException(status_code=404, detail="Pago no encontrado")
    
    # Verificar si el estudiante existe
    estudiante = db.query(EstudianteModel).filter(EstudianteModel.cedula == pago.estudiante_cedula).first()
    if not estudiante:
        raise HTTPException(status_code=404, detail="Estudiante no encontrado")
    
    # Verificar si la cuota existe
    cuota = db.query(CuotaModel).filter(CuotaModel.id == pago.cuota_id).first()
    if not cuota:
        raise HTTPException(status_code=404, detail="Cuota no encontrada")
    
    # Verificar si ya existe otro pago para este estudiante y cuota
    pago_existente = db.query(PagoModel).filter(
        PagoModel.estudiante_cedula == pago.estudiante_cedula,
        PagoModel.cuota_id == pago.cuota_id,
        PagoModel.id != pago_id
    ).first()
    
    if pago_existente:
        raise HTTPException(status_code=400, detail="Ya existe otro registro de pago para este estudiante y cuota")
    
    # Obtener la cuota
    cuota = db.query(CuotaModel).filter(CuotaModel.id == pago.cuota_id).first()
    
    # Si el estado cambia a PAGADO, actualizar la fecha de pago de la cuota
    if pago.estado == EstadoEnum.PAGADO and db_pago.estado != EstadoEnum.PAGADO:
        cuota.fecha_pago = date.today()
    
    # Actualizar los campos
    db_pago.estudiante_cedula = pago.estudiante_cedula
    db_pago.cuota_id = pago.cuota_id
    db_pago.estado = pago.estado
    
    db.commit()
    db.refresh(db_pago)
    return db_pago

@router.patch("/{pago_id}/marcar_pagado", response_model=Pago)
def marcar_pago_como_pagado(pago_id: int, db: Session = Depends(get_db)):
    db_pago = db.query(PagoModel).filter(PagoModel.id == pago_id).first()
    if db_pago is None:
        raise HTTPException(status_code=404, detail="Pago no encontrado")
    
    if db_pago.estado == EstadoEnum.PAGADO:
        raise HTTPException(status_code=400, detail="El pago ya está marcado como pagado")
    
    db_pago.estado = EstadoEnum.PAGADO
    
    # Actualizar la fecha de pago en la cuota
    cuota = db.query(CuotaModel).filter(CuotaModel.id == db_pago.cuota_id).first()
    cuota.fecha_pago = date.today()
    db.commit()
    db.refresh(db_pago)
    return db_pago

@router.patch("/{pago_id}/marcar_vencido", response_model=Pago)
def marcar_pago_como_vencido(pago_id: int, db: Session = Depends(get_db)):
    db_pago = db.query(PagoModel).filter(PagoModel.id == pago_id).first()
    if db_pago is None:
        raise HTTPException(status_code=404, detail="Pago no encontrado")
    
    if db_pago.estado == EstadoEnum.VENCIDO:
        raise HTTPException(status_code=400, detail="El pago ya está marcado como vencido")
    
    if db_pago.estado == EstadoEnum.PAGADO:
        raise HTTPException(status_code=400, detail="No se puede marcar como vencido un pago ya realizado")
    
    db_pago.estado = EstadoEnum.VENCIDO
    db.commit()
    db.refresh(db_pago)
    return db_pago

# Esquema para marcar pagos vencidos en lote
class MarcarVencidosRequest(BaseModel):
    fecha_limite: Optional[date] = None
    cuota_id: Optional[int] = None
    ids_pagos: Optional[List[int]] = None

@router.post("/marcar-vencidos", response_model=dict)
def marcar_pagos_vencidos_batch(request: MarcarVencidosRequest, db: Session = Depends(get_db)):
    """Marca como vencidos los pagos pendientes según los criterios especificados."""
    query = db.query(PagoModel).filter(PagoModel.estado == EstadoEnum.PENDIENTE)
    
    # Aplicar filtros si se proporcionan
    if request.ids_pagos:
        query = query.filter(PagoModel.id.in_(request.ids_pagos))
    
    if request.cuota_id:
        query = query.filter(PagoModel.cuota_id == request.cuota_id)
    
    if request.fecha_limite:
        # Buscar pagos de cuotas con fecha límite anterior a la fecha especificada
        query = query.join(CuotaModel, PagoModel.cuota_id == CuotaModel.id)
        query = query.filter(CuotaModel.fecha_pago < request.fecha_limite)
    
    # Actualizar los pagos a estado VENCIDO
    pagos_a_actualizar = query.all()
    count = 0
    
    for pago in pagos_a_actualizar:
        pago.estado = EstadoEnum.VENCIDO
        count += 1
    
    db.commit()
    
    return {
        "mensaje": f"Se han marcado {count} pagos como vencidos",
        "total_actualizados": count
    }

@router.delete("/{pago_id}", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_pago(pago_id: int, db: Session = Depends(get_db)):
    db_pago = db.query(PagoModel).filter(PagoModel.id == pago_id).first()
    if db_pago is None:
        raise HTTPException(status_code=404, detail="Pago no encontrado")
    
    db.delete(db_pago)
    db.commit()
    return {"message": "Pago eliminado"}

# Endpoint para registrar un nuevo pago
@router.post("/registrar", response_model=Pago, status_code=status.HTTP_201_CREATED)
def registrar_pago(
    estudiante_cedula: int, 
    cuota_id: int, 
    db: Session = Depends(get_db),
    current_user = Depends(get_current_active_user)
):
    # Verificar si el estudiante existe
    estudiante = db.query(EstudianteModel).filter(EstudianteModel.cedula == estudiante_cedula).first()
    if not estudiante:
        raise HTTPException(status_code=404, detail="Estudiante no encontrado")
    
    # Verificar si la cuota existe
    cuota = db.query(CuotaModel).filter(CuotaModel.id == cuota_id).first()
    if not cuota:
        raise HTTPException(status_code=404, detail="Cuota no encontrada")
    
    # Verificar si ya existe un pago para este estudiante y cuota
    pago_existente = db.query(PagoModel).filter(
        PagoModel.estudiante_cedula == estudiante_cedula,
        PagoModel.cuota_id == cuota_id
    ).first()
    
    if pago_existente:
        if pago_existente.estado == EstadoEnum.PAGADO:
            raise HTTPException(status_code=400, detail="Esta cuota ya está pagada")
        # Si existe pero está pendiente, lo actualizamos a pagado
        pago_existente.estado = EstadoEnum.PAGADO
        pago_existente.fecha_pago = datetime.now()
        db.commit()
        db.refresh(pago_existente)
        return pago_existente
    
    # Crear un nuevo pago
    nuevo_pago = PagoModel(
        estudiante_cedula=estudiante_cedula,
        cuota_id=cuota_id,
        estado=EstadoEnum.PAGADO
    )
    
    # Actualizar la fecha de pago en la cuota
    cuota.fecha_pago = date.today()
    db.add(nuevo_pago)
    db.commit()
    db.refresh(nuevo_pago)
    return nuevo_pago
