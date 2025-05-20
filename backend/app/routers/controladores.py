from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from app.database.database import get_db
from app.models.models import Controlador as ControladorModel
from app.schemas.schemas import Controlador, ControladorCreate
from app.auth.auth import get_current_active_user

router = APIRouter(
    prefix="/controladores",
    tags=["controladores"]
)

@router.get("/", response_model=List[Controlador], dependencies=[Depends(get_current_active_user)])
def listar_controladores(skip: int = 0, limit: int = 100, tipo: str = None, db: Session = Depends(get_db)):
    query = db.query(ControladorModel)
    
    # Si se proporciona el parámetro tipo, filtramos por función
    if tipo:
        # Manejar el caso especial para WRITER
        if tipo == "WRITER":
            tipo = "ESCRITOR"
        query = query.filter(ControladorModel.funcion == tipo)
        
    controladores = query.offset(skip).limit(limit).all()
    return controladores

# Endpoint para crear controlador sin autenticación
@router.post("/", response_model=Controlador, status_code=status.HTTP_201_CREATED, dependencies=[])
def crear_controlador(controlador: ControladorCreate, db: Session = Depends(get_db)):
    # Verificar si ya existe un controlador con esa MAC
    db_controlador_existente = db.query(ControladorModel).filter(ControladorModel.mac == controlador.mac).first()
    
    if db_controlador_existente:
        # Si ya existe, actualizar los campos en lugar de lanzar un error
        db_controlador_existente.ubicacion = controlador.ubicacion
        db_controlador_existente.funcion = controlador.funcion
        db_controlador_existente.tipo_acceso = controlador.tipo_acceso
        
        db.commit()
        db.refresh(db_controlador_existente)
        return db_controlador_existente
    
    # Si no existe, crear nuevo controlador
    nuevo_controlador = ControladorModel(
        mac=controlador.mac,
        ubicacion=controlador.ubicacion,
        funcion=controlador.funcion,
        tipo_acceso=controlador.tipo_acceso
    )
    db.add(nuevo_controlador)
    db.commit()
    db.refresh(nuevo_controlador)
    return nuevo_controlador

@router.get("/{controlador_id}", response_model=Controlador, dependencies=[Depends(get_current_active_user)])
def obtener_controlador(controlador_id: int, db: Session = Depends(get_db)):
    db_controlador = db.query(ControladorModel).filter(ControladorModel.id == controlador_id).first()
    if db_controlador is None:
        raise HTTPException(status_code=404, detail="Controlador no encontrado")
    return db_controlador

@router.get("/mac/{mac}", response_model=Controlador, dependencies=[Depends(get_current_active_user)])
def obtener_controlador_por_mac(mac: str, db: Session = Depends(get_db)):
    db_controlador = db.query(ControladorModel).filter(ControladorModel.mac == mac).first()
    if db_controlador is None:
        raise HTTPException(status_code=404, detail="Controlador no encontrado")
    return db_controlador

@router.put("/{controlador_id}", response_model=Controlador, dependencies=[Depends(get_current_active_user)])
def actualizar_controlador(controlador_id: int, controlador: ControladorCreate, db: Session = Depends(get_db)):
    db_controlador = db.query(ControladorModel).filter(ControladorModel.id == controlador_id).first()
    if db_controlador is None:
        raise HTTPException(status_code=404, detail="Controlador no encontrado")
    
    # Verificar si la MAC ya existe en otro controlador
    mac_existe = db.query(ControladorModel).filter(
        ControladorModel.mac == controlador.mac,
        ControladorModel.id != controlador_id
    ).first()
    if mac_existe:
        raise HTTPException(status_code=400, detail="Ya existe un controlador con esa dirección MAC")
    
    # Actualizar los campos
    db_controlador.mac = controlador.mac
    db_controlador.ubicacion = controlador.ubicacion
    db_controlador.funcion = controlador.funcion
    db_controlador.tipo_acceso = controlador.tipo_acceso
    
    db.commit()
    db.refresh(db_controlador)
    return db_controlador

@router.delete("/{controlador_id}", status_code=status.HTTP_204_NO_CONTENT, dependencies=[Depends(get_current_active_user)])
def eliminar_controlador(controlador_id: int, db: Session = Depends(get_db)):
    db_controlador = db.query(ControladorModel).filter(ControladorModel.id == controlador_id).first()
    if db_controlador is None:
        raise HTTPException(status_code=404, detail="Controlador no encontrado")
    
    db.delete(db_controlador)
    db.commit()
    return {"message": "Controlador eliminado"}
