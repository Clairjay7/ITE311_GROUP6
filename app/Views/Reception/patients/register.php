<?php
helper('form');
// Get validation errors - CodeIgniter 4 stores them in _ci_validation_errors when using withInput()
$errors = session()->getFlashdata('_ci_validation_errors') ?? [];
// Also check for custom errors flashdata
if (empty($errors)) {
    $errors = session()->getFlashdata('errors') ?? [];
}
// Handle single error message
$errorMessage = session()->getFlashdata('error');
?>
<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Register In-Patient<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .register-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0;
    }
    
    .page-header {
        background: linear-gradient(135deg, var(--gradient-1) 0%, var(--gradient-2) 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(46, 125, 50, 0.2);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .page-header h1 {
        margin: 0;
        font-size: 26px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .page-header h1 i {
        font-size: 30px;
    }
    
    .back-btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    
    .back-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    .form-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .form-card-body {
        padding: 24px;
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary-color);
        margin: 0 0 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #c8e6c9;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title i {
        font-size: 20px;
    }
    
    .form-section {
        margin-bottom: 32px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }
    
    .form-group {
        margin-bottom: 0;
    }
    
    .form-group.full-width {
        grid-column: 1 / -1;
    }
    
    .form-label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #1e293b;
        font-size: 14px;
    }
    
    .form-label .required {
        color: #ef4444;
        margin-left: 2px;
    }
    
    .form-control, .form-select {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        color: #1e293b;
        background: white;
        transition: all 0.2s ease;
    }
    
    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }
    
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #ef4444;
    }
    
    .form-control[readonly] {
        background: #f8fafc;
        color: #64748b;
    }
    
    .form-hint {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
    }
    
    .invalid-feedback {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
    }
    
    .alert {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 500;
    }
    
    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
    
    .alert-info {
        background: #e8f5e9;
        color: #1b5e20;
        border-left: 4px solid var(--primary-color);
    }
    
    /* Insurance Toggle */
    .insurance-toggle {
        display: flex;
        gap: 16px;
        margin-bottom: 16px;
    }
    
    .insurance-toggle label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        padding: 10px 20px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        transition: all 0.2s ease;
    }
    
    .insurance-toggle input[type="radio"] {
        width: 18px;
        height: 18px;
        accent-color: var(--primary-color);
    }
    
    .insurance-toggle label:has(input:checked) {
        border-color: var(--primary-color);
        background: #e8f5e9;
    }
    
    .insurance-fields {
        display: none;
        padding: 16px;
        background: #f8fafc;
        border-radius: 10px;
        margin-top: 16px;
    }
    
    .insurance-fields.show {
        display: block;
    }
    
    /* Submit Buttons */
    .form-actions {
        display: flex;
        gap: 12px;
        padding-top: 20px;
        border-top: 2px solid #e5e7eb;
        margin-top: 32px;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, var(--gradient-1) 0%, var(--gradient-2) 100%);
        color: white;
        padding: 14px 32px;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 125, 50, 0.3);
    }
    
    .btn-cancel {
        background: #f1f5f9;
        color: #64748b;
        padding: 14px 24px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }
    
    .btn-cancel:hover {
        background: #e5e7eb;
        color: #475569;
    }
    
    /* Room Type Cards */
    .room-type-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }
    
    .room-type-card {
        position: relative;
        padding: 16px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
    }
    
    .room-type-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.1);
    }
    
    .room-type-card input[type="radio"] {
        position: absolute;
        opacity: 0;
    }
    
    .room-type-card:has(input:checked) {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.15);
    }
    
    .room-type-card .room-icon {
        font-size: 32px;
        margin-bottom: 10px;
        display: block;
    }
    
    .room-type-card .room-title {
        font-weight: 700;
        color: #1e293b;
        font-size: 15px;
        margin-bottom: 8px;
    }
    
    .room-type-card .room-details {
        font-size: 12px;
        color: #64748b;
        line-height: 1.5;
    }
    
    .room-type-card .room-details li {
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .room-type-card .room-rate {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #e5e7eb;
        font-size: 13px;
        font-weight: 700;
    }
    
    .room-type-card.private .room-rate {
        color: #7c3aed;
    }
    
    .room-type-card.semi-private .room-rate {
        color: var(--primary-color);
    }
    
    .room-type-card.ward .room-rate {
        color: #10b981;
    }
    
    .room-type-card.icu .room-rate {
        color: #dc2626;
    }
    
    .room-type-card.isolation .room-rate {
        color: #f59e0b;
    }
    
    .price-display {
        font-size: 16px;
        font-weight: 800;
        margin-top: 4px;
    }
    
    .room-type-card.icu:has(input:checked) {
        border-color: #dc2626;
        background: linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%);
    }
    
    .room-type-card.isolation:has(input:checked) {
        border-color: #f59e0b;
        background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
    }
    
    @media (max-width: 768px) {
        .room-type-options {
            grid-template-columns: 1fr;
        }
    }
    
    /* Step-by-Step Form Styles */
    .form-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 32px;
        padding: 0 20px;
        position: relative;
    }
    
    .form-steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 10%;
        right: 10%;
        height: 2px;
        background: #e5e7eb;
        z-index: 0;
    }
    
    .step {
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }
    
    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e5e7eb;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }
    
    .step.active .step-number {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .step.completed .step-number {
        background: #10b981;
        color: white;
    }
    
    .step-title {
        font-size: 12px;
        color: #64748b;
        text-align: center;
        font-weight: 600;
    }
    
    .step.active .step-title {
        color: var(--primary-color);
    }
    
    .step.completed .step-title {
        color: #10b981;
    }
    
    .form-step {
        display: none;
    }
    
    .form-step.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .step-navigation {
        display: flex;
        justify-content: space-between;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid #e5e7eb;
    }
    
    .btn-step {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }
    
    .btn-step-next {
        background: linear-gradient(135deg, var(--gradient-1) 0%, var(--gradient-2) 100%);
        color: white;
        margin-left: auto;
    }
    
    .btn-step-next:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .btn-step-prev {
        background: #f1f5f9;
        color: #64748b;
        border: 2px solid #e5e7eb;
    }
    
    .btn-step-prev:hover {
        background: #e5e7eb;
        color: #475569;
    }
    
    .btn-step:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .validation-error {
        color: #ef4444;
        font-size: 13px;
        margin-top: 8px;
        display: none;
    }
    
    .validation-error.show {
        display: block;
    }
</style>

<div class="register-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-hospital-user"></i>
            In-Patient Registration Form
        </h1>
        <a href="<?= site_url('receptionist/patients') ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Records
        </a>
    </div>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= esc($errorMessage) ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                <?php if (is_array($errors)): ?>
                    <?php foreach ($errors as $field => $error): ?>
                        <li><?= esc(is_array($error) ? $error[0] : $error) ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><?= esc($errors) ?></li>
                <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <div class="form-card-body">
            <!-- Step Indicators -->
            <div class="form-steps">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-title">Patient Info</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-title">Admission</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-title">Medical Info</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-title">Insurance</div>
                </div>
                <div class="step" data-step="5">
                    <div class="step-number">5</div>
                    <div class="step-title">Emergency</div>
                </div>
            </div>
            
            <form method="post" action="<?= site_url('receptionist/patients/store') ?>" id="inpatientForm">
                <?= csrf_field() ?>
                <input type="hidden" name="type" value="In-Patient">
                <input type="hidden" name="visit_type" value="Consultation">

                <!-- STEP 1: PATIENT INFORMATION -->
                <div class="form-step active" data-step="1">
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-user"></i> Patient Information
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">First Name <span class="required">*</span></label>
                            <input type="text" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" 
                                   value="<?= set_value('first_name') ?>" required placeholder="Enter first name">
                            <?php if (isset($errors['first_name'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['first_name']) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" 
                                   value="<?= set_value('middle_name') ?>" placeholder="Optional">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Surname <span class="required">*</span></label>
                            <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" 
                                   value="<?= set_value('last_name') ?>" required placeholder="Enter surname">
                            <?php if (isset($errors['last_name'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['last_name']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Date of Birth <span class="required">*</span></label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control <?= isset($errors['date_of_birth']) ? 'is-invalid' : '' ?>" 
                                   value="<?= set_value('date_of_birth') ?>" max="<?= date('Y-m-d') ?>" required>
                            <?php if (isset($errors['date_of_birth'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['date_of_birth']) ?></div>
                            <?php endif; ?>
                            <div class="form-hint">Age will be calculated automatically</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Age</label>
                            <input type="number" name="age" id="age" class="form-control" 
                                   value="<?= set_value('age') ?>" readonly placeholder="Auto-calculated">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Gender <span class="required">*</span></label>
                            <select name="gender" id="patient_gender" class="form-select <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" required>
                                <option value="">-- Select Gender --</option>
                                <option value="Male" <?= set_select('gender', 'Male') ?>>Male</option>
                                <option value="Female" <?= set_select('gender', 'Female') ?>>Female</option>
                                <option value="Other" <?= set_select('gender', 'Other') ?>>Other</option>
                            </select>
                            <?php if (isset($errors['gender'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['gender']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Contact Number <span class="required">*</span></label>
                            <input type="text" name="contact" class="form-control" 
                                   value="<?= set_value('contact') ?>" required placeholder="09XX-XXX-XXXX">
                        </div>
                        
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Complete Address <span class="required">*</span></label>
                            <input type="text" name="address" class="form-control" 
                                   value="<?= set_value('address') ?>" required placeholder="House No., Street, Barangay, City/Municipality, Province">
                        </div>
                    </div>
                </div>
                </div>

                <!-- STEP 2: ADMISSION DETAILS -->
                <div class="form-step" data-step="2">
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-hospital"></i> Admission Details
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Admitting Doctor <span class="required">*</span></label>
                            <select name="doctor_id" id="admitting_doctor" class="form-select" required>
                                <option value="">-- Select Doctor --</option>
                                <?php if (!empty($doctors)): ?>
                                    <?php foreach ($doctors as $doctor): ?>
                                        <option value="<?= esc($doctor['id']) ?>" <?= set_select('doctor_id', $doctor['id']) ?> data-specialization="<?= esc(strtolower($doctor['specialization'] ?? '')) ?>">
                                            Dr. <?= esc($doctor['doctor_name']) ?> - <?= esc($doctor['specialization'] ?? 'General Practice') ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-hint" id="doctor_hint">Select from the list of available doctors</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Admission Date <span class="required">*</span></label>
                            <input type="date" name="admission_date" class="form-control <?= isset($errors['admission_date']) ? 'is-invalid' : '' ?>" 
                                   value="<?= date('Y-m-d') ?>" 
                                   max="<?= date('Y-m-d') ?>" readonly required style="background-color: #e9ecef; cursor: not-allowed;">
                            <?php if (isset($errors['admission_date'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['admission_date']) ?></div>
                            <?php else: ?>
                                <small class="form-text text-muted">Ang admission date ay awtomatikong nakatakda sa kasalukuyang petsa.</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label class="form-label">Reason for Admission <span class="required">*</span></label>
                            <textarea name="purpose" class="form-control" rows="3" required 
                                      placeholder="Enter the reason for admission or chief complaint"><?= set_value('purpose') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Room Type <span class="required">*</span></label>
                            <select name="room_type" id="room_type" class="form-select" required>
                                <option value="">-- Select Room Type --</option>
                                <option value="Private" <?= set_select('room_type', 'Private') ?>>Private Room - ₱5,000/day (1 patient, own CR)</option>
                                <option value="Semi-Private" <?= set_select('room_type', 'Semi-Private') ?>>Semi-Private Room - ₱3,000/day (2 patients, shared CR)</option>
                                <option value="Ward" <?= set_select('room_type', 'Ward', true) ?>>Ward / General Ward - ₱1,000/day (4-10+ patients, shared facilities)</option>
                                <option value="ICU" <?= set_select('room_type', 'ICU') ?>>ICU (Intensive Care Unit) - ₱8,000/day (Critical care, special equipment)</option>
                                <option value="Isolation" <?= set_select('room_type', 'Isolation') ?>>Isolation Room - ₱6,000/day (For infectious diseases, separate ventilation)</option>
                                <option value="NICU" <?= set_select('room_type', 'NICU') ?> data-age-limit="28">NICU (Neonatal Intensive Care Unit) - ₱10,000/day (For newborns 0-28 days old only)</option>
                            </select>
                            <div class="form-hint">Select the type of room for the patient's admission</div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Room Number <span class="required">*</span></label>
                            <select name="room_number" id="room_number" class="form-select" required>
                                <option value="">-- Select Room Number --</option>
                                <!-- Rooms will be populated dynamically based on selected room type -->
                            </select>
                            <input type="hidden" name="room_id" id="room_id" value="">
                            <div class="form-hint">Select room number based on selected room type</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Bed Number</label>
                            <select name="bed_number" id="bed_number" class="form-select">
                                <option value="">-- Select Bed (Optional) --</option>
                                <!-- Beds will be populated dynamically based on selected room -->
                            </select>
                            <input type="hidden" name="bed_id" id="bed_id" value="">
                            <div class="form-hint">Select bed number if room has multiple beds</div>
                        </div>
                    </div>
                    
                    <!-- Store rooms and beds data for JavaScript -->
                    <script type="application/json" id="rooms-data">
                        <?= json_encode($availableRoomsByType ?? []) ?>
                    </script>
                    <script type="application/json" id="beds-data">
                        <?= json_encode($availableBedsByRoom ?? []) ?>
                    </script>
                </div>
                </div>

                <!-- STEP 3: MEDICAL INFORMATION -->
                <div class="form-step" data-step="3">
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-notes-medical"></i> Medical Information
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label class="form-label">Existing Medical Conditions</label>
                            <textarea name="existing_conditions" class="form-control" rows="2" 
                                      placeholder="e.g., Diabetes, Hypertension, Asthma (Optional)"><?= set_value('existing_conditions') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label class="form-label">Allergies</label>
                            <textarea name="allergies" class="form-control" rows="2" 
                                      placeholder="e.g., Penicillin, Seafood, Latex (Optional)"><?= set_value('allergies') ?></textarea>
                        </div>
                    </div>
                </div>
                </div>

                <!-- STEP 4: INSURANCE INFORMATION -->
                <div class="form-step" data-step="4">
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-shield-alt"></i> Insurance Information
                    </h3>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Fill out only if the patient has insurance.
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Do you have insurance?</label>
                        <div class="insurance-toggle">
                            <label>
                                <input type="radio" name="has_insurance" value="yes" id="insurance_yes"> Yes
                            </label>
                            <label>
                                <input type="radio" name="has_insurance" value="no" id="insurance_no" checked> No
                            </label>
                        </div>
                    </div>
                    
                    <div class="insurance-fields" id="insurance_fields">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Insurance Provider(s) <span class="required">*</span></label>
                                <div class="insurance-checkboxes" id="insurance_checkboxes" style="border: 1px solid #ddd; border-radius: 4px; padding: 15px; max-height: 200px; overflow-y: auto; background: #f9f9f9;">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input insurance-checkbox" type="checkbox" name="insurance_provider[]" value="PhilHealth" id="insurance_philhealth" <?= set_checkbox('insurance_provider[]', 'PhilHealth') ?>>
                                        <label class="form-check-label" for="insurance_philhealth">PhilHealth</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input insurance-checkbox" type="checkbox" name="insurance_provider[]" value="Maxicare" id="insurance_maxicare" <?= set_checkbox('insurance_provider[]', 'Maxicare') ?>>
                                        <label class="form-check-label" for="insurance_maxicare">Maxicare</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input insurance-checkbox" type="checkbox" name="insurance_provider[]" value="Medicard" id="insurance_medicard" <?= set_checkbox('insurance_provider[]', 'Medicard') ?>>
                                        <label class="form-check-label" for="insurance_medicard">Medicard</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input insurance-checkbox" type="checkbox" name="insurance_provider[]" value="Intellicare" id="insurance_intellicare" <?= set_checkbox('insurance_provider[]', 'Intellicare') ?>>
                                        <label class="form-check-label" for="insurance_intellicare">Intellicare</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input insurance-checkbox" type="checkbox" name="insurance_provider[]" value="Pacific Cross" id="insurance_pacific" <?= set_checkbox('insurance_provider[]', 'Pacific Cross') ?>>
                                        <label class="form-check-label" for="insurance_pacific">Pacific Cross</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input insurance-checkbox" type="checkbox" name="insurance_provider[]" value="Cocolife" id="insurance_cocolife" <?= set_checkbox('insurance_provider[]', 'Cocolife') ?>>
                                        <label class="form-check-label" for="insurance_cocolife">Cocolife</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input insurance-checkbox" type="checkbox" name="insurance_provider[]" value="AXA" id="insurance_axa" <?= set_checkbox('insurance_provider[]', 'AXA') ?>>
                                        <label class="form-check-label" for="insurance_axa">AXA Philippines</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input insurance-checkbox" type="checkbox" name="insurance_provider[]" value="Sun Life" id="insurance_sunlife" <?= set_checkbox('insurance_provider[]', 'Sun Life') ?>>
                                        <label class="form-check-label" for="insurance_sunlife">Sun Life</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input insurance-checkbox" type="checkbox" name="insurance_provider[]" value="Pru Life UK" id="insurance_prulife" <?= set_checkbox('insurance_provider[]', 'Pru Life UK') ?>>
                                        <label class="form-check-label" for="insurance_prulife">Pru Life UK</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input insurance-checkbox" type="checkbox" name="insurance_provider[]" value="Other" id="insurance_other" <?= set_checkbox('insurance_provider[]', 'Other') ?>>
                                        <label class="form-check-label" for="insurance_other">Other</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Puwedeng pumili ng isa o higit pa.</small>
                            </div>
                            
                            <div class="form-group" style="width: 100%;">
                                <label class="form-label">Insurance Number / Member ID <span class="required">*</span></label>
                                <div id="insurance_numbers_container" style="border: 1px solid #ddd; border-radius: 4px; padding: 15px; background: #fff; min-height: 50px;">
                                    <p class="text-muted mb-0" id="insurance_numbers_placeholder">Pumili ng Insurance Provider para makita ang Insurance Number fields</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <!-- STEP 5: EMERGENCY CONTACT -->
                <div class="form-step" data-step="5">
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-phone-alt"></i> Emergency Contact
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Emergency Contact Name <span class="required">*</span></label>
                            <input type="text" name="emergency_name" class="form-control" 
                                   value="<?= set_value('emergency_name') ?>" required placeholder="Full name of contact person">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Relationship to Patient <span class="required">*</span></label>
                            <select name="emergency_relationship" class="form-select" required>
                                <option value="">-- Select Relationship --</option>
                                <option value="Spouse" <?= set_select('emergency_relationship', 'Spouse') ?>>Spouse</option>
                                <option value="Parent" <?= set_select('emergency_relationship', 'Parent') ?>>Parent</option>
                                <option value="Child" <?= set_select('emergency_relationship', 'Child') ?>>Child</option>
                                <option value="Sibling" <?= set_select('emergency_relationship', 'Sibling') ?>>Sibling</option>
                                <option value="Relative" <?= set_select('emergency_relationship', 'Relative') ?>>Relative</option>
                                <option value="Friend" <?= set_select('emergency_relationship', 'Friend') ?>>Friend</option>
                                <option value="Guardian" <?= set_select('emergency_relationship', 'Guardian') ?>>Guardian</option>
                                <option value="Other" <?= set_select('emergency_relationship', 'Other') ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Contact Number <span class="required">*</span></label>
                            <input type="text" name="emergency_contact" class="form-control" 
                                   value="<?= set_value('emergency_contact') ?>" required placeholder="09XX-XXX-XXXX">
                        </div>
                    </div>
                </div>
                </div>

                <!-- STEP NAVIGATION -->
                <div class="step-navigation">
                    <button type="button" class="btn-step btn-step-prev" id="prevStep" style="display: none;">
                        <i class="fas fa-arrow-left"></i> Previous
                    </button>
                    <button type="button" class="btn-step btn-step-next" id="nextStep">
                        Next <i class="fas fa-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn-step btn-step-next" id="submitForm" style="display: none;">
                        <i class="fas fa-user-plus"></i> Register In-Patient
                    </button>
                    <a href="<?= site_url('receptionist/patients') ?>" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prevent debugbar errors from blocking form functionality
    if (typeof CI !== 'undefined' && CI.debugbar) {
        try {
            // Suppress debugbar initialization errors
            const originalInit = CI.debugbar.init;
            CI.debugbar.init = function() {
                try {
                    return originalInit.apply(this, arguments);
                } catch (e) {
                    console.warn('Debugbar initialization error (non-critical):', e);
                    return false;
                }
            };
        } catch (e) {
            // Ignore debugbar errors
        }
    }
    
    // Step-by-Step Form Navigation
    let currentStep = 1;
    const totalSteps = 5;
    const formSteps = document.querySelectorAll('.form-step');
    const stepIndicators = document.querySelectorAll('.step');
    const prevBtn = document.getElementById('prevStep');
    const nextBtn = document.getElementById('nextStep');
    const submitBtn = document.getElementById('submitForm');
    
    // Ensure submit button is always clickable on last step
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.style.pointerEvents = 'auto';
        submitBtn.style.opacity = '1';
    }
    
    // Define required fields for each step
    const stepRequiredFields = {
        1: ['first_name', 'last_name', 'date_of_birth', 'gender', 'contact', 'address'],
        2: ['doctor_id', 'admission_date', 'purpose', 'room_type', 'room_number'],
        3: [], // All optional
        4: [], // All optional (insurance)
        5: ['emergency_name', 'emergency_relationship', 'emergency_contact']
    };
    
    // Function to validate a step
    function validateStep(step) {
        const requiredFields = stepRequiredFields[step];
        if (!requiredFields || requiredFields.length === 0) {
            return { valid: true, errors: [] };
        }
        
        const errors = [];
        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (!field) return;
            
            let isValid = true;
            let value = '';
            
            if (field.type === 'radio') {
                const radioGroup = document.querySelectorAll(`[name="${fieldName}"]`);
                const checked = Array.from(radioGroup).some(radio => radio.checked);
                isValid = checked;
            } else if (field.type === 'checkbox') {
                isValid = field.checked;
            } else if (field.tagName === 'SELECT') {
                value = field.value.trim();
                isValid = value !== '' && 
                         value !== '-- Select Doctor --' && 
                         value !== '-- Select Gender --' && 
                         value !== '-- Select Relationship --' &&
                         value !== '-- Select Room Type --' &&
                         value !== '-- Select Room Number --';
            } else {
                value = field.value.trim();
                isValid = value !== '';
            }
            
            if (!isValid) {
                errors.push(fieldName);
                field.classList.add('is-invalid');
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        // Special validation for insurance step
        if (step === 4) {
            const hasInsurance = document.getElementById('insurance_yes')?.checked;
            if (hasInsurance) {
                // Check for selected insurance providers (checkboxes)
                const insuranceCheckboxes = document.querySelectorAll('.insurance-checkbox');
                const selectedProviders = Array.from(insuranceCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value)
                    .filter(Boolean);
                
                if (selectedProviders.length === 0) {
                    errors.push('insurance_provider');
                    // Mark checkboxes container as invalid
                    const checkboxesContainer = document.getElementById('insurance_checkboxes');
                    if (checkboxesContainer) {
                        checkboxesContainer.style.borderColor = '#dc3545';
                    }
                } else {
                    // Check if all insurance number fields are filled for SELECTED providers only
                    const insuranceNumberInputs = document.querySelectorAll('.insurance-number-input');
                    let allFilled = true;
                    let hasVisibleInputs = false;
                    
                    insuranceNumberInputs.forEach(input => {
                        // Only check inputs that are visible (for selected providers)
                        if (input.offsetParent !== null) { // Check if element is visible
                            hasVisibleInputs = true;
                            if (!input.value.trim()) {
                                allFilled = false;
                                input.classList.add('is-invalid');
                            } else {
                                input.classList.remove('is-invalid');
                            }
                        }
                    });
                    
                    // Only validate if there are visible insurance number inputs
                    if (hasVisibleInputs && !allFilled) {
                        errors.push('insurance_number');
                    }
                    
                    // Remove invalid style from checkboxes container if providers are selected
                    const checkboxesContainer = document.getElementById('insurance_checkboxes');
                    if (checkboxesContainer) {
                        checkboxesContainer.style.borderColor = '#ddd';
                    }
                }
            } else {
                // If insurance is "No", clear any validation errors
                const checkboxesContainer = document.getElementById('insurance_checkboxes');
                if (checkboxesContainer) {
                    checkboxesContainer.style.borderColor = '#ddd';
                }
                const insuranceNumberInputs = document.querySelectorAll('.insurance-number-input');
                insuranceNumberInputs.forEach(input => {
                    input.classList.remove('is-invalid');
                });
            }
        }
        
        return {
            valid: errors.length === 0,
            errors: errors
        };
    }
    
    // Function to show step
    function showStep(step) {
        // Hide all steps
        formSteps.forEach((formStep, index) => {
            if (index + 1 === step) {
                formStep.classList.add('active');
            } else {
                formStep.classList.remove('active');
            }
        });
        
        // Update step indicators
        stepIndicators.forEach((indicator, index) => {
            const stepNum = index + 1;
            indicator.classList.remove('active', 'completed');
            if (stepNum < step) {
                indicator.classList.add('completed');
            } else if (stepNum === step) {
                indicator.classList.add('active');
            }
        });
        
        // Update navigation buttons
        if (prevBtn) {
            prevBtn.style.display = step > 1 ? 'inline-flex' : 'none';
        }
        if (nextBtn) {
            nextBtn.style.display = step < totalSteps ? 'inline-flex' : 'none';
        }
        if (submitBtn) {
            submitBtn.style.display = step === totalSteps ? 'inline-flex' : 'none';
        }
        
        // Scroll to top of form
        document.querySelector('.form-card-body').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    // Next step button
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            const validation = validateStep(currentStep);
            if (validation.valid) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            } else {
                // Show error message
                const errorMsg = document.createElement('div');
                errorMsg.className = 'validation-error show';
                errorMsg.innerHTML = '<i class="fas fa-exclamation-circle"></i> Please fill in all required fields before proceeding.';
                errorMsg.style.marginTop = '16px';
                errorMsg.style.padding = '12px';
                errorMsg.style.background = '#fee2e2';
                errorMsg.style.borderRadius = '8px';
                errorMsg.style.borderLeft = '4px solid #ef4444';
                
                // Remove existing error message
                const existingError = document.querySelector('.form-step.active .validation-error');
                if (existingError) {
                    existingError.remove();
                }
                
                // Add error message to current step
                const currentStepElement = document.querySelector('.form-step.active');
                if (currentStepElement) {
                    currentStepElement.appendChild(errorMsg);
                    
                    // Scroll to first invalid field
                    const firstInvalid = currentStepElement.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }
                }
            }
        });
    }
    
    // Previous step button
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });
    }
    
    // Remove validation errors on input
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            const errorMsg = document.querySelector('.form-step.active .validation-error');
            if (errorMsg) {
                errorMsg.remove();
            }
        });
        
        field.addEventListener('change', function() {
            this.classList.remove('is-invalid');
            const errorMsg = document.querySelector('.form-step.active .validation-error');
            if (errorMsg) {
                errorMsg.remove();
            }
        });
    });
    
    // Initialize first step
    showStep(1);
    
    // Auto-calculate age from date of birth
    const dobInput = document.getElementById('date_of_birth');
    const ageInput = document.getElementById('age');
    const doctorSelect = document.getElementById('admitting_doctor');
    const doctorHint = document.getElementById('doctor_hint');
    const roomTypeSelect = document.getElementById('room_type');
    const roomNumberSelect = document.getElementById('room_number');
    
    // Function to calculate age in days
    function calculateAgeInDays(birthDate) {
        if (!birthDate) return -1;
        const birth = new Date(birthDate);
        const today = new Date();
        const diffTime = today - birth;
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        return diffDays >= 0 ? diffDays : -1;
    }
    
    // Function to filter NICU option in Room Type dropdown
    function filterNICUOption() {
        if (!roomTypeSelect) return;
        
        const dobValue = dobInput ? dobInput.value : '';
        const ageInDays = calculateAgeInDays(dobValue);
        const isEligibleForNICU = ageInDays >= 0 && ageInDays <= 28;
        
        // Find NICU option
        const nicuOption = Array.from(roomTypeSelect.options).find(opt => opt.value === 'NICU');
        
        if (nicuOption) {
            if (isEligibleForNICU) {
                // Show NICU option
                nicuOption.style.display = '';
                nicuOption.disabled = false;
            } else {
                // Hide NICU option if currently selected, clear it first
                if (roomTypeSelect.value === 'NICU') {
                    roomTypeSelect.value = '';
                    // Clear room number dropdown
                    if (roomNumberSelect) {
                        roomNumberSelect.innerHTML = '<option value="">-- Select Room Number --</option>';
                    }
                }
                // Hide NICU option
                nicuOption.style.display = 'none';
                nicuOption.disabled = true;
            }
        }
    }
    
    // Function to filter ICU option in Room Type dropdown (hide for 0-28 days old)
    function filterICUOption() {
        if (!roomTypeSelect) return;
        
        const dobValue = dobInput ? dobInput.value : '';
        const ageInDays = calculateAgeInDays(dobValue);
        const isNewborn = ageInDays >= 0 && ageInDays <= 28;
        
        // Find ICU option
        const icuOption = Array.from(roomTypeSelect.options).find(opt => opt.value === 'ICU');
        
        if (icuOption) {
            if (isNewborn) {
                // Hide ICU option if currently selected, clear it first
                if (roomTypeSelect.value === 'ICU') {
                    roomTypeSelect.value = '';
                    // Clear room number dropdown
                    if (roomNumberSelect) {
                        roomNumberSelect.innerHTML = '<option value="">-- Select Room Number --</option>';
                    }
                }
                // Hide ICU option for newborns (0-28 days)
                icuOption.style.display = 'none';
                icuOption.disabled = true;
            } else {
                // Show ICU option for patients 29+ days old
                icuOption.style.display = '';
                icuOption.disabled = false;
            }
        }
    }
    
    // Store original doctor options data
    let originalDoctorOptions = [];
    
    // Initialize original doctor options on page load
    function initializeDoctorOptions() {
        if (doctorSelect && originalDoctorOptions.length === 0) {
            const options = Array.from(doctorSelect.options).slice(1);
            if (options.length > 0) {
                originalDoctorOptions = options.map(option => {
                    let specialization = (option.dataset.specialization || '').toLowerCase();
                    // Fallback: try to parse from text if data attribute is missing
                    if (!specialization && option.textContent) {
                        const match = option.textContent.match(/- ([^-]+)$/);
                        if (match) {
                            specialization = match[1].trim().toLowerCase();
                        }
                    }
                    return {
                        value: option.value,
                        text: option.textContent,
                        specialization: specialization
                    };
                });
            }
        }
    }
    
    // Initialize immediately
    initializeDoctorOptions();
    
    // Function to filter doctors based on age
    function filterDoctorsByAge() {
        if (!doctorSelect) return;
        
        const patientAge = ageInput ? parseInt(ageInput.value) || 0 : 0;
        const isPediatric = patientAge >= 0 && patientAge <= 17;
        const isAdult = patientAge >= 18;
        const currentValue = doctorSelect.value;
        
        // Clear current options
        doctorSelect.innerHTML = '<option value="">-- Select Doctor --</option>';
        
        // If original options not stored yet, try to initialize them
        if (originalDoctorOptions.length === 0) {
            initializeDoctorOptions();
        }
        
        // Filter and add doctors based on age
        originalDoctorOptions.forEach(doctor => {
            let shouldShow = false;
            
            if (isPediatric) {
                // Age 0-17: Show only Pediatrics doctors
                shouldShow = doctor.specialization === 'pediatrics';
                if (doctorHint && shouldShow) {
                    doctorHint.textContent = 'Select from the list of available Pediatric doctors';
                }
            } else if (isAdult) {
                // Age 18+: Show all doctors EXCEPT Pediatrics
                shouldShow = doctor.specialization !== 'pediatrics';
                if (doctorHint) {
                    doctorHint.textContent = 'Select from the list of available doctors (excluding Pediatrics)';
                }
            } else {
                // Age not determined: Show all doctors
                shouldShow = true;
                if (doctorHint) {
                    doctorHint.textContent = 'Select from the list of available doctors';
                }
            }
            
            if (shouldShow) {
                const option = document.createElement('option');
                option.value = doctor.value;
                option.textContent = doctor.text;
                option.dataset.specialization = doctor.specialization;
                doctorSelect.appendChild(option);
            }
        });
        
        // Restore selected value if it's still available
        if (currentValue) {
            const stillAvailable = Array.from(doctorSelect.options).some(opt => opt.value === currentValue);
            if (stillAvailable) {
                doctorSelect.value = currentValue;
            } else {
                doctorSelect.value = '';
            }
        }
    }
    
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
                ageInput.value = age >= 0 ? age : 0;
                
                // Filter doctors when age changes
                filterDoctorsByAge();
                
                // Filter NICU option based on age in days
                filterNICUOption();
                
                // Filter ICU option (hide for 0-28 days old)
                filterICUOption();
                
                // Update room dropdown if Ward, NICU, or ICU is selected
                const selectedRoomType = getSelectedRoomType();
                if (selectedRoomType === 'Ward') {
                    updateRoomNumberDropdown('Ward');
                } else if (selectedRoomType === 'NICU') {
                    updateRoomNumberDropdown('NICU');
                } else if (selectedRoomType === 'ICU') {
                    updateRoomNumberDropdown('ICU');
                }
            } else {
                ageInput.value = '';
                filterDoctorsByAge();
                
                // Hide NICU option when DOB is cleared
                filterNICUOption();
                
                // Show ICU option when DOB is cleared (no age restriction)
                filterICUOption();
                
                // Update room dropdown if Ward, NICU, or ICU is selected
                const selectedRoomType = getSelectedRoomType();
                if (selectedRoomType === 'Ward') {
                    updateRoomNumberDropdown('Ward');
                } else if (selectedRoomType === 'NICU') {
                    updateRoomNumberDropdown('NICU');
                } else if (selectedRoomType === 'ICU') {
                    updateRoomNumberDropdown('ICU');
                }
            }
        });
        
        // Also filter when age input changes directly
        ageInput.addEventListener('input', function() {
            filterDoctorsByAge();
            // Filter NICU option when age changes
            filterNICUOption();
            // Filter ICU option when age changes
            filterICUOption();
            // Update room dropdown if NICU or ICU is selected
            const selectedRoomType = getSelectedRoomType();
            if (selectedRoomType === 'NICU') {
                updateRoomNumberDropdown('NICU');
            } else if (selectedRoomType === 'ICU') {
                updateRoomNumberDropdown('ICU');
            }
        });
        
        // Ensure original options are initialized before filtering
        initializeDoctorOptions();
        
        // Filter doctors on page load
        filterDoctorsByAge();
        
        // Trigger on page load if DOB has value
        if (dobInput.value) {
            dobInput.dispatchEvent(new Event('change'));
        }
    }
    
    // Insurance toggle
    const insuranceYes = document.getElementById('insurance_yes');
    const insuranceNo = document.getElementById('insurance_no');
    const insuranceFields = document.getElementById('insurance_fields');
    const insuranceProvider = document.getElementById('insurance_provider');
    const insuranceNumber = document.getElementById('insurance_number');
    
    function toggleInsuranceFields() {
        const insuranceCheckboxes = document.querySelectorAll('.insurance-checkbox');
        const insuranceNumbersContainer = document.getElementById('insurance_numbers_container');
        
        if (insuranceYes && insuranceYes.checked) {
            insuranceFields.classList.add('show');
            // Mark checkboxes as required (at least one must be checked)
            insuranceCheckboxes.forEach(checkbox => {
                checkbox.required = true;
            });
        } else {
            insuranceFields.classList.remove('show');
            // Uncheck all checkboxes and remove required
            insuranceCheckboxes.forEach(checkbox => {
                checkbox.required = false;
                checkbox.checked = false;
            });
            // Clear insurance numbers container
            if (insuranceNumbersContainer) {
                insuranceNumbersContainer.innerHTML = '';
                const placeholder = document.createElement('p');
                placeholder.className = 'text-muted mb-0';
                placeholder.id = 'insurance_numbers_placeholder';
                placeholder.textContent = 'Pumili ng Insurance Provider para makita ang Insurance Number fields';
                insuranceNumbersContainer.appendChild(placeholder);
            }
        }
    }
    
    if (insuranceYes) insuranceYes.addEventListener('change', toggleInsuranceFields);
    if (insuranceNo) insuranceNo.addEventListener('change', toggleInsuranceFields);
    
    // Auto-fill insurance number based on provider (supports multiple selection via checkboxes)
    // Each provider gets its own separate Insurance Number field
    const insuranceCheckboxes = document.querySelectorAll('.insurance-checkbox');
    const insuranceNumbersContainer = document.getElementById('insurance_numbers_container');
    const insuranceNumbersPlaceholder = document.getElementById('insurance_numbers_placeholder');
    
    if (insuranceCheckboxes.length > 0 && insuranceNumbersContainer) {
        const insuranceFormats = {
            'PhilHealth': () => 'PH-' + Math.floor(100000000000 + Math.random() * 900000000000).toString(),
            'Maxicare': () => 'MC-' + Math.floor(10000000 + Math.random() * 90000000).toString(),
            'Medicard': () => 'MD-' + Math.floor(1000000 + Math.random() * 9000000).toString(),
            'Intellicare': () => 'IC-' + Math.floor(10000000 + Math.random() * 90000000).toString(),
            'Pacific Cross': () => 'PC-' + Math.floor(1000000 + Math.random() * 9000000).toString(),
            'Cocolife': () => 'CL-' + Math.floor(1000000 + Math.random() * 9000000).toString(),
            'AXA': () => 'AXA-' + Math.floor(1000000 + Math.random() * 9000000).toString(),
            'Sun Life': () => 'SL-' + Math.floor(1000000 + Math.random() * 9000000).toString(),
            'Pru Life UK': () => 'PRU-' + Math.floor(1000000 + Math.random() * 9000000).toString(),
            'Other': () => ''
        };

        // Store generated numbers to prevent regeneration when unchecking/checking
        const generatedNumbers = {};

        function updateInsuranceNumbers() {
            // Get all checked insurance providers
            const selectedProviders = Array.from(insuranceCheckboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value)
                .filter(Boolean);
            
            // Clear container
            insuranceNumbersContainer.innerHTML = '';
            
            if (selectedProviders.length === 0) {
                const placeholder = document.createElement('p');
                placeholder.className = 'text-muted mb-0';
                placeholder.id = 'insurance_numbers_placeholder';
                placeholder.textContent = 'Pumili ng Insurance Provider para makita ang Insurance Number fields';
                insuranceNumbersContainer.appendChild(placeholder);
                return;
            }

            // Create a field for each selected provider
            selectedProviders.forEach(provider => {
                const fieldWrapper = document.createElement('div');
                fieldWrapper.className = 'mb-3';
                fieldWrapper.id = `insurance_field_${provider.replace(/\s+/g, '_')}`;
                
                const label = document.createElement('label');
                label.className = 'form-label';
                label.textContent = `${provider} - Insurance Number / Member ID`;
                label.htmlFor = `insurance_number_${provider.replace(/\s+/g, '_')}`;
                
                const input = document.createElement('input');
                input.type = 'text';
                input.name = `insurance_number[${provider}]`;
                input.id = `insurance_number_${provider.replace(/\s+/g, '_')}`;
                input.className = 'form-control insurance-number-input';
                input.required = true;
                
                // Generate or reuse number
                if (!generatedNumbers[provider]) {
                    if (insuranceFormats[provider]) {
                        generatedNumbers[provider] = insuranceFormats[provider]();
                    } else {
                        generatedNumbers[provider] = '';
                    }
                }
                
                input.value = generatedNumbers[provider];
                
                // Handle "Other" provider - make it editable
                if (provider === 'Other') {
                    input.readOnly = false;
                    input.placeholder = 'Enter Member ID for Other';
                    input.value = generatedNumbers[provider] || '';
                } else {
                    input.readOnly = true;
                    input.placeholder = 'Auto-generated';
                }
                
                fieldWrapper.appendChild(label);
                fieldWrapper.appendChild(input);
                insuranceNumbersContainer.appendChild(fieldWrapper);
            });
        }

        // Add event listener to all checkboxes
        insuranceCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // If unchecking, remove the generated number for that provider
                if (!checkbox.checked) {
                    delete generatedNumbers[checkbox.value];
                }
                updateInsuranceNumbers();
            });
        });
        
        // Initialize on page load
        updateInsuranceNumbers();
    }
    
    // Initialize
    toggleInsuranceFields();
    
    // Room Type and Room Number handling
    const bedNumberSelect = document.getElementById('bed_number');
    const roomsDataElement = document.getElementById('rooms-data');
    const bedsDataElement = document.getElementById('beds-data');
    
    // Helper function to get selected room type
    function getSelectedRoomType() {
        return roomTypeSelect ? roomTypeSelect.value : '';
    }
    
    let roomsData = {};
    let bedsData = {};
    
    if (roomsDataElement) {
        try {
            roomsData = JSON.parse(roomsDataElement.textContent);
        } catch (e) {
            console.error('Error parsing rooms data:', e);
        }
    }
    
    if (bedsDataElement) {
        try {
            bedsData = JSON.parse(bedsDataElement.textContent);
        } catch (e) {
            console.error('Error parsing beds data:', e);
        }
    }
    
    function updateRoomNumberDropdown(roomType) {
        if (!roomNumberSelect) return;
        
        // Clear existing options except the first one
        roomNumberSelect.innerHTML = '<option value="">-- Select Room Number --</option>';
        
        // Clear bed dropdown
        if (bedNumberSelect) {
            bedNumberSelect.innerHTML = '<option value="">-- Select Bed (Optional) --</option>';
            document.getElementById('bed_id').value = '';
        }
        
        if (!roomType || !roomsData[roomType] || roomsData[roomType].length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No available rooms for this type';
            option.disabled = true;
            roomNumberSelect.appendChild(option);
            return;
        }
        
        // Get patient gender and age for filtering
        const genderSelect = document.getElementById('patient_gender');
        const ageInput = document.getElementById('age');
        const patientGender = genderSelect ? genderSelect.value.trim() : '';
        const patientAge = ageInput ? parseInt(ageInput.value) : -1;
        
        // Debug: Uncomment to see filtering parameters
        // console.log('Room Filtering - Gender:', patientGender, 'Age:', patientAge, 'Room Type:', roomType);
        
        // Filter rooms based on room type, patient gender, and age
        let filteredRooms = roomsData[roomType];
        
        // Special filtering for NICU room type (0-28 days old only)
        if (roomType === 'NICU') {
            const dobValue = dobInput ? dobInput.value : '';
            const ageInDays = calculateAgeInDays(dobValue);
            const isEligibleForNICU = ageInDays >= 0 && ageInDays <= 28;
            
            if (!isEligibleForNICU) {
                // Patient is not eligible for NICU (29+ days old)
                filteredRooms = [];
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'NICU is only available for patients 0-28 days old';
                option.disabled = true;
                roomNumberSelect.appendChild(option);
                return;
            }
            // If eligible, show all NICU rooms (no further filtering needed)
        }
        
        // Special filtering ONLY for Ward room type
        // Ward Type restrictions apply only when "Ward" is selected
        // Other room types (Private, Semi-Private, ICU, Isolation) show all rooms regardless of age/gender
        if (roomType === 'Ward') {
            filteredRooms = roomsData[roomType].filter(room => {
                // Get ward name and normalize to lowercase for comparison
                const wardName = room.ward || '';
                const ward = wardName.toLowerCase().trim();
                
                // Skip if no ward name
                if (!ward) {
                    return false;
                }
                
                const isPediatric = patientAge >= 0 && patientAge <= 17;
                const isAdult = patientAge >= 18;
                
                // Debug: Uncomment to debug filtering
                // console.log('Filtering room:', wardName, 'Ward (lowercase):', ward, 'Gender:', patientGender, 'Age:', patientAge, 'IsPediatric:', isPediatric, 'IsAdult:', isAdult);
                
                // Age 0-17 (Pediatric Patients)
                if (isPediatric) {
                    // Check exclusions FIRST
                    if (patientGender === 'Male' || patientGender.toLowerCase() === 'male') {
                        // Female Ward should NOT appear for male pediatric patients
                        if (ward.includes('female') && !ward.includes('male')) {
                            return false;
                        }
                        // Show: Pedia Ward, Male Ward, General Ward
                        // Check explicitly for each ward type
                        const isPediaWard = ward.includes('pedia');
                        const isMaleWard = ward.includes('male') && !ward.includes('female');
                        const isGeneralWard = ward.includes('general');
                        
                        if (isPediaWard || isMaleWard || isGeneralWard) {
                            return true;
                        }
                        return false;
                    }
                    
                    if (patientGender === 'Female' || patientGender.toLowerCase() === 'female') {
                        // Male Ward should NOT appear for female pediatric patients
                        // Check if it's a Male Ward (contains 'male' but NOT 'female')
                        if (ward.includes('male') && !ward.includes('female')) {
                            return false;
                        }
                        
                        // Show: Pedia Ward, Female Ward, General Ward
                        // Check explicitly for each ward type
                        const isPediaWard = ward === 'pedia ward' || ward.includes('pedia');
                        const isFemaleWard = ward === 'female ward' || ward.includes('female');
                        const isGeneralWard = ward === 'general ward' || ward.includes('general');
                        
                        // Debug: Uncomment to debug filtering
                        // console.log('Female Pediatric Filter - Ward:', wardName, 'isPedia:', isPediaWard, 'isFemale:', isFemaleWard, 'isGeneral:', isGeneralWard, 'Result:', (isPediaWard || isFemaleWard || isGeneralWard));
                        
                        if (isPediaWard || isFemaleWard || isGeneralWard) {
                            return true;
                        }
                        // Don't show other wards
                        return false;
                    }
                    
                    // If gender not specified: Show Pedia Room and General Ward only
                    if (ward.includes('pedia') || ward.includes('general')) {
                        return true;
                    }
                    
                    // Don't show other wards for pediatric patients
                    return false;
                }
                
                // Age 18+ (Adult Patients)
                if (isAdult) {
                    // Male patients: Male Ward and General Ward appear; Female Ward and Pedia Room should NOT appear
                    if (patientGender === 'Male' || patientGender.toLowerCase() === 'male') {
                        // Don't show Female Ward or Pedia Room for adult males
                        if (ward.includes('female') || ward.includes('pedia')) {
                            return false;
                        }
                        // Show: Male Ward, General Ward
                        // Check explicitly for each ward type
                        const isMaleWard = ward === 'male ward' || (ward.includes('male') && !ward.includes('female'));
                        const isGeneralWard = ward === 'general ward' || ward.includes('general');
                        
                        // Debug: Uncomment to debug filtering
                        // console.log('Male Adult Filter - Ward:', wardName, 'isMale:', isMaleWard, 'isGeneral:', isGeneralWard, 'Result:', (isMaleWard || isGeneralWard));
                        
                        if (isMaleWard || isGeneralWard) {
                            return true;
                        }
                        return false;
                    }
                    
                    // Female patients: Female Ward and General Ward appear; Male Ward and Pedia Room should NOT appear
                    if (patientGender === 'Female' || patientGender.toLowerCase() === 'female') {
                        // Don't show Male Ward or Pedia Room for adult females
                        // Check if it's a Male Ward (contains 'male' but NOT 'female') or Pedia Ward
                        if ((ward.includes('male') && !ward.includes('female')) || ward.includes('pedia')) {
                            return false;
                        }
                        // Show: Female Ward, General Ward
                        // Check explicitly for each ward type
                        const isFemaleWard = ward === 'female ward' || ward.includes('female');
                        const isGeneralWard = ward === 'general ward' || ward.includes('general');
                        
                        // Debug: Uncomment to debug filtering
                        // console.log('Female Adult Filter - Ward:', wardName, 'isFemale:', isFemaleWard, 'isGeneral:', isGeneralWard, 'Result:', (isFemaleWard || isGeneralWard));
                        
                        if (isFemaleWard || isGeneralWard) {
                            return true;
                        }
                        // Don't show other wards
                        return false;
                    }
                    
                    // Other gender: Show General Ward only
                    if (ward.includes('general')) {
                        return true;
                    }
                    return false;
                }
                
                // Default: show General Ward if age not determined
                return ward.includes('general');
            });
        }
        // For other room types (Private, Semi-Private, ICU, Isolation), show all rooms
        
        // Add filtered rooms to dropdown
        if (filteredRooms.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No available rooms for this type';
            option.disabled = true;
            roomNumberSelect.appendChild(option);
            return;
        }
        
        filteredRooms.forEach(room => {
            // STRICT CLIENT-SIDE VALIDATION: Skip occupied rooms
            const roomStatus = room.status ? room.status.toLowerCase().trim() : '';
            if (roomStatus === 'occupied') {
                return; // Skip occupied rooms
            }
            if (room.current_patient_id && room.current_patient_id !== '' && room.current_patient_id !== null) {
                return; // Skip rooms with assigned patients
            }
            // Only show available rooms (status = 'available' or null/empty)
            if (roomStatus && roomStatus !== 'available') {
                return; // Skip non-available rooms
            }
            
            const option = document.createElement('option');
            // Use room_number as value for display, but store room_id separately
            option.value = room.room_number || 'Room ' + (room.id || '');
            option.dataset.roomId = room.id || room.room_id || '';
            option.dataset.roomNumber = room.room_number || '';
            option.dataset.price = room.price || 0;
            
            let roomText = room.room_number || 'Room ' + (room.id || '');
            if (room.ward) {
                roomText += ' - ' + room.ward;
            }
            if (room.bed_count) {
                roomText += ' (' + room.bed_count + ' bed' + (room.bed_count > 1 ? 's' : '') + ')';
            }
            if (room.price && parseFloat(room.price) > 0) {
                const price = parseFloat(room.price).toLocaleString('en-PH', {
                    style: 'currency',
                    currency: 'PHP',
                    minimumFractionDigits: 2
                });
                roomText += ' - ' + price + '/day';
            }
            
            option.textContent = roomText;
            roomNumberSelect.appendChild(option);
        });
        
        // Update price display in room type cards
        updateRoomTypePrices(roomType);
    }
    
    function updateBedDropdown(roomId) {
        if (!bedNumberSelect || !roomId) {
            if (bedNumberSelect) {
                bedNumberSelect.innerHTML = '<option value="">-- Select Bed (Optional) --</option>';
                document.getElementById('bed_id').value = '';
            }
            return;
        }
        
        // Clear existing options
        bedNumberSelect.innerHTML = '<option value="">-- Select Bed (Optional) --</option>';
        document.getElementById('bed_id').value = '';
        
        // First try to get beds from bedsData (separate beds data)
        let beds = bedsData[roomId] || [];
        
        // If not found, try to get from room data
        if (beds.length === 0) {
            const selectedRoom = findRoomById(roomId);
            if (selectedRoom && selectedRoom.available_beds && selectedRoom.available_beds.length > 0) {
                beds = selectedRoom.available_beds;
            }
        }
        
        if (beds.length === 0) {
            // Check if room has multiple beds (bed_count > 1)
            const selectedRoom = findRoomById(roomId);
            if (selectedRoom && selectedRoom.bed_count && selectedRoom.bed_count > 1) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No beds available - Please select another room';
                option.disabled = true;
                bedNumberSelect.appendChild(option);
            }
            // If single bed room, bed selection is optional
            return;
        }
        
        // Add beds to dropdown (only available beds)
        beds.forEach(bed => {
            // STRICT CLIENT-SIDE VALIDATION: Skip occupied beds
            const bedStatus = bed.status ? bed.status.toLowerCase().trim() : '';
            if (bedStatus === 'occupied') {
                return; // Skip occupied beds
            }
            if (bed.current_patient_id && bed.current_patient_id !== '' && bed.current_patient_id !== null) {
                return; // Skip beds with assigned patients
            }
            // Only show available beds (status = 'available' or null/empty)
            if (bedStatus && bedStatus !== 'available') {
                return; // Skip non-available beds
            }
            
            const option = document.createElement('option');
            option.value = bed.bed_number || '';
            option.dataset.bedId = bed.id || '';
            option.dataset.bedNumber = bed.bed_number || '';
            option.textContent = 'Bed ' + (bed.bed_number || bed.id || '');
            bedNumberSelect.appendChild(option);
        });
    }
    
    function findRoomById(roomId) {
        for (const roomType in roomsData) {
            const room = roomsData[roomType].find(r => (r.id || r.room_id) == roomId);
            if (room) return room;
        }
        return null;
    }
    
    function updateRoomTypePrices(selectedRoomType) {
        // Update price display in room type cards based on actual room prices
        const priceMap = {
            'Private': 'price-private',
            'Semi-Private': 'price-semi-private',
            'Ward': 'price-ward',
            'ICU': 'price-icu',
            'Isolation': 'price-isolation'
        };
        
        if (roomsData[selectedRoomType] && roomsData[selectedRoomType].length > 0) {
            const firstRoom = roomsData[selectedRoomType][0];
            if (firstRoom.price && parseFloat(firstRoom.price) > 0) {
                const priceElement = document.getElementById(priceMap[selectedRoomType]);
                if (priceElement) {
                    const price = parseFloat(firstRoom.price).toLocaleString('en-PH', {
                        style: 'currency',
                        currency: 'PHP',
                        minimumFractionDigits: 0
                    });
                    priceElement.textContent = price;
                }
            }
        }
    }
    
    // Update room_id hidden field when room is selected
    if (roomNumberSelect) {
        roomNumberSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const roomIdField = document.getElementById('room_id');
            const roomId = selectedOption ? selectedOption.dataset.roomId : '';
            
            if (roomIdField && roomId) {
                roomIdField.value = roomId;
            }
            
            // Update bed dropdown when room changes
            updateBedDropdown(roomId);
        });
    }
    
    // Update bed_id hidden field when bed is selected
    if (bedNumberSelect) {
        bedNumberSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const bedIdField = document.getElementById('bed_id');
            
            if (bedIdField && selectedOption && selectedOption.dataset.bedId) {
                bedIdField.value = selectedOption.dataset.bedId;
            } else if (bedIdField) {
                bedIdField.value = '';
            }
        });
    }
    
    // Listen for room type changes
    if (roomTypeSelect) {
        roomTypeSelect.addEventListener('change', function() {
            if (this.value) {
                // If NICU is selected, check eligibility first
                if (this.value === 'NICU') {
                    const dobValue = dobInput ? dobInput.value : '';
                    const ageInDays = calculateAgeInDays(dobValue);
                    const isEligibleForNICU = ageInDays >= 0 && ageInDays <= 28;
                    
                    if (!isEligibleForNICU) {
                        alert('NICU is only available for patients 0-28 days old. Please select a different room type.');
                        this.value = '';
                        if (roomNumberSelect) {
                            roomNumberSelect.innerHTML = '<option value="">-- Select Room Number --</option>';
                        }
                        return;
                    }
                }
                updateRoomNumberDropdown(this.value);
            } else {
                // Clear room number dropdown if no room type selected
                if (roomNumberSelect) {
                    roomNumberSelect.innerHTML = '<option value="">-- Select Room Number --</option>';
                }
                if (bedNumberSelect) {
                    bedNumberSelect.innerHTML = '<option value="">-- Select Bed (Optional) --</option>';
                }
            }
        });
    }
    
    // Listen for gender changes - update room dropdown if Ward is selected
    const genderSelect = document.getElementById('patient_gender');
    if (genderSelect) {
        genderSelect.addEventListener('change', function() {
            const selectedRoomType = getSelectedRoomType();
            if (selectedRoomType === 'Ward') {
                updateRoomNumberDropdown('Ward');
            }
        });
    }
    
    // Listen for age changes - update room dropdown if Ward is selected
    if (ageInput) {
        ageInput.addEventListener('input', function() {
            const selectedRoomType = getSelectedRoomType();
            if (selectedRoomType === 'Ward') {
                updateRoomNumberDropdown('Ward');
            }
        });
        
        ageInput.addEventListener('change', function() {
            const selectedRoomType = getSelectedRoomType();
            if (selectedRoomType === 'Ward') {
                updateRoomNumberDropdown('Ward');
            }
        });
    }
    
    // Update room dropdown when DOB changes (since age is auto-calculated)
    if (dobInput && ageInput) {
        dobInput.addEventListener('change', function() {
            // Wait a bit for age to be calculated
            setTimeout(function() {
                const selectedRoomType = getSelectedRoomType();
                if (selectedRoomType === 'Ward') {
                    updateRoomNumberDropdown('Ward');
                }
            }, 100);
        });
    }
    
    // Initialize room dropdown based on default selected room type
    const selectedRoomType = getSelectedRoomType();
    if (selectedRoomType) {
        updateRoomNumberDropdown(selectedRoomType);
    }
    
    // Initialize prices for all room types on page load
    function initializeRoomPrices() {
        const roomTypes = ['Private', 'Semi-Private', 'Ward', 'ICU', 'Isolation'];
        roomTypes.forEach(roomType => {
            if (roomsData[roomType] && roomsData[roomType].length > 0) {
                const firstRoom = roomsData[roomType][0];
                if (firstRoom.price && parseFloat(firstRoom.price) > 0) {
                    const priceMap = {
                        'Private': 'price-private',
                        'Semi-Private': 'price-semi-private',
                        'Ward': 'price-ward',
                        'ICU': 'price-icu',
                        'Isolation': 'price-isolation'
                    };
                    
                    const priceElement = document.getElementById(priceMap[roomType]);
                    if (priceElement) {
                        const price = parseFloat(firstRoom.price).toLocaleString('en-PH', {
                            style: 'currency',
                            currency: 'PHP',
                            minimumFractionDigits: 0
                        });
                        priceElement.textContent = price;
                    }
                }
            }
        });
    }
    
        // Initialize prices on page load
        initializeRoomPrices();
        
        // Initialize NICU and ICU filtering on page load
        filterNICUOption();
        filterICUOption();
    
    // Force enable submit button and add direct click handler
    if (submitBtn) {
        // Remove any disabled attributes
        submitBtn.removeAttribute('disabled');
        submitBtn.disabled = false;
        
        // Force enable button styles
        submitBtn.style.pointerEvents = 'auto';
        submitBtn.style.opacity = '1';
        submitBtn.style.cursor = 'pointer';
        submitBtn.style.display = 'inline-flex';
        
        // Add direct click handler as primary method
        submitBtn.addEventListener('click', function(e) {
            console.log('Submit button clicked directly');
            e.preventDefault();
            e.stopPropagation();
            
            const form = document.getElementById('inpatientForm');
            if (!form) return false;
            
            // Validate all steps
            let allValid = true;
            let firstInvalidStep = null;
            for (let step = 1; step <= totalSteps; step++) {
                const validation = validateStep(step);
                console.log('Step', step, 'validation:', validation);
                if (!validation.valid) {
                    allValid = false;
                    if (!firstInvalidStep) firstInvalidStep = step;
                }
            }
            
            if (!allValid && firstInvalidStep) {
                currentStep = firstInvalidStep;
                showStep(firstInvalidStep);
                alert('Please fill in all required fields before submitting.');
                return false;
            }
            
            // Additional insurance validation
            const insuranceYes = document.getElementById('insurance_yes');
            if (insuranceYes && insuranceYes.checked) {
                const insuranceCheckboxes = document.querySelectorAll('.insurance-checkbox');
                const selectedProviders = Array.from(insuranceCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value)
                    .filter(Boolean);
                    
                if (selectedProviders.length === 0) {
                    currentStep = 4;
                    showStep(4);
                    alert('Please select at least one Insurance Provider.');
                    return false;
                }
                
                // Check insurance numbers
                const insuranceNumberInputs = document.querySelectorAll('.insurance-number-input');
                let allFilled = true;
                let hasVisibleInputs = false;
                
                insuranceNumberInputs.forEach(input => {
                    if (input.offsetParent !== null) {
                        hasVisibleInputs = true;
                        if (!input.value.trim()) {
                            allFilled = false;
                        }
                    }
                });
                
                if (hasVisibleInputs && !allFilled) {
                    currentStep = 4;
                    showStep(4);
                    alert('Please enter the Insurance Number / Member ID for all selected providers.');
                    return false;
                }
            }
            
            // If all validations passed, submit the form
            console.log('All validations passed, submitting form');
            form.submit();
            return false;
        }, true); // Use capture phase to ensure it fires first
    }
    
    // Form validation before submit
    const form = document.getElementById('inpatientForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validate all steps before submitting
            let allValid = true;
            for (let step = 1; step <= totalSteps; step++) {
                const validation = validateStep(step);
                if (!validation.valid) {
                    allValid = false;
                    // Show the step with errors
                    currentStep = step;
                    showStep(step);
                    break;
                }
            }
            
            if (!allValid) {
                e.preventDefault();
                return false;
            }
            
            // Check if insurance is selected but fields are empty
            if (insuranceYes && insuranceYes.checked) {
                const insuranceCheckboxes = document.querySelectorAll('.insurance-checkbox');
                const selectedProviders = Array.from(insuranceCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value)
                    .filter(Boolean);
                if (selectedProviders.length === 0) {
                    e.preventDefault();
                    currentStep = 4;
                    showStep(4);
                    alert('Please select at least one Insurance Provider.');
                    if (insuranceCheckboxes.length > 0) {
                        insuranceCheckboxes[0].focus();
                    }
                    return false;
                }
                
                // Check if all insurance number fields are filled (only for visible/selected providers)
                const insuranceNumberInputs = document.querySelectorAll('.insurance-number-input');
                let allFilled = true;
                let emptyField = null;
                let hasVisibleInputs = false;
                
                insuranceNumberInputs.forEach(input => {
                    // Only check inputs that are visible (for selected providers)
                    if (input.offsetParent !== null) { // Check if element is visible
                        hasVisibleInputs = true;
                        if (!input.value.trim()) {
                            allFilled = false;
                            if (!emptyField) emptyField = input;
                        }
                    }
                });
                
                // Only validate if there are visible insurance number inputs
                if (hasVisibleInputs && !allFilled) {
                    e.preventDefault();
                    currentStep = 4;
                    showStep(4);
                    alert('Please enter the Insurance Number / Member ID for all selected providers.');
                    if (emptyField) emptyField.focus();
                    return false;
                }
            }
        });
    }
});
</script>

<?= $this->endSection() ?>
