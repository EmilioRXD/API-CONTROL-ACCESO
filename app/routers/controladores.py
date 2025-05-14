from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from app.database.database import get_db
from app.models.models import Controlador as ControladorModel
from app.schemas.schemas import Controlador, ControladorCreate
from app.auth.auth import get_current_active_user

router = APIRouter(
    prefix="/controladores",
    tags=["controladores"],
    dependencies=[Depends(get_current_active_user)]
)

@router.get("/", response_model=List[Controlador])
def listar_controladores(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    controladores = db.query(ControladorModel).offset(skip).limit(limit).all()
    return controladores

@router.post("/", response_model=Controlador, status_code=status.HTTP_201_CREATED)
def crear_controlador(controlador: ControladorCreate, db: Session = Depends(get_db)):
    # Verificar si ya existe un controlador con esa MAC
    db_controlador_existente = db.query(ControladorModel).filter(ControladorModel.mac == controlador.mac).first()
    if db_controlador_existente:
        raise HTTPException(status_code=400, detail="Ya existe un controlador con esa dirección MAC")
    
    db_controlador = ControladorModel(
        mac=controlador.mac,
        ubicacion=controlador.ubicacion,
        funcion=controlador.funcion,
        tipo_acceso=controlador.tipo_acceso
    )
    db.add(db_controlador)
    db.commit()
    db.refresh(db_controlador)
    return db_controlador

@router.get("/{controlador_id}", response_model=Controlador)
def obtener_controlador(controlador_id: int, db: Session = Depends(get_db)):
    db_controlador = db.query(ControladorModel).filter(ControladorModel.id == controlador_id).first()
    if db_controlador is None:
        raise HTTPException(status_code=404, detail="Controlador no encontrado")
    return db_controlador

@router.get("/mac/{mac}", response_model=Controlador)
def obtener_controlador_por_mac(mac: str, db: Session = Depends(get_db)):
    db_controlador = db.query(ControladorModel).filter(ControladorModel.mac == mac).first()
    if db_controlador is None:
        raise HTTPException(status_code=404, detail="Controlador no encontrado")
    return db_controlador

@router.put("/{controlador_id}", response_model=Controlador)
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

@router.delete("/{controlador_id}", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_controlador(controlador_id: int, db: Session = Depends(get_db)):
    db_controlador = db.query(ControladorModel).filter(ControladorModel.id == controlador_id).first()
    if db_controlador is None:
        raise HTTPException(status_code=404, detail="Controlador no encontrado")
    
    db.delete(db_controlador)
    db.commit()
    return {"message": "Controlador eliminado"}
