<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Create Medical Order<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .doctor-page-container {
        padding: 0;
    }
    
    .page-header {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(46, 125, 50, 0.2);
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
    
    .form-control-modern {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-control-modern:focus {
        outline: none;
        border-color: #2e7d32;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
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
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
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
    
    .text-danger {
        color: #ef4444;
        font-size: 13px;
        margin-top: 4px;
    }
    
    .alert-modern {
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .alert-modern-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
</style>

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-prescription"></i>
            Create Medical Order
        </h1>
    </div>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert-modern alert-modern-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert-modern alert-modern-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            Please fix the following errors:
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="modern-card">
        <form action="<?= site_url('doctor/orders/store') ?>" method="post">
            <?= csrf_field() ?>
            
            <!-- Preserve vital_id from query string if provided -->
            <?php if (!empty($selected_vital_id ?? null)): ?>
                <input type="hidden" name="vital_id" value="<?= esc($selected_vital_id) ?>">
            <?php endif; ?>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="patient_id">
                    <i class="fas fa-user-injured me-2"></i>
                    Patient <span class="text-danger">*</span>
                </label>
                <select name="patient_id" id="patient_id" class="form-control-modern" required>
                    <option value="">Select Patient</option>
                    <?php foreach ($patients as $patient): ?>
                        <?php 
                        // Always use 'id' field which is set to admin_patients.id for all patients
                        // For receptionist patients, 'id' is already converted to admin_patients.id in the controller
                        $patientId = $patient['id'] ?? $patient['admin_patient_id'] ?? null;
                        $patientName = trim(($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? ''));
                        if (empty($patientName) && !empty($patient['full_name'])) {
                            $patientName = $patient['full_name'];
                        }
                        $sourceLabel = isset($patient['source']) && $patient['source'] === 'receptionist' ? ' (Receptionist)' : '';
                        ?>
                        <?php 
                        // Check if this patient should be selected
                        $isSelected = false;
                        if (!empty($selected_patient_id ?? null)) {
                            $isSelected = ($patientId == $selected_patient_id);
                        } else {
                            $isSelected = (old('patient_id') == $patientId);
                        }
                        ?>
                        <option value="<?= esc($patientId) ?>" data-patient-source="<?= esc($patient['source'] ?? 'admin_patients') ?>" <?= $isSelected ? 'selected' : '' ?>>
                            <?= esc(ucwords($patientName)) ?><?= $sourceLabel ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['patient_id'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['patient_id']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern">
                    <i class="fas fa-tag me-2"></i>
                    Order Type(s) <span class="text-danger">*</span>
                    <small style="display: block; font-weight: normal; color: #64748b; margin-top: 4px;">
                        <i class="fas fa-info-circle"></i> Select one or multiple order types by checking the boxes below.
                    </small>
                </label>
                <div id="orderTypeCheckboxes" style="background: #f8fafc; border: 2px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-top: 12px;">
                    <?php 
                    $selectedOrderTypes = old('order_type') ?? [];
                    if (!is_array($selectedOrderTypes)) {
                        $selectedOrderTypes = [$selectedOrderTypes];
                    }
                    $orderTypeOptions = [
                        'medication' => 'Medication Order (Routes to Pharmacy first)',
                        'lab_test' => 'Laboratory Test Request',
                        'iv_fluids_order' => 'IV Fluids Order',
                        'reassessment_order' => 'Reassessment Order (repeat vitals)',
                    ];
                    ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px;">
                        <?php foreach ($orderTypeOptions as $value => $label): ?>
                            <label style="display: flex; align-items: center; padding: 12px; background: white; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s ease; user-select: none;" 
                                   onmouseover="this.style.borderColor='#2e7d32'; this.style.backgroundColor='#f0fdf4';" 
                                   onmouseout="if(!this.querySelector('input[type=checkbox]').checked) { this.style.borderColor='#e5e7eb'; this.style.backgroundColor='white'; }">
                                <input type="checkbox" 
                                       name="order_type[]" 
                                       value="<?= esc($value) ?>" 
                                       class="order-type-checkbox" 
                                       style="width: 20px; height: 20px; margin-right: 12px; cursor: pointer; accent-color: #2e7d32;"
                                       <?= in_array($value, $selectedOrderTypes) ? 'checked' : '' ?>
                                       onchange="updateOrderTypeSelection(this)">
                                <span style="font-size: 14px; font-weight: 500; color: #1e293b; flex: 1;">
                                    <?= esc($label) ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <small id="selectedOrderTypes" style="display: block; margin-top: 12px; padding: 10px; background: #f1f5f9; border-radius: 8px; color: #475569; border-left: 4px solid #2e7d32;">
                    <strong><i class="fas fa-check-circle"></i> Selected:</strong> <span id="selectedTypesList">None</span>
                </small>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['order_type'])): ?>
                    <div class="text-danger" style="margin-top: 8px;">
                        <i class="fas fa-exclamation-circle"></i> <?= esc(session()->getFlashdata('errors')['order_type']) ?>
            </div>
                <?php endif; ?>
            </div>
            
            <!-- Dynamic Order Type Fields Container -->
            <div id="orderTypeFieldsContainer" style="margin-top: 24px;"></div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group-modern">
                        <label class="form-label-modern" for="frequency">
                            <i class="fas fa-clock me-2"></i>
                            Frequency <span id="frequencyRequired" class="text-danger" style="display: none;">*</span>
                        </label>
                        <input type="text" name="frequency" id="frequency" class="form-control-modern" value="<?= old('frequency') ?>" placeholder="e.g., Every 8 hours, Once daily">
                        <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['frequency'])): ?>
                            <div class="text-danger"><?= esc(session()->getFlashdata('errors')['frequency']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="submit" class="btn-modern btn-modern-primary">
                    <i class="fas fa-save"></i>
                    Create Order
                </button>
                <a href="<?= site_url('doctor/orders') ?>" class="btn-modern btn-modern-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Initialize data from PHP (load once, use globally)
<?php if (!empty($labTests)): ?>
if (!window.labTestsData) {
    window.labTestsData = <?= json_encode($labTests, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
}
<?php endif; ?>

<?php if (!empty($ivFluids)): ?>
if (!window.ivFluidsData) {
    window.ivFluidsData = <?= json_encode($ivFluids, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
}
<?php endif; ?>

<?php if (!empty($medicines)): ?>
if (!window.medicinesData) {
    window.medicinesData = <?= json_encode($medicines, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
}
<?php endif; ?>

// Handle checkbox order types with dynamic fields
const selectedTypesList = document.getElementById('selectedTypesList');
const orderTypeFieldsContainer = document.getElementById('orderTypeFieldsContainer');
const activeFieldSets = new Map(); // Track active field sets by order type

// Order type labels mapping
const orderTypeLabels = {
    'medication': 'Medication Order',
    'lab_test': 'Laboratory Test Request',
    'iv_fluids_order': 'IV Fluids Order',
    'reassessment_order': 'Reassessment Order'
};

// Field templates for each order type
const fieldTemplates = {
    'medication': (index) => {
        // Get medicines from global window variable (already loaded in main script)
        const medicines = window.medicinesData || [];
        
        // Group medicines by category
        const groupedMedicines = {};
        medicines.forEach(med => {
            const category = med.category || 'Other';
            if (!groupedMedicines[category]) {
                groupedMedicines[category] = [];
            }
            groupedMedicines[category].push(med);
        });
        
        let medicinesHTML = '';
        if (medicines.length > 0) {
            Object.keys(groupedMedicines).sort().forEach(category => {
                medicinesHTML += `
                    <div class="medicine-category-group" data-category="${category}" style="margin-bottom: 20px;">
                        <h5 style="margin: 0 0 12px 0; color: #2e7d32; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-tag"></i> ${category}
                        </h5>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 8px;">
                `;
                groupedMedicines[category].forEach((med, medIndex) => {
                    const stockClass = med.quantity < 10 ? 'color: #ef4444;' : (med.quantity < 20 ? 'color: #f59e0b;' : 'color: #10b981;');
                    const stockText = med.quantity < 10 ? `(Low Stock: ${med.quantity})` : `(Stock: ${med.quantity})`;
                    const safeMedName = med.item_name.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                    const safeGenericName = (med.generic_name || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                    const safeCategory = (med.category || 'Other').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                    const medicineId = `med_${index}_${category.replace(/[^a-zA-Z0-9]/g, '_')}_${medIndex}`;
                    medicinesHTML += `
                        <label class="medicine-item" 
                               data-item-name="${safeMedName}" 
                               data-generic-name="${safeGenericName}" 
                               data-category="${safeCategory}"
                               style="display: flex; align-items: center; padding: 10px; background: white; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s ease;" 
                               onmouseover="if(!this.querySelector('input[type=checkbox]').checked) { this.style.borderColor='#2e7d32'; this.style.backgroundColor='#f0fdf4'; }" 
                               onmouseout="if(!this.querySelector('input[type=checkbox]').checked) { this.style.borderColor='#e5e7eb'; this.style.backgroundColor='white'; }">
                            <input type="checkbox" 
                                   name="order_details[${index}][drug_names][]" 
                                   value="${safeMedName}" 
                                   class="medicine-checkbox-${index}"
                                   data-medicine-id="${medicineId}"
                                   data-medicine-name="${safeMedName}"
                                   style="width: 18px; height: 18px; margin-right: 12px; cursor: pointer; accent-color: #2e7d32;"
                                   onchange="handleMedicineSelection(this, ${index})">
                            <span style="font-size: 14px; font-weight: 500; color: #1e293b; flex: 1;">
                                ${med.item_name}
                                ${med.generic_name ? `<br><small style="color: #64748b; font-size: 12px;">${med.generic_name}</small>` : ''}
                            </span>
                            <span style="font-size: 12px; ${stockClass}; font-weight: 600; margin-left: 8px;">
                                ${stockText}
                            </span>
                        </label>
                    `;
                });
                medicinesHTML += `
                        </div>
                    </div>
                `;
            });
        } else {
            medicinesHTML = '<p style="color: #ef4444; padding: 12px;">No medicines available in pharmacy.</p>';
        }
        
        return `
        <div class="modern-card" style="margin-bottom: 24px; border-left: 4px solid #2e7d32;">
            <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); padding: 16px; border-bottom: 2px solid #2e7d32; border-radius: 12px 12px 0 0;">
                <h4 style="margin: 0; color: #065f46; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-pills"></i> Medication Order Details
                </h4>
            </div>
            <div style="padding: 24px;">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <i class="fas fa-pills me-2"></i> Select Drug(s) <span class="text-danger">*</span>
                        <small style="display: block; font-weight: normal; color: #64748b; margin-top: 4px;">
                            <i class="fas fa-info-circle"></i> You can select multiple medicines. Each medicine will have its own dosage form.
                        </small>
                    </label>
                    <div style="margin-bottom: 12px;">
                        <div style="position: relative; margin-bottom: 8px;">
                            <input type="text" 
                                   id="medicine-search-${index}" 
                                   class="form-control-modern" 
                                   placeholder="Search medicines by name, generic name, category, or stock status..." 
                                   style="padding-left: 40px; padding-right: 100px;"
                                   onkeyup="filterMedicines(${index})"
                                   oninput="filterMedicines(${index})"
                                   autocomplete="off">
                            <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                            <button type="button" 
                                    onclick="clearSearch(${index})" 
                                    id="clear-search-btn-${index}"
                                    style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #64748b; cursor: pointer; padding: 4px 8px; display: none;"
                                    title="Clear search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: center; justify-content: space-between;">
                            <small style="color: #64748b; font-size: 12px;">
                                <i class="fas fa-info-circle"></i> 
                                <span id="filter-count-${index}">0</span> medicine(s) found
                            </small>
                            <small style="color: #64748b; font-size: 11px; font-style: italic;">
                                Tip: Search by name, generic name, category, or stock status (e.g., "low stock", "antibiotics")
                            </small>
                        </div>
                    </div>
                    <div id="medicines-container-${index}" style="max-height: 300px; overflow-y: auto; padding: 12px; background: #f8fafc; border: 2px solid #e5e7eb; border-radius: 8px; margin-top: 8px;">
                        ${medicinesHTML}
                    </div>
                </div>
                <div id="medication-dosage-forms-${index}" style="margin-top: 24px;">
                    <!-- Individual dosage forms will be dynamically added here -->
                </div>
            </div>
        </div>
    `;
    },
    'lab_test': (index) => {
        // Get lab tests from global window variable (already loaded in main script)
        const labTests = window.labTestsData || [];
        
        // Group lab tests by test_type
        const groupedTests = {};
        if (Array.isArray(labTests)) {
            labTests.forEach(test => {
                const type = test.test_type || 'Other';
                if (!groupedTests[type]) {
                    groupedTests[type] = [];
                }
                groupedTests[type].push(test);
            });
        }
        
        let labTestsHTML = '';
        if (labTests.length > 0) {
            Object.keys(groupedTests).sort().forEach(type => {
                labTestsHTML += `
                    <div style="margin-bottom: 20px;">
                        <h5 style="margin: 0 0 12px 0; color: #0369a1; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fas fa-tag"></i> ${type}
                        </h5>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 8px;">
                `;
                groupedTests[type].forEach(test => {
                    const specimenCategory = test.specimen_category || 'with_specimen';
                    const isWithSpecimen = specimenCategory === 'with_specimen';
                    const specimenBadge = isWithSpecimen 
                        ? '<span style="display: inline-block; padding: 2px 8px; background: #fef3c7; color: #92400e; border-radius: 4px; font-size: 11px; font-weight: 600; margin-left: 8px;"><i class="fas fa-vial"></i> With Specimen</span>'
                        : '<span style="display: inline-block; padding: 2px 8px; background: #dbeafe; color: #1e40af; border-radius: 4px; font-size: 11px; font-weight: 600; margin-left: 8px;"><i class="fas fa-flask"></i> Without Specimen</span>';
                    
                    labTestsHTML += `
                        <label style="display: flex; align-items: center; padding: 10px; background: white; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s ease;" 
                               onmouseover="if(!this.querySelector('input[type=checkbox]').checked) { this.style.borderColor='#0288d1'; this.style.backgroundColor='#e0f2fe'; }" 
                               onmouseout="if(!this.querySelector('input[type=checkbox]').checked) { this.style.borderColor='#e5e7eb'; this.style.backgroundColor='white'; }">
                            <input type="checkbox" 
                                   name="order_details[${index}][test_names][]" 
                                   value="${test.test_name.replace(/"/g, '&quot;')}" 
                                   class="lab-test-checkbox-${index}"
                                   style="width: 18px; height: 18px; margin-right: 12px; cursor: pointer; accent-color: #0288d1;"
                                   onchange="updateLabTestCheckbox(this)">
                            <span style="font-size: 13px; font-weight: 500; color: #1e293b; flex: 1; display: flex; align-items: center; flex-wrap: wrap; gap: 4px;">
                                ${test.test_name}
                                ${specimenBadge}
                            </span>
                        </label>
                    `;
                });
                labTestsHTML += `
                        </div>
                    </div>
                `;
            });
        } else {
            labTestsHTML = '<p style="color: #ef4444; padding: 12px;">No lab tests available.</p>';
        }
        
        return `
        <div class="modern-card" style="margin-bottom: 24px; border-left: 4px solid #0288d1;">
            <div style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); padding: 16px; border-bottom: 2px solid #0288d1; border-radius: 12px 12px 0 0;">
                <h4 style="margin: 0; color: #0369a1; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-vial"></i> Laboratory Test Request Details
                </h4>
            </div>
            <div style="padding: 24px;">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <i class="fas fa-flask me-2"></i> Select Test(s) <span class="text-danger">*</span>
                        <small style="display: block; font-weight: normal; color: #64748b; margin-top: 4px;">
                            <i class="fas fa-info-circle"></i> You can select multiple lab tests
                        </small>
                    </label>
                    <div style="max-height: 400px; overflow-y: auto; padding: 16px; background: #f8fafc; border: 2px solid #e5e7eb; border-radius: 8px; margin-top: 8px;">
                        ${labTestsHTML}
                    </div>
                </div>
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <i class="fas fa-exclamation-triangle me-2"></i> Priority <span class="text-danger">*</span>
                    </label>
                    <select name="order_details[${index}][priority]" class="form-control-modern" required>
                        <option value="routine">Routine</option>
                        <option value="stat">STAT (Urgent)</option>
                    </select>
                </div>
                <div class="form-group-modern" id="nurse-selection-${index}" style="display: none;">
                    <label class="form-label-modern">
                        <i class="fas fa-user-nurse me-2"></i>
                        Assign to Nurse (For Specimen Collection) <span class="text-danger">*</span>
                        <small style="display: block; font-weight: normal; color: #64748b; margin-top: 4px;">
                            <i class="fas fa-info-circle"></i> Required for tests that need specimen collection
                        </small>
                    </label>
                    <select name="order_details[${index}][nurse_id]" class="form-control-modern nurse-select-lab-${index}">
                        <option value="">Select Nurse</option>
                        <?php foreach ($nurses as $nurse): ?>
                            <option value="<?= esc($nurse['id']) ?>">
                                <?= esc(ucfirst($nurse['username'])) ?> (<?= esc($nurse['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    `;
    },
    'iv_fluids_order': (index) => {
        // Get IV fluids from global window variable (already loaded in main script)
        const ivFluids = window.ivFluidsData || [];
        let ivFluidsHTML = '';
        if (ivFluids.length > 0) {
            ivFluidsHTML = ivFluids.map((fluid, fluidIndex) => {
                const stockClass = fluid.quantity < 10 ? 'color: #ef4444;' : (fluid.quantity < 20 ? 'color: #f59e0b;' : 'color: #10b981;');
                const stockText = fluid.quantity < 10 ? `(Low Stock: ${fluid.quantity})` : `(Stock: ${fluid.quantity})`;
                const safeFluidName = fluid.item_name.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                const fluidId = `iv_fluid_${index}_${fluidIndex}`;
                return `
                    <label style="display: flex; align-items: center; padding: 10px; background: white; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s ease; margin-bottom: 8px;" 
                           onmouseover="if(!this.querySelector('input[type=checkbox]').checked) { this.style.borderColor='#06b6d4'; this.style.backgroundColor='#ecfeff'; }" 
                           onmouseout="if(!this.querySelector('input[type=checkbox]').checked) { this.style.borderColor='#e5e7eb'; this.style.backgroundColor='white'; }">
                        <input type="checkbox" 
                               name="order_details[${index}][fluid_names][]" 
                               value="${safeFluidName}" 
                               class="iv-fluid-checkbox-${index}"
                               data-fluid-id="${fluidId}"
                               data-fluid-name="${safeFluidName}"
                               style="width: 18px; height: 18px; margin-right: 12px; cursor: pointer; accent-color: #06b6d4;"
                               onchange="handleIVFluidSelection(this, ${index})">
                        <span style="font-size: 14px; font-weight: 500; color: #1e293b; flex: 1;">
                            ${fluid.item_name}
                            ${fluid.generic_name ? `<br><small style="color: #64748b; font-size: 12px;">${fluid.generic_name}</small>` : ''}
                        </span>
                        <span style="font-size: 12px; ${stockClass}; font-weight: 600; margin-left: 8px;">
                            ${stockText}
                        </span>
                    </label>
                `;
            }).join('');
        } else {
            ivFluidsHTML = '<p style="color: #ef4444; padding: 12px;">No IV Fluids available in pharmacy.</p>';
        }
        
        return `
        <div class="modern-card" style="margin-bottom: 24px; border-left: 4px solid #06b6d4;">
            <div style="background: linear-gradient(135deg, #ecfeff 0%, #cffafe 100%); padding: 16px; border-bottom: 2px solid #06b6d4; border-radius: 12px 12px 0 0;">
                <h4 style="margin: 0; color: #164e63; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-tint"></i> IV Fluids Order Details
                </h4>
            </div>
            <div style="padding: 24px;">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <i class="fas fa-flask me-2"></i> Select IV Fluid(s) <span class="text-danger">*</span>
                        <small style="display: block; font-weight: normal; color: #64748b; margin-top: 4px;">
                            <i class="fas fa-info-circle"></i> You can select multiple IV fluids. Each fluid will have its own volume and rate.
                        </small>
                    </label>
                    <div style="max-height: 300px; overflow-y: auto; padding: 12px; background: #f8fafc; border: 2px solid #e5e7eb; border-radius: 8px; margin-top: 8px;">
                        ${ivFluidsHTML}
                    </div>
                </div>
                <div id="iv-fluid-details-forms-${index}" style="margin-top: 24px;">
                    <!-- Individual volume and rate forms will be dynamically added here -->
                </div>
            </div>
        </div>
    `;
    },
    'reassessment_order': (index) => `
        <div class="modern-card" style="margin-bottom: 24px; border-left: 4px solid #10b981;">
            <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); padding: 16px; border-bottom: 2px solid #10b981; border-radius: 12px 12px 0 0;">
                <h4 style="margin: 0; color: #065f46; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-redo"></i> Reassessment Order Details
                </h4>
            </div>
            <div style="padding: 24px;">
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <i class="fas fa-clock me-2"></i> Required Time of Next Vitals <span class="text-danger">*</span>
                    </label>
                    <input type="datetime-local" name="order_details[${index}][next_vitals_time]" class="form-control-modern" required>
                </div>
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <i class="fas fa-sticky-note me-2"></i> Notes
                    </label>
                    <textarea name="order_details[${index}][notes]" class="form-control-modern" rows="2" placeholder="Additional notes for reassessment..."></textarea>
                </div>
            </div>
        </div>
    `
};

function updateOrderTypeSelection(checkbox) {
    const label = checkbox.closest('label');
    if (checkbox.checked) {
        label.style.borderColor = '#2e7d32';
        label.style.backgroundColor = '#f0fdf4';
        label.style.boxShadow = '0 2px 8px rgba(46, 125, 50, 0.15)';
    } else {
        label.style.borderColor = '#e5e7eb';
        label.style.backgroundColor = 'white';
        label.style.boxShadow = 'none';
    }
    
    updateSelectedTypes();
    renderOrderTypeFields();
}

function updateSelectedTypes() {
    const checkboxes = document.querySelectorAll('.order-type-checkbox:checked');
    const selected = Array.from(checkboxes).map(cb => orderTypeLabels[cb.value] || cb.value);
    
    if (selected.length > 0) {
        selectedTypesList.textContent = selected.join(', ');
        selectedTypesList.style.color = '#065f46';
    } else {
        selectedTypesList.textContent = 'None';
        selectedTypesList.style.color = '#94a3b8';
    }
}

function renderOrderTypeFields() {
    const checkboxes = document.querySelectorAll('.order-type-checkbox:checked');
    orderTypeFieldsContainer.innerHTML = '';
    activeFieldSets.clear();
    
    checkboxes.forEach((checkbox, index) => {
        const orderType = checkbox.value;
        if (fieldTemplates[orderType]) {
            const fieldSetId = `orderType_${orderType}_${index}`;
            const fieldHTML = fieldTemplates[orderType](index);
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = fieldHTML;
            const fieldSet = tempDiv.firstElementChild;
            fieldSet.id = fieldSetId;
            orderTypeFieldsContainer.appendChild(fieldSet);
            activeFieldSets.set(orderType, fieldSet);
        }
    });
    
    // Add hidden input to track order types with their indices
    checkboxes.forEach((checkbox, index) => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `order_type_mapping[${index}]`;
        hiddenInput.value = checkbox.value;
        orderTypeFieldsContainer.appendChild(hiddenInput);
    });
    
}

// Function to handle medicine selection and show/hide dosage forms
function handleMedicineSelection(checkbox, orderIndex) {
    const label = checkbox.closest('label');
    const medicineName = checkbox.getAttribute('data-medicine-name');
    const medicineId = checkbox.getAttribute('data-medicine-id');
    const containerId = `medication-dosage-forms-${orderIndex}`;
    const container = document.getElementById(containerId);
    
    if (!container) return;
    
    // Update checkbox styling
    if (checkbox.checked) {
        label.style.borderColor = '#2e7d32';
        label.style.backgroundColor = '#dcfce7';
        
        // Create dosage form for this medicine
        const dosageFormId = `dosage-form-${medicineId}`;
        if (!document.getElementById(dosageFormId)) {
            const dosageForm = document.createElement('div');
            dosageForm.id = dosageFormId;
            dosageForm.className = 'medicine-dosage-form';
            dosageForm.setAttribute('data-medicine-name', medicineName);
            dosageForm.setAttribute('data-checkbox-id', checkbox.id || medicineId);
            dosageForm.style.cssText = 'margin-bottom: 24px; padding: 20px; background: #f0fdf4; border: 2px solid #2e7d32; border-radius: 12px;';
            dosageForm.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #2e7d32;">
                    <h5 style="margin: 0; color: #065f46; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-pills"></i> ${medicineName}
                    </h5>
                    <button type="button" onclick="removeMedicineDosageForm('${dosageFormId}')" 
                            style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;"
                            onmouseover="this.style.background='#dc2626'" 
                            onmouseout="this.style.background='#ef4444'">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-syringe me-2"></i> Dosage <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="order_details[${orderIndex}][medicines][${medicineName}][dosage]" 
                                   class="form-control-modern" 
                                   required 
                                   placeholder="e.g., 500mg, 1 tablet">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-clock me-2"></i> Frequency <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="order_details[${orderIndex}][medicines][${medicineName}][frequency]" 
                                   class="form-control-modern" 
                                   required 
                                   placeholder="e.g., Every 8 hours, Once daily">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-route me-2"></i> Route <span class="text-danger">*</span>
                            </label>
                            <select name="order_details[${orderIndex}][medicines][${medicineName}][route]" 
                                    class="form-control-modern" 
                                    required>
                                <option value="">-- Select Route --</option>
                                <option value="oral">Oral</option>
                                <option value="iv">IV (Intravenous)</option>
                                <option value="im">IM (Intramuscular)</option>
                                <option value="sc">SC (Subcutaneous)</option>
                                <option value="topical">Topical</option>
                                <option value="inhalation">Inhalation</option>
                                <option value="rectal">Rectal</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-calendar-alt me-2"></i> Duration <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="order_details[${orderIndex}][medicines][${medicineName}][duration]" 
                                   class="form-control-modern" 
                                   required 
                                   placeholder="e.g., 7 days, 2 weeks">
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(dosageForm);
        }
        } else {
        label.style.borderColor = '#e5e7eb';
        label.style.backgroundColor = 'white';
        
        // Remove dosage form for this medicine
        const dosageFormId = `dosage-form-${medicineId}`;
        const dosageForm = document.getElementById(dosageFormId);
        if (dosageForm) {
            dosageForm.remove();
        }
    }
}

// Function to remove medicine dosage form
function removeMedicineDosageForm(dosageFormId) {
    const dosageForm = document.getElementById(dosageFormId);
    if (dosageForm) {
        // Get medicine name and checkbox ID from data attributes
        const medicineName = dosageForm.getAttribute('data-medicine-name');
        const checkboxId = dosageForm.getAttribute('data-checkbox-id');
        
        // Find the corresponding checkbox and uncheck it
        const container = dosageForm.closest('.modern-card');
        if (container && medicineName) {
            const checkboxes = container.querySelectorAll(`input[type="checkbox"][data-medicine-name]`);
            checkboxes.forEach(cb => {
                const cbMedicineName = cb.getAttribute('data-medicine-name');
                // Compare medicine names (handle HTML entities)
                const decodedCbName = cbMedicineName.replace(/&quot;/g, '"').replace(/&#39;/g, "'");
                const decodedFormName = medicineName.replace(/&quot;/g, '"').replace(/&#39;/g, "'");
                
                if (decodedCbName === decodedFormName || cbMedicineName === medicineName) {
                    cb.checked = false;
                    const orderIndex = parseInt(container.querySelector('[id^="medication-dosage-forms-"]').id.replace('medication-dosage-forms-', ''));
                    handleMedicineSelection(cb, orderIndex);
                }
            });
        }
        dosageForm.remove();
    }
}

// Function to update medicine checkbox styling (kept for backward compatibility)
function updateMedicineCheckbox(checkbox) {
    handleMedicineSelection(checkbox, 0); // Will be overridden by handleMedicineSelection
}

// Function to handle IV fluid selection and show/hide volume/rate forms
function handleIVFluidSelection(checkbox, orderIndex) {
    const label = checkbox.closest('label');
    const fluidName = checkbox.getAttribute('data-fluid-name');
    const fluidId = checkbox.getAttribute('data-fluid-id');
    const containerId = `iv-fluid-details-forms-${orderIndex}`;
    const container = document.getElementById(containerId);
    
    if (!container) return;
    
    // Update checkbox styling
    if (checkbox.checked) {
        label.style.borderColor = '#06b6d4';
        label.style.backgroundColor = '#cffafe';
        
        // Create volume and rate form for this IV fluid
        const detailsFormId = `iv-fluid-form-${fluidId}`;
        if (!document.getElementById(detailsFormId)) {
            const detailsForm = document.createElement('div');
            detailsForm.id = detailsFormId;
            detailsForm.className = 'iv-fluid-details-form';
            detailsForm.setAttribute('data-fluid-name', fluidName);
            detailsForm.setAttribute('data-checkbox-id', checkbox.id || fluidId);
            detailsForm.style.cssText = 'margin-bottom: 24px; padding: 20px; background: #ecfeff; border: 2px solid #06b6d4; border-radius: 12px;';
            detailsForm.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h5 style="margin: 0; color: #164e63; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-tint"></i> ${fluidName}
                    </h5>
                    <button type="button" onclick="removeIVFluidDetailsForm('${detailsFormId}')" 
                            style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-weight me-2"></i> Volume <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="order_details[${orderIndex}][iv_fluids][${fluidName}][volume]" 
                                   class="form-control-modern" 
                                   required 
                                   placeholder="e.g., 500ml, 1000ml">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="fas fa-tachometer-alt me-2"></i> Rate <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="order_details[${orderIndex}][iv_fluids][${fluidName}][rate]" 
                                   class="form-control-modern" 
                                   required 
                                   placeholder="e.g., 100ml/hr, 50 drops/min">
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(detailsForm);
        }
    } else {
        label.style.borderColor = '#e5e7eb';
        label.style.backgroundColor = 'white';
        
        // Remove volume/rate form for this IV fluid
        const detailsFormId = `iv-fluid-form-${fluidId}`;
        const detailsForm = document.getElementById(detailsFormId);
        if (detailsForm) {
            detailsForm.remove();
        }
    }
}

// Function to remove IV fluid details form
function removeIVFluidDetailsForm(detailsFormId) {
    const detailsForm = document.getElementById(detailsFormId);
    if (detailsForm) {
        // Get fluid name and checkbox ID from data attributes
        const fluidName = detailsForm.getAttribute('data-fluid-name');
        const checkboxId = detailsForm.getAttribute('data-checkbox-id');
        
        // Find the corresponding checkbox and uncheck it
        const container = detailsForm.closest('.modern-card');
        if (container && fluidName) {
            const checkboxes = container.querySelectorAll(`input[type="checkbox"][data-fluid-name]`);
            checkboxes.forEach(cb => {
                const cbFluidName = cb.getAttribute('data-fluid-name');
                // Compare fluid names (handle HTML entities)
                const decodedCbName = cbFluidName.replace(/&quot;/g, '"').replace(/&#39;/g, "'");
                const decodedFormName = fluidName.replace(/&quot;/g, '"').replace(/&#39;/g, "'");
                
                if (decodedCbName === decodedFormName || cbFluidName === fluidName) {
                    cb.checked = false;
                    const orderIndex = parseInt(container.querySelector('[id^="iv-fluid-details-forms-"]').id.replace('iv-fluid-details-forms-', ''));
                    handleIVFluidSelection(cb, orderIndex);
                }
            });
        }
        detailsForm.remove();
    }
}

// Function to update lab test checkbox styling and show/hide nurse selection
function updateLabTestCheckbox(checkbox) {
    const label = checkbox.closest('label');
    if (checkbox.checked) {
        label.style.borderColor = '#0288d1';
        label.style.backgroundColor = '#bae6fd';
    } else {
        label.style.borderColor = '#e5e7eb';
        label.style.backgroundColor = 'white';
    }
    
    // Check if any "with specimen" tests are selected
    const container = checkbox.closest('.modern-card');
    if (container) {
        // Extract order index from checkbox class
        const classMatch = checkbox.className.match(/lab-test-checkbox-(\d+)/);
        const orderIndex = classMatch ? classMatch[1] : '0';
        
        // Find all lab test checkboxes in this order type section
        const allCheckboxes = container.querySelectorAll('.lab-test-checkbox-' + orderIndex);
        const labTests = window.labTestsData || [];
        
        let hasWithSpecimen = false;
        allCheckboxes.forEach(cb => {
            if (cb.checked) {
                const testName = cb.value;
                const test = labTests.find(t => t.test_name === testName);
                // Default to 'without_specimen' if specimen_category is not set
                if (test && (test.specimen_category || 'without_specimen') === 'with_specimen') {
                    hasWithSpecimen = true;
                }
            }
        });
        
        // Show/hide nurse selection field based on whether any "with specimen" tests are selected
        const nurseSelectionField = document.getElementById('nurse-selection-' + orderIndex);
        if (nurseSelectionField) {
            const nurseSelect = nurseSelectionField.querySelector('select');
            if (hasWithSpecimen) {
                nurseSelectionField.style.display = 'block';
                if (nurseSelect) {
                    nurseSelect.required = true;
                }
            } else {
                nurseSelectionField.style.display = 'none';
                if (nurseSelect) {
                    nurseSelect.required = false;
                    nurseSelect.value = ''; // Clear selection
                }
            }
        }
    }
}

// Filter medicines based on search input
function filterMedicines(orderIndex) {
    try {
        const container = document.getElementById('medicines-container-' + orderIndex);
        if (!container) {
            console.warn('Medicines container not found for index:', orderIndex);
            return;
        }
        
        // Get search input
        const searchInput = document.getElementById('medicine-search-' + orderIndex);
        const clearBtn = document.getElementById('clear-search-btn-' + orderIndex);
        
        const searchTerm = (searchInput ? searchInput.value : '').toLowerCase().trim();
        
        // Show/hide clear button
        if (clearBtn) {
            clearBtn.style.display = searchTerm ? 'block' : 'none';
        }
        
        const medicineItems = container.querySelectorAll('.medicine-item');
        const categoryGroups = container.querySelectorAll('.medicine-category-group');
        
        let visibleCount = 0;
        
        // Filter medicines based on search term
        categoryGroups.forEach(group => {
            const itemsInGroup = group.querySelectorAll('.medicine-item');
            let groupHasVisible = false;
            
            itemsInGroup.forEach(item => {
                const itemName = (item.getAttribute('data-item-name') || '').toLowerCase();
                const genericName = (item.getAttribute('data-generic-name') || '').toLowerCase();
                const category = (item.getAttribute('data-category') || '').toLowerCase();
                const stockStatus = (item.getAttribute('data-stock-status') || '').toLowerCase();
                const quantity = parseInt(item.getAttribute('data-quantity') || 0);
                
                // Check if search term matches any field
                let matches = false;
                
                if (searchTerm === '') {
                    matches = true;
                } else {
                    // Check direct matches
                    matches = itemName.includes(searchTerm) || 
                             genericName.includes(searchTerm) || 
                             category.includes(searchTerm);
                    
                    // Check stock status keywords
                    if (!matches) {
                        if ((searchTerm.includes('low') || searchTerm.includes('low stock')) && stockStatus === 'low_stock') {
                            matches = true;
                        } else if ((searchTerm.includes('out') || searchTerm.includes('out of stock') || searchTerm.includes('no stock')) && stockStatus === 'out_of_stock') {
                            matches = true;
                        } else if ((searchTerm.includes('in stock') || searchTerm.includes('available')) && stockStatus === 'in_stock') {
                            matches = true;
                        }
                    }
                }
                
                // Show item if matches
                if (matches) {
                    item.style.display = 'flex';
                    groupHasVisible = true;
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show/hide category group based on whether it has visible items
            group.style.display = groupHasVisible ? 'block' : 'none';
        });
        
        // Update filter count
        const filterCountEl = document.getElementById('filter-count-' + orderIndex);
        if (filterCountEl) {
            filterCountEl.textContent = visibleCount;
        }
        
        // Show message if no results
        let noResultsMsg = container.querySelector('.no-results-message');
        if (visibleCount === 0 && searchTerm !== '') {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('p');
                noResultsMsg.className = 'no-results-message';
                noResultsMsg.style.cssText = 'color: #ef4444; padding: 12px; text-align: center; font-weight: 500;';
                noResultsMsg.textContent = 'No medicines found matching your search.';
                container.appendChild(noResultsMsg);
            }
        } else {
            if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }
    } catch (error) {
        console.error('Error filtering medicines:', error);
    }
}

// Clear search
function clearSearch(orderIndex) {
    const searchInput = document.getElementById('medicine-search-' + orderIndex);
    if (searchInput) {
        searchInput.value = '';
        filterMedicines(orderIndex);
        searchInput.focus();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.order-type-checkbox').forEach(checkbox => {
        updateOrderTypeSelection(checkbox);
    });
    
    // Initialize filter counts for all medication sections
    document.querySelectorAll('[id^="medicines-container-"]').forEach(container => {
        const orderIndex = container.id.replace('medicines-container-', '');
        filterMedicines(parseInt(orderIndex));
    });
    
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.order-type-checkbox:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one order type.');
                return false;
            }
        });
    }
});
</script>
<?= $this->endSection() ?>

