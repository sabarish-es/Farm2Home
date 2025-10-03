<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../config/db.php';

require_role('farmer');
verify_csrf();

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
  flash('error', 'Invalid event.');
  header('Location: ../farmer/events.php');
  exit;
}

$pdo = Database::getConnection();
$farmerId = current_user()['id'];

$pdo->beginTransaction();
try {
  // Fetch image to remove from disk if present
  $sel = $pdo->prepare("SELECT image_path FROM events WHERE id = ? AND farmer_id = ?");
  $sel->execute([$id, $farmerId]);
  $row = $sel->fetch();

  if (!$row) {
    $pdo->rollBack();
    flash('error', 'Event not found or not owned by you.');
    header('Location: ../farmer/events.php');
    exit;
  }

  if (!empty($row['image_path'])) {
    $full = __DIR__ . '/../' . $row['image_path'];
    if (is_file($full)) { @unlink($full); }
  }

  $del = $pdo->prepare("DELETE FROM events WHERE id = ? AND farmer_id = ?");
  $del->execute([$id, $farmerId]);

  $pdo->commit();
  flash('success', 'Event deleted.');
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  flash('error', 'Failed to delete event.');
}
header('Location: ../farmer/events.php');
