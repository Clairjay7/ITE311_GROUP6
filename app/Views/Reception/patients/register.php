<?php
helper('form');
$errors = session('errors') ?? [];
?>
<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Register Patient<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/patient-register.css?v=20251113') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="register-page container py-4">
  <div class="page-header mb-3 d-flex justify-content-between align-items-center">
    <h3 class="page-title mb-0">Register Patient</h3>
    <a href="<?= site_url('receptionist/patients') ?>" class="btn btn-outline-secondary">Back to Records</a>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="post" action="<?= site_url('receptionist/patients/store') ?>">
        <?= csrf_field() ?>
        
        <!-- A. Personal Information -->
        <h5 class="section-title mb-2">A. Personal Information</h5>
        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label class="form-label">Patient ID / Registration No.</label>
            <input type="text" name="patient_reg_no" class="form-control" value="<?= set_value('patient_reg_no') ?>" placeholder="Auto or manual">
          </div>
          <div class="col-md-4">
            <label class="form-label">First Name<span class="text-danger">*</span></label>
            <input type="text" name="first_name" class="form-control <?= isset($errors['first_name'])?'is-invalid':'' ?>" value="<?= set_value('first_name') ?>">
            <div class="invalid-feedback"><?= esc($errors['first_name'] ?? '') ?></div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middle_name" class="form-control" value="<?= set_value('middle_name') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Last Name<span class="text-danger">*</span></label>
            <input type="text" name="last_name" class="form-control <?= isset($errors['last_name'])?'is-invalid':'' ?>" value="<?= set_value('last_name') ?>">
            <div class="invalid-feedback"><?= esc($errors['last_name'] ?? '') ?></div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="date_of_birth" class="form-control" value="<?= set_value('date_of_birth') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Age</label>
            <input type="number" name="age" class="form-control" value="<?= set_value('age') ?>" placeholder="Auto if DOB provided">
          </div>
          <div class="col-md-3">
            <label class="form-label">Gender / Sex<span class="text-danger">*</span></label>
            <select name="gender" class="form-select <?= isset($errors['gender'])?'is-invalid':'' ?>">
              <option value="">Select</option>
              <option value="Male" <?= set_select('gender','Male') ?>>Male</option>
              <option value="Female" <?= set_select('gender','Female') ?>>Female</option>
              <option value="Other" <?= set_select('gender','Other') ?>>Other</option>
            </select>
            <div class="invalid-feedback"><?= esc($errors['gender'] ?? '') ?></div>
          </div>
          <div class="col-md-3">
            <label class="form-label">Civil Status</label>
            <select name="civil_status" class="form-select">
              <option value="">Select</option>
              <option <?= set_select('civil_status','Single') ?>>Single</option>
              <option <?= set_select('civil_status','Married') ?>>Married</option>
              <option <?= set_select('civil_status','Widowed') ?>>Widowed</option>
              <option <?= set_select('civil_status','Divorced') ?>>Divorced</option>
              <option <?= set_select('civil_status','Separated') ?>>Separated</option>
              <option <?= set_select('civil_status','Annulled') ?>>Annulled</option>
              <option <?= set_select('civil_status','Other') ?>>Other</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Street</label>
            <input type="text" name="address_street" class="form-control" value="<?= set_value('address_street') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Barangay</label>
            <input type="text" name="address_barangay" class="form-control" value="<?= set_value('address_barangay') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">City</label>
            <input type="text" name="address_city" class="form-control" value="<?= set_value('address_city') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Province</label>
            <input type="text" name="address_province" class="form-control" value="<?= set_value('address_province') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact" class="form-control" value="<?= set_value('contact') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control" value="<?= set_value('email') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Nationality</label>
            <input type="text" name="nationality" class="form-control" value="<?= set_value('nationality') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Religion</label>
            <input type="text" name="religion" class="form-control" value="<?= set_value('religion') ?>">
          </div>
        </div>

        <!-- B. Emergency Contact / Guardian Information -->
        <h5 class="section-title mb-2">B. Emergency Contact / Guardian Information</h5>
        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label">Name of Emergency Contact / Guardian</label>
            <input type="text" name="emergency_name" class="form-control" value="<?= set_value('emergency_name') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Relationship</label>
            <input type="text" name="emergency_relationship" class="form-control" value="<?= set_value('emergency_relationship') ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Contact Number</label>
            <input type="text" name="emergency_contact" class="form-control" value="<?= set_value('emergency_contact') ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Address</label>
            <input type="text" name="emergency_address" class="form-control" value="<?= set_value('emergency_address') ?>">
          </div>
        </div>

        <!-- C. Medical Information -->
        <h5 class="section-title mb-2">C. Medical Information</h5>
        <div class="row g-3 mb-3">
          <div class="col-md-3">
            <label class="form-label">Blood Type</label>
            <select name="blood_type" class="form-select">
              <option value="">Select</option>
              <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt): ?>
                <option value="<?= $bt ?>" <?= set_select('blood_type',$bt) ?>><?= $bt ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-9">
            <label class="form-label">Allergies</label>
            <input type="text" name="allergies" class="form-control" value="<?= set_value('allergies') ?>" placeholder="Comma-separated or brief notes">
          </div>
          <div class="col-12">
            <label class="form-label">Existing Medical Conditions / Illnesses</label>
            <textarea name="existing_conditions" class="form-control" rows="2"><?= set_value('existing_conditions') ?></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Current Medications</label>
            <textarea name="current_medications" class="form-control" rows="2"><?= set_value('current_medications') ?></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Past Surgeries / Hospitalizations</label>
            <textarea name="past_surgeries" class="form-control" rows="2"><?= set_value('past_surgeries') ?></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Family Medical History (optional)</label>
            <textarea name="family_history" class="form-control" rows="2"><?= set_value('family_history') ?></textarea>
          </div>
        </div>

        <!-- D. Insurance / Billing Information -->
        <h5 class="section-title mb-2">D. Insurance / Billing Information</h5>
        <div class="row g-3 mb-3">
          <div class="col-md-4">
            <label class="form-label">Health Insurance Provider</label>
            <input type="text" name="insurance_provider" class="form-control" value="<?= set_value('insurance_provider') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Insurance Number / Policy ID</label>
            <input type="text" name="insurance_number" class="form-control" value="<?= set_value('insurance_number') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">PhilHealth Number</label>
            <input type="text" name="philhealth_number" class="form-control" value="<?= set_value('philhealth_number') ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Billing Address</label>
            <textarea name="billing_address" class="form-control" rows="2"><?= set_value('billing_address') ?></textarea>
          </div>
          <div class="col-md-3">
            <label class="form-label">Payment Type</label>
            <select name="payment_type" class="form-select">
              <option value="">Select</option>
              <option <?= set_select('payment_type','Cash') ?>>Cash</option>
              <option <?= set_select('payment_type','Insurance') ?>>Insurance</option>
              <option <?= set_select('payment_type','Credit') ?>>Credit</option>
            </select>
          </div>
        </div>

        <!-- E. Registration Details -->
        <h5 class="section-title mb-2">E. Registration Details</h5>
        <div class="row g-3 mb-3">
          <div class="col-md-3">
            <label class="form-label">Date of Registration</label>
            <input type="date" name="registration_date" class="form-control" value="<?= set_value('registration_date', date('Y-m-d')) ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Registered By</label>
            <input type="text" name="registered_by" class="form-control" value="<?= set_value('registered_by') ?>" placeholder="Staff name or ID">
          </div>
          <div class="col-md-3">
            <label class="form-label">Type<span class="text-danger">*</span></label>
            <select name="type" id="ptype" class="form-select <?= isset($errors['type'])?'is-invalid':'' ?>" onchange="toggleInpatient()">
              <option value="Out-Patient" <?= set_select('type','Out-Patient', true) ?>>Out-Patient</option>
              <option value="In-Patient" <?= set_select('type','In-Patient') ?>>In-Patient</option>
            </select>
            <div class="invalid-feedback"><?= esc($errors['type'] ?? '') ?></div>
          </div>
          <div class="col-md-3">
            <label class="form-label">Doctor Assigned</label>
            <select name="doctor_id" class="form-select">
              <option value="">-- Select Doctor --</option>
              <?php foreach (($doctors ?? []) as $d): ?>
                <option value="<?= esc($d['id']) ?>" <?= set_select('doctor_id', (string)$d['id']) ?>><?= esc($d['doctor_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Department / Clinic</label>
            <select name="department_id" class="form-select">
              <option value="">-- Select Department --</option>
              <?php foreach (($departments ?? []) as $dep): ?>
                <option value="<?= esc($dep['id']) ?>" <?= set_select('department_id', (string)$dep['id']) ?>><?= esc($dep['department_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Purpose of Visit</label>
            <textarea name="purpose" class="form-control" rows="2"><?= set_value('purpose') ?></textarea>
          </div>
          <div class="col-md-4 inpatient-only" style="display:none;">
            <label class="form-label">Admission Date</label>
            <input type="date" name="admission_date" class="form-control" value="<?= set_value('admission_date') ?>">
          </div>
          <div class="col-md-4 inpatient-only" style="display:none;">
            <label class="form-label">Room Number</label>
            <input type="text" name="room_number" class="form-control" value="<?= set_value('room_number') ?>">
          </div>
        </div>

        <!-- F. Signatures -->
        <h5 class="section-title mb-2">F. Signatures</h5>
        <div class="row g-3 mb-2">
          <div class="col-md-6">
            <label class="form-label">Patient’s Signature (reference)</label>
            <input type="text" name="signature_patient" class="form-control" value="<?= set_value('signature_patient') ?>" placeholder="File path or reference">
          </div>
          <div class="col-md-3">
            <label class="form-label">Date Signed</label>
            <input type="date" name="date_signed" class="form-control" value="<?= set_value('date_signed') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Staff / Receptionist Signature (reference)</label>
            <input type="text" name="signature_staff" class="form-control" value="<?= set_value('signature_staff') ?>" placeholder="File path or reference">
          </div>
        </div>

        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-primary">Save</button>
          <a href="<?= site_url('receptionist/patients') ?>" class="btn btn-outline-secondary">Cancel</a>
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
