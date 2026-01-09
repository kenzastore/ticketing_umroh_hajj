# Tahap 4: Manajemen & Audit (Management & Audit)

Tahap terakhir ini fokus pada pengawasan sistem, pemantauan batas waktu (deadlines), dan peninjauan riwayat perubahan (Audit Log).

## 1. Pemantauan Batas Waktu (Urgent Deadlines)
1.  Buka **Dashboard** utama.
2.  Cari bagian **Urgent Deadlines (H-3 & Past Due)**.
3.  Periksa apakah Movement "GA123456" muncul di sini jika tanggal **Ticketing Deadline** atau **DP/FP Deadline** mendekati H-3 dari hari ini.

## 2. Peninjauan Log Audit
1.  Buka menu **Masters** > **Audit Logs**.
2.  Gunakan filter untuk mencari aksi yang baru saja Anda lakukan:
    -   **Entity Type:** invoice / movement / user
    -   **Action:** CREATE / UPDATE
3.  Klik tombol **View** pada salah satu baris log.
4.  Periksa detail JSON yang menampilkan nilai lama (Old) dan nilai baru (New).

## 3. Titik Verifikasi (Audit & Control)
- [ ] **Verifikasi 4.1:** Apakah aksi "CREATE" untuk Invoice tadi tercatat di Audit Log dengan User ID Anda?
- [ ] **Verifikasi 4.2:** Apakah sistem memberikan peringatan visual (warna merah/badge) pada Dashboard untuk deadline yang mendekati?
- [ ] **Verifikasi 4.3:** Apakah data Master (Agen/Corporate) yang digunakan di Tahap 1 tetap konsisten hingga akhir laporan?

---
**[Selesai - Lanjut ke Ringkasan Kesiapan UAT](./index.md#uat-readiness-checklist)**