#include <EEPROM.h>
#include <HTTPClient.h>
#include "web_portal.h"
#include "access_control.h"
#include <ArduinoJson.h>
 
#define LED_R 4
#define LED_G 16
#define EEPROM_SIZE 512
#define MQTT_PORT 2005
#include "common_defs.h" // Incluir definiciones comunes
const int buzzerPin = 13;

enum Mode {
  MODE_CONFIG,
  MODE_READER,
  MODE_NONE
};

Mode driver_mode = MODE_CONFIG;
//funciones de prueba

//una vez
void _acceso_permitido() {
  Serial.println("[INFO]: Acceso permitido.");

  
  delay(200); digitalWrite(LED_G, HIGH);
  tone(buzzerPin, 523); delay(150);  // C5
  tone(buzzerPin, 659); delay(150);  // E5
  tone(buzzerPin, 784); delay(200);  // G5
  noTone(buzzerPin);
  digitalWrite(LED_G, LOW);
  
}
//tres veces
void _acceso_denegado() {
  Serial.println("[INFO]: Acceso denegado.");
  for (int i = 0; i < 3; i++) {
    delay(200); digitalWrite(LED_R, HIGH); tone(buzzerPin, 300);
    delay(100); digitalWrite(LED_R, LOW); noTone(buzzerPin);
  }
}


void setup() {
  Serial.begin(115200);
  EEPROM.begin(EEPROM_SIZE);
  pinMode(LED_G, OUTPUT);
  pinMode(LED_R, OUTPUT);

  InitCardReader();

  bool result = tryConnect();
  if (result) {driver_mode = MODE_READER;}
}


void loop() {
  serverProcess();

  if (driver_mode == MODE_READER){
    //si se lee una tarjeta.
    if (ScanCards() == 0) {
      HTTPClient http;
      JsonDocument doc;

      String server_path = EEPROM.readString(SERVER_IP_ADDR) + "/validaciones/tarjeta";
      http.begin(server_path.c_str());
      http.addHeader("Content-Type", "application/json");
      String serial = GetActualUID();

      doc["serial"] = serial;
      doc["cedula_estudiante"] = 30998394;
      doc["mac_controlador"] = GetMacAddress();


      String JsonPostRequest = String();
      serializeJson(doc, JsonPostRequest);
      Serial.println(JsonPostRequest);

      int http_rc = http.POST(JsonPostRequest);

      if (http_rc < 0) {
        Serial.print("Error code: "); Serial.println();
        return;
      }

      Serial.print("HTTP Response code: "); Serial.println(http_rc);
      String payload = http.getString();
      deserializeJson(doc, payload.c_str());
      Serial.println(payload);

      bool permitido = bool(doc["acceso_permitido"]);
      String mensaje = String(doc["mensaje"]);

      if (permitido) {
        _acceso_permitido();
      }
      else {
        Serial.print("[ERROR]:"); Serial.println(mensaje);
        _acceso_denegado();
      }
      http.end();
    }
    else if (driver_mode == MODE_NONE) {

    }

  }
}
