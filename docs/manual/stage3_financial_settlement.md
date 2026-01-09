# Tahap 3: Penyelesaian Keuangan (Financial Settlement)

Tahap ini mencakup penagihan kepada pelanggan (Invoice), pencatatan pembayaran (Receipt), dan instruksi transfer ke maskapai (Payment Advise).

## 1. Pembuatan Proforma Invoice
1.  Buka menu **3. Finance** atau klik tombol **Generate Invoice** dari baris Movement yang relevan.
2.  Pilih template (Internal atau Customer). Untuk UAT ini, gunakan **Internal**.
3.  Sistem akan otomatis menarik data Pax (45) dan Selling Fare (Rp 16jt).
4.  Klik **Save & Generate PDF**.

## 2. Pencatatan Pembayaran (Receipt)
Kita akan mencatat Deposit 1 sesuai Storyline.
1.  Buka detail Invoice yang baru dibuat.
2.  Klik tombol **Record Payment**.
3.  Isi data pembayaran:
    -   **Amount:** 144.000.000 (20% Deposit)
    -   **Method:** Bank Transfer
    -   **Reference:** DEP1-AUDIT-001
4.  Klik **Save Payment**.

## 3. Instruksi Pembayaran Maskapai (Payment Advise)
1.  Buka menu **4. Payment Advise**.
2.  Klik **Create Payment Advise**.
3.  Pilih Movement "GA123456" (PT Maju Jaya Abadi).
4.  Data rincian transfer ke maskapai akan terisi otomatis berdasarkan Approved Fare (Rp 14.5jt).
5.  Klik **Generate Advise**.

## 4. Titik Verifikasi (Financial Integrity)
- [ ] **Verifikasi 3.1:** Apakah Total Amount di Invoice muncul sebagai "720.000.000"?
- [ ] **Verifikasi 3.2:** Apakah setelah mencatat pembayaran, status Invoice berubah menjadi "PARTIALLY PAID"?
- [ ] **Verifikasi 3.3:** Apakah di menu **2. Movements**, indikator **DP1** berubah menjadi Hijau/Lunas?
- [ ] **Verifikasi 3.4:** Apakah di Payment Advise, nilai "Transfer Amount" sesuai dengan rincian Storyline (misal: 20% dari total approved fare)?

---
**[Lanjut ke Tahap 4: Manajemen & Audit](./stage4_management_audit.md)**