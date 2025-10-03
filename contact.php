<?php require_once __DIR__ . '/partials/header.php'; ?>
<div class="hero-card">
  <h2>Contact & Support</h2>
  <form class="form" method="post" action="#">
    <label class="label">Name</label>
    <input class="input" type="text" required />
    <label class="label">Email</label>
    <input class="input" type="email" required />
    <label class="label">Message</label>
    <textarea class="textarea" rows="4" required></textarea>
    <button class="btn btn-primary" type="submit">Send</button>
  </form>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
