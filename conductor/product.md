# PROPOSAL RINGKAS (REVISI)

Digitalisasi Proses Ticketing Umroh & Haji (Web-Based + FullView Display)

**Referensi utama:** Worksheet operasional “ELANG EMAS WORKSHEET.xlsx” dengan sheet: FRONT PAGE, BOOKING REQUEST, MOVEMENT, PAYMENT REPORT, INVOICE, Payment advise, RANGKUMAN, TIME LIMIT.

## Contracts (Must Follow)

These documents are binding specs for implementation and must be followed by all agents:

 `conductor/contracts/tcp_group_split_guideline.md`  
 Definition of **TCP** and rules for **split group → multiple PNRs** (MOVEMENT sheet rules).

Contract-first rule:
 If the worksheet format changes, update the contract doc(s) first, then update code/schema.

---

## 1) Latar Belakang

Proses ticketing Umroh & Haji saat ini sudah memiliki format kerja yang cukup rapi di spreadsheet, namun aktivitas harian masih bergantung pada **banyak sheet terpisah** (Booking Request, Movement, Payment Report, Invoice, Payment Advise, Time Limit, dst.) dan koordinasi eksternal melalui **WA/email**. Pada kondisi dinamis (perubahan jadwal, extra flight, cancel, revisi jumlah seat), risiko terbesar adalah **beda versi data** dan **status yang tidak sinkron** antar sheet.

---

## 2) Referensi Alur & Data Existing (berdasarkan Worksheet)

Berikut pemetaan sheet yang saat ini digunakan dan fungsinya:

1. **BOOKING REQUEST**
    Digunakan untuk input permintaan/“demand” group: *Corporate Name*, *Agent Name*, *Skyagent ID*, detail penerbangan multi-segmen (hingga 4 segmen: `FLT_DATE1..4`, `FLT_NO1..4`, `SECTOR1..4`), *Group Size*, *Duration* dan *TTL Days*.

2. **MOVEMENT (Daily/Group Movement)**
    Digunakan untuk tracking proses inti: **PNR**, DP1/DP2, **FP**, **Tour Code**, Carrier/Flight/Sector per segmen, jadwal segmen (DEP/ARR), serta kolom **tanggal deposit ke airlines**, **tanggal deposit ke EEMW**, **fullpay**, **time limit manifest & ticketing**, hingga status **Ticketing Done** dan “Belonging To”.

3. **PAYMENT REPORT**
    Digunakan untuk rekap pembayaran per penerbangan/segmen: *Date of Payment*, *Total Pax*, *Remarks* (mis. selling fares, deposit, fullpay), nilai *Debet*, serta informasi rekening *From/To*.

4. **INVOICE (Proforma Invoice)**
    Memuat format *Proforma Invoice* lengkap: *REF/Tour Code*, PNR, flight information, fares per pax, total amount, serta pembagian pembayaran (contoh: Deposit-1, Deposit-2, Fullpayment) termasuk persentase.

5. **Payment advise**
    Memuat catatan top-up/konfirmasi pembayaran ke maskapai: tanggal pembuatan top up, remarks, tanggal email ke maskapai, konfirmasi, serta ringkasan nilai (deposit/balance/top-up) dan data bank.

6. **TIME LIMIT / FRONT PAGE / RANGKUMAN**
    Mengarah pada kebutuhan **pengingat time limit** (mis. reminder H-3) dan ringkasan per agent.

**Catatan:** Struktur sheet di atas menjadi acuan langsung untuk desain modul dan database di sistem web.

---

## 3) Problem (Kendala Utama)

1. **Data tersebar & rawan beda versi**
    Satu transaksi berjalan lintas sheet (Booking Request → Movement → Invoice → Payment Report → Payment Advise). Saat ada revisi, update harus dilakukan berulang dan rawan terlewat.
2. **Tracking status belum real-time & sulit diaudit**
    Status DP/FP, time limit, ticketing done, dan perubahan segmen penerbangan masih manual; jejak perubahan (siapa mengubah apa, kapan) belum terkunci rapi.
3. **Validasi keuangan berisiko tidak sinkron**
    Payment Report dan Payment Advise dapat berbeda bila tidak ada pengunci aturan sistem (DP, deposit airlines vs deposit internal, fullpay, dll).
4. **Akses lapangan kurang praktis**
    Tim butuh akses cepat dari HP (saat koordinasi agent/kyai/maskapai), tetapi data tersebar di file/sheet.
5. **Pengingat time limit belum otomatis**
    Worksheet sudah menandai kebutuhan reminder, namun eksekusinya masih manual.
6. **Keamanan dokumen belum standar**
    Invoice/receipt/resi digital belum memiliki mekanisme token/QR + validasi akses.

---

## 4) Solusi yang Diusulkan

### Sistem Web-Based + FullView Display (Mobile & Desktop Friendly)

Membangun sistem berbasis web yang **mengadopsi struktur worksheet** menjadi modul terintegrasi, dengan tampilan **FullView** untuk monitoring (TV mode) dan tampilan **mobile-friendly** untuk operasional lapangan. Halaman Login kini didesain minimalist dan user-friendly, menyediakan ringkasan alur kerja serta akses demo yang mudah bagi pengguna baru.

### Fitur inti (MVP)

1. **Request Management** (mengganti BOOKING REQUEST)
    Form input request + multi-segmen flight + pax + durasi + agent/requester. Output: Request ID / Tour Code draft.
2. **Movement Monitoring** (mengganti MOVEMENT)
    Dashboard Daily/Group Movement: PNR, DP1/DP2, FP, time limit, ticketing done, serta log perubahan. FullView Display: filter per tanggal/agent/carrier/status.
    **Fitur TCP Validation:** Validasi otomatis jumlah penumpang split PNR agar sesuai dengan target group size (TCP).
3. **Invoice Generator** (mengganti INVOICE)
    Generate **2 versi**: Internal & untuk Kyai/Requester. Export PDF + template standar. **NOTE:** Invoice template is strictly aligned with `invoice_template_contract.md` for layout, fields, and styling.
4. **Payment Tracking** (mengganti PAYMENT REPORT + Payment advise)
    Input pembayaran, upload bukti, mapping ke segmen/PNR/Tour Code, rekap otomatis, dan export.
5. **Time Limit Reminder** (mengganti TIME LIMIT)
    Notifikasi otomatis (mis. H-3) untuk manifest & ticketing, DP/FP jatuh tempo, dll.
6. **Audit Log & Role Based Access**
    Role: Admin Ticketing, Keuangan, Viewer/Monitor. Semua perubahan tercatat.

---

## 5) Output / Deliverables

* Aplikasi web (Native PHP + MariaDB) dengan role-based access.
• Modul: Request, Movement (FullView), Invoice (2 versi), Payment (Report + Advise), Reminder.
• Template dokumen: invoice PDF (compliant with `invoice_template_contract.md`), payment report, payment advise, (opsional) resi digital.
• Audit log perubahan + export laporan (Excel/PDF).
• Panduan Pengguna (User Manual) terintegrasi dengan web viewer di menu Admin.

---

## 6) Support yang Dibutuhkan

### A. Data Mentah (Wajib)

* Worksheet existing (sudah tersedia): **ELANG EMAS WORKSHEET.xlsx** sebagai baseline.
* Contoh dokumen pendukung: email/WA konfirmasi maskapai, bukti bayar, template invoice/advise.
* Aturan bisnis: definisi DP1/DP2/FP, kondisi “ticketing done”, dan SOP time limit.

### B. SDM (Wajib)

* 1 PIC proses (owner bisnis) untuk validasi aturan.
* 1–2 PIC operasional ticketing untuk uji skenario real.
* 1 PIC keuangan untuk validasi pembayaran & report.
* 1 Admin sistem (user/role).

### C. Infrastruktur (Wajib)

* Server/hosting + database (MariaDB).
* Storage upload (bukti bayar/konfirmasi/PDF).
* Backup + manajemen akses.

---

## 7) Timeline Implementasi (MVP 4–6 Minggu)

* **Minggu 1:** Finalisasi kebutuhan (mapping kolom worksheet → DB) + desain UI/DB.
* **Minggu 2:** Modul Request + import/export dari/ke format worksheet.
* **Minggu 3:** Modul Movement + FullView Display + audit log.
* **Minggu 4:** Modul Invoice (2 versi) + export PDF.
* **Minggu 5 (opsional):** Modul Payment Report/Advise + validasi.
* **Minggu 6 (opsional):** Reminder time limit + training + go-live.

---

## 8) Risiko & Mitigasi Singkat

* **Data awal tidak seragam:** lakukan mapping & cleansing berdasar worksheet.
* **Perubahan proses mendadak:** kunci scope MVP; perubahan mayor masuk fase berikutnya.
* **Adopsi user:** training singkat + panduan 1 halaman + support awal.

---

## 9) Next Step

1. Tetapkan PIC operasional & PIC keuangan.
2. Sepakati scope MVP (modul mana dulu) dan target go-live.
3. Mulai mapping field worksheet ke database + prototyping UI.

### Contracts (Must Follow)

@conductor/contracts/tcp_group_split_guideline.md
