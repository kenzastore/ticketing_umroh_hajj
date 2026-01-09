<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../includes/Parsedown.php';

// RBAC Protection
check_auth('admin');

$page = $_GET['page'] ?? 'index';
$page = basename($page); // Security: prevent directory traversal
$file_path = __DIR__ . "/../../docs/manual/{$page}.md";

if (!file_exists($file_path)) {
    $file_path = __DIR__ . "/../../docs/manual/index.md";
    $page = 'index';
}

$markdown_content = file_get_contents($file_path);
$parsedown = new Parsedown();
$html_content = $parsedown->text($markdown_content);

// Automatic Link Rewriting: Convert .md links to manual.php?page=...
$html_content = preg_replace('/href="(?:\.\/)?([^"]+)\.md"/', 'href="manual.php?page=$1"', $html_content);

$title = "User Manual - " . ucfirst(str_replace('_', ' ', $page));

require_once __DIR__ . '/../shared/header.php';
?>

<div class="row">
    <div class="col-md-3">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h6 class="m-0 fw-bold">Table of Contents</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="manual-toc">
                    <!-- ToC will be generated in Phase 3 -->
                    <a href="manual.php?page=index" class="list-group-item list-group-item-action <?= $page == 'index' ? 'active' : '' ?>">Introduction</a>
                    <a href="manual.php?page=stage1_demand_intake" class="list-group-item list-group-item-action <?= $page == 'stage1_demand_intake' ? 'active' : '' ?>">Stage 1: Demand Intake</a>
                    <a href="manual.php?page=stage2_operational_execution" class="list-group-item list-group-item-action <?= $page == 'stage2_operational_execution' ? 'active' : '' ?>">Stage 2: Operational Execution</a>
                    <a href="manual.php?page=stage3_financial_settlement" class="list-group-item list-group-item-action <?= $page == 'stage3_financial_settlement' ? 'active' : '' ?>">Stage 3: Financial Settlement</a>
                    <a href="manual.php?page=stage4_management_audit" class="list-group-item list-group-item-action <?= $page == 'stage4_management_audit' ? 'active' : '' ?>">Stage 4: Management & Audit</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card shadow-sm">
            <div class="card-body" id="manual-content">
                <div class="markdown-body">
                    <?= $html_content ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
