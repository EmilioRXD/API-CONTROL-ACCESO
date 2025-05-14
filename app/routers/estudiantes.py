from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session, joinedload
from typing import List
from app.database.database import get_db
from app.models.models import Estudiante as EstudianteModel
from app.schemas.schemas import Estudiante, EstudianteCreate, EstudianteDetalle, EstudianteCarrera
from app.auth.auth import get_current_active_user

router = APIRouter(
    prefix="/estudiantes",
    tags=["estudiantes"],
    dependencies=[Depends(get_current_active_user)]
)

@router.get("/", response_model=List[EstudianteCarrera])
def listar_estudiantes(skip: int = 0, limit: int = 100, db: Session = Depends(get_db)):
    estudiantes = db.query(EstudianteModel).options(joinedload(EstudianteModel.carrera)).offset(skip).limit(limit).all()
    
    # Transformar resultados para incluir el nombre de la carrera directamente
    result = []
    for estudiante in estudiantes:
        estudiante_dict = {
            "cedula": estudiante.cedula,
            "nombre": estudiante.nombre,
            "apellido": estudiante.apellido,
            "id_carrera": estudiante.id_carrera,
            "nombre_carrera": estudiante.carrera.nombre if estudiante.carrera else None
        }
        result.append(estudiante_dict)
    
    return result

@router.post("/", response_model=Estudiante, status_code=status.HTTP_201_CREATED)
def crear_estudiante(estudiante: EstudianteCreate, db: Session = Depends(get_db)):
    db_estudiante = db.query(EstudianteModel).filter(EstudianteModel.cedula == estudiante.cedula).first()
    if db_estudiante:
        raise HTTPException(status_code=400, detail="La cédula ya está registrada")
    
    db_estudiante = EstudianteModel(
        cedula=estudiante.cedula,
        nombre=estudiante.nombre,
        apellido=estudiante.apellido,
        id_carrera=estudiante.id_carrera
    )
    db.add(db_estudiante)
    db.commit()
    db.refresh(db_estudiante)
    return db_estudiante

@router.get("/{cedula}", response_model=EstudianteDetalle)
def obtener_estudiante(cedula: int, db: Session = Depends(get_db)):
    db_estudiante = db.query(EstudianteModel).filter(EstudianteModel.cedula == cedula).first()
    if db_estudiante is None:
        raise HTTPException(status_code=404, detail="Estudiante no encontrado")
    return db_estudiante

@router.put("/{cedula}", response_model=Estudiante)
def actualizar_estudiante(cedula: int, estudiante: EstudianteCreate, db: Session = Depends(get_db)):
    db_estudiante = db.query(EstudianteModel).filter(EstudianteModel.cedula == cedula).first()
    if db_estudiante is None:
        raise HTTPException(status_code=404, detail="Estudiante no encontrado")
    
    # Actualizar solo los campos proporcionados
    db_estudiante.nombre = estudiante.nombre
    db_estudiante.apellido = estudiante.apellido
    db_estudiante.id_carrera = estudiante.id_carrera
    
    db.commit()
    db.refresh(db_estudiante)
    return db_estudiante

@router.delete("/{cedula}", status_code=status.HTTP_204_NO_CONTENT)
def eliminar_estudiante(cedula: int, db: Session = Depends(get_db)):
    db_estudiante = db.query(EstudianteModel).filter(EstudianteModel.cedula == cedula).first()
    if db_estudiante is None:
        raise HTTPException(status_code=404, detail="Estudiante no encontrado")
    
    db.delete(db_estudiante)
    db.commit()
    return {"message": "Estudiante eliminado"}
