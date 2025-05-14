from sqlalchemy.orm import Session
from app.models.models import Configuracion
from app.schemas.schemas import ConfiguracionCreate

# Valores predeterminados para configuraciones
DEFAULT_CONFIG = {
    "PERIODO_GRACIA_DIAS": "5",  # Período de gracia en días para pagos pendientes
    "BLOQUEO_ACCESO_VENCIDOS": "true",  # Bloquear acceso si hay pagos vencidos
}

def get_config_value(db: Session, parametro: str) -> str:
    """Obtiene un valor de configuración o devuelve el valor predeterminado si no existe."""
    config = db.query(Configuracion).filter(Configuracion.parametro == parametro).first()
    
    if not config:
        # Si no existe la configuración, la creamos con el valor predeterminado
        if parametro in DEFAULT_CONFIG:
            default_value = DEFAULT_CONFIG[parametro]
            new_config = Configuracion(
                parametro=parametro,
                valor=default_value,
                descripcion=f"Configuración automática para {parametro}"
            )
            db.add(new_config)
            db.commit()
            db.refresh(new_config)
            return default_value
        return None
    
    return config.valor

def get_period_grace_days(db: Session) -> int:
    """Obtiene el período de gracia en días para pagos pendientes."""
    try:
        days_str = get_config_value(db, "PERIODO_GRACIA_DIAS")
        return int(days_str)
    except (ValueError, TypeError):
        # Si hay error en la conversión, devolvemos el valor predeterminado
        return int(DEFAULT_CONFIG["PERIODO_GRACIA_DIAS"])

def is_blocking_access_for_overdue(db: Session) -> bool:
    """Determina si se debe bloquear el acceso por pagos vencidos."""
    try:
        block_str = get_config_value(db, "BLOQUEO_ACCESO_VENCIDOS")
        return block_str.lower() == "true"
    except (AttributeError, TypeError):
        # Si hay error, devolvemos el valor predeterminado
        return DEFAULT_CONFIG["BLOQUEO_ACCESO_VENCIDOS"].lower() == "true"
