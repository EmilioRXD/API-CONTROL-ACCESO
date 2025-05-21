from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from datetime import datetime
from app.database.database import get_db
from app.models.models import Registro as RegistroModel, Tarjeta as TarjetaModel, Controlador as ControladorModel
from app.schemas.schemas import Registro, RegistroCreate, RegistroDetalle, RegistroConControlador
from app.auth.auth import get_current_active_user

router = APIRouter(
    prefix="/registros",
    tags=["registros"],
    dependencies=[Depends(get_current_active_user)]
)

@router.get("/", response_model=List[RegistroConControlador])
def listar_registros(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    # Consulta que incluye datos del controlador junto con los registros
    resultados = db.query(
        RegistroModel,
        ControladorModel.ubicacion.label('ubicacion_controlador'),
        ControladorModel.tipo_acceso.label('tipo_acceso_controlador')
    ).join(
        ControladorModel, RegistroModel.id_controlador == ControladorModel.id
    ).order_by(
        RegistroModel.fecha_hora.desc()
    ).offset(skip).limit(limit).all()
    
    # Convertir los resultados al formato esperado
    registros_con_controlador = []
    for registro, ubicacion, tipo_acceso in resultados:
        registro_dict = {
            "id": registro.id,
            "id_tarjeta": registro.id_tarjeta,
            "id_controlador": registro.id_controlador,
            "fecha_hora": registro.fecha_hora,
            "acceso_permitido": registro.acceso_permitido,
            "ubicacion_controlador": ubicacion,
            "tipo_acceso_controlador": tipo_acceso
        }
        registros_con_controlador.append(registro_dict)
    
    return registros_con_controlador

@router.post("/", response_model=Registro, status_code=status.HTTP_201_CREATED)
def crear_registro(registro: RegistroCreate, db: Session = Depends(get_db)):
    # Verificar si la tarjeta existe
    tarjeta = db.query(TarjetaModel).filter(TarjetaModel.id == registro.id_tarjeta).first()
    if not tarjeta:
        raise HTTPException(status_code=404, detail="Tarjeta no encontrada")
    
    # Verificar si el controlador existe
    controlador = db.query(ControladorModel).filter(ControladorModel.id == registro.id_controlador).first()
    if not controlador:
        raise HTTPException(status_code=404, detail="Controlador no encontrado")
    
    db_registro = RegistroModel(
        id_tarjeta=registro.id_tarjeta,
        id_controlador=registro.id_controlador,
        fecha_hora=registro.fecha_hora,
        acceso_permitido=registro.acceso_permitido
    )
    db.add(db_registro)
    db.commit()
    db.refresh(db_registro)
    return db_registro

@router.get("/{registro_id}", response_model=RegistroDetalle)
def obtener_registro(registro_id: int, db: Session = Depends(get_db)):
    db_registro = db.query(RegistroModel).filter(RegistroModel.id == registro_id).first()
    if db_registro is None:
        raise HTTPException(status_code=404, detail="Registro no encontrado")
    return db_registro

@router.get("/tarjeta/{tarjeta_id}", response_model=List[RegistroConControlador])
def obtener_registros_por_tarjeta(tarjeta_id: int, db: Session = Depends(get_db)):
    # Verificar si la tarjeta existe
    tarjeta = db.query(TarjetaModel).filter(TarjetaModel.id == tarjeta_id).first()
    if not tarjeta:
        raise HTTPException(status_code=404, detail="Tarjeta no encontrada")
    
    # Consulta que incluye datos del controlador junto con los registros
    resultados = db.query(
        RegistroModel,
        ControladorModel.ubicacion.label('ubicacion_controlador'),
        ControladorModel.tipo_acceso.label('tipo_acceso_controlador')
    ).join(
        ControladorModel, RegistroModel.id_controlador == ControladorModel.id
    ).filter(
        RegistroModel.id_tarjeta == tarjeta_id
    ).order_by(
        RegistroModel.fecha_hora.desc()
    ).all()
    
    # Convertir los resultados al formato esperado
    registros_con_controlador = []
    for registro, ubicacion, tipo_acceso in resultados:
        registro_dict = {
            "id": registro.id,
            "id_tarjeta": registro.id_tarjeta,
            "id_controlador": registro.id_controlador,
            "fecha_hora": registro.fecha_hora,
            "acceso_permitido": registro.acceso_permitido,
            "ubicacion_controlador": ubicacion,
            "tipo_acceso_controlador": tipo_acceso
        }
        registros_con_controlador.append(registro_dict)
    
    return registros_con_controlador

@router.get("/controlador/{controlador_id}", response_model=List[Registro])
def obtener_registros_por_controlador(controlador_id: int, db: Session = Depends(get_db)):
    # Verificar si el controlador existe
    controlador = db.query(ControladorModel).filter(ControladorModel.id == controlador_id).first()
    if not controlador:
        raise HTTPException(status_code=404, detail="Controlador no encontrado")
    
    registros = db.query(RegistroModel).filter(RegistroModel.id_controlador == controlador_id).order_by(RegistroModel.fecha_hora.desc()).all()
    return registros

@router.delete("/{registro_id}", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_registro(registro_id: int, db: Session = Depends(get_db)):
    db_registro = db.query(RegistroModel).filter(RegistroModel.id == registro_id).first()
    if db_registro is None:
        raise HTTPException(status_code=404, detail="Registro no encontrado")
    
    db.delete(db_registro)
    db.commit()
    return {"message": "Registro eliminado"}
