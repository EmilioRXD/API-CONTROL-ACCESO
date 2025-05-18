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
logger.info("Environment variables:")
logger.info(f"DATABASE_URL: {os.getenv('DATABASE_URL')}")
logger.info(f"DATABASE_HOST: {os.getenv('DATABASE_HOST')}")
logger.info(f"DATABASE_PORT: {os.getenv('DATABASE_PORT')}")
logger.info(f"DATABASE_USER: {os.getenv('DATABASE_USER')}")
logger.info(f"DATABASE_PASSWORD: {os.getenv('DATABASE_PASSWORD')}")
logger.info(f"DATABASE_NAME: {os.getenv('DATABASE_NAME')}")


# Build the connection string with connection pool settings
DATABASE_URL = f"mysql+pymysql://{os.getenv('DATABASE_USER', 'control_user')}:{os.getenv('DATABASE_PASSWORD', 'control_password')}@mysql:3306/{os.getenv('DATABASE_NAME', 'control_acceso')}"


logger.info(f"Attempting to connect to database at: {DATABASE_URL}")

# Configure engine with connection pool settings
engine = create_engine(
    DATABASE_URL,
    echo=True,
    pool_size=5,
    max_overflow=10,
    pool_timeout=30,
    pool_recycle=1800,  # Recycle connections after 30 minutes
    connect_args={"connect_timeout": 10}  # Add connection timeout
)

# Test the connection
try:
    with engine.connect() as connection:
        logger.info("Successfully connected to database")
        result = connection.execute("SELECT 1").fetchone()
        logger.info(f"Test query result: {result}")
except Exception as e:
    logger.error(f"Failed to connect to database: {str(e)}")

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
