<?php
helper('form');
$errors = session('errors') ?? [];
$initialType = $initialType ?? 'Out-Patient';
?>
<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Register Patient<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/patient-register.css?v=20251113') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="register-page container py-4">
  <div class="page-header mb-3 d-flex justify-content-between align-items-center">
    <h3 class="page-title mb-0">Register In-Patient</h3>
    <a href="<?= site_url('receptionist/patients') ?>" class="btn btn-outline-secondary">Back to Records</a>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="post" action="<?= site_url('receptionist/patients/store') ?>">
        <?= csrf_field() ?>
        
        <!-- Personal Information -->
        <h5 class="section-title mb-3">Personal Information</h5>
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label">First Name<span class="text-danger">*</span></label>
            <input type="text" name="first_name" class="form-control <?= isset($errors['first_name'])?'is-invalid':'' ?>" value="<?= set_value('first_name') ?>" required>
            <div class="invalid-feedback"><?= esc($errors['first_name'] ?? '') ?></div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Last Name<span class="text-danger">*</span></label>
            <input type="text" name="last_name" class="form-control <?= isset($errors['last_name'])?'is-invalid':'' ?>" value="<?= set_value('last_name') ?>" required>
            <div class="invalid-feedback"><?= esc($errors['last_name'] ?? '') ?></div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Gender<span class="text-danger">*</span></label>
            <select name="gender" class="form-select <?= isset($errors['gender'])?'is-invalid':'' ?>" required>
              <option value="">Select</option>
              <option value="Male" <?= set_select('gender','Male') ?>>Male</option>
              <option value="Female" <?= set_select('gender','Female') ?>>Female</option>
              <option value="Other" <?= set_select('gender','Other') ?>>Other</option>
            </select>
            <div class="invalid-feedback"><?= esc($errors['gender'] ?? '') ?></div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-control" value="<?= set_value('date_of_birth') ?>" id="date_of_birth">
          </div>
          <div class="col-md-4">
            <label class="form-label">Age</label>
            <input type="number" name="age" class="form-control" value="<?= set_value('age') ?>" id="age" placeholder="Auto-calculated if DOB provided">
          </div>
          <div class="col-md-4">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact" class="form-control" value="<?= set_value('contact') ?>" placeholder="09XX-XXX-XXXX">
          </div>
          <div class="col-md-12">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="<?= set_value('address') ?>" placeholder="Complete address">
          </div>
        </div>

        <!-- Visit Information -->
        <h5 class="section-title mb-3">Visit Information</h5>
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label">Visit Type<span class="text-danger">*</span></label>
            <select name="visit_type" id="visit_type" class="form-select" required>
              <option value="">-- Select Visit Type --</option>
              <option value="Emergency" <?= set_select('visit_type','Emergency') ?>>Emergency</option>
            </select>
            <small class="text-muted">Emergency cases require room assignment and will go to Nurse Triage first</small>
          </div>
          <div class="col-md-4">
            <label class="form-label">Purpose of Visit / Reason</label>
            <textarea name="purpose" class="form-control" rows="3" placeholder="Reason for visit or chief complaint"><?= set_value('purpose') ?></textarea>
          </div>
          <div class="col-md-2">
            <label class="form-label">Admission Date</label>
            <input type="date" name="admission_date" class="form-control" value="<?= set_value('admission_date', date('Y-m-d')) ?>">
          </div>
          <div class="col-md-2">
            <label class="form-label">Type</label>
            <input type="text" class="form-control" value="In-Patient" readonly>
            <input type="hidden" name="type" value="In-Patient">
          </div>
        </div>
        
        <!-- Room Assignment (Required for In-Patient Emergency) -->
        <div id="room_assignment_section">
          <h5 class="section-title mb-3">Room Assignment</h5>
          <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Room assignment is required for Emergency In-Patient cases.
          </div>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label">Ward<span class="text-danger">*</span></label>
              <select name="ward" id="wardSelect" class="form-select" required>
                <option value="">-- Select Ward --</option>
                <?php foreach (($availableRoomsByWard ?? []) as $wardName => $rooms): ?>
                  <?php if (!empty($rooms)): ?>
                    <option value="<?= esc($wardName) ?>" <?= set_select('ward', $wardName) ?>><?= esc($wardName) ?></option>
                  <?php endif; ?>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Room Number<span class="text-danger">*</span></label>
              <select name="room_number" id="roomSelect" class="form-select" required>
                <option value="">-- Select Room --</option>
              </select>
            </div>
          </div>
        </div>

        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-primary">Register Patient</button>
          <a href="<?= site_url('receptionist/patients') ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
window.addEventListener('DOMContentLoaded', function () {
  // Auto-calculate age from date of birth
  const dobInput = document.getElementById('date_of_birth');
  const ageInput = document.getElementById('age');
  
  if (dobInput && ageInput) {
    dobInput.addEventListener('change', function() {
      if (this.value) {
        const birthDate = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
          age--;
        }
        ageInput.value = age;
      }
    });
  }

  // Ward/Room dropdown handling for In-Patient registration (Emergency only, room assignment always required)
  const wardSelect = document.getElementById('wardSelect');
  const roomSelect = document.getElementById('roomSelect');

  if (wardSelect && roomSelect) {
    const roomsByWard = <?= json_encode($availableRoomsByWard ?? []) ?>;

    function populateRoomsForWard(wardName) {
      roomSelect.innerHTML = '';
      const placeholder = document.createElement('option');
      placeholder.value = '';
      placeholder.textContent = '-- Select Room --';
      roomSelect.appendChild(placeholder);

      const rooms = roomsByWard[wardName] || [];
      rooms.forEach(function (room) {
        const opt = document.createElement('option');
        opt.value = room.room_number;
        opt.textContent = room.room_number;
        roomSelect.appendChild(opt);
      });
    }

    wardSelect.addEventListener('change', function () {
      const wardName = this.value;
      populateRoomsForWard(wardName);
    });
  }
});
</script>
<?= $this->endSection() ?>
