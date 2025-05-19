#include <cstdint>
#include <MFRC522v2.h>
#include <MFRC522DriverSPI.h>
#include <MFRC522DriverPinSimple.h>
#include <MFRC522Debug.h>

#include "access_control.h"

#define SS_PIN 21


MFRC522DriverPinSimple ss_pin(SS_PIN);
MFRC522DriverSPI driver{ss_pin};//spi driver
MFRC522 mfrc{driver}; //class instance
MFRC522::MIFARE_Key key;

static UID actual_uid;       //UID de la tarjeta activa.

byte block_address = 2;      //Bloque de memoria de la tarjeta en que se escribe la cédula.
byte new_block_data[17];
byte buffer_block_size = 18; //tamaño del bloque de memoria de la tarjeta.
byte block_data_read[18];    //array para leer los datos guardados en el bloque de memoria de la tarjeta.


bool authenticate_keyA() {
  if (mfrc.PCD_Authenticate(0x60, block_address, &key, &(mfrc.uid)) != 0) {
    Serial.println("[ERROR]: Key A Authentication failed.");
    return false;
  }
  return true;
}

String ReadBlockFromCard() {
    if (!authenticate_keyA()) {
    return String();
  }

  if (mfrc.MIFARE_Read(block_address, block_data_read, &buffer_block_size) != 0) {
    Serial.println("[ERROR]: Read from block 2 failed.");
    return String();
  }

  return String((char *)block_data_read);
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


void PrintActualUID() {
  Serial.print("\nHEX:");
  for (uint8_t i = 0; i < 4;i++) {
    Serial.print(actual_uid.bytes[i] < 0x10 ? " 0" : " ");
    Serial.print(actual_uid.bytes[i], HEX);
  }
  Serial.println();
  Serial.print("INT: "); Serial.println(actual_uid.integer);
}

//regresa el UID de la tarjeta como string.
String GetActualUID() {
  return String(actual_uid.integer);
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

  for (byte i = 0; i < mfrc.uid.size;i++) {
    actual_uid.bytes[i] = mfrc.uid.uidByte[i];
  }
  return 0;
  
}