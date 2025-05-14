from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from app.database.database import get_db
from app.models.models import Carrera as CarreraModel
from app.schemas.schemas import Carrera, CarreraCreate
from app.auth.auth import get_current_active_user

router = APIRouter(
    prefix="/carreras",
    tags=["carreras"],
    dependencies=[Depends(get_current_active_user)]
)

@router.get("/", response_model=List[Carrera])
def listar_carreras(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    carreras = db.query(CarreraModel).offset(skip).limit(limit).all()
    return carreras

@router.post("/", response_model=Carrera, status_code=status.HTTP_201_CREATED)
def crear_carrera(carrera: CarreraCreate, db: Session = Depends(get_db)):
    db_carrera = CarreraModel(nombre=carrera.nombre)
    db.add(db_carrera)
    db.commit()
    db.refresh(db_carrera)
    return db_carrera

@router.get("/{carrera_id}", response_model=Carrera)
def obtener_carrera(carrera_id: int, db: Session = Depends(get_db)):
    db_carrera = db.query(CarreraModel).filter(CarreraModel.id == carrera_id).first()
    if db_carrera is None:
        raise HTTPException(status_code=404, detail="Carrera no encontrada")
    return db_carrera

@router.put("/{carrera_id}", response_model=Carrera)
def actualizar_carrera(carrera_id: int, carrera: CarreraCreate, db: Session = Depends(get_db)):
    db_carrera = db.query(CarreraModel).filter(CarreraModel.id == carrera_id).first()
    if db_carrera is None:
        raise HTTPException(status_code=404, detail="Carrera no encontrada")
    
    db_carrera.nombre = carrera.nombre
    db.commit()
    db.refresh(db_carrera)
    return db_carrera

@router.delete("/{carrera_id}", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_carrera(carrera_id: int, db: Session = Depends(get_db)):
    db_carrera = db.query(CarreraModel).filter(CarreraModel.id == carrera_id).first()
    if db_carrera is None:
        raise HTTPException(status_code=404, detail="Carrera no encontrada")
    
    db.delete(db_carrera)
    db.commit()
    return {"message": "Carrera eliminada"}
