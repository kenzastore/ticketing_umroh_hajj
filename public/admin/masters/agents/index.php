<?php
require_once __DIR__ . '/../../../../includes/auth.php';
require_once __DIR__ . '/../../../../includes/db_connect.php';

check_auth('admin');

$title = "Manage Agents";
require_once __DIR__ . '/../../../shared/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Manage Agents</h1>
    <div class="d-flex gap-2">
        <select id="limitSelect" class="form-select form-select-sm" style="width: auto;" onchange="changePage(1)">
            <option value="10">10 lines</option>
            <option value="20">20 lines</option>
            <option value="50">50 lines</option>
        </select>
        <a href="create.php" class="btn btn-primary">Add New Agent</a>
    </div>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>NO</th>
                        <th>Name</th>
                        <th>Skyagent ID</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <tr><td colspan="6" class="text-center py-5">Loading...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="pagination-container" class="mt-3"></div>
    </div>
</div>

<script>
let currentPage = 1;

async function loadData() {
    const limit = document.getElementById('limitSelect').value;
    const tbody = document.getElementById('table-body');
    
    try {
        const response = await fetch(`../../../api/agents.php?page=${currentPage}&limit=${limit}&_=${new Date().getTime()}`);
        const json = await response.json();
        
        renderTable(json.data, json.pagination.offset);
        renderPagination(json.pagination);
    } catch (e) {
        console.error(e);
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>`;
    }
}

function renderTable(data, offset) {
    const tbody = document.getElementById('table-body');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">No agents found.</td></tr>`;
        return;
    }

    data.forEach((item, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${offset + i + 1}</td>
            <td class="fw-bold">${item.name}</td>
            <td>${item.skyagent_id || '-'}</td>
            <td>${item.phone || '-'}</td>
            <td>${item.email || '-'}</td>
            <td>
                <a href="edit.php?id=${item.id}" class="btn btn-sm btn-warning text-white"><i class="fas fa-edit"></i> Edit</a>
                <a href="delete.php?id=${item.id}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i> Delete</a>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function renderPagination(p) {
    const container = document.getElementById('pagination-container');
    if (p.totalPages <= 1) { container.innerHTML = ''; return; }

    let html = `<nav><ul class="pagination pagination-sm justify-content-center">
        <li class="page-item ${p.currentPage <= 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="event.preventDefault(); changePage(${p.currentPage - 1})">Prev</a></li>`;
    
    for (let i = 1; i <= p.totalPages; i++) {
        html += `<li class="page-item ${i === p.currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="event.preventDefault(); changePage(${i})">${i}</a></li>`;
    }

    html += `<li class="page-item ${p.currentPage >= p.totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="event.preventDefault(); changePage(${p.currentPage + 1})">Next</a></li>
    </ul></nav><div class="text-center text-muted small">Showing ${p.totalItems} total agents</div>`;
    container.innerHTML = html;
}

function changePage(page) {
    currentPage = page;
    loadData();
}

window.onload = loadData;
</script>

<?php require_once __DIR__ . '/../../../shared/footer.php'; ?>