from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from app.database.database import get_db
from app.models.models import Configuracion as ConfiguracionModel
from app.schemas.schemas import Configuracion, ConfiguracionCreate
from app.auth.auth import get_current_active_user

router = APIRouter(
    prefix="/configuracion",
    tags=["configuracion"],
    dependencies=[Depends(get_current_active_user)]
)

@router.get("/", response_model=List[Configuracion])
def listar_configuraciones(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    configuraciones = db.query(ConfiguracionModel).offset(skip).limit(limit).all()
    return configuraciones

@router.post("/", response_model=Configuracion, status_code=status.HTTP_201_CREATED)
def crear_configuracion(configuracion: ConfiguracionCreate, db: Session = Depends(get_db)):
    db_config = db.query(ConfiguracionModel).filter(ConfiguracionModel.parametro == configuracion.parametro).first()
    if db_config:
        raise HTTPException(status_code=400, detail="El parámetro ya existe")
    
    db_config = ConfiguracionModel(
        parametro=configuracion.parametro,
        valor=configuracion.valor,
        descripcion=configuracion.descripcion
    )
    db.add(db_config)
    db.commit()
    db.refresh(db_config)
    return db_config

@router.get("/{config_id}", response_model=Configuracion)
def obtener_configuracion(config_id: int, db: Session = Depends(get_db)):
    db_config = db.query(ConfiguracionModel).filter(ConfiguracionModel.id == config_id).first()
    if db_config is None:
        raise HTTPException(status_code=404, detail="Configuración no encontrada")
    return db_config

@router.get("/parametro/{parametro}", response_model=Configuracion)
def obtener_configuracion_por_parametro(parametro: str, db: Session = Depends(get_db)):
    db_config = db.query(ConfiguracionModel).filter(ConfiguracionModel.parametro == parametro).first()
    if db_config is None:
        raise HTTPException(status_code=404, detail="Configuración no encontrada")
    return db_config

@router.put("/{config_id}", response_model=Configuracion)
def actualizar_configuracion(config_id: int, configuracion: ConfiguracionCreate, db: Session = Depends(get_db)):
    db_config = db.query(ConfiguracionModel).filter(ConfiguracionModel.id == config_id).first()
    if db_config is None:
        raise HTTPException(status_code=404, detail="Configuración no encontrada")
    
    # Verificar si el parámetro ya existe en otra configuración
    existing_config = db.query(ConfiguracionModel).filter(
        ConfiguracionModel.parametro == configuracion.parametro, 
        ConfiguracionModel.id != config_id
    ).first()
    if existing_config:
        raise HTTPException(status_code=400, detail="El parámetro ya existe en otra configuración")
    
    # Actualizar los campos
    db_config.parametro = configuracion.parametro
    db_config.valor = configuracion.valor
    db_config.descripcion = configuracion.descripcion
    
    db.commit()
    db.refresh(db_config)
    return db_config

@router.delete("/{config_id}", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_configuracion(config_id: int, db: Session = Depends(get_db)):
    db_config = db.query(ConfiguracionModel).filter(ConfiguracionModel.id == config_id).first()
    if db_config is None:
        raise HTTPException(status_code=404, detail="Configuración no encontrada")
    
    db.delete(db_config)
    db.commit()
    return {"message": "Configuración eliminada"}
