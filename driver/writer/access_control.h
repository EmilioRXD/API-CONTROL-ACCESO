#ifndef __ACCESS_CONTROL__
#define __ACCESS_CONTROL__
#include <Arduino.h>

//estructura que guarda el array de bytes y el valor entero
//del UID de la tarjeta 
union UID {
  uint8_t bytes[4];
  uint32_t integer;
};



void BorrarCedula();
String Cedula();
String ReadBlockFromCard();
void SendDataMQTT(const char* data);
void ProcessMQTT();
void ConnectMQTT();
bool checkConnectionPins(int pin1, int pin2);
void printBlock2Data();
void printActualUID();
String GetActualUID();

void HaltReader();
void InitCardReader();
int8_t ScanCards();
int8_t WriteCard(String Value);


#endif //__ACCESS_CONTROL__