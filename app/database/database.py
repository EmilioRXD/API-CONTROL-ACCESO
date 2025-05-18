from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker
import os
from dotenv import load_dotenv
import logging

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Cargar variables de entorno
load_dotenv()

# Log the environment variables
logger.info(f"Loaded environment variables: {os.environ}")

# MySQL Database Configuration
DATABASE_HOST = os.getenv("DATABASE_HOST", "localhost")
DATABASE_PORT = os.getenv("DATABASE_PORT", "8000")  # Changed to default MySQL port
DATABASE_USER = os.getenv("DATABASE_USER", "root")
DATABASE_PASSWORD = os.getenv("DATABASE_PASSWORD", "")  # Empty string for local development
DATABASE_NAME = os.getenv("DATABASE_NAME", "control_acceso")

# Build the connection string with connection pool settings
DATABASE_URL = os.getenv("DATABASE_URL")

logger.info(f"Attempting to connect to database at: {DATABASE_URL}")

# Configure engine with connection pool settings
engine = create_engine(
    DATABASE_URL,
    pool_size=5,
    max_overflow=10,
    pool_timeout=30,
    pool_recycle=1800  # Recycle connections after 30 minutes
)

SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

Base = declarative_base()

# Dependencia para obtener la sesión de la base de datos
def get_db():
    db = SessionLocal()
    try:
        yield db
    except Exception as e:
        db.rollback()
        raise e
    finally:
        db.close()
