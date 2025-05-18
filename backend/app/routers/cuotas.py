from datetime import date
from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List
from app.database.database import get_db
from app.models.models import Cuota as CuotaModel
from app.schemas.schemas import Cuota, CuotaCreate
from app.auth.auth import get_current_active_user

router = APIRouter(
    prefix="/cuotas",
    tags=["cuotas"],
    dependencies=[Depends(get_current_active_user)]
)

@router.get("/", response_model=List[Cuota])
def listar_cuotas(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    cuotas = db.query(CuotaModel).offset(skip).limit(limit).all()
    return cuotas

@router.post("/", response_model=Cuota, status_code=status.HTTP_201_CREATED)
def crear_cuota(cuota: CuotaCreate, db: Session = Depends(get_db)):
    db_cuota = CuotaModel(
        nombre_cuota=cuota.nombre_cuota,
        fecha_pago=cuota.fecha_pago
    )
    db.add(db_cuota)
    db.commit()
    db.refresh(db_cuota)
    return db_cuota

@router.get("/{cuota_id}", response_model=Cuota)
def obtener_cuota(cuota_id: int, db: Session = Depends(get_db)):
    db_cuota = db.query(CuotaModel).filter(CuotaModel.id == cuota_id).first()
    if db_cuota is None:
        raise HTTPException(status_code=404, detail="Cuota no encontrada")
    return db_cuota

@router.put("/{cuota_id}", response_model=Cuota)
def actualizar_cuota(cuota_id: int, cuota: CuotaCreate, db: Session = Depends(get_db)):
    db_cuota = db.query(CuotaModel).filter(CuotaModel.id == cuota_id).first()
    if db_cuota is None:
        raise HTTPException(status_code=404, detail="Cuota no encontrada")
    
    db_cuota.nombre_cuota = cuota.nombre_cuota
    db_cuota.fecha_pago = cuota.fecha_pago
    db.commit()
    db.refresh(db_cuota)
    return db_cuota

@router.delete("/{cuota_id}", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_cuota(cuota_id: int, db: Session = Depends(get_db)):
    db_cuota = db.query(CuotaModel).filter(CuotaModel.id == cuota_id).first()
    if db_cuota is None:
        raise HTTPException(status_code=404, detail="Cuota no encontrada")
    
    db.delete(db_cuota)
    db.commit()
    return {"message": "Cuota eliminada"}
