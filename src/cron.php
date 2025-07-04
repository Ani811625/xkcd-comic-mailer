
<?php
require_once __DIR__ . '/functions.php';

// Configure mail function to use Mailpit
ini_set("SMTP", "localhost");
ini_set("smtp_port", "1025");


// Send XKCD comic to all registered users
echo "[CRON] Sending XKCD comics to subscribers...\n";
sendXKCDUpdatesToSubscribers();
echo "[CRON] Done.\n";
?>
