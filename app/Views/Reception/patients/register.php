<?php
helper('form');
$errors = session('errors') ?? [];
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
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(2, 136, 209, 0.2);
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
        color: #0288d1;
        margin: 0 0 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e0f2fe;
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
        border-color: #0288d1;
        box-shadow: 0 0 0 3px rgba(2, 136, 209, 0.1);
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
        background: #e0f2fe;
        color: #0369a1;
        border-left: 4px solid #0288d1;
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
        accent-color: #0288d1;
    }
    
    .insurance-toggle label:has(input:checked) {
        border-color: #0288d1;
        background: #e0f2fe;
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
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
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
        box-shadow: 0 6px 20px rgba(2, 136, 209, 0.3);
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
        border-color: #0288d1;
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.1);
    }
    
    .room-type-card input[type="radio"] {
        position: absolute;
        opacity: 0;
    }
    
    .room-type-card:has(input:checked) {
        border-color: #0288d1;
        background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.15);
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
        color: #0288d1;
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

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <div class="form-card-body">
            <form method="post" action="<?= site_url('receptionist/patients/store') ?>" id="inpatientForm">
                <?= csrf_field() ?>
                <input type="hidden" name="type" value="In-Patient">
                <input type="hidden" name="visit_type" value="Consultation">

                <!-- PATIENT INFORMATION -->
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
                            <label class="form-label">Gender <span class="required">*</span></label>
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
                        </div>
                        
                        <div class="form-group" style="grid-column: span 2;">
                            <label class="form-label">Complete Address <span class="required">*</span></label>
                            <input type="text" name="address" class="form-control" 
                                   value="<?= set_value('address') ?>" required placeholder="House No., Street, Barangay, City/Municipality, Province">
                        </div>
                    </div>
                </div>

                <!-- ADMISSION DETAILS -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-hospital"></i> Admission Details
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Admitting Doctor <span class="required">*</span></label>
                            <select name="doctor_id" class="form-select" required>
                                <option value="">-- Select Doctor --</option>
                                <?php if (!empty($doctors)): ?>
                                    <?php foreach ($doctors as $doctor): ?>
                                        <option value="<?= esc($doctor['id']) ?>" <?= set_select('doctor_id', $doctor['id']) ?>>
                                            Dr. <?= esc($doctor['doctor_name']) ?> - <?= esc($doctor['specialization'] ?? 'General Practice') ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-hint">Select from the list of available doctors</div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Admission Date <span class="required">*</span></label>
                            <input type="date" name="admission_date" class="form-control <?= isset($errors['admission_date']) ? 'is-invalid' : '' ?>" 
                                   value="<?= set_value('admission_date', date('Y-m-d')) ?>" 
                                   min="<?= date('Y-m-d') ?>" required>
                            <?php if (isset($errors['admission_date'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['admission_date']) ?></div>
                            <?php else: ?>
                                <small class="form-text text-muted">Hindi maaaring pumili ng nakaraang petsa. Dapat ngayon o sa hinaharap.</small>
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
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label class="form-label">Room Type <span class="required">*</span></label>
                        <div class="form-hint" style="margin-bottom: 12px;">Select the type of room for the patient's admission</div>
                        
                        <div class="room-type-options">
                            <!-- Private Room -->
                            <label class="room-type-card private">
                                <input type="radio" name="room_type" value="Private" <?= set_radio('room_type', 'Private') ?> required>
                                <span class="room-icon">🏠</span>
                                <div class="room-title">Private Room</div>
                                <ul class="room-details">
                                    <li><i class="fas fa-user" style="color: #7c3aed;"></i> Isang pasyente lang</li>
                                    <li><i class="fas fa-bath" style="color: #7c3aed;"></i> May sariling CR</li>
                                </ul>
                                <div class="room-rate">
                                    <i class="fas fa-peso-sign"></i> <span class="price-display" id="price-private">₱5,000</span>/day
                                </div>
                            </label>
                            
                            <!-- Semi-Private Room -->
                            <label class="room-type-card semi-private">
                                <input type="radio" name="room_type" value="Semi-Private" <?= set_radio('room_type', 'Semi-Private') ?>>
                                <span class="room-icon">🏘️</span>
                                <div class="room-title">Semi-Private Room</div>
                                <ul class="room-details">
                                    <li><i class="fas fa-users" style="color: #0288d1;"></i> 2 pasyente sa isang kwarto</li>
                                    <li><i class="fas fa-bath" style="color: #0288d1;"></i> May shared CR</li>
                                </ul>
                                <div class="room-rate">
                                    <i class="fas fa-peso-sign"></i> <span class="price-display" id="price-semi-private">₱3,000</span>/day
                                </div>
                            </label>
                            
                            <!-- Ward / General Ward -->
                            <label class="room-type-card ward">
                                <input type="radio" name="room_type" value="Ward" <?= set_radio('room_type', 'Ward', true) ?>>
                                <span class="room-icon">🏥</span>
                                <div class="room-title">Ward (General Ward)</div>
                                <ul class="room-details">
                                    <li><i class="fas fa-users" style="color: #10b981;"></i> 4-10+ patients</li>
                                    <li><i class="fas fa-bath" style="color: #10b981;"></i> Shared facilities</li>
                                </ul>
                                <div class="room-rate">
                                    <i class="fas fa-peso-sign"></i> <span class="price-display" id="price-ward">₱1,000</span>/day
                                </div>
                            </label>
                            
                            <!-- ICU (Intensive Care Unit) -->
                            <label class="room-type-card icu">
                                <input type="radio" name="room_type" value="ICU" <?= set_radio('room_type', 'ICU') ?>>
                                <span class="room-icon">🚨</span>
                                <div class="room-title">ICU (Intensive Care Unit)</div>
                                <ul class="room-details">
                                    <li><i class="fas fa-heartbeat" style="color: #dc2626;"></i> Critical care</li>
                                    <li><i class="fas fa-desktop" style="color: #dc2626;"></i> Special equipment</li>
                                </ul>
                                <div class="room-rate">
                                    <i class="fas fa-peso-sign"></i> <span class="price-display" id="price-icu">₱8,000</span>/day
                                </div>
                            </label>
                            
                            <!-- Isolation Room -->
                            <label class="room-type-card isolation">
                                <input type="radio" name="room_type" value="Isolation" <?= set_radio('room_type', 'Isolation') ?>>
                                <span class="room-icon">🔒</span>
                                <div class="room-title">Isolation Room</div>
                                <ul class="room-details">
                                    <li><i class="fas fa-shield-virus" style="color: #f59e0b;"></i> For infectious diseases</li>
                                    <li><i class="fas fa-door-closed" style="color: #f59e0b;"></i> Separate ventilation</li>
                                </ul>
                                <div class="room-rate">
                                    <i class="fas fa-peso-sign"></i> <span class="price-display" id="price-isolation">₱6,000</span>/day
                                </div>
                            </label>
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

                <!-- MEDICAL INFORMATION -->
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

                <!-- INSURANCE INFORMATION -->
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
                                <label class="form-label">Insurance Provider <span class="required">*</span></label>
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
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Insurance Number / Member ID <span class="required">*</span></label>
                                <input type="text" name="insurance_number" class="form-control" id="insurance_number"
                                       value="<?= set_value('insurance_number') ?>" placeholder="Enter insurance/member ID">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EMERGENCY CONTACT -->
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

                <!-- FORM ACTIONS -->
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
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
    
    // Insurance toggle
    const insuranceYes = document.getElementById('insurance_yes');
    const insuranceNo = document.getElementById('insurance_no');
    const insuranceFields = document.getElementById('insurance_fields');
    const insuranceProvider = document.getElementById('insurance_provider');
    const insuranceNumber = document.getElementById('insurance_number');
    
    function toggleInsuranceFields() {
        if (insuranceYes && insuranceYes.checked) {
            insuranceFields.classList.add('show');
            if (insuranceProvider) insuranceProvider.required = true;
            if (insuranceNumber) insuranceNumber.required = true;
        } else {
            insuranceFields.classList.remove('show');
            if (insuranceProvider) {
                insuranceProvider.required = false;
                insuranceProvider.value = '';
            }
            if (insuranceNumber) {
                insuranceNumber.required = false;
                insuranceNumber.value = '';
            }
        }
    }
    
    if (insuranceYes) insuranceYes.addEventListener('change', toggleInsuranceFields);
    if (insuranceNo) insuranceNo.addEventListener('change', toggleInsuranceFields);
    
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
    
    // Room Type and Room Number handling
    const roomTypeInputs = document.querySelectorAll('input[name="room_type"]');
    const roomNumberSelect = document.getElementById('room_number');
    const bedNumberSelect = document.getElementById('bed_number');
    const roomsDataElement = document.getElementById('rooms-data');
    const bedsDataElement = document.getElementById('beds-data');
    
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
        
        // Add rooms for the selected type
        roomsData[roomType].forEach(room => {
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
        
        // Add beds to dropdown
        beds.forEach(bed => {
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
    roomTypeInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.checked) {
                updateRoomNumberDropdown(this.value);
            }
        });
    });
    
    // Initialize room dropdown based on default selected room type
    const selectedRoomType = document.querySelector('input[name="room_type"]:checked');
    if (selectedRoomType) {
        updateRoomNumberDropdown(selectedRoomType.value);
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
    
    // Form validation before submit
    const form = document.getElementById('inpatientForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Check if insurance is selected but fields are empty
            if (insuranceYes && insuranceYes.checked) {
                if (!insuranceProvider.value.trim()) {
                    e.preventDefault();
                    alert('Please enter the Insurance Provider.');
                    insuranceProvider.focus();
                    return false;
                }
                if (!insuranceNumber.value.trim()) {
                    e.preventDefault();
                    alert('Please enter the Insurance Number / Member ID.');
                    insuranceNumber.focus();
                    return false;
                }
            }
        });
    }
});
</script>

<?= $this->endSection() ?>
