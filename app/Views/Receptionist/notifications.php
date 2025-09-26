<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>🔔 Notifications</h1>
<div class="card">
  <div class="actions-row"><span>Upcoming: John Doe at 10:00 with Dr. Lee</span></div>
  <div class="actions-row"><span>Pending: Registration for Maria Garcia</span></div>
  <div class="actions-row"><span>System: Update available at 22:00</span></div>
</div>
<?= $this->endSection() ?>
