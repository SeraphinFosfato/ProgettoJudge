# Progetto Judge

**Progetto Judge** nasce dall’esigenza di semplificare la gestione delle collezioni di carte da gioco.
Ordinare manualmente decine o centinaia di carte può diventare un processo lungo e noioso: con Judge, basta inserire la carta nel sistema e la piattaforma si occupa automaticamente di riconoscerla, archiviarla e aggiornare la collezione personale.

Grazie a un approccio **ibrido tra hardware e software**, il sistema unisce la potenza di un microcontrollore con fotocamera e sensori, un backend per l’elaborazione OCR, e un database centralizzato per gestire la collezione.

---

## ⚙️ Come funziona

1. L’utente inserisce una carta nell’alloggiamento.
2. Un **sensore a ultrasuoni** rileva l’inserimento e attiva la fotocamera ESP-CAM.
3. L’immagine catturata viene inviata al server via **HTTP POST**.
4. Uno script Python utilizza le **API Google Cloud Vision** per riconoscere il testo sulla carta.
5. Il sistema interroga il database MySQL per verificare la carta e aggiorna la collezione.
6. Il risultato viene comunicato all’utente tramite **LCD**, **LED** e buzzer.
7. La collezione può essere consultata da **interfaccia web**.

---

## 🔑 Funzionalità principali

* **Scansione carte**: riconoscimento automatico tramite fotocamera e OCR.
* **Database collezione**: salvataggio delle proprie carte con quantità e dettagli.
* **Ricerca e consultazione**: possibilità di interrogare il database via web.
* **Gestione collezione**: aggiunta, rimozione e visualizzazione rapida delle carte.
* **Feedback utente**: LED e buzzer per segnalare esito positivo o errori.

---

## 🛠️ Componenti hardware

* ESP32-CAM (OV2640) per acquisizione immagini.
* Sensore a ultrasuoni **HC-SR04** per rilevamento inserimento carte.
* **Display LCD 16x2 (I2C)** per messaggi all’utente.
* LED e buzzer per segnalazioni visive/acustiche.
* Pulsante di reset per riavvio manuale del sistema.

---

## 📦 Stack software

* **Firmware microcontrollore**: C++ (Arduino)
* **Server**: PHP + Python
* **Database**: MySQL
* **OCR**: Google Cloud Vision API
* **Librerie principali**:

  * `esp_camera.h`
  * `WiFi.h`
  * `HTTPClient.h`
  * `LiquidCrystal_I2C.h`
  * `google.cloud.vision`

---

## 🔮 Sviluppi futuri

* Miglioramento della fotocamera per una qualità di scansione superiore.
* Interfaccia web completa per la gestione delle collezioni.
* Ottimizzazione del riconoscimento con modelli OCR personalizzati.

---

## 📚 Contesto

Progetto Judge è stato realizzato come progetto universitario per il corso di **Laboratorio di Sviluppo di Applicazioni IoT** presso l’Università degli Studi della Campania *Luigi Vanvitelli*.
Il suo obiettivo è mostrare come hardware, software e machine learning possano lavorare insieme per rendere più semplice e divertente l’esperienza dei collezionisti.

