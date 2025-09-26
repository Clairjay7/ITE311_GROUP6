<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>📋 Appointments</h1>
<div class="card">
  <div class="actions-row">
    <input type="date" style="padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
    <select style="padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
      <option>All Doctors</option>
    </select>
    <a class="btn" href="#">New Appointment</a>
  </div>
</div>
<div class="spacer"></div>
<div class="grid grid-2">
  <div class="card">
    <h5>Calendar</h5>
    <div style="height:260px; border:1px dashed #e2e8f0; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#64748b;">Calendar View</div>
  </div>
  <div class="card">
    <h5>Appointments</h5>
    <div style="overflow:auto;">
      <table style="width:100%; border-collapse:collapse;">
        <thead>
          <tr>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Date</th>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Time</th>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Doctor</th>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Patient</th>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Status</th>
            <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td style="padding:8px; border-bottom:1px solid #f1f5f9;">2025-09-26</td>
            <td style="padding:8px; border-bottom:1px solid #f1f5f9;">10:00</td>
            <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Dr. Lee</td>
            <td style="padding:8px; border-bottom:1px solid #f1f5f9;">John Doe</td>
            <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#f59e0b;">Pending</td>
            <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn" href="#">Confirm</a></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
