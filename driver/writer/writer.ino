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

bool resultado_conexion;
bool configurado;

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

  configurado = EEPROM.readBool(64);

  resultado_conexion = tryConnect();
  if (resultado_conexion) {
  Serial.println(GetMacAddress());
    ConnectMQTT();
    driver_mode = MODE_READER;
  }
}


void loop() {
  serverProcess();
  if (configurado) {ProcessMQTT();}

  String resultado_escritura;
  String mensaje;

  switch (driver_mode) {

  case MODE_READER: {
    if (!Cedula().isEmpty() && ScanCards() == 0) {
      if (!ReadBlockFromCard().isEmpty())
      {
        resultado_escritura = "failure";
        mensaje = "Ya hay una cédula escrita en la tarjeta.";
        driver_mode = MODE_SENDER;
      }
      printActualUID();
      driver_mode = MODE_WRITER;
    }
    break;
  }
  case MODE_WRITER: {
    if (WriteCard(Cedula()) == 0) {
      printBlock2Data();
      resultado_escritura = "success";
      mensaje = "Cédula escrita exitosamente";
      HaltReader();
      driver_mode = MODE_SENDER;
    }
    break;
  }
  case MODE_SENDER: {
    JsonDocument doc; 
    doc["status"] = resultado_escritura;
    doc["cedula_estudiante"] = Cedula();
    doc["serial"] = GetActualUID();
    doc["message"] = mensaje;

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