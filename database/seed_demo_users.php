<?php
require_once __DIR__ . '/../includes/db_connect.php';

echo "Seeding demo users...\n";

try {
    $pdo->beginTransaction();

    // 1. Ensure roles exist
    $roles = [
        ['name' => 'admin', 'description' => 'Full access to all modules'],
        ['name' => 'operational', 'description' => 'Ticketing and Movement management'],
        ['name' => 'finance', 'description' => 'Access to invoicing and payment modules'],
        ['name' => 'monitor', 'description' => 'View-only access to dashboards']
    ];

    $stmtRole = $pdo->prepare("INSERT IGNORE INTO roles (name, description) VALUES (?, ?)");
    foreach ($roles as $role) {
        $stmtRole->execute([$role['name'], $role['description']]);
    }

    // 2. Fetch role IDs
    $roleIds = [];
    $stmtFetchRoles = $pdo->query("SELECT id, name FROM roles");
    while ($row = $stmtFetchRoles->fetch(PDO::FETCH_ASSOC)) {
        $roleIds[$row['name']] = $row['id'];
    }

    // 3. Seed Users
    $users = [
        [
            'username' => 'admin_demo',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role_id' => $roleIds['admin'],
            'full_name' => 'Demo Administrator'
        ],
        [
            'username' => 'op_demo',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role_id' => $roleIds['operational'],
            'full_name' => 'Demo Operational Staff'
        ],
        [
            'username' => 'finance_demo',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role_id' => $roleIds['finance'],
            'full_name' => 'Demo Finance Officer'
        ],
        [
            'username' => 'monitor_demo',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'role_id' => $roleIds['monitor'],
            'full_name' => 'Demo Monitor/Viewer'
        ]
    ];

    $stmtUser = $pdo->prepare("INSERT IGNORE INTO users (username, password, role_id, full_name) VALUES (?, ?, ?, ?)");
    foreach ($users as $user) {
        $stmtUser->execute([
            $user['username'],
            $user['password'],
            $user['role_id'],
            $user['full_name']
        ]);
        echo "Created/Updated user: " . $user['username'] . "\n";
    }

    $pdo->commit();
    echo "Demo users seeded successfully.\n";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error seeding demo users: " . $e->getMessage() . "\n";
    exit(1);
}
