<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>👥 Visitor Management</h1>
<div class="card">
  <div class="actions-row">
    <input type="text" placeholder="Search patient or visitor..." style="flex:1; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
    <a class="btn" href="#">Search</a>
    <a class="btn btn-secondary" href="#">New Visitor</a>
  </div>
</div>
<div class="spacer"></div>
<div class="card">
  <h5>Active Visitors</h5>
  <div style="overflow:auto;">
    <table style="width:100%; border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Patient</th>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Visitor</th>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Relation</th>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Date</th>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Time In</th>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Jane Smith</td>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Michael Smith</td>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Husband</td>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9;">2025-09-26</td>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9;">14:30</td>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn" href="#">Check-out</a></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
