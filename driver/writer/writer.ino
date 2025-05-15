#include <EEPROM.h>
#include <HTTPClient.h>
#include "web_portal.h"
#include "access_control.h"
#include <ArduinoJson.h>
 
#define LED 2
#define EEPROM_SIZE 512
#define MQTT_PORT 1883




enum Mode {
  MODE_CONFIG,
  MODE_READER,
  MODE_WRITER,
  MODE_SENDER,
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
  if (result) {
  Serial.println(GetMacAddress());
    ConnectMQTT();
    driver_mode = MODE_READER;
  }
}


void loop() {
  serverProcess();
  ProcessMQTT();

  switch (driver_mode) {

  case MODE_READER: {
    if (!Cedula().isEmpty() && ScanCards() == 0) {
      printActualUID();
      driver_mode = MODE_WRITER;
    }
    break;
  }
  case MODE_WRITER: {
    if (WriteCard(Cedula()) == 0) {
      printBlock2Data();
      HaltReader();
      driver_mode = MODE_SENDER;
    }
    break;
  }
  case MODE_SENDER: {
    JsonDocument doc; 
    doc["status"] = "success";
    doc["cedula_estudiante"] = Cedula();
    doc["serial"] = GetActualUID();

    String jsonResponse;
    serializeJson(doc, jsonResponse); 

    SendDataMQTT(jsonResponse.c_str());
    Serial.println("Datos Enviados.");

    BorrarCedula();
    delay(2000);
    driver_mode = MODE_READER;
    break;
  }
  case MODE_NONE: {
    break;
  }
  }

}