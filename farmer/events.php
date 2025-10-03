<?php
$pageTitle = 'Farmer Events';
require_once __DIR__ . '/../partials/header.php';
require_role('farmer');

require_once __DIR__ . '/../config/db.php';
$pdo = Database::getConnection();

$farmerId = current_user()['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title'] ?? '');
  $date = $_POST['event_date'] ?? '';
  $location = trim($_POST['location'] ?? '');
  $description = trim($_POST['description'] ?? '');

  $imagePath = null;
  if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
    finfo_close($finfo);
    if (isset($allowed[$mime])) {
      $ext = $allowed[$mime];
      $uploadDir = __DIR__ . '/../uploads/events';
      if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
      $fname = 'ev_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
      $dest = $uploadDir . '/' . $fname;
      if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
        $imagePath = 'uploads/events/' . $fname; // relative path stored
      }
    }
  }

  if ($title && $date && $location) {
    $ins = $pdo->prepare("INSERT INTO events (title, description, event_date, location, farmer_id, image_path, visible) VALUES (?,?,?,?,?,?,1)");
    $ins->execute([$title, $description, $date, $location, $farmerId, $imagePath]);
    flash('success', 'Event created.');
    header("Location: " . APP_URL . "/farmer/events.php");
    exit;
  } else {
    flash('error', 'Please fill all required fields.');
  }
}

$st = $pdo->prepare("SELECT id, title, event_date, location, visible, image_path FROM events WHERE farmer_id=? ORDER BY event_date DESC");
$st->execute([$farmerId]);
$events = $st->fetchAll();
?>
<h2>My Events</h2>

<form method="post" class="card" style="margin-bottom:1rem" enctype="multipart/form-data">
  <div class="content">
    <div class="grid" style="grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap: 12px;">
      <div>
        <label class="label">Title</label>
        <input class="input" name="title" required />
      </div>
      <div>
        <label class="label">Date</label>
        <input class="input" type="date" name="event_date" required />
      </div>
      <div>
        <label class="label">Location</label>
        <input class="input" name="location" required />
      </div>
      <div>
        <label class="label">Event Image (optional)</label>
        <input class="input" type="file" name="image" accept="image/*" id="eventImage" />
      </div>
    </div>
    <div style="margin-top:.75rem">
      <label class="label">Description</label>
      <textarea class="input" name="description" rows="3"></textarea>
    </div>
    <div style="margin-top:.75rem">
      <img id="previewImg" alt="Event image preview" style="max-width:240px; display:none; border-radius:8px; border:1px solid #eee;" />
    </div>
    <div style="margin-top:.75rem">
      <button class="btn btn-primary" type="submit">Create Event</button>
    </div>
  </div>
</form>
<script>
document.getElementById('eventImage')?.addEventListener('change', (e) => {
  const file = e.target.files?.[0];
  const img = document.getElementById('previewImg');
  if (!file) { img.style.display='none'; img.src=''; return; }
  const url = URL.createObjectURL(file);
  img.src = url;
  img.style.display = 'block';
});
</script>

<?php if (!$events): ?>
  <p>No events yet.</p>
<?php else: ?>
  <div class="grid">
    <?php foreach ($events as $ev): ?>
      <div class="card">
        <div class="content">
          <strong><?= e($ev['title']) ?></strong>
          <p><?= e($ev['location']) ?> · <?= e($ev['event_date']) ?></p>
          <?php if (!empty($ev['image_path'])): ?>
            <img src="<?= e(APP_URL . '/' . $ev['image_path']) ?>" alt="Event image for <?= e($ev['title']) ?>" style="max-width:100%; border-radius:8px; margin:.5rem 0;" />
          <?php endif; ?>
          <span class="badge"><?= $ev['visible'] ? 'Visible' : 'Hidden' ?></span>
          <form method="post" action="<?= e(APP_URL) ?>/actions/event_delete.php" onsubmit="return confirm('Delete this event? This cannot be undone.');" style="margin-top:.5rem">
            <?= csrf_input() ?>
            <input type="hidden" name="id" value="<?= (int)$ev['id'] ?>" />
            <button class="btn btn-danger" type="submit">Delete Event</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
