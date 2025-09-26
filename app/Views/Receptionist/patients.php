<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>📝 Patients</h1>
<div class="card">
  <div class="actions-row">
    <input type="text" id="patientSearch" placeholder="Search by name, ID, phone..." style="flex:1; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
    <a class="btn" href="#">Search</a>
    <a class="btn btn-secondary" href="#">Add Patient</a>
  </div>
</div>
<div class="spacer"></div>
<div class="card">
  <h5>Patient List</h5>
  <div style="overflow:auto;">
    <table style="width:100%; border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Patient ID</th>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Name</th>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Contact</th>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Status</th>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9;">P2025000001</td>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9;">John Doe</td>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9;">0917-000-0000</td>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#10b981;">Active</td>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn btn-secondary" href="#">Edit</a></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
