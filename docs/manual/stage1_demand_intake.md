# Tahap 1: Penerimaan Permintaan (Demand Intake)

Tahap ini mencakup proses awal pencatatan permintaan kuota kursi dari Agen atau Corporate ke dalam sistem. Kita akan menggunakan data dari **[Master Storyline](./storyline.md)**.

## 1. Menyiapkan Data Master
Sebelum membuat permintaan, pastikan data Agen dan Corporate sudah tersedia di sistem.
1.  Buka menu **Masters** > **Travel Agents**. Pastikan "Mutiara Tour & Travel" terdaftar.
2.  Buka menu **Masters** > **Corporates**. Pastikan "PT Maju Jaya Abadi" terdaftar.

## 2. Membuat Booking Request Baru
1.  Buka menu **1. Requests** dari navigasi utama.
2.  Klik tombol **Create New Request**.
3.  Isi formulir dengan data Storyline:
    -   **Corporate Name:** PT Maju Jaya Abadi
    -   **Agent Name:** Mutiara Tour & Travel
    -   **Group Size (TCP):** 45 (Total jamaah satu grup)
    -   **Flight Details (4 Leg):**
        -   Leg 1: 11-05-2026 | TR596 | SUB-SIN
        -   Leg 2: 11-05-2026 | TR597 | SIN-JED
        -   Leg 3: 20-05-2026 | TR598 | JED-SIN
        -   Leg 4: 21-05-2026 | TR599 | SIN-SUB
    -   **Fares (Nett):** 14.500.000
    -   **Fares (Selling):** 16.000.000
4.  Klik **Save Request**.

## 3. Titik Verifikasi (Data Integrity)
Setelah menyimpan, lakukan pengecekan berikut pada tabel daftar permintaan:
- [ ] **Verifikasi 1.1:** Apakah jumlah Pax muncul sebagai "45"?
- [ ] **Verifikasi 1.2:** Apakah rute Leg 1 s/d Leg 4 muncul dengan benar di kolom masing-masing?
- [ ] **Verifikasi 1.3:** Apakah nilai Total Price otomatis terhitung (45 x 16.000.000)? (Catatan: Hindari kerancuan istilah TCP di sini dengan Total Complete Party)

---
**[Lanjut ke Tahap 2: Eksekusi Operasional](./stage2_operational_execution.md)**