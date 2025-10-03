<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../config/db.php';
require_login();
$user = current_user();
$pdo = Database::getConnection();

if ($user['role'] === 'customer') {
  $farmer_id = (int)($_GET['farmer_id'] ?? 0);
  $st = $pdo->prepare("SELECT sender, content, created_at FROM messages WHERE customer_id=? AND farmer_id=? ORDER BY created_at ASC LIMIT 200");
  $st->execute([$user['id'], $farmer_id]);
} else { // farmer view could pass ?customer_id=
  $customer_id = (int)($_GET['customer_id'] ?? 0);
  $st = $pdo->prepare("SELECT sender, content, created_at FROM messages WHERE farmer_id=? AND customer_id=? ORDER BY created_at ASC LIMIT 200");
  $st->execute([$user['id'], $customer_id]);
}
$rows = $st->fetchAll();
foreach($rows as $m){
  $align = $m['sender']===$user['role'] ? 'right' : 'left';
  echo '<div style="text-align:'.$align.';margin:.25rem 0"><span class="badge" style="display:inline-block">'. e($m['content']).'</span></div>';
}
