<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../app/models/Agent.php';

check_auth(['admin', 'finance']);

$summary = Agent::getAgentSummary();
$filename = "agent_summary_" . date('Ymd') . ".xls";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
?>
<table border="1">
    <thead>
        <tr bgcolor="#212529" style="color: white;">
            <th>AGENT NAME</th>
            <th>TOTAL PNRs</th>
            <th>TOTAL PAX</th>
            <th>TOTAL REVENUE</th>
            <th>TOTAL PAID</th>
            <th>BALANCE</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($summary as $row): 
            $balance = $row['total_revenue'] - $row['total_paid'];
        ?>
            <tr>
                <td><?php echo htmlspecialchars($row['agent_name']); ?></td>
                <td align="center"><?php echo $row['total_pnrs']; ?></td>
                <td align="center"><?php echo $row['total_pax']; ?></td>
                <td align="right"><?php echo $row['total_revenue']; ?></td>
                <td align="right"><?php echo $row['total_paid']; ?></td>
                <td align="right"><?php echo $balance; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
