<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/BookingRequest.php';

check_auth('admin');

$requests = BookingRequest::readAll();
$filename = "booking_requests_" . date('Ymd') . ".xls";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
?>
<table border="1">
    <thead>
        <tr>
            <th rowspan="2" valign="middle">NO</th>
            <th rowspan="2" valign="middle">CORPORATE NAME</th>
            <th rowspan="2" valign="middle">Agent Name</th>
            <th rowspan="2" valign="middle">Skyagent ID</th>
            
            <th colspan="3" bgcolor="#FFC107">FLIGHT LEG 1</th>
            <th colspan="3" bgcolor="#FFC107">FLIGHT LEG 2</th>
            <th colspan="3" bgcolor="#FFC107">FLIGHT LEG 3</th>
            <th colspan="3" bgcolor="#FFC107">FLIGHT LEG 4</th>
            
            <th rowspan="2" valign="middle">Group Size</th>
            <th rowspan="2" valign="middle">TCP</th>
            <th rowspan="2" valign="middle">DURATION</th>
            <th rowspan="2" valign="middle">ADD 1</th>
            <th rowspan="2" valign="middle">TTL DAYS</th>
        </tr>
        <tr>
            <!-- Leg 1 -->
            <th bgcolor="#FFC107">DATE</th>
            <th bgcolor="#FFC107">FLIGHT</th>
            <th bgcolor="#FFC107">SECTOR</th>
            <!-- Leg 2 -->
            <th bgcolor="#FFC107">DATE</th>
            <th bgcolor="#FFC107">FLIGHT</th>
            <th bgcolor="#FFC107">SECTOR</th>
            <!-- Leg 3 -->
            <th bgcolor="#FFC107">DATE</th>
            <th bgcolor="#FFC107">FLIGHT</th>
            <th bgcolor="#FFC107">SECTOR</th>
            <!-- Leg 4 -->
            <th bgcolor="#FFC107">DATE</th>
            <th bgcolor="#FFC107">FLIGHT</th>
            <th bgcolor="#FFC107">SECTOR</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($requests as $index => $r): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo htmlspecialchars($r['corporate_name'] ?? '-'); ?></td>
                <td><?php echo htmlspecialchars($r['agent_name'] ?? 'Individual/FID'); ?></td>
                <td><?php echo htmlspecialchars($r['skyagent_id'] ?? '-'); ?></td>

                <?php 
                $legs = $r['legs'] ?? [];
                for ($i = 0; $i < 4; $i++): 
                    $leg = $legs[$i] ?? null;
                ?>
                    <td><?php echo $leg ? date('d/m/Y', strtotime($leg['flight_date'])) : ''; ?></td>
                    <td><?php echo $leg ? htmlspecialchars($leg['flight_no']) : ''; ?></td>
                    <td><?php echo $leg ? htmlspecialchars($leg['sector']) : ''; ?></td>
                <?php endfor; ?>

                <td><?php echo $r['group_size']; ?></td>
                <td><?php echo number_format($r['tcp'] ?? 0, 2); ?></td>
                <td><?php echo $r['duration_days']; ?></td>
                <td><?php echo $r['add1_days']; ?></td>
                <td><?php echo $r['ttl_days']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
