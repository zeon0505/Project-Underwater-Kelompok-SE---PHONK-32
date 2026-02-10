/*
 * Firmware ESP32 - Drone Bawah Air (Monitoring Kualitas Air)
 * Deskripsi: Membaca sensor dan mengirim data ke Laravel Dashboard via WiFi Lokal.
 */

#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// --- KONFIGURASI WIFI ---
const char* ssid = "NAMA_WIFI_ANDA";
const char* password = "PASSWORD_WIFI_ANDA";

// --- KONFIGURASI SERVER ---
// Ganti dengan IP lokal komputer Anda (cek di CMD: ipconfig)
const char* serverUrl = "http://192.168.1.107:8000/api/sensor-data"; 
const char* apiToken = "underwater_drone_secret_token_2026"; 

// --- PIN SENSOR ---
const int pinPH = 34;         // Analog Pin pH
const int pinTDS = 35;        // Analog Pin TDS
const int pinSuhu = 32;       // Pin Suhu
const int pinTurbidity = 33;  // Analog Pin Turbidity
const int pinEC = 36;         // Analog Pin EC
const int pinDO = 39;         // Analog Pin DO

// --- VARIABEL KALIBRASI ---
// Sesuaikan nilai ini setelah pengujian dengan larutan buffer
float phOffset = 0.0;         // Tambah/kurang jika pembacaan pH meleset
int tdsFactor = 1;            // Pengali untuk akurasi TDS

// Interval pengiriman data (ms)
unsigned long previousMillis = 0;
const long interval = 5000; // Kirim data tiap 5 detik

void setup() {
  Serial.begin(115200);

  // Koneksi WiFi
  WiFi.begin(ssid, password);
  Serial.print("Menghubungkan ke WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi Terhubung!");
  Serial.print("IP ESP32: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  unsigned long currentMillis = millis();

  if (currentMillis - previousMillis >= interval) {
    previousMillis = currentMillis;

    if (WiFi.status() == WL_CONNECTED) {
      kirimDataSensor();
    } else {
      Serial.println("WiFi Terputus, mencoba menyambung ulang...");
      WiFi.begin(ssid, password);
    }
  }
}

void kirimDataSensor() {
  // 1. Baca Nilai Sensor (Aktual dengan Kalibrasi)
  // Rumus: (Nilai Analog / Resolusi) * Tegangan Referensi * Faktor Konversi
  float rawPH = (analogRead(pinPH) / 4095.0) * 14.0;
  float phVal = rawPH + phOffset; // Terapan kalibrasi pH
  
  float tempVal = 27.5; // Contoh (Gunakan DS18B20 untuk suhu asli)
  
  int rawTDS = map(analogRead(pinTDS), 0, 4095, 0, 1000);
  int tdsVal = rawTDS * tdsFactor; // Terapan kalibrasi TDS

  float turbidityVal = (analogRead(pinTurbidity) / 4095.0) * 100.0;
  float ecVal = (analogRead(pinEC) / 4095.0) * 2000.0;
  float doVal = (analogRead(pinDO) / 4095.0) * 10.0;

  // 2. Siapkan JSON
  StaticJsonDocument<300> doc;
  doc["ph"] = phVal;
  doc["temperature"] = tempVal;
  doc["tds"] = tdsVal;
  doc["turbidity"] = turbidityVal;
  doc["ec"] = ecVal;
  doc["do"] = doVal;
  doc["drone_id"] = "DRN-UNDER-01";

  String jsonPayload;
  serializeJson(doc, jsonPayload);

  // 4. Kirim via HTTP POST
  HTTPClient http;
  http.begin(serverUrl);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Authorization", "Bearer " + String(apiToken));

  Serial.print("Mengirim data: ");
  Serial.println(jsonPayload);

  int httpResponseCode = http.POST(jsonPayload);

  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.print("Respon Server: ");
    Serial.println(httpResponseCode);
    Serial.println(response);
  } else {
    Serial.print("Error saat mengirim: ");
    Serial.println(httpResponseCode);
  }

  http.end();
}
