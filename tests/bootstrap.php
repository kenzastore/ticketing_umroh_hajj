<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/db_connect.php';

// Globalize $pdo for models that use it on load
$GLOBALS['pdo'] = $pdo;
