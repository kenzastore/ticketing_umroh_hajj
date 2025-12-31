<?php
require_once __DIR__ . '/../../includes/auth.php';
check_auth();

$tv = isset($_GET['tv']) ? 1 : 0;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Daily Group Movement</title>
  <!-- Using CDN as per project convention or fallback -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f4f6f9; }
    body.tv * { font-size: 20px; }
    body.tv .kpi .card-body { padding: 16px; }
    .status-badge { font-weight: 600; font-size: 0.9em; padding: 0.4em 0.8em; border-radius: 4px; color: white;}
    
    .row-delayed { background: #fff3cd !important; }
    .row-canceled { background: #f8d7da !important; }
    .row-airborne { background: #d1ecf1 !important; }
    .row-arrived { background: #d4edda !important; }
    
    .bg-scheduled { background-color: #6c757d; }
    .bg-delayed { background-color: #ffc107; color: black !important; }
    .bg-airborne { background-color: #17a2b8; }
    .bg-arrived { background-color: #28a745; }
    .bg-canceled { background-color: #dc3545; }
  </style>
</head>
<body class="<?= $tv ? 'tv' : '' ?>">
<div class="container-fluid p-3">

  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-0">Daily / Group Movement</h3>
      <small class="text-muted">Flight Monitoring (Track 3)</small>
    </div>
    <div>
      <a class="btn btn-secondary me-2" href="dashboard.php">Back to Dashboard</a>
      <a class="btn btn-outline-secondary me-2" href="?<?= http_build_query(array_merge($_GET, ['tv'=>$tv?0:1])) ?>">
        <?= $tv ? 'Exit TV' : 'TV Mode' ?>
      </a>
      <button class="btn btn-primary" onclick="location.reload()">Refresh</button>
    </div>
  </div>

  <!-- KPI -->
  <div class="row kpi g-2 mb-3">
    <div class="col-6 col-md-3"><div class="card shadow-sm"><div class="card-body text-center">Total<br><b id="kpi_total" class="fs-4">0</b></div></div></div>
    <div class="col-6 col-md-3"><div class="card shadow-sm"><div class="card-body text-center text-success">On-time<br><b id="kpi_ontime" class="fs-4">0</b></div></div></div>
    <div class="col-6 col-md-3"><div class="card shadow-sm"><div class="card-body text-center text-warning">Delayed<br><b id="kpi_delayed" class="fs-4">0</b></div></div></div>
    <div class="col-6 col-md-3"><div class="card shadow-sm"><div class="card-body text-center text-danger">Canceled<br><b id="kpi_canceled" class="fs-4">0</b></div></div></div>
  </div>

  <!-- Filters -->
  <form class="row g-2 mb-3" method="get">
    <input type="hidden" name="tv" value="<?= $tv ?>">
    <div class="col-6 col-md-2"><input class="form-control" type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? date('Y-m-d')) ?>"></div>
    <div class="col-6 col-md-2"><input class="form-control" name="airline" placeholder="Airline (SQ)" value="<?= htmlspecialchars($_GET['airline'] ?? '') ?>"></div>
    <div class="col-6 col-md-2"><input class="form-control" name="origin" placeholder="Origin (SIN)" value="<?= htmlspecialchars($_GET['origin'] ?? '') ?>"></div>
    <div class="col-6 col-md-2"><input class="form-control" name="dest" placeholder="Dest (JED)" value="<?= htmlspecialchars($_GET['dest'] ?? '') ?>"></div>
    <div class="col-6 col-md-2">
      <select class="form-select" name="status">
        <option value="">All Status</option>
        <?php foreach (['SCHEDULED','DELAYED','AIRBORNE','ARRIVED','CANCELED'] as $s): ?>
          <option value="<?= $s ?>" <?= (($_GET['status'] ?? '')===$s)?'selected':'' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-6 col-md-2"><input class="form-control" name="q" placeholder="Search (SQ328)" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"></div>
    <div class="col-12 col-md-2"><button class="btn btn-success w-100">Apply</button></div>
  </form>

  <!-- Table -->
  <div class="card shadow">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Ref</th>
              <th>Route</th>
              <th>Flight</th>
              <th>SCH Dep</th><th>EST Dep</th><th>ACT Dep</th>
              <th>SCH Arr</th><th>EST Arr</th><th>ACT Arr</th>
              <th>Status</th>
              <th>Delay</th>
              <th>Gate</th>
              <th>Updated</th>
            </tr>
          </thead>
          <tbody id="rows">
            <tr><td colspan="13" class="text-center p-3">Loading flights...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<script>
  async function load() {
    try {
        const params = new URLSearchParams(window.location.search);
        const res = await fetch('/api/movement.php?' + params.toString());
        const json = await res.json();

        if (json.error) {
             document.getElementById('rows').innerHTML = `<tr><td colspan="13" class="text-center text-danger">Error: ${json.error}</td></tr>`;
             return;
        }

        document.getElementById('kpi_total').textContent = json.kpi.total;
        document.getElementById('kpi_ontime').textContent = json.kpi.ontime;
        document.getElementById('kpi_delayed').textContent = json.kpi.delayed;
        document.getElementById('kpi_canceled').textContent = json.kpi.canceled;

        const tbody = document.getElementById('rows');
        tbody.innerHTML = '';
        
        if (json.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="13" class="text-center p-3">No flights found for this date. Run the cron script to seed data!</td></tr>';
            return;
        }

        json.data.forEach(r => {
          const tr = document.createElement('tr');
          tr.className =
            r.status === 'DELAYED' ? 'row-delayed' :
            r.status === 'CANCELED' ? 'row-canceled' :
            r.status === 'AIRBORNE' ? 'row-airborne' :
            r.status === 'ARRIVED' ? 'row-arrived' : '';
            
          let badgeClass = 'bg-secondary';
          if (r.status === 'SCHEDULED') badgeClass = 'bg-scheduled';
          else if (r.status === 'DELAYED') badgeClass = 'bg-delayed';
          else if (r.status === 'AIRBORNE') badgeClass = 'bg-airborne';
          else if (r.status === 'ARRIVED') badgeClass = 'bg-arrived';
          else if (r.status === 'CANCELED') badgeClass = 'bg-canceled';

          tr.innerHTML = `
            <td><small>${r.ref || '-'}</small></td>
            <td>${r.origin} &rarr; ${r.dest}</td>
            <td><b>${r.flight_ident}</b></td>
            <td>${r.sched_dep||'-'}</td><td class="text-muted">${r.est_dep||'-'}</td><td>${r.act_dep||'-'}</td>
            <td>${r.sched_arr||'-'}</td><td class="text-muted">${r.est_arr||'-'}</td><td>${r.act_arr||'-'}</td>
            <td><span class="badge ${badgeClass} status-badge">${r.status}</span></td>
            <td class="text-danger fw-bold">${r.delay_minutes ?? ''}</td>
            <td>${(r.dep_gate||'-') + ' / ' + (r.arr_gate||'-')}</td>
            <td><small class="text-muted">${r.updated_at||''}</small></td>
          `;
          tbody.appendChild(tr);
        });
    } catch (e) {
        console.error(e);
        document.getElementById('rows').innerHTML = `<tr><td colspan="13" class="text-center text-danger">Failed to load data.</td></tr>`;
    }
  }

  load();

  // TV mode auto-refresh
  const isTv = new URLSearchParams(location.search).get('tv') === '1';
  if (isTv) setInterval(load, 60000);
</script>
</body>
</html>
