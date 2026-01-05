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
  <style>
    body { background-color: #f8f9fa; font-size: 0.9rem; }
    body.tv { font-size: 1.2rem; }
    .table-container { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .status-badge { font-size: 0.75rem; text-transform: uppercase; }
    .sticky-header th { position: sticky; top: 0; background: #212529; color: white; z-index: 10; }
    .table-responsive { max-height: 80vh; }
  </style>
</head>
<body class="<?= $tv ? 'tv' : '' ?>">

<div class="container-fluid py-3">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h2 class="mb-0 fw-bold">MOVEMENT DASHBOARD</h2>
      <span class="text-muted">Digitalisasi Ticketing Umroh & Haji</span>
    </div>
    <div class="d-none d-md-block">
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Dashboard</a>
        <button class="btn btn-primary btn-sm" onclick="load()">Refresh Data</button>
        <a href="?tv=<?= $tv ? 0 : 1 ?>" class="btn btn-dark btn-sm"><?= $tv ? 'Normal View' : 'Monitor Mode' ?></a>
    </div>
  </div>

  <!-- KPI Summary -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-primary border-4">
            <div class="card-body py-2">
                <div class="small text-muted">TOTAL GROUPS</div>
                <div class="h3 mb-0 fw-bold" id="kpi_total">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-success border-4">
            <div class="card-body py-2">
                <div class="small text-muted">FULL PAYMENT</div>
                <div class="h3 mb-0 fw-bold text-success" id="kpi_paid">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-warning border-4">
            <div class="card-body py-2">
                <div class="small text-muted">PARTIAL PAYMENT (DP)</div>
                <div class="h3 mb-0 fw-bold text-warning" id="kpi_partial">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-danger border-4">
            <div class="card-body py-2">
                <div class="small text-muted">UNPAID</div>
                <div class="h3 mb-0 fw-bold text-danger" id="kpi_unpaid">0</div>
            </div>
        </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="card border-0 shadow-sm mb-4">
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

  <!-- Main Table -->
  <div class="table-container">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 border-top sticky-header">
        <thead>
          <tr>
            <th>PNR</th>
            <th>TOUR CODE</th>
            <th>AGENT</th>
            <th>CARRIER</th>
            <th>SEGMENT (OUT/IN)</th>
            <th>PAX</th>
            <th>DP1</th>
            <th>DP2</th>
            <th>FP</th>
            <th>DONE</th>
            <th>STATUS</th>
          </tr>
        </thead>
        <tbody id="rows">
          <tr><td colspan="11" class="text-center py-5">Loading movement data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  async function load() {
    const rows = document.getElementById('rows');
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

        rows.innerHTML = '';
        if (json.data.length === 0) {
            rows.innerHTML = '<tr><td colspan="11" class="text-center py-4 text-muted">No movements found.</td></tr>';
            return;
        }

        json.data.forEach(m => {
          const tr = document.createElement('tr');
          
          const getStatusBadge = (status) => {
              if (status === 'PAID') return '<span class="badge bg-success status-badge">PAID</span>';
              if (status === 'UNPAID') return '<span class="badge bg-danger status-badge">UNPAID</span>';
              if (status) return `<span class="badge bg-warning text-dark status-badge">${status}</span>`;
              return '<span class="text-muted">-</span>';
          };

          tr.innerHTML = `
            <td><strong class="text-primary">${m.pnr || '-'}</strong></td>
            <td><code>${m.tour_code || '-'}</code></td>
            <td>${m.agent_name || '-'}</td>
            <td>${m.carrier || '-'}</td>
            <td>
                <div class="small">
                    <strong>OUT:</strong> ${m.flight_no_out || '-'} (${m.sector_out || '-'})<br>
                    <strong>IN:</strong> ${m.flight_no_in || '-'} (${m.sector_in || '-'})
                </div>
            </td>
            <td class="fw-bold">${m.passenger_count || 0}</td>
            <td>${getStatusBadge(m.dp1_status)}</td>
            <td>${getStatusBadge(m.dp2_status)}</td>
            <td>${getStatusBadge(m.fp_status)}</td>
            <td>${m.ticketing_done == 1 ? '✅' : '❌'}</td>
            <td><span class="badge bg-info text-dark">${m.live_status || 'SCHEDULED'}</span></td>
          `;
          rows.appendChild(tr);
        });

    } catch (e) {
        rows.innerHTML = `<tr><td colspan="11" class="text-center py-4 text-danger">Error: ${e.message}</td></tr>`;
    }
  }

  load();
  if (<?= $tv ?>) setInterval(load, 30000);
</script>

</body>
</html>