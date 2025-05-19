#include <WiFi.h>
#include <WebServer.h>
#include <DNSServer.h>
#include <ArduinoJson.h>
#include <HTTPClient.h>
#include <EEPROM.h>
#include "esp_mac.h"

#include "config_portal.h"
#include "web_portal.h"

// Estructura para almacenar información de redes WiFi escaneadas
struct WifiNetwork {
  String ssid;
  int32_t rssi;
};

// Array para almacenar las redes encontradas
WifiNetwork scannedNetworks[20]; // Máximo 20 redes
int networkCount = 0;

// Incluir definiciones comunes
#include "common_defs.h"

const byte DNS_PORT = 53;

const char* apSSID = "ESP32_Config";
const char* apPassword = "12345678";

static String savedSSID = "";
static String savedPassword = "";

DNSServer dnsServer;
WebServer server(80);

String GetMacAddress() {
  uint8_t mac[6];
  char macStr[18];
  esp_read_mac(mac, ESP_MAC_WIFI_STA);

  // Formatear la MAC como String
  sprintf(macStr, "%02X:%02X:%02X:%02X:%02X:%02X",
          mac[0], mac[1], mac[2], mac[3], mac[4], mac[5]);  // Almacenar en un objeto String

  return String(macStr);
}

bool connectToWiFi() {
  WiFi.begin(savedSSID.c_str(), savedPassword.c_str());

  Serial.print("[INFO] Conectando a"); Serial.println(savedSSID);


  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 20) {
    delay(500);
    Serial.print(".");
    attempts++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n[OK] Conectado!");
    Serial.print("[INFO] IP local: "); Serial.println(WiFi.localIP());
    return true;
  } else {
    Serial.println("\n[ERROR] No se pudo conectar");
    startCaptivePortal();
    return false;
  }
  //borrarCredenciales();
}

void borrarCredenciales() {
  // Escribe cadenas vacías en las posiciones de memoria
  EEPROM.writeString(SSID_ADDR, "");
  EEPROM.writeString(PASS_ADDR, "");
  // EEPROM.writeString(SERVER_IP_ADDR, "");

  // Confirma los cambios en la EEPROM
  EEPROM.commit();

  Serial.println("Credenciales borradas de la EEPROM");
}

void scanNetworks() {
  // Configurar en modo estación para escanear
  WiFi.mode(WIFI_STA);
  Serial.println("[INFO] Escaneando redes WiFi previo a configuración...");
  
  // Limpiar resultados anteriores
  networkCount = 0;
  
  // Realizar escaneo
  int n = WiFi.scanNetworks();
  Serial.printf("[INFO] Escaneo completado, se encontraron %d redes\n", n);
  
  if (n > 0) {
    // Guardar hasta 20 redes o el número encontrado, lo que sea menor
    int redes_a_guardar = (n > 20) ? 20 : n;
    for (int i = 0; i < redes_a_guardar; i++) {
      scannedNetworks[i].ssid = WiFi.SSID(i);
      scannedNetworks[i].rssi = WiFi.RSSI(i);
      Serial.printf("  %d: %s (%d)\n", i + 1, scannedNetworks[i].ssid.c_str(), scannedNetworks[i].rssi);
      networkCount++;
    }
  }
  
  // Limpiar resultados del escaneo de WiFi para liberar memoria
  WiFi.scanDelete();
}

bool tryConnect() {
  // Leer configuración guardada
  savedSSID = EEPROM.readString(SSID_ADDR);
  savedPassword = EEPROM.readString(PASS_ADDR);

  // Escanear redes WiFi antes de decidir el modo
  scanNetworks();

  //si hay una conexion guardada.
  if (savedSSID.length() > 0) {
    Serial.println("[INFO] Intentando conectar a red guardada...");
    borrarCredenciales();
    return connectToWiFi();
  } else {
    Serial.println("[INFO] Iniciando modo configuración");
    startCaptivePortal();
    return false;
  }
}

void serverProcess() {
  dnsServer.processNextRequest();
  server.handleClient();
}

void handleConfig() {
  uint8_t mac[6];
  char macStr[18];
  esp_read_mac(mac, ESP_MAC_WIFI_STA);

  // Formatear la MAC como String
  sprintf(macStr, "%02X:%02X:%02X:%02X:%02X:%02X",
          mac[0], mac[1], mac[2], mac[3], mac[4], mac[5]);

  String html = String((const char*)part1);
  html += "<span>MAC: " + String(macStr) + "</span>";
  html += String((const char*)part2);

  // Usar las redes precargadas si están disponibles
  if (networkCount > 0) {
    Serial.println("[INFO] Usando redes WiFi precargadas");
    
    for (int i = 0; i < networkCount; i++) {
      // Escapar comillas simples en SSID para evitar problemas en JavaScript
      String ssidEscaped = scannedNetworks[i].ssid;
      ssidEscaped.replace("'", "\\'");
      
      html += "{ ssid: '" + ssidEscaped + "', rssi: " + scannedNetworks[i].rssi + "},";
    }
  } else {
    // Si no hay redes precargadas o se desea actualizar, hacer un nuevo escaneo
    Serial.println("[INFO] No hay redes precargadas, realizando nuevo escaneo");
    
    // Asegurar modo correcto para escanear
    WiFi.mode(WIFI_MODE_APSTA);
    
    // Limpia resultados anteriores si los hay
    if (WiFi.scanComplete() != -2) {
      WiFi.scanDelete();
    }

    int n = WiFi.scanNetworks();
    Serial.printf("[INFO] Escaneo completado, se encontraron %d redes\n", n);

    if (n == 0) {
      html += "{ ssid: 'No se encontraron redes :(', rssi: 4 },";
    } else if (n > 0) {
      for (int i = 0; i < n; i++) {
        String ssidEscaped = WiFi.SSID(i);
        ssidEscaped.replace("'", "\\'");
        html += "{ ssid: '" + ssidEscaped + "', rssi: " + WiFi.RSSI(i) + "},";
      }
    } else {
      // si n < 0, error en escaneo
      html += "{ ssid: 'Error escaneando WiFi', rssi: 0 },";
    }
    
    // Limpiar después de usar
    WiFi.scanDelete();
  }

  html += String((const char*)part3);
  server.send(200, "text/html", html);
}

void handleConnect() {
  String ssid = server.arg("ssid");
  String password = server.arg("password");

  WiFi.mode(WIFI_MODE_APSTA);
  WiFi.begin(ssid.c_str(), password.c_str());

  int attempts = 0;
  while (WiFi.status() != WL_CONNECTED && attempts < 15) {
    delay(500);
    attempts++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    // Crear un objeto JSON para la respuesta exitosa
    DynamicJsonDocument doc(200);  // Tamaño ajustable según necesidad
    doc["status"] = "success";
    doc["ip"] = WiFi.localIP().toString();  // Opcional: incluir la IP

    String jsonResponse;
    serializeJson(doc, jsonResponse);  // Convertir a String JSON
    server.send(200, "application/json", jsonResponse);

    // Guardar en EEPROM
    EEPROM.writeString(SSID_ADDR, ssid);
    EEPROM.writeString(PASS_ADDR, password);
    EEPROM.commit();

    Serial.println("[INFO] Credenciales guardadas");
    // Serial.println("[INFO] Reiniciando...");

    // delay(10000);
    // ESP.restart();
  } else {
    // Crear un objeto JSON para el error
    DynamicJsonDocument doc(200);
    doc["status"] = "error";

    String jsonResponse;
    serializeJson(doc, jsonResponse);

    server.send(200, "application/json", jsonResponse);
    WiFi.mode(WIFI_MODE_AP);  // Cambiar a modo AP si falla la conexión
  }
}

void handleRoot() {
  server.sendHeader("Location", "http://192.168.4.1/config");
  server.send(302, "text/plain", "");
}

void handleServer() {
  // Inicializar el cliente HTTP y el documento JSON para la solicitud
  HTTPClient http;
  DynamicJsonDocument doc(256);

  Serial.println("[INFO] Procesando solicitud al endpoint /server");
  String url, location, type;
  
  // Revisar si hay datos JSON en el cuerpo de la solicitud
  if (server.hasArg("plain")) {
    String jsonBody = server.arg("plain");
    Serial.println("[DEBUG] Cuerpo JSON recibido: " + jsonBody);
    
    // Analizar JSON
    DynamicJsonDocument requestJson(512);
    DeserializationError error = deserializeJson(requestJson, jsonBody);
    
    if (!error) {
      // Extraer valores del JSON
      url = "http://" + requestJson["url"].as<String>();
      location = requestJson["location"].as<String>();
      type = requestJson["type"].as<String>();
      
      Serial.println("[DEBUG] Datos extraídos del JSON:");
      Serial.print("  URL: '"); Serial.print(url); Serial.println("'");
      Serial.print("  Ubicación: '"); Serial.print(location); Serial.println("'");
      Serial.print("  Tipo: '"); Serial.print(type); Serial.println("'");
    } else {
      Serial.print("[ERROR] Error al parsear JSON: "); Serial.println(error.c_str());
    }
  } else {
    // Intento de obtener como parámetros tradicionales (fallback)
    url = "http://" + server.arg("url");
    location = server.arg("location");
    type = server.arg("type");
    Serial.println("[DEBUG] No se encontró JSON, usando parámetros tradicionales");
  }
  
  String server_path = url + "/controladores/";
  Serial.print("[INFO] Conectando a: "); Serial.println(server_path);
  
  http.begin(server_path.c_str());
  http.addHeader("Content-Type", "application/json");
  http.addHeader("accept", "application/json");
  http.setTimeout(10000); // Aumentar timeout a 10 segundos
  
  // Get MAC address for the device
  String mac = GetMacAddress();

  doc["mac"] = mac;
  doc["ubicacion"] = location;
  doc["funcion"] = "LECTOR"; //Cambiar para ESP32 Escritor
  doc["tipo_acceso"] = type;
  
  String jsonRequest = String();
  serializeJson(doc, jsonRequest);
  Serial.println("Sending request: " + jsonRequest);
  
  int httpResponseCode = http.POST(jsonRequest);
  
  if (httpResponseCode == 200 || httpResponseCode == 201) {
    
    // Guardar la URL en EEPROM para futuras referencias
    EEPROM.writeString(SERVER_IP_ADDR, url);
    Serial.println("[INFO] Datos Guardados: " + EEPROM.readString(SERVER_IP_ADDR));
    EEPROM.commit();

    DynamicJsonDocument doc(200);
    doc["status"] = "success";
    String jsonResponse;
    serializeJson(doc, jsonResponse);  // Convertir a String JSON
    server.send(200, "application/json", jsonResponse);

    Serial.print("HTTP Response code: ");
    Serial.println(httpResponseCode);

    Serial.println("[INFO] Credenciales guardadas");
    Serial.println("[INFO] Reiniciando...");

    delay(10000);
    http.end();
    ESP.restart();
  } else {
    // Create error response
    DynamicJsonDocument errorDoc(128);
    errorDoc["status"] = "error";
    errorDoc["message"] = "Failed to connect to server";
    String errorResponse;
    serializeJson(errorDoc, errorResponse);
    server.send(500, "application/json", errorResponse);

    Serial.print("Error code: ");
    Serial.println(httpResponseCode);
  }
  http.end();
}

void setupWebServer() {
  // Handler para el captive portal
  server.on("/", handleRoot);
  server.on("/generate_204", handleRoot);         // Android captive portal check
  server.on("/fwlink", handleRoot);               // Microsoft captive portal check
  server.on("/hotspot-detect.html", handleRoot);  // Apple captive portal
  server.on("/connect", HTTP_POST, handleConnect);
  server.on("/server", HTTP_POST, handleServer);  // Corregido para usar handleServer en lugar de handleConnect
  server.on("/config", handleConfig);
  
  // Añadir endpoint para rescanear redes bajo demanda
  server.on("/scan", []() {
    Serial.println("[INFO] Solicitud de rescaneo manual recibida");
    // Resetear las redes guardadas
    networkCount = 0;
    // Forzar un nuevo escaneo al llamar a handleConfig
    handleConfig();
  });

  server.onNotFound(handleRoot);  // Captura todas las URLs no definidas

  server.begin();
  Serial.println("[INFO] Servidor HTTP iniciado");
}

void startCaptivePortal() {
  // Cambiar a modo AP+STA para permitir escaneado mientras se sirve como AP
  WiFi.mode(WIFI_MODE_APSTA);
  WiFi.softAP(apSSID, apPassword);
  Serial.print("[INFO] AP creado: "); Serial.println(apSSID);
  Serial.print("[INFO] IP del AP: "); Serial.println(WiFi.softAPIP());

  // Configurar DNS captive portal
  dnsServer.start(DNS_PORT, "*", WiFi.softAPIP());

  setupWebServer();
}