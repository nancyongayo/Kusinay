<?php
// View last 50 lines of PHP error log
$logFile = 'C:\xampp\php\logs\php_error_log';

if (!file_exists($logFile)) {
    die("Log file not found at: $logFile");
}

$lines = file($logFile);
$lastLines = array_slice($lines, -50);

echo "<h2>Last 50 lines of PHP Error Log</h2>";
echo "<pre style='background:#f5f5f5;padding:1rem;border:1px solid #ddd;overflow:auto;max-height:600px'>";
foreach ($lastLines as $line) {
    // Highlight setupAccount lines
    if (strpos($line, 'setupAccount') !== false) {
        echo "<strong style='color:#d63384'>" . htmlspecialchars($line) . "</strong>";
    } else {
        echo htmlspecialchars($line);
    }
}
echo "</pre>";

echo "<p><a href='view_logs.php'>Refresh</a></p>";
?>
