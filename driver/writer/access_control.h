#ifndef __ACCESS_CONTROL__
#define __ACCESS_CONTROL__
#include <Arduino.h>

//estructura que guarda el array de bytes y el valor entero
//del UID de la tarjeta 
union UID {
  uint8_t bytes[4];
  uint32_t integer;
};

enum Mode {
  MODE_CONFIG,
  MODE_READER,
  MODE_WRITER,
  MODE_SENDER,
  MODE_NONE
};

void BorrarCedula();
String Cedula();
void SendDataMQTT(const char* data);
void ProcessMQTT();
void ConnectMQTT();

void printBlock2Data();
void printActualUID();
String GetActualUID();

void HaltReader();
void InitCardReader();
int8_t ScanCards();
int8_t WriteCard(String Value);


#endif //__ACCESS_CONTROL__