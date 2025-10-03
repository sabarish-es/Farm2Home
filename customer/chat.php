<?php require_once __DIR__ . '/../partials/header.php'; require_role('customer'); require_once __DIR__ . '/../config/db.php';
$pdo = Database::getConnection();
// list farmers who have products or by all farmers
$farmers = $pdo->query("SELECT id, name FROM users WHERE role='farmer' ORDER BY name ASC")->fetchAll();
$farmerId = (int)($_GET['farmer_id'] ?? ($farmers[0]['id'] ?? 0));
?>
<h2>Chat with Farmers</h2>
<form method="get" class="form" style="max-width:400px">
  <label class="label">Choose Farmer</label>
  <select class="select" name="farmer_id" onchange="this.form.submit()">
    <?php foreach($farmers as $f): ?>
      <option value="<?= (int)$f['id'] ?>" <?= $f['id']===$farmerId?'selected':'' ?>><?= e($f['name']) ?></option>
    <?php endforeach; ?>
  </select>
</form>
<div id="chat" class="hero-card" style="margin-top:1rem">
  <div id="messages" style="height:300px;overflow:auto;border:1px solid #eee;padding:.5rem;border-radius:8px;background:#fff"></div>
  <form id="sendForm" class="search" style="margin-top:.5rem" onsubmit="sendMessage(event)">
    <input id="msg" type="text" placeholder="Type your message..." required />
    <input type="hidden" id="farmer" value="<?= (int)$farmerId ?>" />
    <button class="btn btn-primary" type="submit">Send</button>
  </form>
</div>
<script>
const messagesBox = document.getElementById('messages')
const farmer = document.getElementById('farmer').value
const fetchMessages = async () => {
  const res = await fetch('<?= e(APP_URL) ?>/actions/chat_fetch.php?farmer_id=' + farmer)
  messagesBox.innerHTML = await res.text()
  messagesBox.scrollTop = messagesBox.scrollHeight
}
const poller = (function(){ const p = setInterval(fetchMessages, 2000); return {stop:()=>clearInterval(p)} })()
fetchMessages()
async function sendMessage(e){
  e.preventDefault()
  const msg = document.getElementById('msg')
  if (!msg.value.trim()) return
  await fetch('<?= e(APP_URL) ?>/actions/chat_send.php', {
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body: new URLSearchParams({ farmer_id: farmer, message: msg.value, csrf: '<?= e(csrf_token()) ?>' })
  })
  msg.value = ''
  fetchMessages()
}
</script>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
