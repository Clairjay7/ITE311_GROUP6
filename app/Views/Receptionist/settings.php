<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>⚙️ Settings</h1>
<div class="card">
  <div class="actions-row">
    <label style="flex:1;">Notifications</label>
    <select style="padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
      <option>Enabled</option>
      <option>Disabled</option>
    </select>
  </div>
  <div class="actions-row">
    <label style="flex:1;">Default Doctor Filter</label>
    <select style="padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
      <option>All</option>
    </select>
  </div>
</div>
<?= $this->endSection() ?>
