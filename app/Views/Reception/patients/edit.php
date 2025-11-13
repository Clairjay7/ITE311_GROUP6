<?php
helper('form');
$errors = session('errors') ?? [];
?>
<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Edit Patient<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="container py-4">
  <div class="mb-3 d-flex justify-content-between align-items-center">
    <h3 class="mb-0">Edit Patient</h3>
    <a href="/receptionist/patients" class="btn btn-outline-secondary">Back to Records</a>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="post" action="/receptionist/patients/update/<?= esc($patient['patient_id']) ?>">
        <?= csrf_field() ?>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Full Name<span class="text-danger">*</span></label>
            <input type="text" name="full_name" class="form-control <?= isset($errors['full_name'])?'is-invalid':'' ?>" value="<?= set_value('full_name', $patient['full_name'] ?? '') ?>">
            <div class="invalid-feedback"><?= esc($errors['full_name'] ?? '') ?></div>
          </div>
          <div class="col-md-3">
            <label class="form-label">Gender<span class="text-danger">*</span></label>
            <select name="gender" class="form-select <?= isset($errors['gender'])?'is-invalid':'' ?>">
              <?php $g = set_value('gender', $patient['gender'] ?? ''); ?>
              <option value="Male" <?= $g==='Male'?'selected':''; ?>>Male</option>
              <option value="Female" <?= $g==='Female'?'selected':''; ?>>Female</option>
              <option value="Other" <?= $g==='Other'?'selected':''; ?>>Other</option>
            </select>
            <div class="invalid-feedback"><?= esc($errors['gender'] ?? '') ?></div>
          </div>
          <div class="col-md-3">
            <label class="form-label">Age<span class="text-danger">*</span></label>
            <input type="number" name="age" class="form-control <?= isset($errors['age'])?'is-invalid':'' ?>" value="<?= set_value('age', $patient['age'] ?? '') ?>">
            <div class="invalid-feedback"><?= esc($errors['age'] ?? '') ?></div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact" class="form-control" value="<?= set_value('contact', $patient['contact'] ?? '') ?>">
          </div>
          <div class="col-md-8">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="<?= set_value('address', $patient['address'] ?? '') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Type<span class="text-danger">*</span></label>
            <?php $t = set_value('type', $patient['type'] ?? 'Out-Patient'); ?>
            <select name="type" id="ptype" class="form-select <?= isset($errors['type'])?'is-invalid':'' ?>" onchange="toggleInpatient()">
              <option value="Out-Patient" <?= $t==='Out-Patient'?'selected':''; ?>>Out-Patient</option>
              <option value="In-Patient" <?= $t==='In-Patient'?'selected':''; ?>>In-Patient</option>
            </select>
            <div class="invalid-feedback"><?= esc($errors['type'] ?? '') ?></div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Doctor Assigned</label>
            <select name="doctor_id" class="form-select">
              <option value="">-- Select Doctor --</option>
              <?php foreach (($doctors ?? []) as $d): ?>
                <option value="<?= esc($d['id']) ?>" <?= (string)($patient['doctor_id'] ?? '')===(string)$d['id']?'selected':''; ?>><?= esc($d['doctor_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Department</label>
            <select name="department_id" class="form-select">
              <option value="">-- Select Department --</option>
              <?php foreach (($departments ?? []) as $dep): ?>
                <option value="<?= esc($dep['id']) ?>" <?= (string)($patient['department_id'] ?? '')===(string)$dep['id']?'selected':''; ?>><?= esc($dep['department_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Purpose / Complaint</label>
            <textarea name="purpose" class="form-control" rows="3"><?= set_value('purpose', $patient['purpose'] ?? '') ?></textarea>
          </div>
          <div class="col-md-4 inpatient-only" style="display:none;">
            <label class="form-label">Admission Date</label>
            <input type="date" name="admission_date" class="form-control" value="<?= set_value('admission_date', $patient['admission_date'] ?? '') ?>">
          </div>
          <div class="col-md-4 inpatient-only" style="display:none;">
            <label class="form-label">Room Number</label>
            <input type="text" name="room_number" class="form-control" value="<?= set_value('room_number', $patient['room_number'] ?? '') ?>">
          </div>
        </div>
        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-primary">Update</button>
          <a href="/receptionist/patients" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
function toggleInpatient(){
  const isIn = document.getElementById('ptype').value === 'In-Patient';
  document.querySelectorAll('.inpatient-only').forEach(el => el.style.display = isIn ? 'block':'none');
}
window.addEventListener('DOMContentLoaded', toggleInpatient);
</script>
<?= $this->endSection() ?>
