# Tahap 3: Penyelesaian Keuangan (Invoicing & Payments)

## Pendahuluan
Tahap ini mencakup semua aktivitas yang berkaitan dengan penagihan ke agen (Invoice), pencatatan pembayaran yang masuk, hingga instruksi pembayaran keluar (Payment Advice) ke maskapai.

## Peran yang Bertanggung Jawab
- **Finance Officer**
- **Administrator**

## Prasyarat
- **Movement** yang aktif dengan PNR dan Tour Code.
- Sudah ada kesepakatan harga (Approved Fare, Selling Fare).

## Langkah-langkah

### 1. Pembuatan Invoice (External)
1.  Buka menu **3. Invoice** dari Dashboard Finance.
2.  Pilih **Movement** yang akan dibuatkan Invoice-nya.
3.  Isi detail Invoice seperti nomor, tanggal, perhatian kepada, alamat, dan total pax/harga per pax.
4.  Klik **Save & Generate Invoice**.
5.  Setelah Invoice dibuat, Anda dapat melihat detailnya dan mengunduh versi **PDF** yang siap dikirimkan ke agen/klien. Pastikan data seperti jumlah pax, harga, dan jadwal pembayaran sudah sesuai.

### 2. Pencatatan Pembayaran Masuk
1.  Dari detail Invoice, scroll ke bawah ke bagian **Record New Payment**.
2.  Masukkan **Amount Paid**, **Payment Date**, dan **Payment Method**.
3.  Klik **Record Payment**.
4.  Verifikasi histori pembayaran dan status Invoice (UNPAID, PARTIALLY PAID, PAID).

### 3. Pembuatan Payment Advice (Internal)
1.  Buka menu **5. Payment Advise** dari Dashboard Finance.
2.  Klik **Create New Advise**.
3.  Pilih **Movement** terkait. Sistem akan mengisi data otomatis seperti Agent Name, Tour Code, PNR.
4.  Verifikasi detail kursi, harga yang disetujui, jumlah deposit, dan sisa pembayaran (Balance Payment).
5.  Isi informasi **Recipient Bank (Airline)** dan **Remitter Bank (EEMW)**.
6.  Klik **Generate Payment Advise**.
7.  Catatan ini digunakan sebagai instruksi pembayaran internal ke maskapai dan untuk keperluan audit.

## Tips & Catatan
-   **Konsistensi Data**: Pastikan semua data keuangan konsisten antara Movement, Invoice, dan Payment Advice.
-   **Audit Trail**: Setiap transaksi pembayaran tercatat dan dapat ditelusuri.

---
[Kembali ke Beranda](./index.md)
