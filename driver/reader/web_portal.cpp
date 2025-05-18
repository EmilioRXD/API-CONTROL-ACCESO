#include <WiFi.h>
#include <WebServer.h>
#include <DNSServer.h>
#include <ArduinoJson.h>
#include "esp_mac.h"
#include <EEPROM.h>

#include "config_portal.h"
#include "web_portal.h"


#define SSID_ADDR 0
#define PASS_ADDR 32
#define SERVER_IP_ADDR 64


const byte DNS_PORT = 53;

const char* apSSID = "ESP32_Config";
const char* apPassword = "12345678";

static String savedSSID = "";
static String savedPassword = "";
static String server_ip = "";

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
  EEPROM.writeString(SERVER_IP_ADDR, "");

  // Confirma los cambios en la EEPROM
  EEPROM.commit();

  Serial.println("Credenciales borradas de la EEPROM");
}


bool tryConnect() {
  // Leer configuración guardada
  savedSSID = EEPROM.readString(SSID_ADDR);
  savedPassword = EEPROM.readString(PASS_ADDR);

  //si hay una conexion guardada.
  if (savedSSID.length() > 0) {
    Serial.println("[INFO] Intentando conectar a red guardada...");
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
          mac[0], mac[1], mac[2], mac[3], mac[4], mac[5]);  // Almacenar en un objeto String

  String html = String((const char*)part1);
  html += "<span>MAC: " + String(macStr) + "</span>";
  html += String((const char*)part2);

  int n = WiFi.scanNetworks();
  if (n == 0) {
    html += "{ ssid: 'No se encontraron redes :(', rssi: 4 },";
  } else {
    for (int i = 0; i < n; i++) {
      html += "{ ssid: '" + WiFi.SSID(i) + "', rssi:" + WiFi.RSSI(i) + "},";
    }
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
    Serial.println("[INFO] Reiniciando...");

    delay(10000);
    ESP.restart();
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


void setupWebServer() {
  // Handler para el captive portal
  server.on("/", handleRoot);
  server.on("/generate_204", handleRoot);         // Android captive portal check
  server.on("/fwlink", handleRoot);               // Microsoft captive portal check
  server.on("/hotspot-detect.html", handleRoot);  // Apple captive portal
  server.on("/connect", HTTP_POST, handleConnect);
  server.on("/config", handleConfig);

  server.onNotFound(handleRoot);  // Captura todas las URLs no definidas

  server.begin();
  Serial.println("[INFO] Servidor HTTP iniciado");
}

void startCaptivePortal() {
  WiFi.softAP(apSSID, apPassword);
  Serial.print("[INFO] AP creado: "); Serial.println(apSSID);
  Serial.print("[INFO] IP del AP: "); Serial.println(WiFi.softAPIP());

  // Configurar DNS captive portal
  dnsServer.start(DNS_PORT, "*", WiFi.softAPIP());

  setupWebServer();
}