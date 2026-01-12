# Tahap 2: Eksekusi Operasional (Operational Execution)

Tahap ini mengubah niat (Request) menjadi pergerakan nyata (Movement) dengan identitas penerbangan yang unik (PNR/Tour Code).

## 1. Konversi Request ke Movement
1.  Cari data "PT Maju Jaya Abadi" di menu **1. Requests**.
2.  Klik tombol **Eye (Detail)** atau langsung tombol **Convert** jika tersedia.
3.  Isi data identitas penerbangan sesuai Storyline:
    -   **PNR:** GA123456
    -   **Tour Code:** UMRAH-MAY-26-001
    -   **Carrier:** Scoot
4.  Klik **Confirm Conversion**.

## 2. Manajemen Movement (Dashboard)
1.  Buka menu **2. Movements**.
2.  Data yang baru dikonversi akan muncul di daftar teratas.
3.  Perhatikan indikator warna untuk status pembayaran (DP1, DP2, FP). Saat ini seharusnya masih berwarna **Merah/Pending**.

## 3. Titik Verifikasi (Handover Integrity)
Pastikan data dari Tahap 1 terbawa dengan sempurna:
- [ ] **Verifikasi 2.1:** Apakah nama Agen "Mutiara Tour & Travel" tetap sama di detail Movement?
- [ ] **Verifikasi 2.2:** Apakah jumlah Pax "45" terbawa ke Movement?
- [ ] **Verifikasi 2.3:** Apakah jadwal Leg 1 s/d Leg 4 tetap sinkron dengan data awal?
- [ ] **Verifikasi 2.4:** Cari di menu **1. Requests**, apakah status baris data tadi sudah ditandai sebagai "CONVERTED" (biasanya berwarna hijau/abu-abu redup)?

## 4. Penanganan Split PNR (TCP Group Split)
Jika satu grup (misal 45 pax) dibagi menjadi beberapa kode PNR (misal PNR-A 25 pax dan PNR-B 20 pax):

1.  **Tentukan Target TCP:** Pada halaman **Edit Movement**, pastikan kolom **Target TCP** terisi dengan total jamaah keseluruhan grup (misal: 45).
2.  **Identitas Grup:** Semua baris pergerakan (split) yang tergabung dalam satu grup **WAJIB** memiliki **Tour Code** dan **Movement No (Request ID)** yang identik.
3.  **Monitor Group Sum:** Sistem akan menjumlahkan secara otomatis seluruh jamaah dari PNR yang memiliki identitas grup yang sama.
4.  **Validasi Ketat:** 
    -   Jika total jamaah split melebihi Target TCP, teks "Current Group Sum" akan berubah menjadi **MERAH**.
    -   Tombol **Update Movement** akan berubah menjadi **"TCP Exceeded"** dan tidak bisa diklik (Disabled).
    -   Anda harus menyesuaikan jumlah pax atau menaikkan Target TCP agar data bisa disimpan.

---
**[Lanjut ke Tahap 3: Penyelesaian Keuangan](./stage3_financial_settlement.md)**