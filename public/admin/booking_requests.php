<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';

check_auth('admin');

$title = "Admin Dashboard - Booking Requests";
require_once __DIR__ . '/../shared/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Booking Requests</h1>
    <div>
        <a href="export_requests.php" class="btn btn-success me-2"><i class="fas fa-file-excel"></i> Export to Excel</a>
        <a href="new_request.php" class="btn btn-primary"><i class="fas fa-plus"></i> Create New Request</a>
    </div>
</div>

<!-- Date Filter Form -->
<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="row g-2 align-items-center" id="filterForm" onsubmit="event.preventDefault(); changePage(1);">
            <input type="hidden" name="page" id="currentPage" value="1">
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">Start</span>
                    <input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-01'); ?>">
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">End</span>
                    <input type="date" name="end_date" class="form-control" value="<?php echo date('Y-m-t'); ?>">
                </div>
            </div>
            <div class="col-md-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">Show</span>
                    <select name="limit" class="form-select" onchange="changePage(1)">
                        <option value="10">10 lines</option>
                        <option value="20">20 lines</option>
                        <option value="50">50 lines</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-search me-1"></i>Filter</button>
                <button type="button" class="btn btn-sm btn-outline-secondary w-100" onclick="resetFilters()"><i class="fas fa-undo me-1"></i>Reset</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div id="top-scrollbar-container" style="overflow-x: auto; overflow-y: hidden; height: 15px; margin-bottom: 5px; display: none;">
            <div id="top-scrollbar-spacer" style="height: 1px;"></div>
        </div>

        <div class="table-responsive" id="table-responsive">
            <table id="requests-table" class="table table-bordered table-hover text-nowrap align-middle" style="font-size: 0.9rem;">
                <thead class="table-dark">
                    <tr>
                        <th rowspan="2">NO</th>
                        <th rowspan="2">CORPORATE NAME</th>
                        <th rowspan="2">Agent Name</th>
                        <th rowspan="2">Skyagent ID</th>
                        <th colspan="3" class="text-center bg-warning text-dark">LEG 1</th>
                        <th colspan="3" class="text-center bg-warning text-dark">LEG 2</th>
                        <th colspan="3" class="text-center bg-warning text-dark">LEG 3</th>
                        <th colspan="3" class="text-center bg-warning text-dark">LEG 4</th>
                        <th rowspan="2">PAX</th>
                        <th rowspan="2">TCP</th>
                        <th rowspan="2">DUR</th>
                        <th rowspan="2">ADD 1</th>
                        <th rowspan="2">TTL</th>
                        <th rowspan="2">Actions</th>
                    </tr>
                    <tr class="bg-warning text-dark">
                        <th>DATE</th><th>FLT</th><th>SECTOR</th>
                        <th>DATE</th><th>FLT</th><th>SECTOR</th>
                        <th>DATE</th><th>FLT</th><th>SECTOR</th>
                        <th>DATE</th><th>FLT</th><th>SECTOR</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <tr><td colspan="21" class="text-center py-5">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        
        <div id="pagination-container" class="mt-3"></div>
    </div>
</div>

<script>
async function loadData() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    params.append('_', new Date().getTime());

    try {
        const response = await fetch('../api/booking_requests.php?' + params.toString());
        const json = await response.json();
        renderTable(json.data, json.pagination.offset);
        renderPagination(json.pagination);
        syncScrolls();
    } catch (e) {
        console.error(e);
        document.getElementById('table-body').innerHTML = `<tr><td colspan="21" class="text-center text-danger">Error loading data</td></tr>`;
    }
}

function renderTable(data, offset) {
    const tbody = document.getElementById('table-body');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="21" class="text-center py-4 text-muted">No records found.</td></tr>`;
        return;
    }

    data.forEach((r, i) => {
        const tr = document.createElement('tr');
        if (r.is_converted == 1) tr.className = 'table-success opacity-75';
        
        let legsHtml = '';
        for (let j = 0; j < 4; j++) {
            const leg = r.legs && r.legs[j] ? r.legs[j] : null;
            legsHtml += `<td>${leg ? formatDate(leg.flight_date) : ''}</td>`;
            legsHtml += `<td>${leg ? leg.flight_no : ''}</td>`;
            legsHtml += `<td>${leg ? leg.sector : ''}</td>`;
        }

        tr.innerHTML = `
            <td>${offset + i + 1} ${r.is_converted == 1 ? '<br><span class="badge bg-secondary" style="font-size:0.6rem">CONVERTED</span>' : ''}</td>
            <td>${r.corporate_name || '-'}</td>
            <td>${r.agent_name || 'Individual'}</td>
            <td>${r.skyagent_id || '-'}</td>
            ${legsHtml}
            <td>${r.group_size}</td>
            <td>${Number(r.tcp).toLocaleString()}</td>
            <td>${r.duration_days}</td>
            <td>${r.add1_days}</td>
            <td>${r.ttl_days}</td>
            <td>
                <div class="btn-group btn-group-sm">
                    <a href="request_detail.php?id=${r.id}" class="btn btn-info text-white"><i class="fas fa-eye"></i></a>
                    ${r.is_converted == 0 
                        ? `<a href="mailto:?subject=Booking Request ${r.request_no}" class="btn btn-warning"><i class="fas fa-envelope"></i></a>`
                        : `<button class="btn btn-secondary" disabled><i class="fas fa-check"></i></button>`
                    }
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function renderPagination(p) {
    const container = document.getElementById('pagination-container');
    if (p.totalPages <= 1) { container.innerHTML = ''; return; }

    let html = `<nav><ul class="pagination pagination-sm justify-content-center">
        <li class="page-item ${p.currentPage <= 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="changePage(${p.currentPage - 1})">Prev</a></li>`;
    
    for (let i = 1; i <= p.totalPages; i++) {
        html += `<li class="page-item ${i === p.currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="changePage(${i})">${i}</a></li>`;
    }

    html += `<li class="page-item ${p.currentPage >= p.totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="changePage(${p.currentPage + 1})">Next</a></li>
    </ul></nav><div class="text-center text-muted small">Showing ${p.totalItems} total requests</div>`;
    container.innerHTML = html;
}

function changePage(page) {
    document.getElementById('currentPage').value = page;
    loadData();
}

function resetFilters() {
    document.getElementById('filterForm').reset();
    changePage(1);
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleDateString('en-GB');
}

// Double Scroll Sync
function syncScrolls() {
    const topContainer = document.getElementById('top-scrollbar-container');
    const topSpacer = document.getElementById('top-scrollbar-spacer');
    const tableResp = document.getElementById('table-responsive');
    const table = document.getElementById('requests-table');

    if (table.offsetWidth > tableResp.offsetWidth) {
        topContainer.style.display = 'block';
        topSpacer.style.width = table.offsetWidth + 'px';
    } else {
        topContainer.style.display = 'none';
    }

    topContainer.onscroll = () => tableResp.scrollLeft = topContainer.scrollLeft;
    tableResp.onscroll = () => topContainer.scrollLeft = tableResp.scrollLeft;
}

window.onload = loadData;
window.onresize = syncScrolls;
</script>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>