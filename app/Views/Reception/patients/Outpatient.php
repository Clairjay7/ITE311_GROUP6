<?php
helper('form');
$errors = session('errors') ?? [];
?>
<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Register Out-Patient<?= $this->endSection() ?>

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
    
    .page-header p {
        margin: 8px 0 0;
        font-size: 14px;
        opacity: 0.9;
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
    
    .form-label .optional {
        color: #94a3b8;
        font-weight: 400;
        font-size: 12px;
        margin-left: 4px;
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
    
    /* Visit Type Cards */
    .visit-type-options {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 16px;
    }
    
    .visit-type-card {
        position: relative;
        padding: 16px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
    }
    
    .visit-type-card:hover {
        border-color: var(--primary-color);
    }
    
    .visit-type-card input[type="radio"] {
        position: absolute;
        opacity: 0;
    }
    
    .visit-type-card:has(input:checked) {
        border-color: var(--primary-color);
        background: #e8f5e9;
    }
    
    .visit-type-card i {
        font-size: 28px;
        color: var(--primary-color);
        margin-bottom: 8px;
        display: block;
    }
    
    .visit-type-card .title {
        font-weight: 600;
        color: #1e293b;
        font-size: 14px;
    }
    
    /* Triage Category */
    .triage-options {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .triage-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .triage-option input[type="radio"] {
        width: 16px;
        height: 16px;
    }
    
    .triage-option.non-urgent:has(input:checked) {
        border-color: #10b981;
        background: #ecfdf5;
    }
    
    .triage-option.less-urgent:has(input:checked) {
        border-color: #f59e0b;
        background: #fef3c7;
    }
    
    .triage-option.urgent:has(input:checked) {
        border-color: #ef4444;
        background: #fee2e2;
    }
    
    /* Vital Signs Grid */
    .vitals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
    }
    
    .vital-input {
        position: relative;
    }
    
    .vital-input .unit {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 12px;
        pointer-events: none;
    }
    
    .vital-input input {
        padding-right: 50px;
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
    
    /* Type Badge */
    .type-badge {
        display: inline-block;
        padding: 8px 16px;
        background: linear-gradient(135deg, var(--gradient-1) 0%, var(--gradient-2) 100%);
        color: white;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }
    
    @media (max-width: 768px) {
        .visit-type-options {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="register-container">
    <div class="page-header">
        <div>
            <h1>
                <i class="fas fa-user-plus"></i>
                Out-Patient Registration Form
            </h1>
            <p>For patients receiving consultation, follow-up, or medical check-up services</p>
        </div>
        <a href="<?= site_url('receptionist/patients') ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Records
        </a>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= esc(session()->getFlashdata('error')) ?>
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
                    <div class="step-title">Visit Details</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-title">Additional</div>
                </div>
            </div>
            
            <form method="post" action="<?= site_url('receptionist/patients/store') ?>" id="outpatientForm">
        <?= csrf_field() ?>
                <input type="hidden" name="type" value="Out-Patient">

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
                                   value="<?= set_value('first_name') ?>" required placeholder="Enter patient's first name">
                            <?php if (isset($errors['first_name'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['first_name']) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" 
                                   value="<?= set_value('middle_name') ?>" placeholder="Enter middle name">
          </div>
                        
                        <div class="form-group">
                            <label class="form-label">Surname <span class="required">*</span></label>
                            <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" 
                                   value="<?= set_value('last_name') ?>" required placeholder="Enter patient's surname">
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
                            <label class="form-label">Sex <span class="required">*</span></label>
                            <select name="gender" class="form-select <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" required>
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
                            <div class="form-hint">Please provide an active contact number</div>
                        </div>
                        
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Address <span class="required">*</span></label>
                            <input type="text" name="address" class="form-control" 
                                   value="<?= set_value('address') ?>" required placeholder="Complete home address of the patient">
                        </div>
          </div>
          </div>
                </div>

                <!-- STEP 2: VISIT DETAILS -->
                <div class="form-step" data-step="2">
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-clipboard-list"></i> Visit Details
                    </h3>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label">Visit Type <span class="required">*</span></label>
                        <div class="form-hint" style="margin-bottom: 12px;">Please select the purpose of the patient's Out-Patient visit</div>
                        
                        <div class="visit-type-options">
                            <label class="visit-type-card">
                                <input type="radio" name="visit_type" value="Consultation" <?= set_radio('visit_type', 'Consultation', true) ?> required>
                                <i class="fas fa-stethoscope"></i>
                                <span class="title">Consultation</span>
                            </label>
                            
                            <label class="visit-type-card">
                                <input type="radio" name="visit_type" value="Follow-up" <?= set_radio('visit_type', 'Follow-up') ?>>
                                <i class="fas fa-redo"></i>
                                <span class="title">Follow-Up</span>
                            </label>
                            
                            <label class="visit-type-card">
                                <input type="radio" name="visit_type" value="Check-up" <?= set_radio('visit_type', 'Check-up') ?>>
                                <i class="fas fa-heartbeat"></i>
                                <span class="title">Medical Check-Up</span>
                            </label>
          </div>
        </div>
        
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Attending Doctor <span class="required" id="doctor_required_indicator">*</span></label>
              <select name="doctor_id" id="doctor_id" class="form-select" onchange="enableAppointmentDayDropdown(this.value)">
                                <option value="">-- Choose Doctor --</option>
                <?php if (!empty($doctors)): ?>
                  <?php foreach ($doctors as $doctor): ?>
                    <option value="<?= esc($doctor['id']) ?>" <?= set_select('doctor_id', (string)$doctor['id']) ?> data-specialization="<?= esc(strtolower($doctor['specialization'] ?? '')) ?>">
                                            Dr. <?= esc($doctor['doctor_name'] ?? $doctor['id']) ?>
                      <?php if (!empty($doctor['specialization'])): ?>
                        - <?= esc($doctor['specialization']) ?>
                      <?php endif; ?>
                    </option>
                  <?php endforeach; ?>
                <?php else: ?>
                                    <option value="" disabled>No doctors available</option>
                <?php endif; ?>
              </select>
                            <div class="form-hint" id="doctor_hint">Please choose the doctor assigned for this visit</div>
                            
                            <!-- Doctor Schedule Display -->
                            <div id="doctor_schedule_display" style="display: none; margin-top: 12px; padding: 12px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #10b981;">
                                <div style="font-weight: 600; color: #10b981; margin-bottom: 8px;">
                                    <i class="fas fa-calendar-alt"></i> Doctor's Schedule for Selected Date:
                                </div>
                                <div id="schedule_times" style="color: #475569; font-size: 13px;"></div>
                                <div id="booked_slots" style="margin-top: 8px; color: #ef4444; font-size: 12px; font-weight: 600;"></div>
                            </div>
            </div>
                        
                        <div class="form-group">
              <label class="form-label">Appointment Day <span class="required">*</span></label>
                            <select name="appointment_day" id="appointment_day" class="form-select" required disabled style="pointer-events: none; opacity: 0.6;">
                                <option value="">-- Select Day --</option>
                                <!-- Options will be populated dynamically via JavaScript -->
                            </select>
                            <input type="hidden" name="appointment_date" id="appointment_date" value="">
                            <div class="form-hint" id="date_hint">Please select a doctor first, then choose a day (Monday-Friday)</div>
                            
                            <!-- Doctor Schedule Display -->
                            <div id="doctor_schedule_display" style="display: none; margin-top: 12px; padding: 12px; background: #e8f5e9; border-radius: 8px; border-left: 4px solid #2e7d32;">
                                <div style="font-weight: 600; color: #2e7d32; margin-bottom: 8px;">
                                    <i class="fas fa-calendar-check"></i> Doctor's Schedule:
                                </div>
                                <div id="schedule_info" style="color: #475569; font-size: 13px;"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
              <label class="form-label">Appointment Time <span class="required">*</span></label>
                            <select name="appointment_time" id="appointment_time" class="form-select" required disabled style="pointer-events: none; opacity: 0.6;">
                                <option value="">-- Select Time --</option>
                            </select>
                            <div class="form-hint" id="time_hint">Please select doctor and date first to see available times</div>
                            <div id="time_error" class="text-danger" style="display: none; margin-top: 8px; font-size: 13px;"></div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label class="form-label">Reason for Visit <span class="required">*</span></label>
                            <textarea name="purpose" class="form-control" rows="3" required 
                                      placeholder="Briefly describe the patient's primary concern or purpose"><?= set_value('purpose') ?></textarea>
                        </div>
                    </div>
                </div>
                </div>

                <!-- STEP 3: ADDITIONAL DETAILS -->
                <div class="form-step" data-step="3">
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-plus-circle"></i> Additional Details <span class="optional">(Optional)</span>
                    </h3>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label">Triage Category <span class="optional">(if applicable)</span></label>
                        <div class="triage-options">
                            <label class="triage-option non-urgent">
                                <input type="radio" name="triage_category" value="non-urgent">
                                <span style="color: #10b981; font-weight: 600;">🟢 Non-Urgent</span>
                            </label>
                            
                            <label class="triage-option less-urgent">
                                <input type="radio" name="triage_category" value="less-urgent">
                                <span style="color: #f59e0b; font-weight: 600;">🟡 Less Urgent</span>
                            </label>
                            
                            <label class="triage-option urgent">
                                <input type="radio" name="triage_category" value="urgent">
                                <span style="color: #ef4444; font-weight: 600;">🔴 Urgent</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Vital Signs <span class="optional">(May be encoded if taken during OPD triage)</span></label>
                        <div class="vitals-grid">
                            <div class="vital-input">
                                <label class="form-label" style="font-size: 12px; color: #64748b;">Blood Pressure</label>
                                <input type="text" name="vital_bp" class="form-control" placeholder="120/80">
                                <span class="unit">mmHg</span>
                            </div>
                            
                            <div class="vital-input">
                                <label class="form-label" style="font-size: 12px; color: #64748b;">Temperature</label>
                                <input type="text" name="vital_temp" class="form-control" placeholder="36.5">
                                <span class="unit">°C</span>
                            </div>
                            
                            <div class="vital-input">
                                <label class="form-label" style="font-size: 12px; color: #64748b;">Pulse Rate</label>
                                <input type="text" name="vital_pulse" class="form-control" placeholder="72">
                                <span class="unit">bpm</span>
                            </div>
                            
                            <div class="vital-input">
                                <label class="form-label" style="font-size: 12px; color: #64748b;">Respiratory Rate</label>
                                <input type="text" name="vital_resp" class="form-control" placeholder="16">
                                <span class="unit">/min</span>
                            </div>
                            
                            <div class="vital-input">
                                <label class="form-label" style="font-size: 12px; color: #64748b;">Weight</label>
                                <input type="text" name="vital_weight" class="form-control" placeholder="65">
                                <span class="unit">kg</span>
                            </div>
                            
                            <div class="vital-input">
                                <label class="form-label" style="font-size: 12px; color: #64748b;">Height</label>
                                <input type="text" name="vital_height" class="form-control" placeholder="165">
                                <span class="unit">cm</span>
                            </div>
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
                        <i class="fas fa-user-plus"></i> Register Out-Patient
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
    // Step-by-Step Form Navigation
    let currentStep = 1;
    const totalSteps = 3;
    const formSteps = document.querySelectorAll('.form-step');
    const stepIndicators = document.querySelectorAll('.step');
    const prevBtn = document.getElementById('prevStep');
    const nextBtn = document.getElementById('nextStep');
    const submitBtn = document.getElementById('submitForm');
    
    // Define required fields for each step
    const stepRequiredFields = {
        1: ['first_name', 'last_name', 'date_of_birth', 'gender', 'contact', 'address'],
        2: ['visit_type', 'purpose'], // doctor_id, appointment_day, appointment_time are conditional
        3: [] // All optional (additional details)
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
                isValid = value !== '' && value !== '-- Select Gender --';
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
        
        // Special validation for step 2 (Visit Details)
        if (step === 2) {
            const visitType = document.querySelector('input[name="visit_type"]:checked')?.value;
            if (visitType === 'Consultation') {
                const doctorId = document.getElementById('doctor_id');
                const appointmentDay = document.getElementById('appointment_day');
                const appointmentTime = document.getElementById('appointment_time');
                
                if (!doctorId?.value || doctorId.value === '-- Choose Doctor --') {
                    errors.push('doctor_id');
                    if (doctorId) doctorId.classList.add('is-invalid');
                }
                if (!appointmentDay?.value || appointmentDay.disabled) {
                    errors.push('appointment_day');
                    if (appointmentDay) appointmentDay.classList.add('is-invalid');
                }
                if (!appointmentTime?.value || appointmentTime.disabled) {
                    errors.push('appointment_time');
                    if (appointmentTime) appointmentTime.classList.add('is-invalid');
                }
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
  const doctorSelect = document.getElementById('doctor_id');
  const doctorHint = document.getElementById('doctor_hint');
  
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
  
  // Function to filter doctors based on age
  function filterDoctorsByAge() {
    if (!doctorSelect) return;
    
    const patientAge = ageInput ? parseInt(ageInput.value) : -1;
    const isPediatric = patientAge >= 0 && patientAge <= 17;
    const isAdult = patientAge >= 18;
    const currentValue = doctorSelect.value;
    
    // Clear current options
    doctorSelect.innerHTML = '<option value="">-- Choose Doctor --</option>';
    
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
          doctorHint.textContent = 'Please choose the doctor assigned for this visit';
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
    
    // Trigger onchange event to update appointment day dropdown if doctor was already selected
    if (doctorSelect.value && typeof enableAppointmentDayDropdown === 'function') {
      enableAppointmentDayDropdown(doctorSelect.value);
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
      } else {
        ageInput.value = '';
        filterDoctorsByAge();
      }
    });
    
    // Also filter when age input changes directly
    ageInput.addEventListener('input', function() {
      filterDoctorsByAge();
    });
    
    // Initialize original doctor options if not already done
    initializeDoctorOptions();
    
    // Filter doctors on page load
    filterDoctorsByAge();
    
    // Trigger on page load if DOB has value
    if (dobInput.value) {
      dobInput.dispatchEvent(new Event('change'));
    }
  }
  
    // Make doctor_id required only when Consultation is selected
    const visitTypeRadios = document.querySelectorAll('input[name="visit_type"]');
    // doctorSelect is already declared above
    const doctorRequiredIndicator = document.getElementById('doctor_required_indicator');
    // doctorHint is already declared above
    
    function toggleDoctorRequirement() {
        const selectedVisitType = document.querySelector('input[name="visit_type"]:checked');
        const isConsultation = selectedVisitType && selectedVisitType.value === 'Consultation';
        
        if (isConsultation) {
            // Make doctor required for Consultation
            doctorSelect.setAttribute('required', 'required');
            if (doctorRequiredIndicator) {
                doctorRequiredIndicator.style.display = 'inline';
            }
            if (doctorHint) {
                doctorHint.textContent = 'Doctor assignment is required for Consultation. Patient will be immediately assigned to selected doctor.';
                doctorHint.style.color = '#2e7d32';
                doctorHint.style.fontWeight = '600';
            }
        } else {
            // Make doctor optional for other visit types
            doctorSelect.removeAttribute('required');
            if (doctorRequiredIndicator) {
                doctorRequiredIndicator.style.display = 'none';
            }
            if (doctorHint) {
                doctorHint.textContent = 'Please choose the doctor assigned for this visit (optional for Follow-up and Check-up)';
                doctorHint.style.color = '';
                doctorHint.style.fontWeight = '';
            }
        }
    }
    
    // Add event listeners to visit type radios
    visitTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleDoctorRequirement);
    });
    
    // Initialize on page load
    toggleDoctorRequirement();
    
    // Load available times based on doctor and day selection
    // Note: doctorSelect is already declared above, no need to redeclare
    const appointmentDaySelect = document.getElementById('appointment_day');
    const appointmentDateInput = document.getElementById('appointment_date'); // Hidden field
    const appointmentTimeSelect = document.getElementById('appointment_time');
    const timeHint = document.getElementById('time_hint');
    const timeError = document.getElementById('time_error');
    const doctorScheduleDisplay = document.getElementById('doctor_schedule_display');
    const scheduleInfo = document.getElementById('schedule_info');
    const dateHint = document.getElementById('date_hint');
    
    // Store doctor's schedule information
    let doctorScheduleInfo = null;
    const today = new Date();
    
    // Function to get next occurrence of a day (Monday-Friday)
    function getNextDayOfWeek(dayName) {
        const days = {
            'monday': 1,
            'tuesday': 2,
            'wednesday': 3,
            'thursday': 4,
            'friday': 5
        };
        
        const targetDay = days[dayName.toLowerCase()];
        if (!targetDay) return null;
        
        const currentDay = today.getDay(); // 0 = Sunday, 1 = Monday, etc.
        let daysUntilTarget = targetDay - currentDay;
        
        // If target day has passed this week, get next week's occurrence
        if (daysUntilTarget <= 0) {
            daysUntilTarget += 7;
        }
        
        const nextDate = new Date(today);
        nextDate.setDate(today.getDate() + daysUntilTarget);
        
        return nextDate.toISOString().split('T')[0]; // Return YYYY-MM-DD format
    }
    
    function loadAvailableTimes() {
        const doctorId = doctorSelect.value;
        const selectedDate = appointmentDaySelect.value; // This is now the actual date (YYYY-MM-DD)
        
        // Clear previous options
        appointmentTimeSelect.innerHTML = '<option value="">-- Select Time --</option>';
        timeError.style.display = 'none';
        
        if (!doctorId) {
            timeHint.textContent = 'Please select a doctor first';
            timeHint.style.color = '#ef4444';
            appointmentTimeSelect.disabled = true;
            return;
        }
        
        if (!selectedDate) {
            timeHint.textContent = 'Please select a day';
            timeHint.style.color = '#ef4444';
            appointmentTimeSelect.disabled = true;
            return;
        }
        
        // The selectedDate is already in YYYY-MM-DD format, so use it directly
        const actualDate = selectedDate;
        
        // Set the hidden date field - CRITICAL for form submission
        if (appointmentDateInput) {
            appointmentDateInput.value = actualDate;
            console.log('Set appointment_date hidden field to:', actualDate);
        } else {
            console.error('appointment_date hidden field not found!');
        }
        
        // Enable time select immediately
        appointmentTimeSelect.removeAttribute('disabled');
        appointmentTimeSelect.disabled = false;
        appointmentTimeSelect.style.pointerEvents = 'auto';
        appointmentTimeSelect.style.opacity = '1';
        appointmentTimeSelect.style.cursor = 'pointer';
        appointmentTimeSelect.style.backgroundColor = '#fff';
        appointmentTimeSelect.removeAttribute('readonly');
        
        timeHint.textContent = 'Loading available times...';
        timeHint.style.color = '#10b981';
        
        // Fetch available times via AJAX using the calculated date
        fetch(`<?= site_url('receptionist/patients/get-available-times') ?>?doctor_id=${doctorId}&date=${actualDate}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.times && data.times.length > 0) {
                // Populate time select with available times only
                data.times.forEach(time => {
                    const option = document.createElement('option');
                    option.value = time.value;
                    option.textContent = time.label + ' (Available)';
                    appointmentTimeSelect.appendChild(option);
                });
                
                // Ensure time select is enabled and clickable
                appointmentTimeSelect.removeAttribute('disabled');
                appointmentTimeSelect.disabled = false;
                appointmentTimeSelect.style.pointerEvents = 'auto';
                appointmentTimeSelect.style.opacity = '1';
                appointmentTimeSelect.style.cursor = 'pointer';
                appointmentTimeSelect.style.backgroundColor = '#fff';
                appointmentTimeSelect.removeAttribute('readonly');
                
                // Show available hours info
                if (data.available_hours && data.available_hours.length > 0) {
                    const hours = data.available_hours.map(h => {
                        const start = new Date('2000-01-01 ' + h.start).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                        const end = new Date('2000-01-01 ' + h.end).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                        return `${start} - ${end}`;
                    }).join(', ');
                    timeHint.textContent = `Available time slots: ${data.times.length} slots available`;
                    timeHint.style.color = '#2e7d32';
                } else {
                    timeHint.textContent = 'Available times loaded';
                    timeHint.style.color = '#2e7d32';
                }
            } else {
                appointmentTimeSelect.disabled = false; // Still enable it even if no times
                timeError.textContent = data.message || 'No available time slots for this doctor and date. Please select another day or doctor.';
                timeError.style.display = 'block';
                timeHint.textContent = 'No available times';
                timeHint.style.color = '#ef4444';
            }
        })
        .catch(error => {
            console.error('Error loading available times:', error);
            appointmentTimeSelect.disabled = false; // Enable on error too
            timeError.textContent = 'Error loading available times. Please try again.';
            timeError.style.display = 'block';
            timeHint.textContent = 'Error loading times';
            timeHint.style.color = '#ef4444';
        });
    }
    
    // Load doctor's schedule when doctor is selected
    function loadDoctorSchedule() {
        const doctorId = doctorSelect.value;
        
        // Hide schedule if no doctor selected
        if (!doctorId) {
            doctorScheduleDisplay.style.display = 'none';
            appointmentDaySelect.disabled = true;
            appointmentDaySelect.value = '';
            appointmentTimeSelect.disabled = true;
            appointmentTimeSelect.innerHTML = '<option value="">-- Select Time --</option>';
            dateHint.textContent = 'Please select a doctor first to see schedule';
            dateHint.style.color = '';
            return;
        }
        
        // Enable day select immediately
        appointmentDaySelect.removeAttribute('disabled');
        appointmentDaySelect.disabled = false;
        appointmentDaySelect.style.pointerEvents = 'auto';
        appointmentDaySelect.style.opacity = '1';
        appointmentDaySelect.style.cursor = 'pointer';
        appointmentDaySelect.style.backgroundColor = '#fff';
        
        // Show loading state
        dateHint.textContent = 'Loading doctor schedule...';
        dateHint.style.color = '#10b981';
        
        // Fetch doctor's schedule dates to get schedule info
        fetch(`<?= site_url('receptionist/patients/get-doctor-schedule-dates') ?>?doctor_id=${doctorId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.schedule_dates && data.schedule_dates.length > 0) {
                // Store schedule info
                doctorScheduleInfo = data.schedule_dates;
                
                // Populate Appointment Day dropdown with actual dates
                appointmentDaySelect.innerHTML = '<option value="">-- Select Day --</option>';
                data.schedule_dates.forEach(schedule => {
                    const option = document.createElement('option');
                    // Use display_text if available, otherwise use date_formatted
                    const displayText = schedule.display_text || schedule.date_formatted || schedule.date;
                    option.value = schedule.date; // Store the actual date (YYYY-MM-DD)
                    option.textContent = displayText; // Display: "Monday, Jan 15" or "Jan 15, 2025 (Monday)"
                    option.setAttribute('data-date', schedule.date);
                    appointmentDaySelect.appendChild(option);
                });
                
                // Show doctor's schedule
                doctorScheduleDisplay.style.display = 'block';
                
                // Get unique schedule hours (usually same for all weekdays)
                const scheduleHours = [];
                data.schedule_dates.forEach(schedule => {
                    schedule.available_hours.forEach(hour => {
                        if (!scheduleHours.includes(hour)) {
                            scheduleHours.push(hour);
                        }
                    });
                });
                
                // Format schedule hours for display
                let formattedHours = scheduleHours.join(', ');
                if (scheduleHours.length === 0) {
                    // Default schedule if not found
                    formattedHours = '9:00 AM - 12:00 PM, 1:00 PM - 4:00 PM';
                }
                
                scheduleInfo.innerHTML = `
                    <div style="margin-bottom: 4px;"><strong>Available Hours:</strong> ${formattedHours}</div>
                    <div style="font-size: 12px; color: #64748b;">Schedule available Monday to Friday (Saturday & Sunday are rest days)</div>
                `;
                
                dateHint.textContent = 'Select a day to see available times';
                dateHint.style.color = '#2e7d32';
                
                // Ensure day dropdown is enabled and clickable
                appointmentDaySelect.removeAttribute('disabled');
                appointmentDaySelect.disabled = false;
                appointmentDaySelect.style.pointerEvents = 'auto';
                appointmentDaySelect.style.opacity = '1';
                appointmentDaySelect.style.cursor = 'pointer';
                appointmentDaySelect.style.backgroundColor = '#fff';
                appointmentDaySelect.removeAttribute('readonly');
                
                // Show that Monday-Friday are available
                console.log('Doctor schedule loaded. Available days: Monday-Friday');
            } else {
                doctorScheduleDisplay.style.display = 'none';
                appointmentDaySelect.setAttribute('disabled', 'disabled');
                appointmentDaySelect.disabled = true;
                appointmentDaySelect.value = '';
                appointmentTimeSelect.setAttribute('disabled', 'disabled');
                appointmentTimeSelect.disabled = true;
                appointmentTimeSelect.innerHTML = '<option value="">-- Select Time --</option>';
                dateHint.textContent = 'Doctor has no available schedule. Please select another doctor.';
                dateHint.style.color = '#ef4444';
            }
        })
        .catch(error => {
            console.error('Error loading doctor schedule:', error);
            doctorScheduleDisplay.style.display = 'none';
            appointmentDaySelect.disabled = true;
        });
    }
    
    // Function to enable appointment day dropdown (global function for inline handler)
    function enableAppointmentDayDropdown(doctorId) {
        const appointmentDaySelect = document.getElementById('appointment_day');
        if (!appointmentDaySelect) {
            console.error('appointmentDaySelect element not found!');
            return;
        }
        
        if (doctorId && doctorId !== '' && doctorId !== '0') {
            try {
                appointmentDaySelect.removeAttribute('disabled');
                appointmentDaySelect.disabled = false;
                appointmentDaySelect.removeAttribute('readonly');
                appointmentDaySelect.classList.remove('disabled');
                appointmentDaySelect.style.cssText = 'pointer-events: auto !important; opacity: 1 !important; cursor: pointer !important; background-color: #fff !important;';
                
                console.log('✅ Appointment Day ENABLED via inline handler - disabled:', appointmentDaySelect.disabled);
                
                // Also trigger the schedule load if the function exists
                if (typeof loadDoctorSchedule === 'function') {
                    loadDoctorSchedule();
                }
            } catch (e) {
                console.error('Error enabling appointment day:', e);
            }
        } else {
            appointmentDaySelect.setAttribute('disabled', 'disabled');
            appointmentDaySelect.disabled = true;
            appointmentDaySelect.style.cssText = 'pointer-events: none !important; opacity: 0.6 !important;';
        }
    }
    
    // Function to enable appointment day dropdown (local function)
    function enableAppointmentDay() {
        if (!appointmentDaySelect) {
            console.error('appointmentDaySelect element not found!');
            return;
        }
        
        try {
            appointmentDaySelect.removeAttribute('disabled');
            appointmentDaySelect.disabled = false;
            appointmentDaySelect.removeAttribute('readonly');
            appointmentDaySelect.classList.remove('disabled');
            appointmentDaySelect.style.cssText = 'pointer-events: auto !important; opacity: 1 !important; cursor: pointer !important; background-color: #fff !important;';
            
            console.log('✅ Appointment Day ENABLED - disabled:', appointmentDaySelect.disabled, 'pointer-events:', appointmentDaySelect.style.pointerEvents);
        } catch (e) {
            console.error('Error enabling appointment day:', e);
        }
    }
    
    // Function to disable appointment day dropdown
    function disableAppointmentDay() {
        if (!appointmentDaySelect) return;
        
        appointmentDaySelect.setAttribute('disabled', 'disabled');
        appointmentDaySelect.disabled = true;
        appointmentDaySelect.style.cssText = 'pointer-events: none !important; opacity: 0.6 !important;';
    }
    
    // Load schedule when doctor changes
    if (doctorSelect && appointmentDaySelect) {
        console.log('Setting up doctor select event listener...');
        
        // Use both change and input events to catch all changes
        doctorSelect.addEventListener('change', function() {
            const selectedDoctorId = this.value;
            console.log('🔵 Doctor CHANGE event - selected:', selectedDoctorId);
            
            if (selectedDoctorId && selectedDoctorId !== '' && selectedDoctorId !== '0') {
                // IMMEDIATELY enable day dropdown
                enableAppointmentDay();
                
                // Load schedule
                loadDoctorSchedule();
                
                // Reset day and time selections
                appointmentDaySelect.value = '';
                appointmentTimeSelect.innerHTML = '<option value="">-- Select Time --</option>';
                appointmentTimeSelect.setAttribute('disabled', 'disabled');
                appointmentTimeSelect.disabled = true;
                appointmentTimeSelect.style.cssText = 'pointer-events: none !important; opacity: 0.6 !important;';
            } else {
                // Disable if no doctor selected
                disableAppointmentDay();
                appointmentDaySelect.value = '';
                appointmentTimeSelect.setAttribute('disabled', 'disabled');
                appointmentTimeSelect.disabled = true;
                appointmentTimeSelect.innerHTML = '<option value="">-- Select Time --</option>';
                if (doctorScheduleDisplay) {
                    doctorScheduleDisplay.style.display = 'none';
                }
            }
        });
        
        // Also listen to input event (for some browsers)
        doctorSelect.addEventListener('input', function() {
            const selectedDoctorId = this.value;
            console.log('🟢 Doctor INPUT event - selected:', selectedDoctorId);
            
            if (selectedDoctorId && selectedDoctorId !== '' && selectedDoctorId !== '0') {
                enableAppointmentDay();
            } else {
                disableAppointmentDay();
            }
        });
        
        // Also load schedule if doctor is already selected on page load
        setTimeout(function() {
            if (doctorSelect && doctorSelect.value && doctorSelect.value !== '' && doctorSelect.value !== '0') {
                console.log('🟡 Doctor already selected on page load, enabling dropdowns...');
                enableAppointmentDay();
                if (typeof loadDoctorSchedule === 'function') {
                    loadDoctorSchedule();
                }
            }
        }, 100);
    } else {
        console.error('❌ Doctor select or appointment day select not found!', {
            doctorSelect: !!doctorSelect,
            appointmentDaySelect: !!appointmentDaySelect
        });
    }
    
    // Load times when day changes
        if (appointmentDaySelect) {
        appointmentDaySelect.addEventListener('change', function() {
            const selectedDate = this.value; // This is the actual date (YYYY-MM-DD)
            
            // CRITICAL: Set the hidden appointment_date field immediately when day is selected
            if (appointmentDateInput && selectedDate) {
                appointmentDateInput.value = selectedDate;
                console.log('✅ Set appointment_date to:', selectedDate);
            } else {
                console.error('❌ Cannot set appointment_date - input field or date missing');
            }
            
            if (selectedDate && selectedDate !== '') {
                appointmentTimeSelect.removeAttribute('disabled');
                appointmentTimeSelect.disabled = false;
                appointmentTimeSelect.style.pointerEvents = 'auto';
                appointmentTimeSelect.style.opacity = '1';
                appointmentTimeSelect.style.cursor = 'pointer';
                appointmentTimeSelect.style.backgroundColor = '#fff';
                appointmentTimeSelect.removeAttribute('readonly');
                loadAvailableTimes();
            } else {
                appointmentTimeSelect.setAttribute('disabled', 'disabled');
                appointmentTimeSelect.disabled = true;
                appointmentTimeSelect.style.pointerEvents = 'none';
                appointmentTimeSelect.style.opacity = '0.6';
                appointmentTimeSelect.innerHTML = '<option value="">-- Select Time --</option>';
                if (appointmentDateInput) {
                    appointmentDateInput.value = '';
                }
            }
        });
    }
    
    // Validate selected time on form submit
    const outpatientForm = document.getElementById('outpatientForm');
    if (outpatientForm) {
        outpatientForm.addEventListener('submit', function(e) {
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
            
            // Additional validation for appointment fields (if Consultation visit type)
            const visitType = document.querySelector('input[name="visit_type"]:checked')?.value;
            if (visitType === 'Consultation') {
                const selectedTime = appointmentTimeSelect?.value;
                const selectedDay = appointmentDaySelect?.value;
                const doctorId = doctorSelect?.value;
                
                if (!doctorId) {
                    e.preventDefault();
                    currentStep = 2;
                    showStep(2);
                    alert('Please select a doctor.');
                    return false;
                }
                
                if (!selectedDay) {
                    e.preventDefault();
                    currentStep = 2;
                    showStep(2);
                    alert('Please select an appointment day (Monday-Friday).');
                    return false;
                }
                
                if (!selectedTime) {
                    e.preventDefault();
                    currentStep = 2;
                    showStep(2);
                    alert('Please select an available appointment time.');
                    return false;
                }
                
                // Ensure the hidden date field is set
                if (typeof getNextDayOfWeek === 'function') {
                    const actualDate = getNextDayOfWeek(selectedDay);
                    if (actualDate && appointmentDateInput) {
                        appointmentDateInput.value = actualDate;
                    }
                }
            }
            
            // Additional validation: check if time is still available
            // This is a client-side check, server-side validation will also be done
        });
    }
});
</script>

<?= $this->endSection() ?>
