<?php require_once __DIR__ . '/../partials/header.php'; require_role('farmer'); require_once __DIR__ . '/../config/db.php';
$pdo = Database::getConnection();
$fid = current_user()['id'];
$notifs = $pdo->prepare("SELECT id, type, payload, created_at FROM notifications WHERE farmer_id=? AND is_read=0 ORDER BY created_at DESC LIMIT 10");
$notifs->execute([$fid]);
$notifs = $notifs->fetchAll();

$contactsSt = $pdo->prepare("
  SELECT u.id, u.name, MAX(m.created_at) AS last_at
  FROM users u
  INNER JOIN messages m ON m.customer_id = u.id
  WHERE m.farmer_id = ?
  GROUP BY u.id, u.name
  ORDER BY last_at DESC
");
$contactsSt->execute([$fid]);
$contacts = $contactsSt->fetchAll();
$customerId = (int)($_GET['customer_id'] ?? ($contacts[0]['id'] ?? 0));
?>
<h2>Farmer Dashboard</h2>
<div class="grid grid-4">
  <a class="card" href="<?= e(APP_URL) ?>/farmer/products.php"><div class="content"><strong>Manage Products</strong></div></a>
  <a class="card" href="<?= e(APP_URL) ?>/farmer/orders.php"><div class="content"><strong>Orders</strong></div></a>
  <a class="card" href="<?= e(APP_URL) ?>/farmer/events.php"><div class="content"><strong>Events</strong></div></a>
  <a class="card" href="<?= e(APP_URL) ?>/farmer/reports.php"><div class="content"><strong>Sales Reports</strong></div></a>
</div>
<div class="hero-card" style="margin-top:1rem">
  <h3>New Notifications</h3>
  <?php if (!$notifs): ?>
    <p>No new notifications.</p>
  <?php else: ?>
    <ul>
      <?php foreach($notifs as $n): $payload = json_decode($n['payload'], true); ?>
        <li>New order #<?= (int)$payload['order_id'] ?> amount <?= money((float)$payload['amount']) ?> (<?= e($n['created_at']) ?>)</li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

<div class="hero-card" style="margin-top:1rem">
  <h3>Messages</h3>
  <?php if (!$contacts): ?>
    <p>No conversations yet. Customers will appear here once they message you.</p>
  <?php else: ?>
    <form method="get" class="form" style="max-width:420px">
      <label class="label">Choose Customer</label>
      <select class="select" name="customer_id" onchange="this.form.submit()">
        <?php foreach($contacts as $c): ?>
          <option value="<?= (int)$c['id'] ?>" <?= $c['id']===$customerId?'selected':'' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </form>
    <div id="f-chat" style="margin-top:.75rem">
      <div id="f-messages" style="height:300px;overflow:auto;border:1px solid #eee;padding:.5rem;border-radius:8px;background:#fff"></div>
      <form id="f-sendForm" class="search" style="margin-top:.5rem" onsubmit="sendFarmerMessage(event)">
        <input id="f-msg" type="text" placeholder="Type your message..." required />
        <input type="hidden" id="f-customer" value="<?= (int)$customerId ?>" />
        <button class="btn btn-primary" type="submit">Send</button>
      </form>
    </div>
    <script>
      const fMessagesBox = document.getElementById('f-messages');
      const fCustomer = document.getElementById('f-customer')?.value;
      async function fetchFarmerMessages(){
        if(!fCustomer) return;
        const res = await fetch('<?= e(APP_URL) ?>/actions/chat_fetch.php?customer_id=' + fCustomer);
        fMessagesBox.innerHTML = await res.text();
        fMessagesBox.scrollTop = fMessagesBox.scrollHeight;
      }
      const fPoller = (function(){ const p = setInterval(fetchFarmerMessages, 2000); return { stop:()=>clearInterval(p) } })();
      fetchFarmerMessages();

      async function sendFarmerMessage(e){
        e.preventDefault();
        const msg = document.getElementById('f-msg');
        if (!msg.value.trim()) return;
        await fetch('<?= e(APP_URL) ?>/actions/chat_send.php', {
          method:'POST',
          headers:{'Content-Type':'application/x-www-form-urlencoded'},
          body: new URLSearchParams({ customer_id: fCustomer, message: msg.value, csrf: '<?= e(csrf_token()) ?>' })
        });
        msg.value = '';
        fetchFarmerMessages();
      }
    </script>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
