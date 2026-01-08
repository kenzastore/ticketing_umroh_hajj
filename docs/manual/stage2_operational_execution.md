# Tahap 2: Eksekusi Operasional (Movement & Monitoring)

## Pendahuluan
Setelah permintaan disetujui, langkah selanjutnya adalah mengeksekusi rencana perjalanan tersebut menjadi pergerakan nyata yang dipantau melalui PNR (Passenger Name Record).

## Peran yang Bertanggung Jawab
- **Ticketing/Operational Staff**
- **Administrator**
- **Monitor/Viewer** (Hanya memantau)

## Prasyarat
- Terdapat data **Booking Request** dengan status Pending (Belum Dikonversi).

## Langkah-langkah

### 1. Konversi ke Movement (PNR Assignment)
1.  Buka menu **Booking Request**.
2.  Pilih salah satu permintaan, lalu klik tombol **View** (ikon mata).
3.  Di halaman detail, klik tombol **Convert to Movement**.
4.  Masukkan data operasional inti:
    -   **PNR**: Kode booking dari maskapai.
    -   **Tour Code**: Kode grup tur.
    -   **Carrier**: Nama maskapai.
5.  Klik **Finalize Conversion**. Data akan dipindahkan ke modul Movement dan dikunci (tidak dapat diedit sebagai Request lagi).

### 2. Monitoring via FullView Dashboard
1.  Buka menu **2. Movement**.
2.  Halaman ini menampilkan dashboard pergerakan grup secara real-time.
3.  **Filter & Pencarian**: Gunakan kotak pencarian untuk mencari berdasarkan PNR, Agen, atau Tour Code. Gunakan filter tanggal keberangkatan untuk menyaring tampilan.
4.  **Monitor Mode**: Klik tombol **Monitor Mode** untuk mengubah tampilan menjadi lebih besar (FullView), sangat cocok untuk ditampilkan pada TV di ruang operasional. Halaman ini akan melakukan refresh otomatis setiap 30 detik.

### 3. Pembaruan Status Operasional
1.  Pada daftar Movement, klik tombol **Edit** pada salah satu baris.
2.  Perbarui status penting seperti:
    -   **Ticketing Done**: Centang jika tiket sudah diterbitkan semua.
    -   **Time Limit**: Pantau batas waktu manifest dan ticketing.

## Tips & Catatan
-   **Indikator Warna**: Perhatikan warna pada status pembayaran (DP1, DP2, FP). Warna hijau menandakan sudah lunas, kuning untuk parsial, dan merah/tanpa warna untuk yang belum dibayar.
-   **Urgent Deadlines**: Pantau bagian "Urgent Deadlines" pada Dashboard utama untuk pengingat H-3 sebelum batas waktu berakhir.

---
[Kembali ke Beranda](./index.md)
