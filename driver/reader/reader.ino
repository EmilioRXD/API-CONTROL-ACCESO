#include <EEPROM.h>
#include <HTTPClient.h>
#include "web_portal.h"
#include "access_control.h"
#include <ArduinoJson.h>
 
#define LED 2
#define EEPROM_SIZE 512
#define MQTT_PORT 2005


//cambiar a ip de computadora y puerto donde se aloja el api
String server_ip = "http://192.168.1.7:8000/";

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

  delay(200); digitalWrite(LED, HIGH);
  delay(500); digitalWrite(LED, LOW);

}
//tres veces
void _acceso_denegado() {
  Serial.println("[INFO]: Acceso denegado.");
  for (int i=0;i<3;i++) {
    delay(200); digitalWrite(LED, HIGH);
    delay(200); digitalWrite(LED, LOW);
  }
}


void setup() {
  Serial.begin(115200);
  EEPROM.begin(EEPROM_SIZE);
  pinMode(LED, OUTPUT);

  if (checkConnectionPins(15, 0)) {
    borrarCredenciales();
    }

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

      String server_path = server_ip + "/validaciones/tarjeta/";
      http.begin(server_path.c_str());
      http.addHeader("Content-Type", "application/json");

      doc["serial"] = serial;
      doc["cedula_estudiante"] = ReadBlockFromCard();
      doc["mac_controlador"] = GetMacAddress();

      string JsonPostRequest = String();
      serializeJson(doc, JsonPostRequest);

      String serial = GetActualUID();
      int http_rc = http.POST(JsonPostRequest);

      if (http_rc <= 0) {
        Serial.print("Error code: "); Serial.println();
        return;
      }

      Serial.print("HTTP Response code: "); Serial.println(http_rc);
      String payload = http.getString();
      deserializeJson(doc, payload.c_str());

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
