<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>🚪 Check-in & Check-out</h1>
<div class="grid grid-2">
  <div class="card">
    <h5>Arrivals Queue</h5>
    <ul style="margin:8px 0 0 16px;">
      <li>John Doe — Waiting <a class="btn" href="#">Check-in</a></li>
      <li>Jane Smith — Checked-in <a class="btn btn-secondary" href="#">Check-out</a></li>
    </ul>
  </div>
  <div class="card">
    <h5>Departures</h5>
    <ul style="margin:8px 0 0 16px;">
      <li>Maria Garcia — Checked-out at 14:45</li>
    </ul>
  </div>
</div>
<?= $this->endSection() ?>
