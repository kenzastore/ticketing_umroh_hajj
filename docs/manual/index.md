# Panduan Pengguna (User Manual)
Digitalisasi Proses Ticketing Umroh & Haji

Selamat datang di panduan resmi penggunaan sistem Ticketing Umroh & Haji. Panduan ini disusun berdasarkan alur kerja bisnis dari awal hingga akhir.

Untuk mempermudah pengujian, kita akan menggunakan skenario tunggal yang dapat diikuti dari awal hingga akhir:
**[Master Storyline: Group Umrah Mei 2026](./storyline.md)**

## Peran Pengguna (User Roles)
Setiap peran memiliki akses dan tugas yang berbeda:
- **Administrator:** Manajemen pengguna, data master (Agen/Corporate), dan pengawasan sistem.
- **Ticketing/Operational Staff:** Input permintaan (Booking Request) dan manajemen pergerakan (Movement).
- **Finance Officer:** Pembuatan Invoice, pencatatan pembayaran, dan Payment Advice.
- **Monitor/Viewer:** Memantau dashboard FullView dan indikator KPI.

## Alur Kerja Bisnis (Business Workflow)

### [Tahap 1: Penerimaan Permintaan (Booking Request)](./stage1_demand_intake.md)
Proses input permintaan kuota kursi dari agen atau korporat.

### [Tahap 2: Eksekusi Operasional (Movement & Monitoring)](./stage2_operational_execution.md)
Konversi permintaan menjadi pergerakan (PNR), pemantauan jadwal penerbangan, dan status manifest.

### [Tahap 3: Penyelesaian Keuangan (Invoicing & Payments)](./stage3_financial_settlement.md)
Penagihan (Invoice), konfirmasi pembayaran (Receipt), dan instruksi pembayaran ke maskapai (Payment Advice).

### [Tahap 4: Manajemen & Audit](./stage4_management_audit.md)
Proses monitoring deadline dan peninjauan riwayat perubahan (Audit Log).

---

## UAT Readiness Checklist
Gunakan daftar ini untuk memastikan sistem siap untuk User Acceptance Testing (UAT). Checklist ini merangkum verifikasi dari Tahap 1 s/d 4.

| No | Item Pemeriksaan | Target Hasil | Status |
|:---|:---|:---|:---:|
| 1 | **Demand Intake** | Booking Request tersimpan dengan multi-leg flight yang benar. | [ ] |
| 2 | **Conversion** | Request berhasil diubah menjadi Movement dengan PNR/Tour Code. | [ ] |
| 3 | **Data Handover** | Pax, Agen, dan Corporate terbawa dengan benar ke Movement. | [ ] |
| 4 | **Invoicing** | Proforma Invoice terbuat otomatis dengan kalkulasi (Pax x Fare). | [ ] |
| 5 | **Payment** | Pencatatan pembayaran mengubah status Invoice dan indikator DP di Movement. | [ ] |
| 6 | **Audit Trail** | Setiap aksi perubahan (Create/Update) tercatat di menu Audit Logs. | [ ] |
| 7 | **Monitoring** | Dashboard menampilkan peringatan visual untuk deadline mendekati (H-3). | [ ] |

---
© 2026 PT Elang Emas Mandiri Indonesia
