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
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(16, 185, 129, 0.2);
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
        color: #10b981;
        margin: 0 0 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #d1fae5;
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
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
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
        background: #ecfdf5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }
    
    /* Insurance Toggle */
    .insurance-toggle {
        display: flex;
        gap: 16px;
        margin-bottom: 16px;
        flex-wrap: wrap;
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
        font-weight: 500;
    }
    
    .insurance-toggle input[type="radio"],
    .insurance-toggle input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #10b981;
    }
    
    .insurance-toggle label:has(input:checked) {
        border-color: #10b981;
        background: #ecfdf5;
    }
    
    .insurance-fields {
        display: block;
        padding: 16px;
        background: #f8fafc;
        border-radius: 10px;
        margin-top: 16px;
    }
    
    .insurance-fields.hidden {
        display: none;
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
        border-color: #10b981;
    }
    
    .visit-type-card input[type="radio"] {
        position: absolute;
        opacity: 0;
    }
    
    .visit-type-card:has(input:checked) {
        border-color: #10b981;
        background: #ecfdf5;
    }
    
    .visit-type-card i {
        font-size: 28px;
        color: #10b981;
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
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
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
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
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
    
    /* Type Badge */
    .type-badge {
        display: inline-block;
        padding: 8px 16px;
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
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
            <form method="post" action="<?= site_url('receptionist/patients/store') ?>" id="outpatientForm">
        <?= csrf_field() ?>
                <input type="hidden" name="type" value="Out-Patient">

                <!-- PATIENT INFORMATION -->
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
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" 
                                   value="<?= set_value('date_of_birth') ?>" required>
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

                <!-- VISIT DETAILS -->
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
                    <option value="<?= esc($doctor['id']) ?>" <?= set_select('doctor_id', (string)$doctor['id']) ?>>
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
                            <div id="doctor_schedule_display" style="display: none; margin-top: 12px; padding: 12px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #0288d1;">
                                <div style="font-weight: 600; color: #0288d1; margin-bottom: 8px;">
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

                <!-- INSURANCE INFORMATION -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-shield-alt"></i> Insurance Information <span class="optional">(Optional)</span>
                    </h3>
                    
                    <div class="form-group">
                        <div class="insurance-toggle">
                            <label>
                                <input type="checkbox" name="no_insurance" id="no_insurance" value="1"> 
                                <i class="fas fa-times-circle" style="color: #94a3b8;"></i> No Insurance
                            </label>
                        </div>
                    </div>
                    
                    <div class="insurance-fields" id="insurance_fields">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Insurance Provider</label>
                                <select name="insurance_provider" class="form-select" id="insurance_provider">
                                    <option value="">-- Select Provider --</option>
                                    <option value="PhilHealth" <?= set_select('insurance_provider', 'PhilHealth') ?>>PhilHealth</option>
                                    <option value="Maxicare" <?= set_select('insurance_provider', 'Maxicare') ?>>Maxicare</option>
                                    <option value="Medicard" <?= set_select('insurance_provider', 'Medicard') ?>>Medicard</option>
                                    <option value="Intellicare" <?= set_select('insurance_provider', 'Intellicare') ?>>Intellicare</option>
                                    <option value="Pacific Cross" <?= set_select('insurance_provider', 'Pacific Cross') ?>>Pacific Cross</option>
                                    <option value="Cocolife" <?= set_select('insurance_provider', 'Cocolife') ?>>Cocolife</option>
                                    <option value="AXA" <?= set_select('insurance_provider', 'AXA') ?>>AXA Philippines</option>
                                    <option value="Sun Life" <?= set_select('insurance_provider', 'Sun Life') ?>>Sun Life</option>
                                    <option value="Pru Life UK" <?= set_select('insurance_provider', 'Pru Life UK') ?>>Pru Life UK</option>
                                    <option value="Other" <?= set_select('insurance_provider', 'Other') ?>>Other</option>
                                </select>
                                <div class="form-hint">Select the patient's insurance provider if applicable</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Policy Number / Member ID</label>
                                <input type="text" name="insurance_number" class="form-control" id="insurance_number"
                                       value="<?= set_value('insurance_number') ?>" placeholder="Enter policy or member ID">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Coverage Type</label>
                                <select name="payment_type" class="form-select">
                                    <option value="">-- Select Coverage --</option>
                                    <option value="Insurance" <?= set_select('payment_type', 'Insurance') ?>>Full Insurance Coverage</option>
                                    <option value="Cash" <?= set_select('payment_type', 'Cash') ?>>Partial Coverage (Co-pay)</option>
                                    <option value="Credit" <?= set_select('payment_type', 'Credit') ?>>Credit/Deferred Payment</option>
                                </select>
                                <div class="form-hint">Select basic coverage information if needed</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ADDITIONAL DETAILS -->
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

                <!-- FORM ACTIONS -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
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
                ageInput.value = age >= 0 ? age : 0;
            } else {
                ageInput.value = '';
            }
        });
        
        // Trigger on page load if DOB has value
        if (dobInput.value) {
            dobInput.dispatchEvent(new Event('change'));
        }
    }
    
    // No Insurance toggle
    const noInsuranceCheckbox = document.getElementById('no_insurance');
    const insuranceFields = document.getElementById('insurance_fields');
    const insuranceProvider = document.getElementById('insurance_provider');
    const insuranceNumber = document.getElementById('insurance_number');
    
    function toggleInsuranceFields() {
        if (noInsuranceCheckbox && noInsuranceCheckbox.checked) {
            insuranceFields.classList.add('hidden');
            if (insuranceProvider) insuranceProvider.value = '';
            if (insuranceNumber) insuranceNumber.value = '';
        } else {
            insuranceFields.classList.remove('hidden');
        }
    }
    
    if (noInsuranceCheckbox) {
        noInsuranceCheckbox.addEventListener('change', toggleInsuranceFields);
    }
    
    // Auto-fill insurance number based on provider
    if (insuranceProvider && insuranceNumber) {
        // Insurance provider number formats
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
            'Other': () => 'INS-' + Math.floor(1000000 + Math.random() * 9000000).toString()
        };
        
        insuranceProvider.addEventListener('change', function() {
            const selectedProvider = this.value;
            
            if (selectedProvider && selectedProvider !== '') {
                // Auto-generate number based on provider
                if (insuranceFormats[selectedProvider]) {
                    const generatedNumber = insuranceFormats[selectedProvider]();
                    insuranceNumber.value = generatedNumber;
                    insuranceNumber.focus();
                    // Select all text for easy editing
                    setTimeout(() => {
                        insuranceNumber.select();
                    }, 10);
                }
            } else {
                // Clear if no provider selected
                insuranceNumber.value = '';
            }
        });
    }
  
    // Initialize
    toggleInsuranceFields();
    
    // Make doctor_id required only when Consultation is selected
    const visitTypeRadios = document.querySelectorAll('input[name="visit_type"]');
    const doctorSelect = document.getElementById('doctor_id');
    const doctorRequiredIndicator = document.getElementById('doctor_required_indicator');
    const doctorHint = document.getElementById('doctor_hint');
    
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
        timeHint.style.color = '#0288d1';
        
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
        dateHint.style.color = '#0288d1';
        
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
            const selectedTime = appointmentTimeSelect.value;
            const selectedDay = appointmentDaySelect.value;
            const doctorId = doctorSelect.value;
            
            if (!doctorId) {
                e.preventDefault();
                alert('Please select a doctor.');
                return false;
            }
            
            if (!selectedDay) {
                e.preventDefault();
                alert('Please select an appointment day (Monday-Friday).');
                return false;
            }
            
            if (!selectedTime) {
                e.preventDefault();
                alert('Please select an available appointment time.');
                return false;
            }
            
            // Ensure the hidden date field is set
            const actualDate = getNextDayOfWeek(selectedDay);
            if (actualDate) {
                appointmentDateInput.value = actualDate;
            }
            
            // Additional validation: check if time is still available
            // This is a client-side check, server-side validation will also be done
        });
    }
});
</script>

<?= $this->endSection() ?>
