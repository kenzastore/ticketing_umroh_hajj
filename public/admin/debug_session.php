<?php
session_start();
echo "<pre>";
echo "<h2>Session Contents:</h2>";
print_r($_SESSION);
echo "<h2>User Role Check:</h2>";
if (isset($_SESSION['role_name'])) {
    echo "Current Role Name: " . $_SESSION['role_name'] . "\n";
    if ($_SESSION['role_name'] === 'admin') {
        echo "Role is 'admin'.\n";
    } else {
        echo "Role is NOT 'admin'.\n";
    }
    if (in_array($_SESSION['role_name'], ['admin', 'monitor'])) {
        echo "Role is authorized for ['admin', 'monitor'].\n";
    } else {
        echo "Role is NOT authorized for ['admin', 'monitor'].\n";
    }
} else {
    echo "Role name is NOT set in session.\n";
}
echo "</pre>";
?>
