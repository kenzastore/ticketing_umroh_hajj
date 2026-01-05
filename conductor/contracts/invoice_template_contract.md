# Invoice Template Contract (ELANG EMAS) — INVOICE Sheet

This document defines the **field-to-layout contract** for generating the *Proforma Invoice* template based on the `INVOICE` sheet inside `ELANG_EMAS_WORKSHEET.xlsx`.

> Goal: keep the web/PDF template consistent with the worksheet format and make changes auditable.

---

## 1) Header (Right-side Recipient + Invoice Date)

| Area | Cell | Field | Type/Format |
|---|---:|---|---|
| Invoice Date | `J4` | `invoice_date` | Date |
| Label | `J6` | (fixed) **Attention to** | Text |
| Recipient Name | `J7` | `attention_to_name` | Text |
| Recipient Company | `J8` | `recipient_company_name` | Text |
| Address line 1 | `J9` | `recipient_address_line1` | Text |
| Address line 2 | `J10` | `recipient_address_line2` | Text |

Notes:
- If the address requires more lines, append from `J11` downward (optional expansion rule).

---

## 2) Title + Reference (Left / Center)

| Area | Cell | Field | Notes |
|---|---:|---|---|
| Title | `C13` | (fixed) **PROFORMA INVOICE** | Static label |
| Reference ID | `C14` | `reference_id` | Printed as `REF : <value>` |

---

## 3) Main Table Header (Invoice Columns)

Header row (worksheet): **row 15**.

| Header | Cell | Field |
|---|---:|---|
| DOT | `C15` | `dot_date` |
| DESCRIPTION | `D15` | `description` |
| TOTAL PAX | `H15` | `total_pax` |
| FARES | `I15` | `fare_per_pax` |
| AMOUNT IN IDR | `J15` | `amount_idr` |

---

## 4) Summary Line (DOT + PNR + PAX)

Summary row (worksheet): **row 18**.

| Cell | Field | Notes |
|---:|---|---|
| `C18` | `dot_date` | Date |
| `D18` | `pnr` | Printed as `PNR : <value>` |
| `H18` | `total_pax` | Integer |

---

## 5) Flight Information Section (Legs)

Label:
- `D19` = **Flight Information** (fixed)

Leg rows (default): **row 20 to row 23** (max 4 legs).

| Cell | Field | Notes |
|---:|---|---|
| `D{row}` | `flight_date` | Date |
| `E{row}` | `flight_no` | Airline flight number |
| `F{row}` | `sector` | Route shorthand (e.g., `SUBSIN`, `SINJED`) |
| `G{row}` | `time_range` | Text range; may include `+1` |

**Default range:** `D20:G23`

Rules:
- MVP rule: support up to **4 legs** exactly like the worksheet.
- If more legs are required in the future:
  - Option A: insert new rows in the generated format (Excel-based), or
  - Option B: render a dynamic table in HTML/PDF while keeping the same columns.

---

## 6) Fare Breakdown + Payment Staging (DP1/DP2/Fullpayment)

### 6.1 Base Fare Row

Row (worksheet): **row 25**.

| Cell | Field | Notes |
|---:|---|---|
| `D25` | (fixed) `Fares` | Label |
| `H25` | `total_pax` | Pax |
| `I25` | `fare_per_pax` | Per pax |
| `J25` | `amount_idr` | Total |

### 6.2 Staging Rows (Non-refundable Deposits + Fullpayment)

Rows: **26–28**

| Row | Description Cell | Percent Cell | Pax | Fare | Amount |
|---:|---:|---:|---:|---:|---:|
| 26 | `D26` `deposit_1_label` | `G26` `deposit_1_percent` | `H26` | `I26` | `J26` |
| 27 | `D27` `deposit_2_label` | `G27` `deposit_2_percent` | `H27` | `I27` | `J27` |
| 28 | `D28` `fullpayment_label` | `G28` `fullpayment_percent` | `H28` | `I28` | `J28` |

Important behavior (as per template):
- Staging values (`I26/I27/I28` and `J26/J27/J28`) are commonly **negative** to represent deductions from the base total in `J25`.

---

## 7) Totals

| Area | Cell | Field |
|---|---:|---|
| Total Payment (label) | `I30` | (fixed) |
| Total Payment (value) | `J30` | `total_payment_idr` |
| Balance (label) | `I31` | (fixed) |
| Balance (value) | `J31` | `balance_idr` |

---

## 8) Remarks (Terms & Conditions)

- Label: `C32` = **REMARKS**
- Items: `C33` to `C37` (5 lines) = `remarks[]`

Rule:
- Keep remark numbering and sentence order consistent to match the worksheet.

---

## 9) Time Limit (Deadlines)

Header: `C38` = **6. TIME LIMIT**

| Item | Label Cell | Date Cell | Field |
|---|---:|---:|---|
| Deposit-1 | `C39` | `E39` | `deposit_1_deadline` |
| Deposit-2 | `C40` | `E40` | `deposit_2_deadline` |
| Fullpayment | `C41` | `E41` | `fullpayment_deadline` |
| Ticketing | `C42` | `E42` | `ticketing_deadline` |

---

## 10) Bank Transfer Details

Label: `C44` = **Payment via bank transfer to Bank account:**

| Area | Cell | Field |
|---|---:|---|
| Bank/Branch | `C46` | `bank_name` |
| Account Name | `C47` | `bank_account_name` |
| Account No | `C48` | `bank_account_no` |

---

## 11) Footer Contact

| Cell | Field |
|---:|---|
| `C52` | `issuer_company_name` |
| `C53` | `contact_person_name` |
| `C54` | `contact_phone` |

---

## 12) Recommended Payload Model (for PHP)

```json
{
  "invoice_date": "2026-01-05",
  "reference_id": "11MAY-45S-26D-TR",
  "pnr": "ABC123",
  "dot_date": "2026-05-11",
  "total_pax": 45,
  "recipient": {
    "attention_to_name": "Kyai ...",
    "recipient_company_name": "Pesantren ...",
    "recipient_address_line1": "Alamat 1",
    "recipient_address_line2": "Alamat 2"
  },
  "flight_legs": [
    { "flight_date": "2026-05-11", "flight_no": "TR123", "sector": "SUBSIN", "time_range": "0900-1300" }
  ],
  "fare": {
    "fare_per_pax": 1200000,
    "amount_idr": 54000000,
    "stages": [
      { "label": "Non Refundable Deposit-1st", "percent": 0.2, "fare": -240000, "amount_idr": -10800000 },
      { "label": "Non Refundable Deposit-2nd", "percent": 0.3, "fare": -360000, "amount_idr": -16200000 },
      { "label": "Non Refundable Fullpayment", "percent": 0.5, "fare": -600000, "amount_idr": -27000000 }
    ],
    "total_payment_idr": 0,
    "balance_idr": 0
  },
  "deadlines": {
    "deposit_1_deadline": "2026-04-01",
    "deposit_2_deadline": "2026-04-15",
    "fullpayment_deadline": "2026-05-01",
    "ticketing_deadline": "2026-04-25"
  },
  "bank": {
    "bank_name": "Bank ...",
    "bank_account_name": "PT Elang Emas ...",
    "bank_account_no": "1234567890"
  },
  "footer": {
    "issuer_company_name": "PT ELANG EMAS ...",
    "contact_person_name": "Ihsan",
    "contact_phone": "+62..."
  }
}
```

---

## 13) Change Control (Worksheet Drift)

Any time `ELANG_EMAS_WORKSHEET.xlsx` changes:
1. Update this contract first.
2. Only then update the generator (HTML/PDF/Excel fill).
3. Record the change in `DECISIONS.md` with a version/date stamp.

