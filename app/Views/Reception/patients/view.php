<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Patient Details<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/patient-view.css?v=20251113') ?>">
<style>
    .field-row {
        display: none; /* Hide by default, show only if has value */
    }
    .field-row.has-value {
        display: block;
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
// Helper function to check if field has value
function hasValue($value) {
    return !empty($value) && $value !== '-' && trim($value) !== '';
}
?>
<div class="patient-view container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Patient Details</h3>
    <?php if (!empty($patient['type'])): ?>
      <span class="badge <?= ($patient['type'] ?? '')==='In-Patient'?'bg-info':'bg-success' ?>">
        <?= esc($patient['type']) ?>
      </span>
    <?php endif; ?>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <!-- A. Personal Information -->
      <h6 class="section-title mb-2">A. Personal Information</h6>
      <div class="row g-3 mb-3">
        <div class="col-md-3">
          <div class="text-muted">Patient ID</div>
          <div class="fw-semibold">#<?= esc($patient['patient_id']) ?></div>
        </div>
        <?php if (hasValue($patient['patient_reg_no'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Reg. No.</div>
          <div class="fw-semibold"><?= esc($patient['patient_reg_no']) ?></div>
        </div>
        <?php endif; ?>
        <div class="col-md-6">
          <div class="text-muted">Full Name</div>
          <div class="fw-semibold"><?= esc($patient['full_name'] ?? trim(($patient['first_name'] ?? '').' '.($patient['middle_name'] ?? '').' '.($patient['last_name'] ?? ''))) ?></div>
        </div>
        <?php if (hasValue($patient['gender'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Gender / Sex</div>
          <div class="fw-semibold"><?= esc($patient['gender']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['civil_status'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Civil Status</div>
          <div class="fw-semibold"><?= esc($patient['civil_status']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['date_of_birth'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Date of Birth</div>
          <div class="fw-semibold"><?= esc($patient['date_of_birth']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['age'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Age</div>
          <div class="fw-semibold"><?= esc($patient['age']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['address'] ?? '')): ?>
        <div class="col-md-6 field-row has-value">
          <div class="text-muted">Address</div>
          <div class="fw-semibold"><?= esc($patient['address']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['contact'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Contact</div>
          <div class="fw-semibold"><?= esc($patient['contact']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['email'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Email</div>
          <div class="fw-semibold"><?= esc($patient['email']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['nationality'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Nationality</div>
          <div class="fw-semibold"><?= esc($patient['nationality']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['religion'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Religion</div>
          <div class="fw-semibold"><?= esc($patient['religion']) ?></div>
        </div>
        <?php endif; ?>
      </div>

      <!-- B. Emergency Contact / Guardian Information -->
      <?php if (hasValue($patient['emergency_name'] ?? '') || hasValue($patient['emergency_contact'] ?? '') || hasValue($patient['emergency_relationship'] ?? '')): ?>
      <h6 class="section-title mb-2">B. Emergency Contact / Guardian Information</h6>
      <div class="row g-3 mb-3">
        <?php if (hasValue($patient['emergency_name'] ?? '')): ?>
        <div class="col-md-6 field-row has-value">
          <div class="text-muted">Name</div>
          <div class="fw-semibold"><?= esc($patient['emergency_name']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['emergency_relationship'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Relationship</div>
          <div class="fw-semibold"><?= esc($patient['emergency_relationship']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['emergency_contact'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Contact</div>
          <div class="fw-semibold"><?= esc($patient['emergency_contact']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['emergency_address'] ?? '')): ?>
        <div class="col-12 field-row has-value">
          <div class="text-muted">Address</div>
          <div class="fw-semibold"><?= esc($patient['emergency_address']) ?></div>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- C. Medical Information -->
      <?php if (hasValue($patient['blood_type'] ?? '') || hasValue($patient['allergies'] ?? '') || hasValue($patient['existing_conditions'] ?? '') || hasValue($patient['current_medications'] ?? '') || hasValue($patient['past_surgeries'] ?? '') || hasValue($patient['family_history'] ?? '')): ?>
      <h6 class="section-title mb-2">C. Medical Information</h6>
      <div class="row g-3 mb-3">
        <?php if (hasValue($patient['blood_type'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Blood Type</div>
          <div class="fw-semibold"><?= esc($patient['blood_type']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['allergies'] ?? '')): ?>
        <div class="col-md-9 field-row has-value">
          <div class="text-muted">Allergies</div>
          <div class="fw-semibold"><?= esc($patient['allergies']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['existing_conditions'] ?? '')): ?>
        <div class="col-12 field-row has-value">
          <div class="text-muted">Existing Conditions</div>
          <div class="fw-semibold"><?= nl2br(esc($patient['existing_conditions'])) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['current_medications'] ?? '')): ?>
        <div class="col-12 field-row has-value">
          <div class="text-muted">Current Medications</div>
          <div class="fw-semibold"><?= nl2br(esc($patient['current_medications'])) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['past_surgeries'] ?? '')): ?>
        <div class="col-12 field-row has-value">
          <div class="text-muted">Past Surgeries / Hospitalizations</div>
          <div class="fw-semibold"><?= nl2br(esc($patient['past_surgeries'])) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['family_history'] ?? '')): ?>
        <div class="col-12 field-row has-value">
          <div class="text-muted">Family Medical History</div>
          <div class="fw-semibold"><?= nl2br(esc($patient['family_history'])) ?></div>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- D. Insurance / Billing Information -->
      <?php if (hasValue($patient['insurance_provider'] ?? '') || hasValue($patient['insurance_number'] ?? '') || hasValue($patient['philhealth_number'] ?? '') || hasValue($patient['payment_type'] ?? '')): ?>
      <h6 class="section-title mb-2">D. Insurance / Billing Information</h6>
      <div class="row g-3 mb-3">
        <?php if (hasValue($patient['insurance_provider'] ?? '')): ?>
        <div class="col-md-4 field-row has-value">
          <div class="text-muted">Insurance Provider</div>
          <div class="fw-semibold"><?= esc($patient['insurance_provider']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['insurance_number'] ?? '')): ?>
        <div class="col-md-4 field-row has-value">
          <div class="text-muted">Policy ID</div>
          <div class="fw-semibold"><?= esc($patient['insurance_number']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['philhealth_number'] ?? '')): ?>
        <div class="col-md-4 field-row has-value">
          <div class="text-muted">PhilHealth Number</div>
          <div class="fw-semibold"><?= esc($patient['philhealth_number']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['payment_type'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Payment Type</div>
          <div class="fw-semibold"><?= esc($patient['payment_type']) ?></div>
        </div>
        <?php endif; ?>
      </div>
      <?php endif; ?>

      <!-- E. Registration Details -->
      <h6 class="section-title mb-2">E. Registration Details</h6>
      <div class="row g-3 mb-3">
        <?php if (hasValue($patient['registration_date'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Date of Registration</div>
          <div class="fw-semibold"><?= esc($patient['registration_date']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['registered_by'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Registered By</div>
          <div class="fw-semibold"><?= esc($patient['registered_by']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (!empty($patient['type'])): ?>
        <div class="col-md-3">
          <div class="text-muted">Type</div>
          <div><span class="badge <?= ($patient['type'] ?? '')==='In-Patient'?'bg-info':'bg-success' ?>"><?= esc($patient['type']) ?></span></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['doctor_name'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Doctor Assigned</div>
          <div class="fw-semibold"><?= esc($patient['doctor_name']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['department_name'] ?? '')): ?>
        <div class="col-md-3 field-row has-value">
          <div class="text-muted">Department / Clinic</div>
          <div class="fw-semibold"><?= esc($patient['department_name']) ?></div>
        </div>
        <?php endif; ?>
        <?php if (hasValue($patient['purpose'] ?? '')): ?>
        <div class="col-12 field-row has-value">
          <div class="text-muted">Purpose of Visit</div>
          <div class="fw-semibold"><?= nl2br(esc($patient['purpose'])) ?></div>
        </div>
        <?php endif; ?>
        <?php if (($patient['type'] ?? '') === 'In-Patient'): ?>
          <?php if (hasValue($patient['admission_date'] ?? '')): ?>
          <div class="col-md-4 field-row has-value">
            <div class="text-muted">Admission Date</div>
            <div class="fw-semibold"><?= esc($patient['admission_date']) ?></div>
          </div>
          <?php endif; ?>
          <?php if (hasValue($patient['room_number'] ?? '')): ?>
          <div class="col-md-4 field-row has-value">
            <div class="text-muted">Room Number</div>
            <div class="fw-semibold"><?= esc($patient['room_number']) ?></div>
          </div>
          <?php endif; ?>
        <?php endif; ?>
      </div>

    </div>
  </div>
  <div class="action-bar text-center mt-3">
    <a class="btn btn-outline-secondary me-2" href="<?= site_url('receptionist/patients') ?>">Back</a>
    <a class="btn btn-primary" href="<?= site_url('receptionist/patients/edit/'.esc($patient['patient_id'])) ?>">Edit</a>
  </div>
</div>
<?= $this->endSection() ?>
