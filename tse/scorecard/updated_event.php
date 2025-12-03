<?php
// updated_event.php
require_once('../connections/pdoconnect.php');

$db = new DatabaseConnect();

// Check if event_id is provided
if (!isset($_POST['event_id'])) {
    echo "❌ No event ID received.";
    exit;
}

$event_id = $_POST['event_id'];

// 1️⃣ Check current status of the event
$query_check = "SELECT events_status FROM events WHERE events_id = ?";
$db->query($query_check);
$db->bind(1, $event_id);
$event = $db->rowsingle();

if (!$event) {
    echo "⚠️ Event not found.";
    exit;
}

// 2️⃣ If already ended, stop
if ($event['events_status'] === 'Ended') {
    echo "ℹ️ This event is already ended.";
    exit;
}

// 3️⃣ Update the event status to 'Ended' and set updated_date
$query_update = "UPDATE events 
                 SET events_status = 'Ended',
                     updated_date = NOW()
                 WHERE events_id = ?";

$db->query($query_update);
$db->bind(1, $event_id);

if ($db->execute()) {
    echo "✅ Event successfully ended.";
} else {
    echo "❌ Failed to end the event.";
}

$db->close();
?>
