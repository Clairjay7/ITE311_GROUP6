<?= $this->extend('template/header') ?>
<?php helper('form'); ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    :root {
        --primary-color: #2e7d32;
        --gradient-1: #2e7d32;
        --gradient-2: #4caf50;
    }
    
    .register-container {
        max-width: 1200px;
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
    
    .form-control[readonly] {
        background: #f8fafc;
        color: #64748b;
    }
    
    .form-hint {
        font-size: 12px;
        color: #64748b;
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
    
    .patient-type-toggle {
        display: flex;
        gap: 16px;
        margin-bottom: 16px;
    }
    
    .patient-type-toggle label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        padding: 10px 20px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        transition: all 0.2s ease;
        flex: 1;
    }
    
    .patient-type-toggle input[type="radio"] {
        width: 18px;
        height: 18px;
        accent-color: var(--primary-color);
    }
    
    .patient-type-toggle label:has(input:checked) {
        border-color: var(--primary-color);
        background: #e8f5e9;
    }
    
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
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .btn-cancel {
        background: #6b7280;
        color: white;
        padding: 14px 32px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    
    .btn-cancel:hover {
        background: #4b5563;
        color: white;
    }
</style>

<div class="register-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-vial"></i>
            <?= esc($title) ?>
        </h1>
        <a href="<?= base_url('admin/lab') ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <strong>Validation Errors:</strong>
            <ul style="margin: 8px 0 0 20px; padding: 0;">
                <?php foreach (session()->getFlashdata('errors') as $field => $error): ?>
                    <li><?= esc(is_array($error) ? $error[0] : $error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('admin/lab/store') ?>" id="labServiceForm">
        <?= csrf_field() ?>
        
        <div class="form-card">
            <div class="form-card-body">
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-user-tag"></i> Patient Type
                    </h3>
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label class="form-label">Select Patient Type <span class="required">*</span></label>
                            <div class="patient-type-toggle">
                                <label>
                                    <input type="radio" name="patient_source" value="walkin" id="patient_source_walkin" onchange="togglePatientForm()" required <?= set_radio('patient_source', 'walkin') ?>>
                                    <span>Walk-in</span>
                                </label>
                                <label>
                                    <input type="radio" name="patient_source" value="patient" id="patient_source_patient" onchange="togglePatientForm()" required <?= set_radio('patient_source', 'patient', true) ?>>
                                    <span>Patient</span>
                                </label>
                            </div>
                            <div class="form-hint">Select "Walk-in" for non-registered patients or "Patient" for registered patients</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Walk-in Form -->
        <div id="walkin_form" style="display: none;">
            <div class="form-card">
                <div class="form-card-body">
                    <!-- 1. Personal Information -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-user"></i> Personal Information
                        </h3>
                        
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="form-label" for="walkin_full_name">Full Name <span class="required">*</span></label>
                                <input type="text" id="walkin_full_name" name="walkin_full_name" class="form-control" placeholder="Enter full name" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="walkin_date_of_birth">Date of Birth</label>
                                <input type="date" id="walkin_date_of_birth" name="walkin_date_of_birth" class="form-control" onchange="calculateAge()" max="<?= date('Y-m-d') ?>">
                                <div class="form-hint">Age will be calculated automatically</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="walkin_age">Age</label>
                                <input type="number" id="walkin_age" name="walkin_age" class="form-control" placeholder="Auto-calculated" min="0" max="150" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="walkin_gender">Sex / Gender <span class="required">*</span></label>
                                <select id="walkin_gender" name="walkin_gender" class="form-select" required>
                                    <option value="">-- Select Gender --</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="walkin_contact">Contact Number / Email <span class="required">*</span></label>
                                <input type="text" id="walkin_contact" name="walkin_contact" class="form-control" placeholder="Phone or Email" required>
                            </div>
                            
                            <div class="form-group full-width">
                                <label class="form-label" for="walkin_address">Address</label>
                                <textarea id="walkin_address" name="walkin_address" class="form-control" rows="2" placeholder="Enter address for result delivery (Optional)"></textarea>
                                <div class="form-hint">Optional: for result delivery</div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Lab Test Selection -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-vial"></i> Lab Test Selection
                        </h3>
                        
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="form-label" for="walkin_test_type">Test Requested <span class="required">*</span></label>
                                <select id="walkin_test_type" name="walkin_test_type" class="form-select" required onchange="updateWalkinTestInfo()">
                        <option value="">-- Select Lab Test --</option>
                        <?php
                        $categoryLabels = [
                            'with_specimen' => '🔬 With Specimen (Requires Physical Specimen)',
                            'without_specimen' => '📋 Without Specimen (No Physical Specimen Needed)'
                        ];
                        $allCategories = ['with_specimen', 'without_specimen'];
                        ?>
                        <?php if (!empty($labTests)): ?>
                            <?php foreach ($allCategories as $category): ?>
                                <?php if (isset($labTests[$category]) && is_array($labTests[$category]) && !empty($labTests[$category])): ?>
                                    <optgroup label="<?= esc($categoryLabels[$category] ?? ucfirst(str_replace('_', ' ', $category))) ?>">
                                        <?php foreach ($labTests[$category] as $testType => $tests): ?>
                                            <?php if (is_array($tests)): ?>
                                                <?php foreach ($tests as $test): ?>
                                                    <?php if (is_array($test)): ?>
                                                        <option value="<?= esc($test['test_name']) ?>" 
                                                                data-test-type="<?= esc($test['test_type']) ?>"
                                                                data-specimen-category="<?= esc($test['specimen_category'] ?? 'with_specimen') ?>"
                                                                data-description="<?= esc($test['description'] ?? '') ?>"
                                                                data-normal-range="<?= esc($test['normal_range'] ?? '') ?>"
                                                                data-price="<?= esc($test['price'] ?? '0.00') ?>">
                                                            <?= esc($test['test_name']) ?> 
                                                            <span style="color: #64748b;">(<?= esc($test['test_type']) ?>)</span>
                                                            <?php if (!empty($test['price'])): ?>
                                                                - ₱<?= number_format($test['price'], 2) ?>
                                                            <?php endif; ?>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No lab tests available. Please add lab tests first.</option>
                        <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="walkin_request_date">Date of Request / Sample Collection <span class="required">*</span></label>
                                <input type="date" id="walkin_request_date" name="walkin_request_date" class="form-control" value="<?= date('Y-m-d') ?>" required readonly style="background: #f8fafc; cursor: not-allowed;">
                                <div class="form-hint">Automatically set to today's date</div>
                            </div>
                        </div>
                        
                        <div id="walkin_test_info" style="display: none; margin-top: 16px; padding: 16px; background: #e0f2fe; border-radius: 10px; border-left: 4px solid #0288d1;">
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                                <div>
                                    <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Test Type</div>
                                    <div id="walkin_test_type_display" style="font-weight: 600; color: #1e293b;"></div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Price</div>
                                    <div id="walkin_test_price_display" style="font-weight: 600; color: #2e7d32;"></div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Normal Range</div>
                                    <div id="walkin_test_range_display" style="font-weight: 600; color: #1e293b;"></div>
                                </div>
                            </div>
                            <div id="walkin_test_description_display" style="margin-top: 12px; font-size: 13px; color: #475569;"></div>
                        </div>
                    </div>
                    
                    <!-- 3. Nurse Assignment (for with_specimen tests) -->
                    <div class="form-section" id="walkin_nurse_field_group" style="display: none;">
                        <h3 class="section-title">
                            <i class="fas fa-user-nurse"></i> Nurse Assignment
                        </h3>
                        
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="form-label" for="walkin_nurse_id">Nurse <span id="walkin_nurse_required_indicator" class="required" style="display: none;">*</span> <span id="walkin_nurse_label_text">(Will collect specimen)</span></label>
                                <select id="walkin_nurse_id" name="walkin_nurse_id" class="form-select">
                                    <option value="">-- Select Nurse --</option>
                                    <?php foreach ($nurses as $nurse): ?>
                                        <option value="<?= esc($nurse['id']) ?>">
                                            <?= esc($nurse['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-hint" id="walkin_nurse_help_text">Select a nurse who will collect the specimen from the patient</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Walk-in Submit Button -->
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-check-circle"></i> Create Lab Service
                        </button>
                        <a href="<?= base_url('admin/lab') ?>" class="btn-cancel">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patient Selection Form -->
        <div id="patient_form">
            <div class="form-card">
                <div class="form-card-body">
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-user"></i> Patient Information
                        </h3>
                        
                        <div class="form-row">
                            <div class="form-group full-width" id="admin_patients_group">
                                <label class="form-label" for="patient_id_admin">Patient <span class="required">*</span></label>
                                <select id="patient_id_admin" name="patient_id" class="form-select patient-select">
                                    <option value="">Select Patient</option>
                                    <?php foreach ($adminPatients as $patient): ?>
                                        <option value="<?= esc($patient['id']) ?>" <?= set_select('patient_id', $patient['id']) ?>>
                                            <?= esc(trim($patient['firstname'] . ' ' . $patient['lastname'])) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group full-width" id="hms_patients_group" style="display: none;">
                                <label class="form-label" for="patient_id_hms">Patient <span class="required">*</span></label>
                                <select id="patient_id_hms" class="form-select patient-select">
                                    <option value="">Select Patient</option>
                                    <?php foreach ($hmsPatients as $patient): ?>
                                        <option value="<?= esc($patient['id']) ?>" <?= set_select('patient_id', $patient['id']) ?>>
                                            <?= esc(trim($patient['firstname'] . ' ' . $patient['lastname'])) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section" id="nurse_field_group" style="display: none;">
                        <h3 class="section-title">
                            <i class="fas fa-user-nurse"></i> Nurse Assignment
                        </h3>
                        
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="form-label" for="nurse_id">Nurse <span id="nurse_required_indicator" class="required" style="display: none;">*</span> <span id="nurse_label_text">(Will collect specimen)</span></label>
                                <select id="nurse_id" name="nurse_id" class="form-select">
                                    <option value="">-- Select Nurse --</option>
                                    <?php foreach ($nurses as $nurse): ?>
                                        <option value="<?= esc($nurse['id']) ?>">
                                            <?= esc($nurse['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-hint" id="nurse_help_text">Select a nurse who will collect the specimen from the patient</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section" id="patient_test_section">
                        <h3 class="section-title">
                            <i class="fas fa-vial"></i> Lab Test Selection
                        </h3>
                        
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="form-label" for="test_type">Lab Test <span class="required">*</span></label>
                                <select id="test_type" name="test_type" class="form-select" onchange="updateTestInfo()">
                <option value="">-- Select Lab Test --</option>
                <?php
                $categoryLabels = [
                    'with_specimen' => '🔬 With Specimen (Requires Physical Specimen)',
                    'without_specimen' => '📋 Without Specimen (No Physical Specimen Needed)'
                ];
                
                // Ensure both categories are shown, even if empty
                $allCategories = ['with_specimen', 'without_specimen'];
                ?>
                <?php if (!empty($labTests)): ?>
                    <?php foreach ($allCategories as $category): ?>
                        <?php if (isset($labTests[$category]) && is_array($labTests[$category]) && !empty($labTests[$category])): ?>
                            <optgroup label="<?= esc($categoryLabels[$category] ?? ucfirst(str_replace('_', ' ', $category))) ?>">
                                <?php foreach ($labTests[$category] as $testType => $tests): ?>
                                    <?php if (is_array($tests)): ?>
                                        <?php foreach ($tests as $test): ?>
                                            <?php if (is_array($test)): ?>
                                                <option value="<?= esc($test['test_name']) ?>" 
                                                        data-test-type="<?= esc($test['test_type']) ?>"
                                                        data-specimen-category="<?= esc($test['specimen_category'] ?? 'with_specimen') ?>"
                                                        data-description="<?= esc($test['description'] ?? '') ?>"
                                                        data-normal-range="<?= esc($test['normal_range'] ?? '') ?>"
                                                        data-price="<?= esc($test['price'] ?? '0.00') ?>">
                                                    <?= esc($test['test_name']) ?> 
                                                    <span style="color: #64748b;">(<?= esc($test['test_type']) ?>)</span>
                                                    <?php if (!empty($test['price'])): ?>
                                                        - ₱<?= number_format($test['price'], 2) ?>
                                                    <?php endif; ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="" disabled>No lab tests available. Please add lab tests first.</option>
                <?php endif; ?>
            </select>
            <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">
                <i class="fas fa-info-circle"></i> Select a lab test from the dropdown
            </small>
        </div>
        
        <div id="test_info" style="display: none; margin-top: 16px; padding: 16px; background: #e0f2fe; border-radius: 10px; border-left: 4px solid #0288d1;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                <div>
                    <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Test Type</div>
                    <div id="test_type_display" style="font-weight: 600; color: #1e293b;"></div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Price</div>
                    <div id="test_price_display" style="font-weight: 600; color: #2e7d32;"></div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Normal Range</div>
                    <div id="test_range_display" style="font-weight: 600; color: #1e293b;"></div>
                </div>
            </div>
            <div id="test_description_display" style="margin-top: 12px; font-size: 13px; color: #475569;"></div>
        </div>
                    </div>
                    
                    <!-- Patient Form Submit Button -->
                    <div class="form-actions" id="patient_form_actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-check-circle"></i> Create Lab Service
                        </button>
                        <a href="<?= base_url('admin/lab') ?>" class="btn-cancel">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.admin-module { padding: 24px; }
.module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.module-header h2 { margin: 0; color: #2e7d32; }
.btn { padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; border: none; cursor: pointer; }
.btn-primary { background: #2e7d32; color: white; }
.btn-secondary { background: #6b7280; color: white; }
.form-container { background: white; padding: 24px; border-radius: 8px; max-width: 600px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #374151; }
.form-control { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
.form-actions { display: flex; gap: 12px; margin-top: 24px; }
.form-control:focus { outline: none; border-color: #2e7d32; box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1); }
</style>

<script>
function togglePatientForm() {
    const walkinRadio = document.getElementById('patient_source_walkin');
    const patientRadio = document.getElementById('patient_source_patient');
    const walkinForm = document.getElementById('walkin_form');
    const patientForm = document.getElementById('patient_form');
    const patientTestSection = document.getElementById('patient_test_section');
    const patientFormActions = document.getElementById('patient_form_actions');
    
    if (walkinRadio.checked) {
        walkinForm.style.display = 'block';
        patientForm.style.display = 'none';
        patientTestSection.style.display = 'none';
        patientFormActions.style.display = 'none';
        // Make walk-in fields required
        document.getElementById('walkin_full_name').setAttribute('required', 'required');
        document.getElementById('walkin_gender').setAttribute('required', 'required');
        document.getElementById('walkin_contact').setAttribute('required', 'required');
        document.getElementById('walkin_test_type').setAttribute('required', 'required');
        document.getElementById('walkin_request_date').setAttribute('required', 'required');
        // Remove required from patient fields
        document.getElementById('test_type').removeAttribute('required');
        document.getElementById('patient_id_admin').removeAttribute('required');
    } else if (patientRadio.checked) {
        walkinForm.style.display = 'none';
        patientForm.style.display = 'block';
        patientTestSection.style.display = 'block';
        patientFormActions.style.display = 'flex';
        // Make patient fields required
        document.getElementById('test_type').setAttribute('required', 'required');
        document.getElementById('patient_id_admin').setAttribute('required', 'required');
        // Remove required from walk-in fields
        document.getElementById('walkin_full_name').removeAttribute('required');
        document.getElementById('walkin_gender').removeAttribute('required');
        document.getElementById('walkin_contact').removeAttribute('required');
        document.getElementById('walkin_test_type').removeAttribute('required');
        document.getElementById('walkin_request_date').removeAttribute('required');
    }
}

function updateWalkinTestInfo() {
    const select = document.getElementById('walkin_test_type');
    const selectedOption = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('walkin_test_info');
    const nurseFieldGroup = document.getElementById('walkin_nurse_field_group');
    const nurseSelect = document.getElementById('walkin_nurse_id');
    const nurseRequiredIndicator = document.getElementById('walkin_nurse_required_indicator');
    const nurseLabelText = document.getElementById('walkin_nurse_label_text');
    const nurseHelpText = document.getElementById('walkin_nurse_help_text');
    
    if (!selectedOption || !selectedOption.value || selectedOption.value === '') {
        infoDiv.style.display = 'none';
        nurseFieldGroup.style.display = 'none';
        nurseSelect.removeAttribute('required');
        nurseRequiredIndicator.style.display = 'none';
        return;
    }
    
    const testType = selectedOption.dataset.testType || '';
    const description = selectedOption.dataset.description || '';
    const normalRange = selectedOption.dataset.normalRange || 'N/A';
    const price = selectedOption.dataset.price || '0.00';
    const specimenCategory = selectedOption.dataset.specimenCategory || 'with_specimen';
    
    document.getElementById('walkin_test_type_display').textContent = testType || 'N/A';
    document.getElementById('walkin_test_price_display').textContent = price > 0 ? '₱' + parseFloat(price).toFixed(2) : 'Free';
    document.getElementById('walkin_test_range_display').textContent = normalRange || 'N/A';
    document.getElementById('walkin_test_description_display').textContent = description || 'No description available';
    
    // Show test info
    infoDiv.style.display = 'block';
    
    // Handle nurse field based on specimen category - same logic as patient form
    if (specimenCategory === 'with_specimen') {
        // With specimen - nurse is REQUIRED
        nurseFieldGroup.style.display = 'block';
        nurseSelect.setAttribute('required', 'required');
        nurseRequiredIndicator.style.display = 'inline';
        nurseLabelText.textContent = '(Will collect specimen)';
        nurseHelpText.innerHTML = '<i class="fas fa-info-circle"></i> <span style="color: #ef4444;">Required:</span> Select a nurse who will collect the specimen from the patient';
    } else {
        // Without specimen - nurse is NOT required - HIDE the field
        nurseFieldGroup.style.display = 'none';
        nurseSelect.removeAttribute('required');
        nurseRequiredIndicator.style.display = 'none';
        nurseSelect.value = ''; // Clear selection
    }
}

// Calculate age from date of birth
function calculateAge() {
    const dobInput = document.getElementById('walkin_date_of_birth');
    const ageInput = document.getElementById('walkin_age');
    
    if (dobInput && dobInput.value) {
        const birthDate = new Date(dobInput.value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        // Adjust age if birthday hasn't occurred this year
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        if (age >= 0 && age <= 150) {
            ageInput.value = age;
        } else {
            ageInput.value = '';
        }
    } else {
        ageInput.value = '';
    }
}

// Call on page load to set initial state
document.addEventListener('DOMContentLoaded', function() {
    togglePatientForm();
    
    // Set walk-in request date to today (always) - readonly
    const walkinRequestDate = document.getElementById('walkin_request_date');
    if (walkinRequestDate) {
        const today = new Date().toISOString().split('T')[0];
        walkinRequestDate.value = today;
        // Ensure it's always today even if user tries to change it
        walkinRequestDate.addEventListener('change', function() {
            this.value = today;
        });
    }
    
    // If date of birth has a value on load, calculate age
    const dobInput = document.getElementById('walkin_date_of_birth');
    if (dobInput && dobInput.value) {
        calculateAge();
    }
});

function updateTestInfo() {
    const select = document.getElementById('test_type');
    const selectedOption = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('test_info');
    const nurseField = document.getElementById('nurse_id');
    const nurseFieldGroup = document.getElementById('nurse_field_group');
    const nurseRequiredIndicator = document.getElementById('nurse_required_indicator');
    const nurseLabelText = document.getElementById('nurse_label_text');
    const nurseHelpText = document.getElementById('nurse_help_text');
    
    if (!selectedOption || !selectedOption.value || selectedOption.value === '') {
        infoDiv.style.display = 'none';
        // Reset nurse field to default
        nurseField.required = false;
        nurseRequiredIndicator.style.display = 'none';
        nurseLabelText.textContent = '(Will collect specimen)';
        nurseHelpText.textContent = 'Select a nurse who will collect the specimen from the patient';
        return;
    }
    
    const testType = selectedOption.dataset.testType || '';
    const description = selectedOption.dataset.description || '';
    const normalRange = selectedOption.dataset.normalRange || 'N/A';
    const price = selectedOption.dataset.price || '0.00';
    const specimenCategory = selectedOption.dataset.specimenCategory || 'with_specimen';
    
    document.getElementById('test_type_display').textContent = testType || 'N/A';
    document.getElementById('test_price_display').textContent = price > 0 ? '₱' + parseFloat(price).toFixed(2) : 'Free';
    document.getElementById('test_range_display').textContent = normalRange || 'N/A';
    document.getElementById('test_description_display').textContent = description || 'No description available';
    
    infoDiv.style.display = 'block';
    
    // Update nurse field based on specimen category
    if (specimenCategory === 'with_specimen') {
        // With specimen - nurse is REQUIRED
        nurseFieldGroup.style.display = 'block';
        nurseField.setAttribute('required', 'required');
        nurseRequiredIndicator.style.display = 'inline';
        nurseLabelText.textContent = '(Will collect specimen)';
        nurseHelpText.innerHTML = '<i class="fas fa-info-circle"></i> <span style="color: #ef4444;">Required:</span> Select a nurse who will collect the specimen from the patient';
    } else {
        // Without specimen - nurse is NOT required - HIDE the field
        nurseFieldGroup.style.display = 'none';
        nurseField.removeAttribute('required');
        nurseField.value = ''; // Clear selection
    }
}

</script>
<?= $this->endSection() ?>

