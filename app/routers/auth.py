from fastapi import APIRouter, Depends, HTTPException, status
from fastapi.security import OAuth2PasswordRequestForm
from sqlalchemy.orm import Session
from datetime import timedelta
from app.database.database import get_db
from app.auth.auth import authenticate_user, create_access_token, JWT_EXPIRATION_MINUTES
from app.schemas.schemas import Token, UsuarioCreate, Usuario
from app.models.models import Usuario as UsuarioModel
from app.auth.auth import get_password_hash

router = APIRouter(tags=["autenticación"])

@router.post("/token", response_model=Token)
async def login_for_access_token(form_data: OAuth2PasswordRequestForm = Depends(), db: Session = Depends(get_db)):
    user = authenticate_user(db, form_data.username, form_data.password)
    if not user:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Correo electrónico o contraseña incorrectos",
            headers={"WWW-Authenticate": "Bearer"},
        )
    access_token_expires = timedelta(minutes=JWT_EXPIRATION_MINUTES)
    access_token = create_access_token(
        data={"sub": user.correo_electronico}, expires_delta=access_token_expires
    )
    return {"access_token": access_token, "token_type": "bearer"}

@router.post("/usuarios/", response_model=Usuario, status_code=status.HTTP_201_CREATED)
def crear_usuario(usuario: UsuarioCreate, db: Session = Depends(get_db)):
    db_user = db.query(UsuarioModel).filter(UsuarioModel.correo_electronico == usuario.correo_electronico).first()
    if db_user:
        raise HTTPException(status_code=400, detail="El correo electrónico ya está registrado")
    
    hashed_password = get_password_hash(usuario.contraseña)
    db_user = UsuarioModel(
        nombre=usuario.nombre,
        apellido=usuario.apellido,
        correo_electronico=usuario.correo_electronico,
        hash_contraseña=hashed_password
    )
    db.add(db_user)
    db.commit()
    db.refresh(db_user)
    return db_user
