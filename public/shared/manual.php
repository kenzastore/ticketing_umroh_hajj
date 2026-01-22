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

// Get all manual pages for ToC
$manual_files = glob(__DIR__ . '/../../docs/manual/*.md');
$toc_items = [];
foreach ($manual_files as $file) {
    $slug = basename($file, '.md');
    if ($slug === 'template') continue; // Skip template
    
    // Human-readable title
    $title_label = ucfirst(str_replace('_', ' ', $slug));
    if ($slug === 'index') $title_label = "Introduction";
    
    $toc_items[] = [
        'slug' => $slug,
        'label' => $title_label
    ];
}

// Sort: index first, then by filename
usort($toc_items, function($a, $b) {
    if ($a['slug'] === 'index') return -1;
    if ($b['slug'] === 'index') return 1;
    return strcmp($a['slug'], $b['slug']);
});

$title = "User Manual - " . ucfirst(str_replace('_', ' ', $page));

require_once __DIR__ . '/../shared/header.php';

// Flash Messages
$successMsg = $_SESSION['success_msg'] ?? null;
$errorMsg = $_SESSION['error_msg'] ?? null;
unset($_SESSION['success_msg'], $_SESSION['error_msg']);
?>

<style>
    .markdown-body h1, .markdown-body h2, .markdown-body h3 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
        border-bottom: 1px solid #eee;
        padding-bottom: 0.5rem;
    }
    .markdown-body p {
        margin-bottom: 1rem;
    }
    .markdown-body ul, .markdown-body ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
</style>

<div class="row">
    <?php if ($successMsg): ?>
        <div class="col-12">
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($successMsg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
        <div class="col-12">
            <div class="alert alert-danger shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> <?= nl2br(htmlspecialchars($errorMsg)) ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-md-3">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h6 class="m-0 fw-bold"><i class="fas fa-book me-2"></i>Table of Contents</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" id="manual-toc">
                    <?php foreach ($toc_items as $item): ?>
                        <a href="manual.php?page=<?= $item['slug'] ?>" class="list-group-item list-group-item-action <?= $page == $item['slug'] ? 'active' : '' ?>">
                            <?= htmlspecialchars($item['label']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if ($_SESSION['role_name'] == 'admin'): ?>
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="m-0 fw-bold"><i class="fas fa-tools me-2"></i>UAT Management</h6>
                </div>
                <div class="card-body text-center py-3">
                    <p class="small text-muted mb-3">Wipe all data and regenerate 100 synchronized test records.</p>
                    <form action="/admin/generate_uat_data.php" method="POST" onsubmit="return confirm('WARNING: This will delete ALL existing operational data. Are you sure?');">
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="fas fa-sync-alt me-1"></i> Reset UAT Data
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
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
