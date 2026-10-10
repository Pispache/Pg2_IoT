#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <DHT.h>

#define DHTPIN D4
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);

// Configure las dos redes Wi-Fi disponibles.
const char* WIFI_PRIMARY_SSID = "WIFI_PRINCIPAL";
const char* WIFI_PRIMARY_PASS = "CLAVE_PRINCIPAL";
const char* WIFI_SECONDARY_SSID = "WIFI_SECUNDARIA";
const char* WIFI_SECONDARY_PASS = "CLAVE_SECUNDARIA";

// IMPORTANTE: use la IP LAN de la Mac/servidor accesible desde el Wemos.
// Ejemplo si ejecuta: php artisan serve --host=0.0.0.0 --port=8000
const char* API_URL = "http://192.168.1.100:8000/api/v1/readings";
const char* DEVICE_SERIAL = "IOT-DEMO-001";
const char* DEVICE_TOKEN = "IOT-DEMO-001-SECRET-2026";
const char* FIRMWARE_VERSION = "1.0.0";

const unsigned long READING_INTERVAL_MS = 60UL * 60UL * 1000UL; // 60 minutos
unsigned long lastReadingAt = 0;

bool connectTo(const char* ssid, const char* password, unsigned long timeoutMs = 12000) {
  if (strlen(ssid) == 0) return false;
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  unsigned long start = millis();
  while (WiFi.status() != WL_CONNECTED && millis() - start < timeoutMs) {
    delay(400);
    yield();
  }
  return WiFi.status() == WL_CONNECTED;
}

bool ensureWiFi() {
  if (WiFi.status() == WL_CONNECTED) return true;
  WiFi.disconnect();
  delay(250);
  if (connectTo(WIFI_PRIMARY_SSID, WIFI_PRIMARY_PASS)) return true;
  WiFi.disconnect();
  delay(250);
  return connectTo(WIFI_SECONDARY_SSID, WIFI_SECONDARY_PASS);
}

float readBatteryPercent() {
  // A0 admite un rango limitado según la placa. Ajuste esta conversión
  // de acuerdo con el divisor resistivo y el circuito real de batería.
  int raw = analogRead(A0);
  float pct = (raw / 1023.0f) * 100.0f;
  if (pct < 0) pct = 0;
  if (pct > 100) pct = 100;
  return pct;
}

bool sendReading(float temperature, float humidity, float battery) {
  if (!ensureWiFi()) {
    Serial.println("Sin conexión Wi-Fi.");
    return false;
  }

  WiFiClient client;
  HTTPClient http;
  if (!http.begin(client, API_URL)) {
    Serial.println("No fue posible iniciar HTTP.");
    return false;
  }

  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");
  http.addHeader("X-Device-Token", DEVICE_TOKEN);

  String json = "{";
  json += "\"serial\":\"" + String(DEVICE_SERIAL) + "\",";
  json += "\"temperature\":" + String(temperature, 2) + ",";
  json += "\"humidity\":" + String(humidity, 2) + ",";
  json += "\"battery\":" + String(battery, 2) + ",";
  json += "\"wifi_signal\":" + String(WiFi.RSSI()) + ",";
  json += "\"firmware_version\":\"" + String(FIRMWARE_VERSION) + "\"";
  json += "}";

  int code = http.POST(json);
  String response = http.getString();
  Serial.printf("HTTP %d\n", code);
  Serial.println(response);
  http.end();
  return code >= 200 && code < 300;
}

void takeAndSendReading() {
  float humidity = dht.readHumidity();
  float temperature = dht.readTemperature();
  if (isnan(humidity) || isnan(temperature)) {
    Serial.println("Error al leer DHT11. Se intentará nuevamente en 10 segundos.");
    delay(10000);
    humidity = dht.readHumidity();
    temperature = dht.readTemperature();
    if (isnan(humidity) || isnan(temperature)) return;
  }
  float battery = readBatteryPercent();
  Serial.printf("T=%.2f C | H=%.2f %% | Bat=%.2f %%\n", temperature, humidity, battery);
  sendReading(temperature, humidity, battery);
}

void setup() {
  Serial.begin(115200);
  delay(200);
  dht.begin();
  ensureWiFi();
  takeAndSendReading();
  lastReadingAt = millis();
}

void loop() {
  if (millis() - lastReadingAt >= READING_INTERVAL_MS) {
    takeAndSendReading();
    lastReadingAt = millis();
  }
  if (WiFi.status() != WL_CONNECTED) ensureWiFi();
  delay(500);
}
