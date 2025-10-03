<?php require_once __DIR__ . '/partials/header.php'; require_once __DIR__ . '/config/db.php';
$pdo = Database::getConnection();
$events = $pdo->query("SELECT id, title, event_date, location, description, image_path FROM events WHERE visible=1 ORDER BY event_date ASC LIMIT 20")->fetchAll();
?>
<h2>Upcoming Events</h2>
<div class="grid grid-4">
  <?php foreach($events as $ev): ?>
    <div class="card">
      <?php
        $img = !empty($ev['image_path']) ? (APP_URL . '/' . $ev['image_path']) : (APP_URL . '/assets/img/sample-fruit.jpg');
      ?>
      <img src="<?= e($img) ?>" alt="Event image for <?= e($ev['title']) ?>" />
      <div class="content">
        <strong><?= e($ev['title']) ?></strong>
        <div class="meta"><span><?= e(date('M d, Y', strtotime($ev['event_date']))) ?></span><span><?= e($ev['location']) ?></span></div>
        <p><?= e(substr((string)($ev['description'] ?? ''), 0, 100)) ?>...</p>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
