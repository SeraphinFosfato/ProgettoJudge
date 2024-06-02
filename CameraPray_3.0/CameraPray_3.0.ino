#include "esp_camera.h"
#include <WiFi.h>
#include <HTTPClient.h>
#include <LiquidCrystal_I2C.h>
#include <Wire.h>

#define CAMERA_MODEL_WROVER_KIT
#define SERVER_TIME_RESPONSE 2000
#define TRIGGER_PIN 0
#define ECHO_PIN 2
#define MIN_DELTA 2
#define BLUE_PIN 33
#define RED_PIN 32
#define BUZZER_PIN 12

#include "camera_pins.h"

//-----------------------------------VARIABILI GLOBALI-----------------------

LiquidCrystal_I2C lcd(0x27, 16, 2);
camera_config_t config;
const char* ssid = "Nome Connessione";
const char* password = "Password";
const char* serverAddress = "http://192.168.X.X/upload.php";
const char* serverResponseUrl = "http://192.168.X.X/scan.php";
float base_distance;
const int sda_pin = 15;
const int scl_pin = 13;

//-----------------------------------SETUP-----------------------------------

void setup()
{
  pinMode(BLUE_PIN, OUTPUT);
  pinMode(RED_PIN, OUTPUT);
  pinMode(BUZZER_PIN, OUTPUT);
  pinMode(TRIGGER_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);
  
  Wire.setPins(sda_pin, scl_pin); // Cambiamo i pin dei segnali sda e scl perché constrastavano con i pin usati dalla fotocamera
  Wire.begin();

  Serial.begin(115200);

  lcd.init();
  lcd.backlight();
  // Uso: lcd.setCursor(coloumn, row);

  //---------------------------------CAMERA SETUP----------------------------
  
  config_init();
  if(psramFound()) // Se la psram è disponibile aumenta le prestazioni della telecamera
  {
    Serial.println("Using psram");
    config.jpeg_quality = 10;
    config.fb_count = 2;
    config.grab_mode = CAMERA_GRAB_LATEST;
  }
  else
  {
    Serial.println("Not using psram");
    config.frame_size = FRAMESIZE_SVGA;
    config.fb_location = CAMERA_FB_IN_DRAM;
  }
  esp_err_t err = esp_camera_init(&config);
  if (err != ESP_OK)
  {
    Serial.println("Camera initialization failed");
    force_reset("     Errore", "   Fotocamera");
  }
  //------------------------------------PRESENTAZIONE------------------
  lcd.setCursor(0, 0);
  lcd.print("Progetto JUDGE!");
  lcd.setCursor(3, 1);
  lcd.print("Benvenuto!");
  delay(3000);
  lcd.clear();
  lcd.print("Initializing");
  delay(500);
  lcd.print(".");
  delay(500);
  lcd.print(".");
  delay(500);
  lcd.print(".");
  delay(500);
  lcd.clear();
  lcd.setCursor(0, 0);
  
  //------------------------------------WIFI SETUP------------------

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED)
  {
    Serial.println("Connecting...");
    delay(1000);
    lcd.clear();
    lcd.setCursor(3, 0);
    lcd.print("Connecting");
    lcd.setCursor(3, 1);
    lcd.print("to WiFi");
    delay(500);
    lcd.print(".");
    delay(500);
    lcd.print(".");
    delay(500);
    lcd.print(".");
  }
  lcd.clear();
  lcd.setCursor(3, 0);
  lcd.print("Connected!");
  Serial.println("Connected to WiFi, Ip: ");
  Serial.println(WiFi.localIP());
  delay(1000);
  lcd.clear();

  //-------------------------------CALCOLO DEL VALORE DI DISTANZA BASE DEL SENSORE IN SETUP---------

  digitalWrite(TRIGGER_PIN, LOW);
  delayMicroseconds(2);
  float distance = cast_echo();

  lcd.print("Distanza base: ");
  lcd.setCursor(0, 1);
  lcd.print(distance);
  lcd.print(" cm");
  delay(1000);
  lcd.clear();
  
  Serial.print(distance);
  Serial.println(" cm");
  base_distance = distance;

  lcd.setCursor(4, 0);
  lcd.print("Inserire");
  lcd.setCursor(4, 1);
  lcd.print("carta...");
  Serial.print("Waiting for card...");
}

//------------------------------------LOOP-----------------------

void loop()
{
  float cast_distance = cast_echo();
  float delta = base_distance - cast_distance;
  
  if (delta > MIN_DELTA) // Rileva la carta quando la differenza di distanza supera la soglia
  {
    Serial.print(cast_distance);
    Serial.println(" cm");
    lcd.clear();
    lcd.setCursor(2, 0);
    lcd.print("Scattando...");
    Serial.println("Camera Grab");
    delay(2000);
    
    captureAndSendPhoto();

    lcd.clear();
    lcd.setCursor(4, 0);
    lcd.print("Inserire");
    lcd.setCursor(4, 1);
    lcd.print("carta...");
    Serial.print("Waiting for card...");
  }
}

//----------------------------------------SCATTO ED INVIO DELLA FOTO-----------------

void captureAndSendPhoto()
{
  camera_fb_t * fb = esp_camera_fb_get(); // Funzione che scatta la foto
  // fb è la struttura che contiene la foto
  if (!fb)
  {
    Serial.println("Camera capture failed");
    force_reset(" Errore Camera!","");
  }
  
  WiFiClient client;
  HTTPClient http;

  lcd.clear();
  lcd.print("Rimuovere Carta");
  delay(1000);
  lcd.clear();
  lcd.setCursor(1, 0);
  lcd.print("Processando...");

  //---------------------------------------INVIO POST--------------------------
  http.begin(client, serverAddress);
  http.addHeader("Content-Type", "image/jpeg");
  int httpResponseCode = http.POST((uint8_t*)fb->buf, fb->len);
  
  Serial.print("HTTP Response code: ");
  Serial.println(httpResponseCode);

  switch(httpResponseCode)
  {
    case 200:
    {
      String response = http.getString();
      Serial.println(response);
      http.end();
      
      esp_camera_fb_return(fb);
  
      Serial.println("Waiting for GET");
      delay(SERVER_TIME_RESPONSE); // Attende che il server elabora la richiesta
      waitForServerResponse();
      break;
    }
    case -1:
    {
      force_reset("   Errore di", "  Connessione");
      break;
    }
    case -3:
    {
      http.end();
      esp_camera_fb_return(fb);
      lcd.clear();
      lcd.print("    Immagine");
      lcd.setCursor(0,1);
      lcd.print("    Corrotta");
      delay(2000);
      break;
    }
    case -11:
    {
      http.end();
      esp_camera_fb_return(fb);
      lcd.clear();
      lcd.print("  Errore Lato");
      lcd.setCursor(0,1);
      lcd.print("     Server");
      delay(2000);
      break;
    }
    default:
    {
      force_reset("     Errore", "   Sconosciuto!");
      break;
    }
  }
}

//-------------------------------------------INVIO E RICEZIONE GET-----------------------------

void waitForServerResponse()
{
  WiFiClient client;
  HTTPClient http;

  http.begin(client, serverResponseUrl);
  int httpResponseCode = http.GET(); // Invio Get

  if (httpResponseCode > 0)
  {
    String response = http.getString();

    // Gestione formattazione stringa
    char label[16];
    char card_name[16];
    split_string(response.c_str(), label, card_name);
    bool notFound = label[2] == 'N';
    
    // Segnalazione risultato
    if(notFound)
    {
      digitalWrite(RED_PIN, HIGH);
      digitalWrite(BUZZER_PIN, HIGH);
    }
    else
      digitalWrite(BLUE_PIN, HIGH);
    lcd.clear();
    lcd.print(label);
    lcd.setCursor(0, 1);
    lcd.print(card_name);
    delay(1000);
    digitalWrite(BUZZER_PIN, LOW);
    delay(2000);
    lcd.clear();
    if(notFound)
      digitalWrite(RED_PIN, LOW);
    else
      digitalWrite(BLUE_PIN, LOW);
    
    Serial.print("Server Response: ");
    Serial.println(response);
    
  }
  else
  {
    lcd.clear();
    lcd.print("     Errore");
    lcd.setCursor(0,1);
    lcd.print(" Ricezione Dati");
    delay(2000);
    Serial.print("Error on HTTP request: ");
    Serial.println(httpResponseCode);
  }

  http.end();
}

//----------------------------------------INVIO SEGNALE ULTRASUONO--------------------

float cast_echo()
{
  // Attivazione segnale
  delay(100);
  digitalWrite(TRIGGER_PIN, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIGGER_PIN, LOW);

  //Ricezione segnale
  long duration = pulseIn(ECHO_PIN, HIGH);
  float distance = (duration * 0.0343) / 2;
  return distance;
}

//-------------------------------------DIVISIONE STRINGA IN DUE--------------------

void split_string(const char* input, char* part1, char* part2)
{  
    strncpy(part1, input, 16);
    part1[16] = '\0';
    strncpy(part2, input + 16, 16);
    part2[16] = '\0';
}

//-------------------------------------RESET FORZATO-------------------------------

void force_reset(String error_high, String error_low)
{
  lcd.clear();
  while(true)
  {
    lcd.print(error_high);
    lcd.setCursor(0,1);
    lcd.print(error_low);
    digitalWrite(BUZZER_PIN, HIGH);
    delay(1000);
    lcd.clear();
    digitalWrite(BUZZER_PIN, LOW);
    delay(1000);
    lcd.print(" Reset Hardware");
    lcd.setCursor(0,1);
    lcd.print("   Richiesto!");
    digitalWrite(BUZZER_PIN, HIGH);
    delay(1000);
    lcd.clear();
    digitalWrite(BUZZER_PIN, LOW);
    delay(1000);
  }
}

//------------------------------------CONFIGURAZIONE CAMERA--------------------

void config_init()
{
  config.ledc_channel = LEDC_CHANNEL_0;
  config.ledc_timer = LEDC_TIMER_0;
  config.pin_d0 = Y2_GPIO_NUM;
  config.pin_d1 = Y3_GPIO_NUM;
  config.pin_d2 = Y4_GPIO_NUM;
  config.pin_d3 = Y5_GPIO_NUM;
  config.pin_d4 = Y6_GPIO_NUM;
  config.pin_d5 = Y7_GPIO_NUM;
  config.pin_d6 = Y8_GPIO_NUM;
  config.pin_d7 = Y9_GPIO_NUM;
  config.pin_xclk = XCLK_GPIO_NUM;
  config.pin_pclk = PCLK_GPIO_NUM;
  config.pin_vsync = VSYNC_GPIO_NUM;
  config.pin_href = HREF_GPIO_NUM;
  config.pin_sccb_sda = SIOD_GPIO_NUM;
  config.pin_sccb_scl = SIOC_GPIO_NUM;
  config.pin_pwdn = PWDN_GPIO_NUM;
  config.pin_reset = RESET_GPIO_NUM;
  config.xclk_freq_hz = 20000000;
  config.frame_size = FRAMESIZE_UXGA;
  config.pixel_format = PIXFORMAT_JPEG; // for streaming
  //config.pixel_format = PIXFORMAT_RGB565; // for face detection/recognition
  config.grab_mode = CAMERA_GRAB_WHEN_EMPTY;
  config.fb_location = CAMERA_FB_IN_PSRAM;
  config.jpeg_quality = 12;
  config.fb_count = 1;
}
