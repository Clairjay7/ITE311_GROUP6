<?php helper('form'); ?>
<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Start Consultation<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .doctor-page-container {
        padding: 0;
    }
    
    .page-header {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(2, 136, 209, 0.2);
        color: white;
    }
    
    .page-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        padding: 32px;
        margin-bottom: 24px;
    }
    
    .patient-info-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        border-left: 4px solid #0288d1;
    }
    
    .patient-info-card h3 {
        margin: 0 0 16px 0;
        color: #1e293b;
        font-size: 18px;
        font-weight: 600;
    }
    
    .patient-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }
    
    .info-item {
        display: flex;
        flex-direction: column;
    }
    
    .info-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
        margin-bottom: 4px;
    }
    
    .info-value {
        font-size: 14px;
        color: #1e293b;
        font-weight: 600;
    }
    
    .form-group-modern {
        margin-bottom: 24px;
    }
    
    .form-label-modern {
        display: block;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .form-label-modern .required {
        color: #ef4444;
    }
    
    .form-control-modern {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
        font-family: inherit;
    }
    
    .form-control-modern:focus {
        outline: none;
        border-color: #0288d1;
        box-shadow: 0 0 0 3px rgba(2, 136, 209, 0.1);
    }
    
    textarea.form-control-modern {
        min-height: 120px;
        resize: vertical;
    }
    
    .btn-modern {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        cursor: pointer;
    }
    
    .btn-modern-primary {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
    }
    
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(2, 136, 209, 0.4);
        color: white;
    }
    
    .btn-modern-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    
    .btn-modern-secondary:hover {
        background: #e2e8f0;
        color: #475569;
    }
    
    .alert {
        padding: 16px;
        border-radius: 10px;
        margin-bottom: 24px;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }
    
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
</style>

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-stethoscope"></i>
            Start Consultation
        </h1>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> Please correct the following errors:
            <ul style="margin: 8px 0 0 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Patient Information Card -->
    <div class="patient-info-card">
        <h3><i class="fas fa-user-injured"></i> Patient Information</h3>
        <div class="patient-info-grid">
            <div class="info-item">
                <span class="info-label">Patient Name</span>
                <span class="info-value">
                    <?= esc($patient['full_name'] ?? ($patient['first_name'] ?? $patient['firstname'] ?? '') . ' ' . ($patient['last_name'] ?? $patient['lastname'] ?? '')) ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Patient ID</span>
                <span class="info-value">#<?= esc($patient['patient_id'] ?? $patient['id']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Date of Birth</span>
                <span class="info-value"><?= esc($patient['date_of_birth'] ?? $patient['birthdate'] ?? 'N/A') ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Gender</span>
                <span class="info-value"><?= esc(ucfirst($patient['gender'] ?? 'N/A')) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Contact</span>
                <span class="info-value"><?= esc($patient['contact'] ?? 'N/A') ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Visit Type</span>
                <span class="info-value"><?= esc($patient['visit_type'] ?? $patient['type'] ?? 'N/A') ?></span>
            </div>
        </div>
    </div>

    <!-- Consultation Form -->
    <div class="modern-card">
        <form method="post" action="<?= site_url('doctor/consultations/store') ?>" id="consultationForm">
            <?= csrf_field() ?>
            
            <input type="hidden" name="patient_id" value="<?= esc($patientId) ?>">
            <input type="hidden" name="source" value="<?= esc($source) ?>">
            
            <div class="form-group-modern">
                <label class="form-label-modern">
                    Chief Complaint <span class="required">*</span>
                </label>
                <textarea 
                    name="chief_complaint" 
                    class="form-control-modern" 
                    required 
                    placeholder="Enter the patient's chief complaint..."
                    rows="3"><?= set_value('chief_complaint') ?></textarea>
            </div>


            <!-- Prescription and Lab Test Selection -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <i class="fas fa-pills"></i> Prescription
                    </label>
                    <div style="margin-bottom: 12px;">
                        <input 
                            type="text" 
                            id="prescription_search" 
                            placeholder="Search medicines..." 
                            class="form-control-modern"
                            style="width: 100%; padding: 10px 16px; font-size: 14px;">
                    </div>
                    <div id="prescription_container" style="background: #f8fafc; border: 2px solid #e5e7eb; border-radius: 12px; padding: 16px; max-height: 300px; overflow-y: auto;">
                        <?php if (!empty($medicines)): ?>
                            <?php
                            // Group medicines by category
                            $groupedMedicines = [];
                            foreach ($medicines as $med) {
                                $category = $med['category'] ?? 'Other';
                                if (!isset($groupedMedicines[$category])) {
                                    $groupedMedicines[$category] = [];
                                }
                                $groupedMedicines[$category][] = $med;
                            }
                            ?>
                            <?php foreach ($groupedMedicines as $category => $meds): ?>
                                <div class="prescription-category" data-category="<?= esc(strtolower($category)) ?>" style="margin-bottom: 20px;">
                                    <h5 style="margin: 0 0 12px 0; color: #0288d1; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px;">
                                        <i class="fas fa-tag"></i> <?= esc($category) ?>
                                    </h5>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 8px;">
                                        <?php foreach ($meds as $med): ?>
                                            <?php
                                            $stock = $med['quantity'] ?? 0;
                                            $stockClass = $stock > 10 ? 'color: #10b981;' : ($stock > 0 ? 'color: #f59e0b;' : 'color: #ef4444;');
                                            $stockText = $stock > 10 ? "Stock: {$stock}" : ($stock > 0 ? "Low Stock: {$stock}" : 'Out of Stock');
                                            ?>
                                            <label class="prescription-item" 
                                                   data-medicine-name="<?= esc(strtolower($med['item_name'] . ' ' . ($med['generic_name'] ?? ''))) ?>"
                                                   data-category="<?= esc(strtolower($category)) ?>"
                                                   style="display: flex; align-items: center; padding: 10px; background: white; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s ease; user-select: none;" 
                                                   onmouseover="if(!this.querySelector('input[type=checkbox]').checked) { this.style.borderColor='#0288d1'; this.style.backgroundColor='#e3f2fd'; }" 
                                                   onmouseout="if(!this.querySelector('input[type=checkbox]').checked) { this.style.borderColor='#e5e7eb'; this.style.backgroundColor='white'; }">
                                                <input type="checkbox" 
                                                       name="prescription[]" 
                                                       value="<?= esc($med['id']) ?>" 
                                                       class="prescription-checkbox"
                                                       data-medicine-id="<?= esc($med['id']) ?>"
                                                       data-medicine-name="<?= esc($med['item_name']) ?>"
                                                       style="width: 18px; height: 18px; margin-right: 12px; cursor: pointer; accent-color: #0288d1;"
                                                       <?= $stock <= 0 ? 'disabled' : '' ?>>
                                                <span style="font-size: 13px; font-weight: 500; color: #1e293b; flex: 1;">
                                                    <?= esc($med['item_name']) ?>
                                                    <?php if (!empty($med['generic_name'])): ?>
                                                        <br><small style="color: #64748b; font-size: 11px;"><?= esc($med['generic_name']) ?></small>
                                                    <?php endif; ?>
                                                </span>
                                                <span style="font-size: 11px; <?= $stockClass ?> font-weight: 600; margin-left: 8px;">
                                                    <?= $stockText ?>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color: #ef4444; padding: 12px; text-align: center;">No medicines available in pharmacy.</p>
                        <?php endif; ?>
                    </div>
                    <small style="color: #64748b; font-size: 12px; margin-top: 8px; display: block;">
                        <i class="fas fa-info-circle"></i> Select one or more medicines by checking the boxes
                    </small>
                    
                    <!-- Prescription Details Section (shown when medicines are selected) -->
                    <div id="prescription_details_section" style="display: none; margin-top: 20px; background: #f0fdf4; border: 2px solid #10b981; border-radius: 12px; padding: 20px;">
                        <h4 style="margin: 0 0 16px 0; color: #065f46; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-prescription-bottle-alt"></i> Prescription Details
                        </h4>
                        <div id="prescription_details_container">
                            <!-- Dynamic prescription details will be added here -->
                        </div>
                    </div>
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <i class="fas fa-vial"></i> Lab Test
                    </label>
                    <div style="margin-bottom: 12px;">
                        <input 
                            type="text" 
                            id="lab_test_search" 
                            placeholder="Search lab tests..." 
                            class="form-control-modern"
                            style="width: 100%; padding: 10px 16px; font-size: 14px;">
                    </div>
                    <div id="lab_test_container" style="background: #f8fafc; border: 2px solid #e5e7eb; border-radius: 12px; padding: 16px; max-height: 300px; overflow-y: auto;">
                        <?php if (!empty($labTests)): ?>
                            <?php
                            // Group lab tests by test_type
                            $groupedLabTests = [];
                            foreach ($labTests as $test) {
                                $testType = $test['test_type'] ?? 'Other';
                                if (!isset($groupedLabTests[$testType])) {
                                    $groupedLabTests[$testType] = [];
                                }
                                $groupedLabTests[$testType][] = $test;
                            }
                            ?>
                            <?php foreach ($groupedLabTests as $testType => $tests): ?>
                                <div class="lab-test-category" data-test-type="<?= esc(strtolower($testType)) ?>" style="margin-bottom: 20px;">
                                    <h5 style="margin: 0 0 12px 0; color: #0288d1; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px;">
                                        <i class="fas fa-flask"></i> <?= esc($testType) ?>
                                    </h5>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 8px;">
                                        <?php foreach ($tests as $test): ?>
                                            <label class="lab-test-item" 
                                                   data-test-name="<?= esc(strtolower($test['test_name'])) ?>"
                                                   data-test-type="<?= esc(strtolower($testType)) ?>"
                                                   style="display: flex; align-items: center; padding: 10px; background: white; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s ease; user-select: none;" 
                                                   onmouseover="if(!this.querySelector('input[type=checkbox]').checked) { this.style.borderColor='#0288d1'; this.style.backgroundColor='#e3f2fd'; }" 
                                                   onmouseout="if(!this.querySelector('input[type=checkbox]').checked) { this.style.borderColor='#e5e7eb'; this.style.backgroundColor='white'; }">
                                                <input type="checkbox" 
                                                       name="lab_test[]" 
                                                       value="<?= esc($test['id']) ?>" 
                                                       class="lab-test-checkbox"
                                                       data-test-id="<?= esc($test['id']) ?>"
                                                       data-test-name="<?= esc($test['test_name']) ?>"
                                                       data-specimen-category="<?= esc($test['specimen_category'] ?? 'with_specimen') ?>"
                                                       style="width: 18px; height: 18px; margin-right: 12px; cursor: pointer; accent-color: #0288d1;">
                                                <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                                                    <span style="font-size: 13px; font-weight: 500; color: #1e293b;">
                                                        <?= esc($test['test_name']) ?>
                                                    </span>
                                                    <?php 
                                                    $specimenCategory = $test['specimen_category'] ?? 'with_specimen';
                                                    $isWithSpecimen = ($specimenCategory === 'with_specimen');
                                                    ?>
                                                    <span style="font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: 600; display: inline-block; width: fit-content; <?= $isWithSpecimen ? 'background: #e3f2fd; color: #1976d2;' : 'background: #fff3e0; color: #f57c00;' ?>">
                                                        <i class="fas <?= $isWithSpecimen ? 'fa-vial' : 'fa-flask' ?>"></i> 
                                                        <?= $isWithSpecimen ? 'With Specimen' : 'Without Specimen' ?>
                                                    </span>
                                                </div>
                                                <?php if (!empty($test['price'])): ?>
                                                    <span style="font-size: 11px; color: #64748b; font-weight: 600; margin-left: 8px;">
                                                        ₱<?= number_format($test['price'], 2) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color: #ef4444; padding: 12px; text-align: center;">No lab tests available.</p>
                        <?php endif; ?>
                    </div>
                    <small style="color: #64748b; font-size: 12px; margin-top: 8px; display: block;">
                        <i class="fas fa-info-circle"></i> Select one or more lab tests by checking the boxes
                    </small>
                    
                    <!-- Nurse Assignment (shown when "with_specimen" lab tests are selected) -->
                    <div id="nurse_field_group" style="display: none; margin-top: 20px; background: #e0f2fe; border: 2px solid #0288d1; border-radius: 12px; padding: 20px;">
                        <h4 style="margin: 0 0 16px 0; color: #0288d1; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-user-nurse"></i> Nurse Assignment
                        </h4>
                        <div class="form-group-modern" style="margin-bottom: 0;">
                            <label class="form-label-modern">
                                Nurse <span id="nurse_required_indicator" class="required" style="display: none;">*</span> 
                                <span id="nurse_label_text">(Will collect specimen)</span>
                            </label>
                            <select 
                                id="nurse_id" 
                                name="nurse_id" 
                                class="form-control-modern"
                                style="width: 100%;">
                                <option value="">-- Select Nurse --</option>
                                <?php if (!empty($nurses)): ?>
                                    <?php foreach ($nurses as $nurse): ?>
                                        <option value="<?= esc($nurse['id']) ?>" <?= (!($nurse['is_available'] ?? true)) ? 'disabled style="color: #ef4444;"' : '' ?>>
                                            <?= esc($nurse['username'] ?? $nurse['email'] ?? 'Nurse #' . $nurse['id']) ?>
                                            <?php if (!($nurse['is_available'] ?? true)): ?>
                                                (Unavailable)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No nurses available</option>
                                <?php endif; ?>
                            </select>
                            <small id="nurse_help_text" style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">
                                <i class="fas fa-info-circle"></i> Select a nurse who will collect the specimen from the patient
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Follow-up Checkbox -->
            <div class="form-group-modern" style="background: #f8fafc; border: 2px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; user-select: none;">
                    <input 
                        type="checkbox" 
                        name="follow_up" 
                        value="1" 
                        id="follow_up_checkbox"
                        style="width: 20px; height: 20px; cursor: pointer; accent-color: #0288d1;">
                    <span style="font-size: 15px; font-weight: 600; color: #1e293b;">
                        <i class="fas fa-calendar-check"></i> Schedule Follow-up Consultation
                    </span>
                </label>
                <div id="follow_up_details" style="display: none; margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group-modern" style="margin-bottom: 0;">
                            <label class="form-label-modern">Follow-up Date</label>
                            <input 
                                type="date" 
                                name="follow_up_date" 
                                class="form-control-modern" 
                                min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>
                        <div class="form-group-modern" style="margin-bottom: 0;">
                            <label class="form-label-modern">Follow-up Time</label>
                            <input 
                                type="time" 
                                name="follow_up_time" 
                                class="form-control-modern">
                        </div>
                    </div>
                    <div class="form-group-modern" style="margin-bottom: 0; margin-top: 12px;">
                        <label class="form-label-modern">Follow-up Reason</label>
                        <textarea 
                            name="follow_up_reason" 
                            class="form-control-modern" 
                            placeholder="Reason for follow-up consultation..."
                            rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px;">
                <a href="<?= site_url('doctor/patients') ?>" class="btn-modern btn-modern-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn-modern btn-modern-primary">
                    <i class="fas fa-save"></i> Complete Consultation
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Appointment date and time are already set from the backend
    // No need to override them
    
    // Prescription Search Functionality
    const prescriptionSearch = document.getElementById('prescription_search');
    if (prescriptionSearch) {
        prescriptionSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const prescriptionItems = document.querySelectorAll('.prescription-item');
            const prescriptionCategories = document.querySelectorAll('.prescription-category');
            
            prescriptionItems.forEach(item => {
                const medicineName = item.getAttribute('data-medicine-name') || '';
                const category = item.getAttribute('data-category') || '';
                
                if (searchTerm === '' || medicineName.includes(searchTerm) || category.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Hide/show categories based on visible items
            prescriptionCategories.forEach(category => {
                const categoryItems = category.querySelectorAll('.prescription-item');
                const visibleItems = Array.from(categoryItems).filter(item => item.style.display !== 'none');
                
                if (visibleItems.length === 0) {
                    category.style.display = 'none';
                } else {
                    category.style.display = 'block';
                }
            });
        });
    }
    
    // Lab Test Search Functionality
    const labTestSearch = document.getElementById('lab_test_search');
    if (labTestSearch) {
        labTestSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const labTestItems = document.querySelectorAll('.lab-test-item');
            const labTestCategories = document.querySelectorAll('.lab-test-category');
            
            labTestItems.forEach(item => {
                const testName = item.getAttribute('data-test-name') || '';
                const testType = item.getAttribute('data-test-type') || '';
                
                if (searchTerm === '' || testName.includes(searchTerm) || testType.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Hide/show categories based on visible items
            labTestCategories.forEach(category => {
                const categoryItems = category.querySelectorAll('.lab-test-item');
                const visibleItems = Array.from(categoryItems).filter(item => item.style.display !== 'none');
                
                if (visibleItems.length === 0) {
                    category.style.display = 'none';
                } else {
                    category.style.display = 'block';
                }
            });
        });
    }
    
    // Prescription Details - Show fields when medicines are selected
    const prescriptionCheckboxes = document.querySelectorAll('.prescription-checkbox');
    const prescriptionDetailsSection = document.getElementById('prescription_details_section');
    const prescriptionDetailsContainer = document.getElementById('prescription_details_container');
    
    function updatePrescriptionDetails() {
        const selectedMedicines = Array.from(document.querySelectorAll('.prescription-checkbox:checked'));
        
        if (selectedMedicines.length > 0) {
            prescriptionDetailsSection.style.display = 'block';
            
            // Clear existing details
            prescriptionDetailsContainer.innerHTML = '';
            
            selectedMedicines.forEach((checkbox, index) => {
                const medicineId = checkbox.value;
                const medicineName = checkbox.getAttribute('data-medicine-name') || 'Medicine';
                
                const detailCard = document.createElement('div');
                detailCard.className = 'prescription-detail-card';
                detailCard.style.cssText = 'background: white; border: 2px solid #10b981; border-radius: 10px; padding: 16px; margin-bottom: 16px;';
                detailCard.innerHTML = `
                    <h5 style="margin: 0 0 12px 0; color: #065f46; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-pills"></i> ${medicineName}
                    </h5>
                    <input type="hidden" name="prescription_details[${index}][medicine_id]" value="${medicineId}">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="form-group-modern" style="margin-bottom: 0;">
                            <label class="form-label-modern" style="font-size: 13px;">Dosage</label>
                            <input 
                                type="text" 
                                name="prescription_details[${index}][dosage]" 
                                class="form-control-modern" 
                                placeholder="e.g., 500mg, 1 tablet"
                                style="font-size: 13px; padding: 8px 12px;">
                        </div>
                        <div class="form-group-modern" style="margin-bottom: 0;">
                            <label class="form-label-modern" style="font-size: 13px;">Frequency</label>
                            <input 
                                type="text" 
                                name="prescription_details[${index}][frequency]" 
                                class="form-control-modern" 
                                placeholder="e.g., Every 8 hours, Once daily"
                                style="font-size: 13px; padding: 8px 12px;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="form-group-modern" style="margin-bottom: 0;">
                            <label class="form-label-modern" style="font-size: 13px;">Duration</label>
                            <input 
                                type="text" 
                                name="prescription_details[${index}][duration]" 
                                class="form-control-modern" 
                                placeholder="e.g., 7 days, 2 weeks"
                                style="font-size: 13px; padding: 8px 12px;">
                        </div>
                        <div class="form-group-modern" style="margin-bottom: 0;">
                            <label class="form-label-modern" style="font-size: 13px;">When to Take</label>
                            <select 
                                name="prescription_details[${index}][when_to_take]" 
                                class="form-control-modern"
                                style="font-size: 13px; padding: 8px 12px;">
                                <option value="">-- Select --</option>
                                <option value="before_meal">Before Meal</option>
                                <option value="after_meal">After Meal</option>
                                <option value="with_meal">With Meal</option>
                                <option value="empty_stomach">Empty Stomach</option>
                                <option value="as_needed">As Needed (PRN)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group-modern" style="margin-bottom: 0;">
                        <label class="form-label-modern" style="font-size: 13px;">Instructions</label>
                        <textarea 
                            name="prescription_details[${index}][instructions]" 
                            class="form-control-modern" 
                            placeholder="Additional instructions for taking this medicine..."
                            rows="2"
                            style="font-size: 13px; padding: 8px 12px;"></textarea>
                    </div>
                `;
                prescriptionDetailsContainer.appendChild(detailCard);
            });
        } else {
            prescriptionDetailsSection.style.display = 'none';
            prescriptionDetailsContainer.innerHTML = '';
        }
    }
    
    // Add event listeners to prescription checkboxes
    prescriptionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updatePrescriptionDetails);
    });
    
    // Follow-up checkbox toggle
    const followUpCheckbox = document.getElementById('follow_up_checkbox');
    const followUpDetails = document.getElementById('follow_up_details');
    
    if (followUpCheckbox && followUpDetails) {
        followUpCheckbox.addEventListener('change', function() {
            if (this.checked) {
                followUpDetails.style.display = 'block';
            } else {
                followUpDetails.style.display = 'none';
            }
        });
    }
    
    // Lab Test Specimen Category Logic - Similar to Admin Lab Form
    const labTestCheckboxes = document.querySelectorAll('.lab-test-checkbox');
    const nurseFieldGroup = document.getElementById('nurse_field_group');
    const nurseSelect = document.getElementById('nurse_id');
    const nurseRequiredIndicator = document.getElementById('nurse_required_indicator');
    const nurseLabelText = document.getElementById('nurse_label_text');
    const nurseHelpText = document.getElementById('nurse_help_text');
    
    function updateLabTestSpecimenInfo() {
        const selectedLabTests = Array.from(document.querySelectorAll('.lab-test-checkbox:checked'));
        
        // Check if any selected test requires specimen
        const hasWithSpecimen = selectedLabTests.some(checkbox => {
            const specimenCategory = checkbox.getAttribute('data-specimen-category') || 'with_specimen';
            return specimenCategory === 'with_specimen';
        });
        
        const hasWithoutSpecimen = selectedLabTests.some(checkbox => {
            const specimenCategory = checkbox.getAttribute('data-specimen-category') || 'with_specimen';
            return specimenCategory === 'without_specimen';
        });
        
        // Handle nurse field based on specimen category - same logic as admin form
        if (hasWithSpecimen && nurseFieldGroup && nurseSelect) {
            // With specimen - nurse is REQUIRED
            nurseFieldGroup.style.display = 'block';
            nurseSelect.setAttribute('required', 'required');
            if (nurseRequiredIndicator) {
                nurseRequiredIndicator.style.display = 'inline';
            }
            if (nurseLabelText) {
                nurseLabelText.textContent = '(Will collect specimen)';
            }
            if (nurseHelpText) {
                nurseHelpText.innerHTML = '<i class="fas fa-info-circle"></i> <span style="color: #ef4444;">Required:</span> Select a nurse who will collect the specimen from the patient';
            }
        } else {
            // Without specimen or no tests selected - nurse is NOT required - HIDE the field
            if (nurseFieldGroup) {
                nurseFieldGroup.style.display = 'none';
            }
            if (nurseSelect) {
                nurseSelect.removeAttribute('required');
                nurseSelect.value = ''; // Clear selection
            }
            if (nurseRequiredIndicator) {
                nurseRequiredIndicator.style.display = 'none';
            }
        }
        
        // Visual feedback: Highlight selected tests based on specimen category
        selectedLabTests.forEach(checkbox => {
            const specimenCategory = checkbox.getAttribute('data-specimen-category') || 'with_specimen';
            const label = checkbox.closest('.lab-test-item');
            
            if (label) {
                if (specimenCategory === 'with_specimen') {
                    // With specimen - blue highlight
                    label.style.borderColor = '#1976d2';
                    label.style.backgroundColor = '#e3f2fd';
                } else {
                    // Without specimen - orange highlight
                    label.style.borderColor = '#f57c00';
                    label.style.backgroundColor = '#fff3e0';
                }
            }
        });
        
        // Log for debugging (similar to admin form logic)
        console.log('Lab Tests Selected:', {
            total: selectedLabTests.length,
            withSpecimen: hasWithSpecimen,
            withoutSpecimen: hasWithoutSpecimen
        });
    }
    
    // Add event listeners to all lab test checkboxes
    labTestCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const label = this.closest('.lab-test-item');
            
            if (!this.checked && label) {
                // Reset styling when unchecked
                label.style.borderColor = '#e5e7eb';
                label.style.backgroundColor = 'white';
            }
            
            // Update specimen info
            updateLabTestSpecimenInfo();
        });
    });
    
    // Initialize on page load if any tests are pre-selected
    updateLabTestSpecimenInfo();
});
</script>

<?= $this->endSection() ?>

