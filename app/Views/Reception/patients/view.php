<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Patient Details<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Patient Details</h3>
    <div>
      <a class="btn btn-outline-secondary" href="/receptionist/patients">Back</a>
      <a class="btn btn-primary" href="/receptionist/patients/edit/<?= esc($patient['patient_id']) ?>">Edit</a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <div class="text-muted">Patient ID</div>
          <div class="fw-semibold">#<?= esc($patient['patient_id']) ?></div>
        </div>
        <div class="col-md-5">
          <div class="text-muted">Full Name</div>
          <div class="fw-semibold"><?= esc($patient['full_name']) ?></div>
        </div>
        <div class="col-md-2">
          <div class="text-muted">Gender</div>
          <div class="fw-semibold"><?= esc($patient['gender']) ?></div>
        </div>
        <div class="col-md-2">
          <div class="text-muted">Age</div>
          <div class="fw-semibold"><?= esc($patient['age']) ?></div>
        </div>
        <div class="col-md-4">
          <div class="text-muted">Type</div>
          <div><span class="badge <?= $patient['type']==='In-Patient'?'bg-info':'bg-success' ?>"><?= esc($patient['type']) ?></span></div>
        </div>
        <div class="col-md-4">
          <div class="text-muted">Doctor Assigned</div>
          <div class="fw-semibold"><?= esc($patient['doctor_name'] ?? '-') ?></div>
        </div>
        <div class="col-md-4">
          <div class="text-muted">Department</div>
          <div class="fw-semibold"><?= esc($patient['department_name'] ?? '-') ?></div>
        </div>
        <div class="col-md-6">
          <div class="text-muted">Contact</div>
          <div class="fw-semibold"><?= esc($patient['contact'] ?? '-') ?></div>
        </div>
        <div class="col-md-6">
          <div class="text-muted">Address</div>
          <div class="fw-semibold"><?= esc($patient['address'] ?? '-') ?></div>
        </div>
        <div class="col-md-12">
          <div class="text-muted">Purpose / Complaint</div>
          <div class="fw-semibold"><?= nl2br(esc($patient['purpose'] ?? '-')) ?></div>
        </div>
        <?php if (($patient['type'] ?? '') === 'In-Patient'): ?>
        <div class="col-md-4">
          <div class="text-muted">Admission Date</div>
          <div class="fw-semibold"><?= esc($patient['admission_date'] ?? '-') ?></div>
        </div>
        <div class="col-md-4">
          <div class="text-muted">Room Number</div>
          <div class="fw-semibold"><?= esc($patient['room_number'] ?? '-') ?></div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
