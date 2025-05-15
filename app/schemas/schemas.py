from pydantic import BaseModel, Field, EmailStr
from typing import Optional, List
from datetime import date, datetime
from enum import Enum

# Enums
class FuncionEnum(str, Enum):
    LECTOR = "LECTOR"
    ESCRITOR = "ESCRITOR"

class TipoAccesoEnum(str, Enum):
    ENTRADA = "ENTRADA"
    SALIDA = "SALIDA"

class EstadoEnum(str, Enum):
    PAGADO = "PAGADO"
    PENDIENTE = "PENDIENTE"
    VENCIDO = "VENCIDO"

# Carrera
class CarreraBase(BaseModel):
    nombre: str

class CarreraCreate(CarreraBase):
    pass

class Carrera(CarreraBase):
    id: int

    class Config:
        from_attributes = True

# Estudiante
class EstudianteBase(BaseModel):
    nombre: str
    apellido: str
    id_carrera: Optional[int] = None

class EstudianteCreate(EstudianteBase):
    cedula: int

class Estudiante(EstudianteBase):
    cedula: int
    
    class Config:
        from_attributes = True

class EstudianteDetalle(Estudiante):
    carrera: Optional[Carrera] = None
    
    class Config:
        from_attributes = True

class EstudianteCarrera(Estudiante):
    nombre_carrera: Optional[str] = None
    
    class Config:
        from_attributes = True

# Usuario
class UsuarioBase(BaseModel):
    nombre: str
    apellido: str
    correo_electronico: str

class UsuarioCreate(UsuarioBase):
    contraseña: str

class Usuario(UsuarioBase):
    id: int
    
    class Config:
        from_attributes = True

# Configuracion
class ConfiguracionBase(BaseModel):
    parametro: str
    valor: str
    descripcion: Optional[str] = None

class ConfiguracionCreate(ConfiguracionBase):
    pass

class Configuracion(ConfiguracionBase):
    id: int
    
    class Config:
        from_attributes = True

# Tarjeta
class TarjetaBase(BaseModel):
    serial: str
    estudiante_cedula: int
    fecha_emision: date
    fecha_expiracion: date
    activa: bool = True

class TarjetaCreate(TarjetaBase):
    pass

class Tarjeta(TarjetaBase):
    id: int
    
    class Config:
        from_attributes = True

class TarjetaDetalle(Tarjeta):
    nombre_estudiante: str
    apellido_estudiante: str
    
    class Config:
        from_attributes = True

# Cuota
class CuotaBase(BaseModel):
    nombre_cuota: str
    fecha_pago: Optional[date] = None

class CuotaCreate(CuotaBase):
    pass

class Cuota(CuotaBase):
    id: int
    
    class Config:
        from_attributes = True

# Controlador
class ControladorBase(BaseModel):
    mac: str
    ubicacion: str
    funcion: FuncionEnum = FuncionEnum.LECTOR
    tipo_acceso: TipoAccesoEnum = TipoAccesoEnum.ENTRADA

class ControladorCreate(ControladorBase):
    pass

class Controlador(ControladorBase):
    id: int
    
    class Config:
        from_attributes = True

# Pago
class PagoBase(BaseModel):
    estudiante_cedula: int
    cuota_id: int
    estado: EstadoEnum = EstadoEnum.PENDIENTE

class PagoCreate(PagoBase):
    pass

class Pago(PagoBase):
    id: int
    
    class Config:
        from_attributes = True

class PagoDetalle(Pago):
    estudiante: Estudiante
    cuota: Cuota
    
    class Config:
        from_attributes = True

class PagoListado(BaseModel):
    id: int
    estudiante_cedula: int
    nombre_estudiante: str
    apellido_estudiante: str
    nombre_cuota: str
    fecha_vencimiento: Optional[date] = None
    estado: EstadoEnum
    
    class Config:
        from_attributes = True

# Registro
class RegistroBase(BaseModel):
    id_tarjeta: int
    id_controlador: int
    fecha_hora: datetime
    acceso_permitido: bool

class RegistroCreate(RegistroBase):
    pass

class Registro(RegistroBase):
    id: int
    
    class Config:
        from_attributes = True

class RegistroDetalle(Registro):
    tarjeta: Tarjeta
    controlador: Controlador
    
    class Config:
        from_attributes = True

# Token
class Token(BaseModel):
    access_token: str
    token_type: str

class TokenData(BaseModel):
    email: Optional[str] = None
    id: Optional[int] = None

# Validación de tarjeta desde ESP32
class ValidacionTarjeta(BaseModel):
    serial: str
    mac_controlador: str
    cedula_estudiante: int

class RespuestaValidacion(BaseModel):
    acceso_permitido: bool
    mensaje: str

# Asignación de tarjeta mediante MQTT
class AsignacionTarjeta(BaseModel):
    estudiante_cedula: int
    mac_escritor: str
    fecha_emision: Optional[date] = None
    fecha_expiracion: Optional[date] = None

class RespuestaAsignacion(BaseModel):
    success: bool
    mensaje: str
    serial: Optional[str] = None
    tarjeta_id: Optional[int] = None
