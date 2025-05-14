from fastapi import FastAPI, Depends
from fastapi.middleware.cors import CORSMiddleware
from app.database.database import engine, Base
from app.auth.auth import get_current_active_user

# Importar los routers
from app.routers import auth, carreras, estudiantes, usuarios, configuracion
from app.routers import tarjetas, cuotas, controladores, pagos, registros, validaciones

# Crear tablas en la base de datos
Base.metadata.create_all(bind=engine)

app = FastAPI(
    title="Sistema de Control de Acceso y Pagos",
    description="API para el sistema de control de acceso y pagos de estudiantes",
    version="1.0.0"
)

# Configurar CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Permite todas las origins en desarrollo
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Incluir los routers
app.include_router(auth.router)
app.include_router(carreras.router)
app.include_router(estudiantes.router)
app.include_router(usuarios.router)
app.include_router(configuracion.router)
app.include_router(tarjetas.router)
app.include_router(cuotas.router)
app.include_router(controladores.router)
app.include_router(pagos.router)
app.include_router(registros.router)
app.include_router(validaciones.router)

@app.get("/", tags=["root"])
async def root():
    return {"message": "Bienvenido a la API del Sistema de Control de Acceso y Pagos"}

@app.get("/status", tags=["status"])
async def status():
    return {"status": "online"}

@app.get("/protected", tags=["test"])
async def protected_route(current_user = Depends(get_current_active_user)):
    return {"message": "Ruta protegida accedida correctamente", "user": current_user.correo_electronico}
