<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>New Appointment<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/appointments.css?v=20251114') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="appointments-page container py-4">
  <div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h3 class="page-title mb-0">New Appointment</h3>
    <a class="btn btn-outline-secondary" href="<?= site_url('receptionist/appointments/list') ?>">Back to Tracker</a>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="post" action="<?= site_url('appointments/create') ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Patient (Name or ID)</label>
            <input type="text" name="patient_ref" class="form-control" placeholder="e.g. #123 or Juan Dela Cruz" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Department</label>
            <?php if (!empty($departments ?? [])): ?>
              <select name="department" class="form-select" required>
                <option value="" disabled selected>Select department</option>
                <?php foreach (($departments ?? []) as $dept): ?>
                  <option value="<?= esc($dept['department_name']) ?>"><?= esc($dept['department_name']) ?></option>
                <?php endforeach; ?>
              </select>
            <?php else: ?>
              <input type="text" name="department" class="form-control" placeholder="e.g. Pediatrics" required>
            <?php endif; ?>
          </div>
          <div class="col-md-6">
            <label class="form-label">Doctor</label>
            <?php if (!empty($doctors ?? [])): ?>
              <select name="doctor_id" class="form-select" required>
                <option value="" disabled selected>Select doctor</option>
                <?php foreach (($doctors ?? []) as $doc): ?>
                  <option value="<?= (int)$doc['id'] ?>">
                    <?= esc($doc['username']) ?> <?= $doc['email'] ? '(' . esc($doc['email']) . ')' : '' ?>
                  </option>
                <?php endforeach; ?>
              </select>
            <?php else: ?>
              <input type="text" name="doctor" class="form-control" placeholder="e.g. dr.username or email" required>
            <?php endif; ?>
          </div>
          <div class="col-md-3">
            <label class="form-label">Date</label>
            <input type="date" name="appointment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Time</label>
            <input type="time" name="appointment_time" class="form-control" value="<?= date('H:i') ?>" required>
          </div>
          <div class="col-12">
            <label class="form-label">Notes</label>
            <textarea name="notes" rows="2" class="form-control" placeholder="Optional notes"></textarea>
          </div>
        </div>
        <div class="mt-3 d-flex justify-content-end gap-2">
          <button type="submit" class="btn btn-primary">Save</button>
          <a href="<?= site_url('receptionist/appointments/list') ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
