<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>💵 Billing & Payments</h1>
<div class="card">
  <div class="actions-row">
    <input type="text" placeholder="Search patient or invoice..." style="flex:1; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
    <a class="btn" href="#">Search</a>
    <a class="btn btn-secondary" href="#">New Invoice</a>
  </div>
</div>
<div class="spacer"></div>
<div class="card">
  <h5>Pending Bills</h5>
  <div style="overflow:auto;">
    <table style="width:100%; border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Patient</th>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Amount</th>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Due Date</th>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Status</th>
          <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9;">John Doe</td>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9;">₱3,500.00</td>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9;">2025-10-05</td>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#ef4444;">Pending</td>
          <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn" href="#">Collect</a></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<?= $this->endSection() ?>
