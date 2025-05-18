from sqlalchemy import Boolean, Column, Integer, String, ForeignKey, Date, DateTime, Text, Enum
from sqlalchemy.orm import relationship

from app.database.database import Base

class Carrera(Base):
    __tablename__ = "Carreras"

    id = Column(Integer, primary_key=True, index=True, autoincrement=True)
    nombre = Column(String(255), nullable=False)

    # Relaciones
    estudiantes = relationship("Estudiante", back_populates="carrera")


class Estudiante(Base):
    __tablename__ = "Estudiantes"

    cedula = Column(Integer, primary_key=True, index=True)
    nombre = Column(String(255), nullable=False)
    apellido = Column(String(255), nullable=False)
    id_carrera = Column(Integer, ForeignKey("Carreras.id"))

    # Relaciones
    carrera = relationship("Carrera", back_populates="estudiantes")
    tarjetas = relationship("Tarjeta", back_populates="estudiante", cascade="all, delete-orphan")
    pagos = relationship("Pago", back_populates="estudiante", cascade="all, delete-orphan")


class Usuario(Base):
    __tablename__ = "Usuarios"

    id = Column(Integer, primary_key=True, index=True, autoincrement=True)
    nombre = Column(String(255), nullable=False)
    apellido = Column(String(255), nullable=False)
    correo_electronico = Column(String(255), nullable=False)
    hash_contraseña = Column(String(255), nullable=False)


class Configuracion(Base):
    __tablename__ = "Configuracion"

    id = Column(Integer, primary_key=True, index=True, autoincrement=True)
    parametro = Column(String(255), unique=True, nullable=False)
    valor = Column(String(255), nullable=False)
    descripcion = Column(Text)


class Tarjeta(Base):
    __tablename__ = "Tarjetas"

    id = Column(Integer, primary_key=True, index=True, autoincrement=True)
    serial = Column(String(20), nullable=False)
    estudiante_cedula = Column(Integer, ForeignKey("Estudiantes.cedula", ondelete="CASCADE", onupdate="CASCADE"), nullable=False, index=True)
    fecha_emision = Column(Date, nullable=False)
    fecha_expiracion = Column(Date, nullable=False)
    activa = Column(Boolean, nullable=False, default=True)

    # Relaciones
    estudiante = relationship("Estudiante", back_populates="tarjetas")
    registros = relationship("Registro", back_populates="tarjeta", cascade="all, delete-orphan")


class Cuota(Base):
    __tablename__ = "Cuotas"

    id = Column(Integer, primary_key=True, index=True, autoincrement=True)
    nombre_cuota = Column(String(255), nullable=False)
    fecha_pago = Column(Date)

    # Relaciones
    pagos = relationship("Pago", back_populates="cuota", cascade="all, delete-orphan")


class Controlador(Base):
    __tablename__ = "Controladores"

    id = Column(Integer, primary_key=True, index=True, autoincrement=True)
    mac = Column(String(17), nullable=False)
    ubicacion = Column(String(255), nullable=False)
    funcion = Column(Enum('LECTOR', 'ESCRITOR', name='funcion_enum'), nullable=False, default='LECTOR')
    tipo_acceso = Column(Enum('ENTRADA', 'SALIDA', name='tipo_acceso_enum'), nullable=False, default='ENTRADA')

    # Relaciones
    registros = relationship("Registro", back_populates="controlador", cascade="all, delete-orphan")


class Pago(Base):
    __tablename__ = "Pagos"

    id = Column(Integer, primary_key=True, index=True, autoincrement=True)
    estudiante_cedula = Column(Integer, ForeignKey("Estudiantes.cedula", ondelete="CASCADE", onupdate="CASCADE"), nullable=False)
    cuota_id = Column(Integer, ForeignKey("Cuotas.id", ondelete="CASCADE", onupdate="CASCADE"), nullable=False)
    estado = Column(Enum('PAGADO', 'PENDIENTE', 'VENCIDO', name='estado_enum'), nullable=False, default='PENDIENTE')

    # Relaciones
    estudiante = relationship("Estudiante", back_populates="pagos")
    cuota = relationship("Cuota", back_populates="pagos")


class Registro(Base):
    __tablename__ = "Registros"

    id = Column(Integer, primary_key=True, index=True, autoincrement=True)
    id_tarjeta = Column(Integer, ForeignKey("Tarjetas.id", ondelete="CASCADE", onupdate="CASCADE"), nullable=False)
    id_controlador = Column(Integer, ForeignKey("Controladores.id", ondelete="CASCADE", onupdate="CASCADE"), nullable=False)
    fecha_hora = Column(DateTime, nullable=False)
    acceso_permitido = Column(Boolean, nullable=False)

    # Relaciones
    tarjeta = relationship("Tarjeta", back_populates="registros")
    controlador = relationship("Controlador", back_populates="registros")
