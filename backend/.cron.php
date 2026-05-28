<?php
// Run this script every hour using cron:
// 0 * * * * php /path/to/backend/cron.php

require_once __DIR__ . '/controllers/controller.reservation.php';

$controller = new ReservationController();

// Auto-cancel expired reservations
$controller->autoCancelExpired();

// Send expiration notifications
$controller->notifyExpiringReservations();

echo "Cron job completed at " . date('Y-m-d H:i:s') . "\n";
