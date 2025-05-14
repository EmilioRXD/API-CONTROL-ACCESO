from datetime import date, timedelta

def is_within_grace_period(payment_date: date, grace_days: int) -> bool:
    """
    Determina si la fecha dada está dentro del período de gracia.
    
    Args:
        payment_date: Fecha de vencimiento del pago
        grace_days: Número de días de gracia
    
    Returns:
        True si la fecha + período de gracia es posterior o igual a la fecha actual
    """
    if not payment_date:
        return False
    
    try:
        today = date.today()
        grace_end_date = payment_date + timedelta(days=grace_days)
        return today <= grace_end_date
    except (TypeError, ValueError):
        # En caso de error con las fechas, asumimos que no está en período de gracia
        return False

def days_until_overdue(payment_date: date) -> int:
    """
    Calcula cuántos días faltan para que un pago venza.
    
    Args:
        payment_date: Fecha de vencimiento del pago
    
    Returns:
        Número de días hasta el vencimiento. Negativo si ya venció.
    """
    if not payment_date:
        return 0
    
    try:
        today = date.today()
        delta = payment_date - today
        return delta.days
    except (TypeError, ValueError):
        return 0
