# The TCP terms and how to fill the group size column and TCP if i want to group split to multiple PNR

This note is a practical guideline for **Umroh/Hajj group ticketing** (ELANG EMAS workflow) to avoid confusion when a **single group is split into multiple PNRs**.

It is designed to be referenced by **Gemini CLI Conductor** and used as a stable contract for data entry + system rules.

---

## 1) What “TCP” means in airline/group ticketing

**TCP** commonly refers to **Total Complete Party** (a.k.a. *The Complete Party*).

**Meaning:** the **total number of passengers that belong to the same group/party**, even if they are divided into multiple bookings (multiple PNRs).

Why it matters:
- Airlines/ticketing teams need to know that multiple PNRs are actually one **single group**.
- It helps with group handling, seat blocks, ticketing deadlines, and payment staging.

---

## 2) Key concept: “Group Size” vs “TCP”

When a group is **not split**:
- **Group Size = TCP** (they are the same)

When a group **is split into multiple PNRs**:
- **TCP** = the **total group size** (fixed for the whole group)
- **Group Size** = the pax count **inside one PNR** (varies per PNR)

### The rule you must always satisfy
✅ **SUM(Group Size per PNR) = TCP**

---

## 3) Where the PNR lives in your workbook

In your workflow:
- **PNR is recorded in the `MOVEMENT` sheet**.
- A group is tied together using a group key such as **`TOUR CODE`** (and/or a consistent `REF`/request id if present).

That means:
- Splitting is documented **in MOVEMENT**, not in the high-level schedule/request fields.

Reference mapping (from MOVEMENT sheet layout):
- **PNR** → Booking code per split
- **TOUR CODE** → Group identifier (same for all PNRs in one group)
- **Passenger** → Pax count for that PNR (this is your “Group Size per PNR”)

---

## 4) How to fill the columns when splitting to multiple PNRs

### Step A — Decide the total group (TCP)
Example: total jamaah = **40 pax**
- Set **TCP = 40**

### Step B — Decide the split allocation (Group Size per PNR)
Example split:
- PNR-A = 25 pax
- PNR-B = 15 pax

Validation:
- 25 + 15 = 40 ✅

### Step C — Enter it into MOVEMENT (one row per PNR)
Create **one MOVEMENT row per PNR**, and keep the group key consistent:

| TOUR CODE | PNR   | Passenger (Group Size per PNR) | TCP |
|---|---|---:|---:|
| SAME TOUR CODE | PNR-A | 25 | 40 |
| SAME TOUR CODE | PNR-B | 15 | 40 |

**Important:**
- `TOUR CODE` must be the **same** for all PNR rows that belong to the same group.
- `Passenger` differs per row based on how you split the group.

> If your MOVEMENT sheet does not have a TCP column, TCP can be **derived** (recommended) using SUM(Passenger) per TOUR CODE.

---

## 5) How to fill the “Group Size” and “TCP” on schedule/request summary pages (no PNR)

If a page/section does **not** have a PNR column (only flight legs, dates, duration, etc.) then it should represent the **whole group**, not the splits.

For the group-level summary:
- **Group Size = TCP** (overall total)
- Example: group total 40 → Group Size 40, TCP 40

The per-PNR details remain only in MOVEMENT.

---

## 6) Recommended system rule (to prevent human error)

### Option A (recommended): TCP is computed, not manually typed
- TCP_total for a group = **SUM(Passenger)** for all MOVEMENT rows that share the same TOUR CODE.

Benefits:
- No risk of mismatch between typed TCP and split totals.
- Your UI can show “TCP total” automatically.

### Option B: TCP is stored explicitly
If you store TCP in each split row (repeated value):
- Enforce validation: **SUM(Passenger) == TCP**
- If mismatch → show error/warning and block “FP Merah / Confirmed” actions.

---

## 7) Edge cases

### A) Different itineraries across PNRs
If PNR-A and PNR-B have different routes/dates, confirm the business meaning:
- If they are still one group (same trip concept) → keep same TOUR CODE / TCP group.
- If truly different trips/packages → treat as different groups (different TOUR CODE / TCP).

### B) PNR not available yet
If you need to prepare split allocation before PNR is issued:
- Store provisional split lines under the same TOUR CODE with `pnr = NULL` (system), or use a temporary split label (worksheet).
- Once airline issues PNRs, replace temporary values with real PNR codes.

---

## 8) Minimal “Definition of Done” for group split

A group split is considered correct when:
- [ ] All split rows share the same **TOUR CODE**
- [ ] Each split row has a **PNR** (or explicitly `NULL` if pending)
- [ ] Each split row has **Passenger** filled correctly
- [ ] **SUM(Passenger)** across the group equals the intended TCP
- [ ] Payment deadlines and ticketing deadlines remain consistent with the group policy

---

## 9) How to use this doc in Gemini CLI Conductor

### A) Put this file into your repo
Suggested location:
- `conductor/contracts/tcp_group_split_guideline.md`

### B) Ensure Conductor loads it
In Gemini CLI interactive session, attach it:
```text
@conductor/contracts/tcp_group_split_guideline.md
```

Or include it in `GEMINI.md`:
```md
@conductor/contracts/tcp_group_split_guideline.md
```

After editing `GEMINI.md`:
```text
/memory refresh
```

### C) Enforce as a rule in Conductor tasks
In any track/task touching MOVEMENT/PNR/grouping, add:
- “Must follow tcp_group_split_guideline.md”
- “Stop and ask if the worksheet columns differ”
