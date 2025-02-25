<?php
require_once('../../includes/session_manager.php');
require_once('../../includes/db_connect.php');
require_once('../verify_admin.php');

header('Content-Type: application/json');

try {
    // Get total active users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE status = 'active'");
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get monthly revenue
    $stmt = $pdo->query("
        SELECT COALESCE(SUM(amount), 0) as total 
        FROM transactions 
        WHERE MONTH(transaction_date) = MONTH(CURRENT_DATE()) 
        AND YEAR(transaction_date) = YEAR(CURRENT_DATE())
    ");
    $monthlyRevenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get active tools count
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tools WHERE status = 'active'");
    $activeTools = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get recent activity
    $stmt = $pdo->query("
        SELECT 
            activity_time as time,
            action,
            username as user,
            details
        FROM activity_logs
        ORDER BY activity_time DESC
        LIMIT 10
    ");
    $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'totalUsers' => $totalUsers,
        'monthlyRevenue' => number_format($monthlyRevenue, 2),
        'activeTools' => $activeTools,
        'recentActivity' => $recentActivity
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>
