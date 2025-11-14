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
              <th>#</th>
              <th>Patient</th>
              <th>Department</th>
              <th>Doctor</th>
              <th>Date</th>
              <th>Time</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="8" class="text-center text-muted py-4">No data source wired yet.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
