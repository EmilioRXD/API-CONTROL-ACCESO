#include <cstdint>
#include "access_control.h"
#include <MFRC522v2.h>
#include <MFRC522DriverSPI.h>
#include <MFRC522DriverPinSimple.h>
#include <MFRC522Debug.h>
#include <WiFi.h>
#include <PubSubClient.h>


String      SERVER_IP        = "192.168.1.7";
uint16_t    SERVER_MQTT_PORT = 2005;


//Lector de Tarjetas-
#define SS_PIN 21

static UID actual_uid;
static String cedula;

MFRC522DriverPinSimple ss_pin(SS_PIN);
MFRC522DriverSPI driver{ss_pin};//spi driver
MFRC522 mfrc{driver}; //class instance
MFRC522::MIFARE_Key key;

byte block_address = 2; //Bloque de memoria de la tarjeta en que se escribe la cédula.
byte new_block_data[17];  //informacion que se escribirá en el bloque 2
byte buffer_block_size = 18;
byte block_data_read[18]; //array para leer los datos del bloque 2
//Lector de tarjetas


//MQTT
WiFiClient wifi_client;
PubSubClient client(wifi_client);



void msg_callback(char* topic, byte* message, unsigned int length) {
  cedula = "";
  JsonDocument doc;

  deserializejson(doc, (char *)message);

  for (int i = 0; i < length; i++) {
    Serial.print((char)message[i]);
    cedula += (char)message[i];
  }

  cedula = doc["student_id"];

  Serial.println();
  Serial.print("Cedula: "); Serial.println(cedula);
}

void BorrarCedula() {
  cedula = "";
}

String Cedula() {
  return cedula;
}


void reconnect() {
  while (!client.connected()) {
    Serial.println("[INFO] Conectando a server MQTT...");

    if (client.connect("EmisorTarjetas")) {
      Serial.println("[OK] Conectado correctamente a server MQTT.");

      String topico = String("esp32/") + GetMacAddress() + String("/card/assign");
      client.subscribe(topico.c_str());
    }
    else {
      Serial.print("[ERROR] Conexion MQTT fallida, rc= "); Serial.print(client.state());
      Serial.println(" Intentando de nuevo en 3 segundos");
      delay(3000);

    }
  }
}

void ConnectMQTT() {
  client.setServer(SERVER_IP.c_str(), SERVER_MQTT_PORT);
  client.setCallback(msg_callback);
}
void ProcessMQTT() {
  if (!client.connected()) {
    reconnect();
  }
  client.loop();
}

void SendDataMQTT(const char* data) {
  String topico = String("esp32/") + GetMacAddress() + String("/card/response");
  client.publish(topico.c_str(), data);
}

//MQTT





bool authenticate_keyA() {
  if (mfrc.PCD_Authenticate(0x60, block_address, &key, &(mfrc.uid)) != 0) {
    return false;
  }
  return true;
}


void printBlock2Data() {
  if (!authenticate_keyA()) {
    Serial.println("[ERROR] Autenticacion KeyA.");
    return;
  }

  if (mfrc.MIFARE_Read(block_address, block_data_read, &buffer_block_size) != 0) {
    Serial.println("[ERROR] fallo al leer el bloque 2 de la tarjeta.");
    return;
  }

  Serial.print("Datos en Bloque 2: ");
  for (byte i = 0; i < 16; i++) {
    Serial.print((char)block_data_read[i]);
  }
  Serial.println();
}



int8_t WriteCard(String value) {
  value.getBytes(new_block_data, 16);

  if (!authenticate_keyA()) {
    Serial.println("[ERROR] Autenticación KeyA al escribir datos.");
    return -1;
  }

  if (mfrc.MIFARE_Write(block_address, new_block_data, 16) != 0) {
    Serial.println("[ERROR] Fallo al escribir los datos en la tarjeta");
    return -2;
  }

  Serial.println("[OK] Datos escritos correctamente en la tarjeta");
  return 0;
  

}

void InitCardReader() {
  mfrc.PCD_Init();

  for (byte i = 0; i < 6;i++) {
    key.keyByte[i] = 0xFF;
  }
}

//Pausa el lector hasta que se quite la tarjeta actual.
void HaltReader() {
  mfrc.PICC_HaltA();
  mfrc.PCD_StopCrypto1();
}


void printActualUID() {
  Serial.print("\nHEX:");
  for (uint8_t i = 0; i < 4;i++) {
    Serial.print(actual_uid.bytes[i] < 0x10 ? " 0" : " ");
    Serial.print(actual_uid.bytes[i], HEX);   
  }
  Serial.println();
  Serial.print("INT: "); Serial.println(actual_uid.integer);
}

String GetActualUID() {
  return String((char *)actual_uid.integer);
}


//Regresa:
//-1 si no hay tarjetas presentes.
//-2 si no se pudo leer la tarjeta.
//0 si se leyó la tarjeta.
int8_t ScanCards() {
  if (!mfrc.PICC_IsNewCardPresent())
   return -1;

  if (!mfrc.PICC_ReadCardSerial())
    return -2;

  for (byte i = 0;i < mfrc.uid.size;i++) {
    actual_uid.bytes[i] = mfrc.uid.uidByte[i];
  }
  return 0;
  
}