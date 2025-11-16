<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Appointment Tracker<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/appointments.css?v=20251114') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="appointments-page container py-4">
  <div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h3 class="page-title mb-0">Appointment Tracker</h3>
    <a class="btn btn-primary" href="<?= site_url('receptionist/appointments/book') ?>">New Appointment</a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="get" class="toolbar row g-2 mb-3">
        <div class="col-12 col-md-3">
          <input type="date" class="form-control" name="date" value="<?= esc($_GET['date'] ?? '') ?>">
        </div>
        <div class="col-12 col-md-6">
          <input type="text" class="form-control" name="q" placeholder="Search by patient, doctor, department" value="<?= esc($_GET['q'] ?? '') ?>">
        </div>
        <div class="col-12 col-md-3 d-grid">
          <button class="btn btn-outline-secondary" type="submit">Filter</button>
        </div>
      </form>

      <div class="table-wrap table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="text-align:center; width:60px;">No#</th>
              <th style="text-align:center; width:120px;">Patient</th>
              <th style="text-align:center; width:140px;">Department</th>
              <th style="text-align:center;">Doctor</th>
              <th style="text-align:center;">Date</th>
              <th style="text-align:center;">Time</th>
              <th style="text-align:center;">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($appointments ?? [])): ?>
              <?php $i = 1; foreach ($appointments as $apt): ?>
                <tr>
                  <td style="text-align:center;"><?= $i++ ?></td>
                  <td style="text-align:center;">
                    <?= esc(trim(($apt['patient_first_name'] ?? '') . ' ' . ($apt['patient_last_name'] ?? ''))) ?: 'N/A' ?>
                  </td>
                  <td style="text-align:center;">
                    <?= esc($apt['appointment_type'] ?? 'N/A') ?>
                  </td>
                  <td style="text-align:center;">
                    <?= esc($apt['doctor_name'] ?? 'N/A') ?>
                  </td>
                  <td style="text-align:center;"><?= esc($apt['appointment_date'] ?? '') ?></td>
                  <td style="text-align:center;"><?= esc(substr($apt['appointment_time'] ?? '', 0, 5)) ?></td>
                  <td style="text-align:center;" class="text-capitalize"><?= esc($apt['status'] ?? 'scheduled') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-4">No appointments found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>