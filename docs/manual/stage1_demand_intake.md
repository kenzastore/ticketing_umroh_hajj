# Tahap 1: Penerimaan Permintaan (Booking Request)

## Pendahuluan
Tahap ini adalah titik awal dari seluruh proses bisnis. Di sini, staf operasional memasukkan permintaan kuota kursi (demand) dari agen perjalanan atau korporat.

## Peran yang Bertanggung Jawab
- **Ticketing/Operational Staff**
- **Administrator**

## Prasyarat
- Data **Agen** dan **Corporate** sudah terdaftar di sistem (Menu: Master Data).

## Langkah-langkah

### 1. Membuat Permintaan Baru
1.  Buka menu **1. Booking Request** dari Dashboard.
2.  Klik tombol **Create New Request** di pojok kanan atas.
3.  Isi formulir permintaan:
    -   **Corporate Name**: Pilih dari daftar yang tersedia.
    -   **Agent Name**: Pilih agen yang meminta.
    -   **Group Size (Pax)**: Masukkan jumlah jamaah.
    -   **Flight Segments**: Masukkan detail penerbangan (hingga 4 segmen/leg). Pastikan tanggal, nomor penerbangan, dan sektor (rute) diisi dengan benar.
4.  Klik **Save Request**.

### 2. Memantau Daftar Permintaan
1.  Halaman utama **Booking Requests** menampilkan semua permintaan yang masuk.
2.  Permintaan yang baru dibuat akan memiliki status default (belum dikonversi).
3.  Gunakan fitur **Filter** (berdasarkan rentang tanggal) atau **Export to Excel** untuk pelaporan harian.

## Tips & Catatan
-   **Multi-segment**: Sistem mendukung hingga 4 leg penerbangan (misal: SUB-SIN, SIN-JED, JED-SIN, SIN-SUB).
-   **TTL (Time To Live)**: Perhatikan kolom TTL untuk mengetahui batas waktu berlakunya penawaran harga/permintaan ini.

---
[Kembali ke Beranda](./index.md)
