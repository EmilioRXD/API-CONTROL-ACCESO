import paho.mqtt.client as mqtt
import json
import time
import logging
import threading
import uuid
from typing import Dict, Any, Optional, Callable

# Configurar logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class MQTTService:
    def __init__(self, broker_host="localhost", broker_port=1883, client_id=None):
        """
        Inicializa el servicio MQTT para comunicarse con los dispositivos ESP32.
        
        Args:
            broker_host: Host del broker MQTT
            broker_port: Puerto del broker MQTT
            client_id: ID de cliente opcional (se genera uno aleatorio si no se proporciona)
        """
        self.broker_host = broker_host
        self.broker_port = broker_port
        self.client_id = client_id or f"backend-{uuid.uuid4().hex[:8]}"
        self.client = mqtt.Client(client_id=self.client_id)
        
        # Configurar callbacks
        self.client.on_connect = self._on_connect
        self.client.on_message = self._on_message
        self.client.on_disconnect = self._on_disconnect
        
        # Mapeo de tópicos a callbacks
        self.topic_callbacks = {}
        
        # Información de solicitudes pendientes
        self.pending_requests = {}
        
        # Estado de conexión
        self.connected = False
        
        # Inicializar bloqueo
        self.lock = threading.Lock()
    
    def connect(self):
        """Conecta al broker MQTT."""
        try:
            self.client.connect(self.broker_host, self.broker_port, 60)
            # Inicia el bucle en un hilo separado
            self.client.loop_start()
            return True
        except Exception as e:
            logger.error(f"Error al conectar con el broker MQTT: {str(e)}")
            return False
    
    def disconnect(self):
        """Desconecta del broker MQTT."""
        self.client.loop_stop()
        self.client.disconnect()
    
    def _on_connect(self, client, userdata, flags, rc):
        """Callback cuando se establece la conexión."""
        if rc == 0:
            logger.info("Conectado al broker MQTT")
            self.connected = True
        else:
            logger.error(f"Error de conexión con código: {rc}")
            self.connected = False
    
    def _on_disconnect(self, client, userdata, rc):
        """Callback cuando se desconecta del broker."""
        logger.info(f"Desconectado del broker MQTT con código: {rc}")
        self.connected = False
    
    def _on_message(self, client, userdata, msg):
        """Callback cuando se recibe un mensaje."""
        try:
            logger.info(f"Mensaje recibido en tópico {msg.topic}: {msg.payload.decode()}")
            
            # Buscar en las suscripciones registradas
            for topic_pattern, callback in self.topic_callbacks.items():
                # Coincidencia exacta o con comodines
                if msg.topic == topic_pattern or ('+' in topic_pattern and self._match_topic(msg.topic, topic_pattern)):
                    callback(msg.topic, msg.payload.decode())
                    break
            
            # Procesar solicitudes pendientes
            with self.lock:
                for request_id, request_info in list(self.pending_requests.items()):
                    if request_info['response_topic'] == msg.topic:
                        event = request_info.get('event')
                        if event:
                            request_info['response'] = msg.payload.decode()
                            event.set()
        except Exception as e:
            logger.error(f"Error al procesar mensaje MQTT: {str(e)}")
    
    def _match_topic(self, actual_topic, pattern):
        """Comprueba si un tópico coincide con un patrón que incluye comodines."""
        pattern_parts = pattern.split('/')
        actual_parts = actual_topic.split('/')
        
        if len(pattern_parts) != len(actual_parts):
            return False
        
        for i, pattern_part in enumerate(pattern_parts):
            if pattern_part != '+' and pattern_part != actual_parts[i]:
                return False
        
        return True
    
    def subscribe(self, topic, callback):
        """
        Suscribe a un tópico MQTT.
        
        Args:
            topic: Tópico a suscribir
            callback: Función a llamar cuando se recibe un mensaje en este tópico
        """
        try:
            self.client.subscribe(topic)
            self.topic_callbacks[topic] = callback
            logger.info(f"Suscrito al tópico: {topic}")
            return True
        except Exception as e:
            logger.error(f"Error al suscribir al tópico {topic}: {str(e)}")
            return False
    
    def publish(self, topic, payload, qos=0, retain=False):
        """
        Publica un mensaje en un tópico MQTT.
        
        Args:
            topic: Tópico donde publicar
            payload: Mensaje a publicar (puede ser string o dict que se convertirá a JSON)
            qos: Calidad de servicio (0, 1 o 2)
            retain: Si el mensaje debe ser retenido por el broker
        """
        try:
            if isinstance(payload, dict):
                payload = json.dumps(payload)
            
            result = self.client.publish(topic, payload, qos, retain)
            if result.rc == mqtt.MQTT_ERR_SUCCESS:
                logger.info(f"Mensaje publicado en tópico {topic}")
                return True
            else:
                logger.error(f"Error al publicar en tópico {topic}: {result.rc}")
                return False
        except Exception as e:
            logger.error(f"Error al publicar en tópico {topic}: {str(e)}")
            return False
    
    def request_card_assignment(self, writer_mac, student_id, timeout=30):
        """
        Solicita la asignación de una tarjeta a un estudiante a través de un ESP32 escritor.
        
        Args:
            writer_mac: MAC del ESP32 con función ESCRITOR
            student_id: Cédula del estudiante
            timeout: Tiempo de espera máximo en segundos
            
        Returns:
            dict: Respuesta del ESP32 o None si hay error o timeout
        """
        # Generar ID único para esta solicitud
        request_id = str(uuid.uuid4())
        
        # Tópicos para esta operación
        request_topic = f"esp32/{writer_mac}/card/assign"
        response_topic = f"esp32/{writer_mac}/card/response"
        
        # Crear evento para esperar la respuesta
        event = threading.Event()
        
        # Registrar la solicitud pendiente
        with self.lock:
            self.pending_requests[request_id] = {
                'response_topic': response_topic,
                'event': event,
                'response': None,
                'timestamp': time.time()
            }
        
        # Suscribirse al tópico de respuesta si aún no lo está
        if response_topic not in self.topic_callbacks:
            self.subscribe(response_topic, lambda topic, msg: None)  # La lógica principal está en _on_message
        
        # Enviar la solicitud
        payload = {
            'request_id': request_id,
            'student_id': student_id,
            'timestamp': time.time()
        }
        
        if not self.publish(request_topic, payload):
            logger.error(f"Error al publicar solicitud para {writer_mac}")
            with self.lock:
                self.pending_requests.pop(request_id, None)
            return None
        
        # Esperar respuesta o timeout
        if event.wait(timeout):
            # Se recibió respuesta
            with self.lock:
                response_data = self.pending_requests.pop(request_id, {}).get('response')
                
            try:
                if isinstance(response_data, str):
                    response_json = json.loads(response_data)
                    return response_json
                return response_data
            except json.JSONDecodeError:
                logger.error(f"Error al decodificar respuesta JSON: {response_data}")
                return None
        else:
            # Timeout
            logger.warning(f"Timeout al esperar respuesta del ESP32 {writer_mac}")
            with self.lock:
                self.pending_requests.pop(request_id, None)
            return None
    
    def cleanup_expired_requests(self, max_age=60):
        """Limpia las solicitudes pendientes que han expirado."""
        current_time = time.time()
        with self.lock:
            for request_id, request_info in list(self.pending_requests.items()):
                if current_time - request_info['timestamp'] > max_age:
                    logger.info(f"Eliminando solicitud expirada: {request_id}")
                    self.pending_requests.pop(request_id, None)

# Singleton para acceso global
mqtt_service = None

def get_mqtt_service(broker_host=None, broker_port=None):
    """
    Obtiene una instancia singleton del servicio MQTT.
    
    Args:
        broker_host: Host del broker MQTT
        broker_port: Puerto del broker MQTT
        
    Returns:
        MQTTService: Instancia del servicio MQTT
    """
    global mqtt_service
    
    if mqtt_service is None and (broker_host is not None and broker_port is not None):
        mqtt_service = MQTTService(broker_host, broker_port)
        mqtt_service.connect()
    
    return mqtt_service
