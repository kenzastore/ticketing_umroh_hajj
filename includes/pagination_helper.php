<?php
/**
 * Simple Pagination Helper
 */
function getPaginationData($totalItems, $itemsPerPage, $currentPage) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($totalPages, (int)$currentPage));
    $offset = ($currentPage - 1) * $itemsPerPage;

    return [
        'totalItems' => $totalItems,
        'itemsPerPage' => $itemsPerPage,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'offset' => $offset
    ];
}

function renderPagination($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) return '';

    $html = '<nav aria-label="Page navigation"><ul class="pagination pagination-sm justify-content-center">';
    
    // Previous
    $prevDisabled = ($currentPage <= 1) ? 'disabled' : '';
    $prevUrl = $baseUrl . (strpos($baseUrl, '?') !== false ? '&' : '?') . 'page=' . ($currentPage - 1);
    $html .= '<li class="page-item ' . $prevDisabled . '"><a class="page-link" href="' . $prevUrl . '">Previous</a></li>';

    // Pages
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($i == $currentPage) ? 'active' : '';
        $pageUrl = $baseUrl . (strpos($baseUrl, '?') !== false ? '&' : '?') . 'page=' . $i;
        $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $pageUrl . '">' . $i . '</a></li>';
    }

    // Next
    $nextDisabled = ($currentPage >= $totalPages) ? 'disabled' : '';
    $nextUrl = $baseUrl . (strpos($baseUrl, '?') !== false ? '&' : '?') . 'page=' . ($currentPage + 1);
    $html .= '<li class="page-item ' . $nextDisabled . '"><a class="page-link" href="' . $nextUrl . '">Next</a></li>';

    $html .= '</ul></nav>';
    return $html;
}
