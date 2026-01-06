<?php
require_once __DIR__ . '/../../includes/auth.php';
check_auth(['admin', 'monitor']);

$tv = isset($_GET['tv']) ? 1 : 0;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daily Group Movement - FullView</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body { background-color: #f8f9fa; font-size: 0.8rem; }
    body.tv { font-size: 1rem; }
    .table-container { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .status-badge { font-size: 0.7rem; text-transform: uppercase; }
    thead th { position: sticky; top: 0; background: #212529; color: white; z-index: 10; white-space: nowrap; vertical-align: middle; text-align: center; }
    .table-responsive { max-height: 70vh; }
    .bg-orange { background-color: #fd7e14 !important; color: white; }
    .bg-brown { background-color: #795548 !important; color: white; }
    .bg-yellow-light { background-color: #fff9c4 !important; }
    .text-xs { font-size: 0.65rem; }
    .table-sm td, .table-sm th { padding: 0.25rem 0.4rem; }
    .hover-scale:hover { background-color: #f1f3f5; }
  </style>
</head>
<body class="<?= $tv ? 'tv' : '' ?>">

<div class="container-fluid py-3">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-0 fw-bold">MOVEMENT DASHBOARD</h2>
      <span class="text-muted small">Digitalisasi Ticketing Umroh & Haji</span>
    </div>
    <div class="d-none d-md-block">
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Dashboard</a>
        <button class="btn btn-primary btn-sm" onclick="load()">Refresh Data</button>
        <a href="?tv=<?= $tv ? 0 : 1 ?>" class="btn btn-dark btn-sm"><?= $tv ? 'Normal View' : 'Monitor Mode' ?></a>
    </div>
  </div>

  <!-- KPI Summary -->
  <div class="row g-2 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-primary border-4">
            <div class="card-body py-2">
                <div class="text-xs text-muted">TOTAL GROUPS</div>
                <div class="h4 mb-0 fw-bold" id="kpi_total">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-success border-4">
            <div class="card-body py-2">
                <div class="text-xs text-muted">FULL PAYMENT</div>
                <div class="h4 mb-0 fw-bold text-success" id="kpi_paid">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-warning border-4">
            <div class="card-body py-2">
                <div class="text-xs text-muted">PARTIAL PAYMENT (DP)</div>
                <div class="h4 mb-0 fw-bold text-warning" id="kpi_partial">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-danger border-4">
            <div class="card-body py-2">
                <div class="text-xs text-muted">UNPAID</div>
                <div class="h4 mb-0 fw-bold text-danger" id="kpi_unpaid">0</div>
            </div>
        </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="row g-2 align-items-center" id="filterForm">
            <div class="col-md-2">
                <input type="date" name="date" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <input type="text" name="q" class="form-control form-control-sm" placeholder="Search Agent, PNR, or Tour Code...">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-primary w-100" onclick="load()">Apply Filter</button>
            </div>
        </form>
    </div>
  </div>

  <!-- Umrah Section -->
  <h5 class="fw-bold text-primary mb-2"><i class="fas fa-kaaba me-2"></i>UMRAH MOVEMENT</h5>
  <div class="table-container mb-4">
    <div class="table-responsive">
      <table class="table table-bordered table-sm hover-scale align-middle mb-0">
        <thead class="bg-dark text-white">
          <tr class="text-xs">
            <th rowspan="2">NO</th>
            <th rowspan="2">Travel Agent</th>
            <th rowspan="2">Creation</th>
            <th rowspan="2">PNR</th>
            <th colspan="3" class="bg-orange">PAYMENT STATUS</th>
            <th rowspan="2">TOUR CODE</th>
            <th rowspan="2">CARR</th>
            <th rowspan="2">FLT NO</th>
            <th rowspan="2">SECTOR</th>
            <th colspan="4" class="bg-primary">SCHEDULE (OUT/IN)</th>
            <th rowspan="2">PTRN</th>
            <th rowspan="2">PAX</th>
            <th colspan="4" class="bg-success">PRICING (IDR)</th>
            <th colspan="3" class="bg-info">1ST DEPOSIT</th>
            <th colspan="3" class="bg-info">2ND DEPOSIT</th>
            <th colspan="2" class="bg-info">FULLPAY</th>
            <th rowspan="2">TIME LIMIT</th>
            <th rowspan="2">DONE</th>
            <th rowspan="2">TTL</th>
          </tr>
          <tr class="text-xs">
            <th class="bg-orange">DP1</th>
            <th class="bg-orange">DP2</th>
            <th class="bg-orange">FP</th>
            <th class="bg-primary">DEP1</th>
            <th class="bg-primary">DEP2</th>
            <th class="bg-primary">ARR3</th>
            <th class="bg-primary">ARR4</th>
            <th class="bg-success">APPR</th>
            <th class="bg-success">SELL</th>
            <th class="bg-success">NETT</th>
            <th class="bg-success">TOTAL</th>
            <th class="bg-info">AMT</th>
            <th class="bg-info">AIRL</th>
            <th class="bg-info">EEMW</th>
            <th class="bg-info">AMT</th>
            <th class="bg-info">AIRL</th>
            <th class="bg-info">EEMW</th>
            <th class="bg-info">AIRL</th>
            <th class="bg-info">EEMW</th>
          </tr>
        </thead>
        <tbody id="rows_umrah">
          <tr><td colspan="35" class="text-center py-5">Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Hajji Section -->
  <h5 class="fw-bold text-danger mb-2"><i class="fas fa-mosque me-2"></i>HAJJI MOVEMENT</h5>
  <div class="table-container">
    <div class="table-responsive">
      <table class="table table-bordered table-sm hover-scale align-middle mb-0">
        <thead class="bg-dark text-white">
          <tr class="text-xs">
            <th rowspan="2">NO</th>
            <th rowspan="2">Travel Agent</th>
            <th rowspan="2">Creation</th>
            <th rowspan="2">PNR</th>
            <th colspan="3" class="bg-orange">PAYMENT STATUS</th>
            <th rowspan="2">TOUR CODE</th>
            <th rowspan="2">CARR</th>
            <th rowspan="2">FLT NO</th>
            <th rowspan="2">SECTOR</th>
            <th colspan="4" class="bg-primary">SCHEDULE (OUT/IN)</th>
            <th rowspan="2">PTRN</th>
            <th rowspan="2">PAX</th>
            <th colspan="4" class="bg-success">PRICING (IDR)</th>
            <th colspan="3" class="bg-info">1ST DEPOSIT</th>
            <th colspan="3" class="bg-info">2ND DEPOSIT</th>
            <th colspan="2" class="bg-info">FULLPAY</th>
            <th rowspan="2">TIME LIMIT</th>
            <th rowspan="2">DONE</th>
            <th rowspan="2">TTL</th>
          </tr>
          <tr class="text-xs">
            <th class="bg-orange">DP1</th>
            <th class="bg-orange">DP2</th>
            <th class="bg-orange">FP</th>
            <th class="bg-primary">DEP1</th>
            <th class="bg-primary">DEP2</th>
            <th class="bg-primary">ARR3</th>
            <th class="bg-primary">ARR4</th>
            <th class="bg-success">APPR</th>
            <th class="bg-success">SELL</th>
            <th class="bg-success">NETT</th>
            <th class="bg-success">TOTAL</th>
            <th class="bg-info">AMT</th>
            <th class="bg-info">AIRL</th>
            <th class="bg-info">EEMW</th>
            <th class="bg-info">AMT</th>
            <th class="bg-info">AIRL</th>
            <th class="bg-info">EEMW</th>
            <th class="bg-info">AIRL</th>
            <th class="bg-info">EEMW</th>
          </tr>
        </thead>
        <tbody id="rows_hajji">
          <tr><td colspan="35" class="text-center py-5">Loading...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  function formatCurr(val) {
      if (!val) return '-';
      return new Intl.NumberFormat('id-ID').format(val);
  }

  function formatDate(val) {
      if (!val) return '-';
      const d = new Date(val);
      return d.toLocaleDateString('en-GB', { day:'2-digit', month:'short' });
  }

  function renderTable(containerId, data) {
    const rows = document.getElementById(containerId);
    rows.innerHTML = '';
    if (data.length === 0) {
        rows.innerHTML = `<tr><td colspan="35" class="text-center py-4 text-muted small">No data found.</td></tr>`;
        return;
    }

    data.forEach((m, index) => {
      const tr = document.createElement('tr');
      tr.className = 'text-center';
      
      const getStatusColor = (status) => {
          if (status === 'PAID') return 'bg-success text-white';
          if (status === 'UNPAID') return 'bg-danger text-white';
          if (status && status.includes('DONE')) return 'bg-success text-white';
          if (status && status !== 'NIL' && status !== 'XXXXXX') return 'bg-warning text-dark';
          return '';
      };

      tr.innerHTML = `
        <td class="bg-light">${index + 1}</td>
        <td class="text-start" style="min-width:120px">${m.agent_name || '-'}</td>
        <td>${formatDate(m.created_date)}</td>
        <td class="fw-bold text-primary">${m.pnr || '-'}</td>
        <td class="${getStatusColor(m.dp1_status)}">${m.dp1_status || '-'}</td>
        <td class="${getStatusColor(m.dp2_status)}">${m.dp2_status || '-'}</td>
        <td class="${getStatusColor(m.fp_status)}">${m.fp_status || '-'}</td>
        <td class="text-start small"><code>${m.tour_code || '-'}</code></td>
        <td>${m.carrier || '-'}</td>
        <td>${m.flight_no_out || '-'}</td>
        <td>${m.sector_out || '-'}</td>
        <td>${formatDate(m.dep_seg1_date)}</td>
        <td>${formatDate(m.dep_seg2_date)}</td>
        <td>${formatDate(m.arr_seg3_date)}</td>
        <td>${formatDate(m.arr_seg4_date)}</td>
        <td>${m.pattern_code || '-'}</td>
        <td class="fw-bold">${m.passenger_count || 0}</td>
        <td>${formatCurr(m.approved_fare)}</td>
        <td>${formatCurr(m.selling_fare)}</td>
        <td>${formatCurr(m.nett_selling)}</td>
        <td>${formatCurr(m.total_selling)}</td>
        <td>${formatCurr(m.deposit1_airlines_amount)}</td>
        <td>${formatDate(m.deposit1_airlines_date)}</td>
        <td>${formatDate(m.deposit1_eemw_date)}</td>
        <td>${formatCurr(m.deposit2_airlines_amount)}</td>
        <td>${formatDate(m.deposit2_airlines_date)}</td>
        <td>${formatDate(m.deposit2_eemw_date)}</td>
        <td>${formatDate(m.fullpay_airlines_date)}</td>
        <td>${formatDate(m.fullpay_eemw_date)}</td>
        <td class="bg-yellow-light">${formatDate(m.ticketing_deadline)}</td>
        <td>${m.ticketing_done == 1 ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-times-circle text-muted"></i>'}</td>
        <td>${m.ttl_days || '-'}</td>
      `;
      rows.appendChild(tr);
    });
  }

  async function load() {
    try {
        const formData = new FormData(document.getElementById('filterForm'));
        const params = new URLSearchParams(formData);
        const res = await fetch('../api/movement.php?' + params.toString());
        const json = await res.json();

        if (json.error) throw new Error(json.error);

        // Update KPIs
        document.getElementById('kpi_total').textContent = json.kpi.total;
        document.getElementById('kpi_paid').textContent = json.kpi.paid;
        document.getElementById('kpi_partial').textContent = json.kpi.partial;
        document.getElementById('kpi_unpaid').textContent = json.kpi.unpaid;

        renderTable('rows_umrah', json.data.umrah);
        renderTable('rows_hajji', json.data.hajji);

    } catch (e) {
        console.error(e);
        document.getElementById('rows_umrah').innerHTML = `<tr><td colspan="35" class="text-center py-4 text-danger">Error: ${e.message}</td></tr>`;
    }
  }

  load();
  if (<?= $tv ?>) setInterval(load, 30000);
</script>

</body>
</html>
