<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Create Admission Orders<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .page-header {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(46, 125, 50, 0.2);
        color: white;
    }
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .card-header {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f4 100%);
        padding: 20px 24px;
        border-bottom: 2px solid #c8e6c9;
    }
    .card-body {
        padding: 24px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        font-size: 14px;
    }
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
    }
    .form-control:focus {
        outline: none;
        border-color: #2e7d32;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }
    .btn-modern {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-primary {
        background: #2e7d32;
        color: white;
    }
    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    .dynamic-item {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
        position: relative;
    }
    .btn-remove {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 6px 12px;
        cursor: pointer;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-clipboard-list"></i> Create Admission Orders</h1>
    <p style="margin: 8px 0 0; opacity: 0.9;">
        Patient: <?= esc(ucwords($admission['firstname'] . ' ' . $admission['lastname'])) ?> | 
        Room: <?= esc($admission['room_number'] ?? 'N/A') ?>
    </p>
</div>

<form action="<?= site_url('doctor/admission-orders/store') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="admission_id" value="<?= $admission['id'] ?>">
    <input type="hidden" name="patient_id" value="<?= $admission['patient_id'] ?>">

    <!-- Assigned Nurse -->
    <div class="modern-card">
        <div class="card-header">
            <h3 style="margin: 0; color: #2e7d32;"><i class="fas fa-user-nurse"></i> Assign Nurse</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Assigned Nurse <span style="color: #ef4444;">*</span></label>
                <select name="nurse_id" class="form-control" required>
                    <option value="">-- Select Nurse --</option>
                    <?php if (!empty($nurses)): ?>
                        <?php foreach ($nurses as $nurse): ?>
                            <option value="<?= $nurse['id'] ?>" <?= old('nurse_id') == $nurse['id'] ? 'selected' : '' ?>>
                                <?= esc($nurse['username']) ?> 
                                <?php if (!empty($nurse['email'])): ?>
                                    (<?= esc($nurse['email']) ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No nurses available</option>
                    <?php endif; ?>
                </select>
                <small style="display: block; margin-top: 8px; color: #64748b; font-size: 12px;">
                    <i class="fas fa-info-circle"></i> Select the nurse who will execute these orders.
                </small>
            </div>
        </div>
    </div>

    <!-- Treatment Plan -->
    <div class="modern-card">
        <div class="card-header">
            <h3 style="margin: 0; color: #2e7d32;"><i class="fas fa-stethoscope"></i> Admission Treatment Plan</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Treatment Plan & Suspected Diagnosis</label>
                <textarea name="treatment_plan" class="form-control" rows="5" 
                    placeholder="Describe the treatment plan, suspected diagnosis, and immediate care needed..."><?= old('treatment_plan') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Medications -->
    <div class="modern-card">
        <div class="card-header">
            <h3 style="margin: 0; color: #2e7d32;"><i class="fas fa-pills"></i> Medication Orders</h3>
        </div>
        <div class="card-body">
            <div id="medications-container">
                <div class="dynamic-item medication-item">
                    <button type="button" class="btn-remove" onclick="removeItem(this)">×</button>
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px;">
                        <div>
                            <label class="form-label">Medicine Name</label>
                            <select name="medications[0][medicine_name]" class="form-control" onchange="updateMedicineInfo(0, this)">
                                <option value="">-- Select Medicine --</option>
                                <?php if (!empty($medicines)): ?>
                                    <?php foreach ($medicines as $medicine): ?>
                                        <option value="<?= esc($medicine['item_name']) ?>" 
                                            data-description="<?= esc($medicine['description'] ?? '') ?>"
                                            data-quantity="<?= esc($medicine['quantity']) ?>"
                                            data-price="<?= esc($medicine['price']) ?>">
                                            <?= esc($medicine['item_name']) ?> 
                                            <?php if ($medicine['quantity'] < 10): ?>
                                                <span style="color: #ef4444;">(Low Stock: <?= $medicine['quantity'] ?>)</span>
                                            <?php elseif ($medicine['quantity'] < 20): ?>
                                                <span style="color: #f59e0b;">(Stock: <?= $medicine['quantity'] ?>)</span>
                                            <?php else: ?>
                                                <span style="color: #10b981;">(Stock: <?= $medicine['quantity'] ?>)</span>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No medicines available in pharmacy</option>
                                <?php endif; ?>
                            </select>
                            <?php if (empty($medicines)): ?>
                                <div style="margin-top: 8px; padding: 8px; background: #fee2e2; border-radius: 6px; color: #991b1b; font-size: 13px;">
                                    <i class="fas fa-exclamation-triangle"></i> No medicines available in pharmacy. Please contact pharmacy staff.
                                </div>
                            <?php endif; ?>
                            <small id="medicineInfo0" style="display: none; margin-top: 8px; padding: 8px; background: #f1f5f9; border-radius: 6px; color: #475569; font-size: 12px;">
                                <strong>Description:</strong> <span id="medicineDescription0"></span><br>
                                <strong>Available Stock:</strong> <span id="medicineStock0"></span> units<br>
                                <strong>Price:</strong> ₱<span id="medicinePrice0"></span>
                            </small>
                        </div>
                        <div>
                            <label class="form-label">Dosage</label>
                            <input type="text" name="medications[0][dosage]" class="form-control" placeholder="e.g., 500mg">
                        </div>
                        <div>
                            <label class="form-label">Frequency</label>
                            <input type="text" name="medications[0][frequency]" class="form-control" placeholder="e.g., Every 8 hours">
                        </div>
                        <div>
                            <label class="form-label">Duration</label>
                            <input type="text" name="medications[0][duration]" class="form-control" placeholder="e.g., 7 days">
                        </div>
                    </div>
                    <div style="margin-top: 12px;">
                        <label class="form-label">Instructions</label>
                        <textarea name="medications[0][instructions]" class="form-control" rows="2" placeholder="Special instructions..."></textarea>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-modern btn-secondary" onclick="addMedication()">
                <i class="fas fa-plus"></i> Add Medication
            </button>
        </div>
    </div>

    <!-- Lab Tests -->
    <div class="modern-card">
        <div class="card-header">
            <h3 style="margin: 0; color: #2e7d32;"><i class="fas fa-vial"></i> Laboratory Requests</h3>
        </div>
        <div class="card-body">
            <div id="lab-tests-container">
                <div class="dynamic-item lab-item">
                    <button type="button" class="btn-remove" onclick="removeItem(this)">×</button>
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px;">
                        <div>
                            <label class="form-label">Test Name</label>
                            <select name="lab_tests[0][test_name]" class="form-control" onchange="updateLabTestInfo(0, this)">
                                <option value="">-- Select Lab Test --</option>
                                <?php if (!empty($labTests)): ?>
                                    <?php 
                                    $groupedTests = [];
                                    foreach ($labTests as $test) {
                                        $groupedTests[$test['test_type']][] = $test;
                                    }
                                    ?>
                                    <?php foreach ($groupedTests as $testType => $tests): ?>
                                        <optgroup label="<?= esc($testType) ?>">
                                            <?php foreach ($tests as $test): ?>
                                                <option value="<?= esc($test['test_name']) ?>" 
                                                    data-type="<?= esc($test['test_type']) ?>"
                                                    data-description="<?= esc($test['description'] ?? '') ?>"
                                                    data-normal-range="<?= esc($test['normal_range'] ?? '') ?>"
                                                    data-price="<?= esc($test['price']) ?>">
                                                    <?= esc($test['test_name']) ?> 
                                                    <span style="color: #64748b;">(₱<?= number_format($test['price'], 2) ?>)</span>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No lab tests available</option>
                                <?php endif; ?>
                            </select>
                            <?php if (empty($labTests)): ?>
                                <div style="margin-top: 8px; padding: 8px; background: #fee2e2; border-radius: 6px; color: #991b1b; font-size: 13px;">
                                    <i class="fas fa-exclamation-triangle"></i> No lab tests available. Please run the seeder to populate lab tests.
                                </div>
                            <?php endif; ?>
                            <small id="labTestInfo0" style="display: none; margin-top: 8px; padding: 8px; background: #f1f5f9; border-radius: 6px; color: #475569; font-size: 12px;">
                                <strong>Type:</strong> <span id="labTestType0"></span><br>
                                <strong>Description:</strong> <span id="labTestDescription0"></span><br>
                                <strong>Normal Range:</strong> <span id="labTestNormalRange0"></span><br>
                                <strong>Price:</strong> ₱<span id="labTestPrice0"></span>
                            </small>
                        </div>
                        <div>
                            <label class="form-label">Priority</label>
                            <select name="lab_tests[0][priority]" class="form-control">
                                <option value="routine">Routine</option>
                                <option value="urgent">Urgent</option>
                                <option value="stat">STAT</option>
                            </select>
                        </div>
                    </div>
                    <div style="margin-top: 12px;">
                        <label class="form-label">Instructions/Remarks</label>
                        <textarea name="lab_tests[0][instructions]" class="form-control" rows="2" placeholder="Special instructions..."></textarea>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-modern btn-secondary" onclick="addLabTest()">
                <i class="fas fa-plus"></i> Add Lab Test
            </button>
        </div>
    </div>

    <!-- Procedures/Imaging -->
    <div class="modern-card">
        <div class="card-header">
            <h3 style="margin: 0; color: #2e7d32;"><i class="fas fa-x-ray"></i> Procedures & Imaging</h3>
        </div>
        <div class="card-body">
            <div id="procedures-container">
                <div class="dynamic-item procedure-item">
                    <button type="button" class="btn-remove" onclick="removeItem(this)">×</button>
                    <div>
                        <label class="form-label">Procedure Name</label>
                        <input type="text" name="procedures[0][procedure_name]" class="form-control" placeholder="e.g., Ultrasound, ECG, CT Scan">
                    </div>
                    <div style="margin-top: 12px;">
                        <label class="form-label">Instructions/Remarks</label>
                        <textarea name="procedures[0][instructions]" class="form-control" rows="2" placeholder="Special instructions..."></textarea>
                    </div>
                </div>
            </div>
            <button type="button" class="btn-modern btn-secondary" onclick="addProcedure()">
                <i class="fas fa-plus"></i> Add Procedure
            </button>
        </div>
    </div>

    <!-- Nursing Care Instructions -->
    <div class="modern-card">
        <div class="card-header">
            <h3 style="margin: 0; color: #2e7d32;"><i class="fas fa-user-nurse"></i> Nursing Care Instructions</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Nursing Care Instructions</label>
                <textarea name="nursing_care" class="form-control" rows="4" 
                    placeholder="e.g., Turn every 2 hours, Monitor I/O, Bed rest, Oxygen if needed, Vital signs every 4 hours..."><?= old('nursing_care') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Diet & Activity -->
    <div class="modern-card">
        <div class="card-header">
            <h3 style="margin: 0; color: #2e7d32;"><i class="fas fa-utensils"></i> Diet & Activity Orders</h3>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label class="form-label">Diet Order</label>
                    <input type="text" name="diet" class="form-control" placeholder="e.g., NPO, Clear liquids, Soft diet" value="<?= old('diet') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Activity Order</label>
                    <input type="text" name="activity" class="form-control" placeholder="e.g., Bed rest, Ambulate, As tolerated" value="<?= old('activity') ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Admitting Notes -->
    <div class="modern-card">
        <div class="card-header">
            <h3 style="margin: 0; color: #2e7d32;"><i class="fas fa-file-medical"></i> Admitting Notes</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Initial Admitting Notes</label>
                <textarea name="admitting_notes" class="form-control" rows="5" 
                    placeholder="Write your initial assessment, findings, and plan for the next 24 hours..."><?= old('admitting_notes') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
        <a href="<?= site_url('doctor/admission-orders/view/' . $admission['id']) ?>" class="btn-modern btn-secondary">
            <i class="fas fa-times"></i> Cancel
        </a>
        <button type="submit" class="btn-modern btn-primary">
            <i class="fas fa-save"></i> Save Admission Orders
        </button>
    </div>
</form>

<script>
let medicationCount = 1;
let labTestCount = 1;
let procedureCount = 1;

// Medicines data from PHP
const medicinesData = <?= json_encode($medicines ?? []) ?>;

function buildMedicinesOptions() {
    let options = '<option value="">-- Select Medicine --</option>';
    if (medicinesData && medicinesData.length > 0) {
        medicinesData.forEach(medicine => {
            const stockColor = medicine.quantity < 10 ? '#ef4444' : (medicine.quantity < 20 ? '#f59e0b' : '#10b981');
            const stockText = medicine.quantity < 10 ? `(Low Stock: ${medicine.quantity})` : `(Stock: ${medicine.quantity})`;
            options += `<option value="${escapeHtml(medicine.item_name)}" 
                data-description="${escapeHtml(medicine.description || '')}"
                data-quantity="${medicine.quantity}"
                data-price="${medicine.price}">
                ${escapeHtml(medicine.item_name)} <span style="color: ${stockColor};">${stockText}</span>
            </option>`;
        });
    } else {
        options += '<option value="" disabled>No medicines available in pharmacy</option>';
    }
    return options;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function addMedication() {
    const container = document.getElementById('medications-container');
    const item = document.createElement('div');
    item.className = 'dynamic-item medication-item';
    
    item.innerHTML = `
        <button type="button" class="btn-remove" onclick="removeItem(this)">×</button>
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 12px;">
            <div>
                <label class="form-label">Medicine Name</label>
                <select name="medications[${medicationCount}][medicine_name]" class="form-control medicine-select" onchange="updateMedicineInfo(${medicationCount}, this)">
                    ${buildMedicinesOptions()}
                </select>
                <small id="medicineInfo${medicationCount}" style="display: none; margin-top: 8px; padding: 8px; background: #f1f5f9; border-radius: 6px; color: #475569; font-size: 12px;">
                    <strong>Description:</strong> <span id="medicineDescription${medicationCount}"></span><br>
                    <strong>Available Stock:</strong> <span id="medicineStock${medicationCount}"></span> units<br>
                    <strong>Price:</strong> ₱<span id="medicinePrice${medicationCount}"></span>
                </small>
            </div>
            <div>
                <label class="form-label">Dosage</label>
                <input type="text" name="medications[${medicationCount}][dosage]" class="form-control" placeholder="e.g., 500mg">
            </div>
            <div>
                <label class="form-label">Frequency</label>
                <input type="text" name="medications[${medicationCount}][frequency]" class="form-control" placeholder="e.g., Every 8 hours">
            </div>
            <div>
                <label class="form-label">Duration</label>
                <input type="text" name="medications[${medicationCount}][duration]" class="form-control" placeholder="e.g., 7 days">
            </div>
        </div>
        <div style="margin-top: 12px;">
            <label class="form-label">Instructions</label>
            <textarea name="medications[${medicationCount}][instructions]" class="form-control" rows="2" placeholder="Special instructions..."></textarea>
        </div>
    `;
    container.appendChild(item);
    medicationCount++;
}

// Lab tests data from PHP
const labTestsData = <?= json_encode($labTests ?? []) ?>;

function buildLabTestsOptions() {
    let options = '<option value="">-- Select Lab Test --</option>';
    if (labTestsData && labTestsData.length > 0) {
        // Group by test type
        const grouped = {};
        labTestsData.forEach(test => {
            if (!grouped[test.test_type]) {
                grouped[test.test_type] = [];
            }
            grouped[test.test_type].push(test);
        });
        
        // Build optgroups
        Object.keys(grouped).sort().forEach(testType => {
            options += `<optgroup label="${escapeHtml(testType)}">`;
            grouped[testType].forEach(test => {
                options += `<option value="${escapeHtml(test.test_name)}" 
                    data-type="${escapeHtml(test.test_type)}"
                    data-description="${escapeHtml(test.description || '')}"
                    data-normal-range="${escapeHtml(test.normal_range || '')}"
                    data-price="${test.price}">
                    ${escapeHtml(test.test_name)} <span style="color: #64748b;">(₱${parseFloat(test.price).toFixed(2)})</span>
                </option>`;
            });
            options += `</optgroup>`;
        });
    } else {
        options += '<option value="" disabled>No lab tests available</option>';
    }
    return options;
}

function addLabTest() {
    const container = document.getElementById('lab-tests-container');
    const item = document.createElement('div');
    item.className = 'dynamic-item lab-item';
    item.innerHTML = `
        <button type="button" class="btn-remove" onclick="removeItem(this)">×</button>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px;">
            <div>
                <label class="form-label">Test Name <span style="color: #ef4444;">*</span></label>
                <select name="lab_tests[${labTestCount}][test_name]" class="form-control lab-test-select" required onchange="updateLabTestInfo(${labTestCount}, this)">
                    ${buildLabTestsOptions()}
                </select>
                <small id="labTestInfo${labTestCount}" style="display: none; margin-top: 8px; padding: 8px; background: #f1f5f9; border-radius: 6px; color: #475569; font-size: 12px;">
                    <strong>Type:</strong> <span id="labTestType${labTestCount}"></span><br>
                    <strong>Description:</strong> <span id="labTestDescription${labTestCount}"></span><br>
                    <strong>Normal Range:</strong> <span id="labTestNormalRange${labTestCount}"></span><br>
                    <strong>Price:</strong> ₱<span id="labTestPrice${labTestCount}"></span>
                </small>
            </div>
            <div>
                <label class="form-label">Priority</label>
                <select name="lab_tests[${labTestCount}][priority]" class="form-control">
                    <option value="routine">Routine</option>
                    <option value="urgent">Urgent</option>
                    <option value="stat">STAT</option>
                </select>
            </div>
        </div>
        <div style="margin-top: 12px;">
            <label class="form-label">Instructions/Remarks</label>
            <textarea name="lab_tests[${labTestCount}][instructions]" class="form-control" rows="2" placeholder="Special instructions..."></textarea>
        </div>
    `;
    container.appendChild(item);
    labTestCount++;
}

function updateLabTestInfo(index, select) {
    const option = select.options[select.selectedIndex];
    const infoDiv = document.getElementById(`labTestInfo${index}`);
    const typeSpan = document.getElementById(`labTestType${index}`);
    const descSpan = document.getElementById(`labTestDescription${index}`);
    const rangeSpan = document.getElementById(`labTestNormalRange${index}`);
    const priceSpan = document.getElementById(`labTestPrice${index}`);
    
    if (option.value && option.dataset.type) {
        typeSpan.textContent = option.dataset.type || 'N/A';
        descSpan.textContent = option.dataset.description || 'N/A';
        rangeSpan.textContent = option.dataset.normalRange || 'N/A';
        priceSpan.textContent = parseFloat(option.dataset.price || 0).toFixed(2);
        infoDiv.style.display = 'block';
    } else {
        infoDiv.style.display = 'none';
    }
}

function addProcedure() {
    const container = document.getElementById('procedures-container');
    const item = document.createElement('div');
    item.className = 'dynamic-item procedure-item';
    item.innerHTML = `
        <button type="button" class="btn-remove" onclick="removeItem(this)">×</button>
        <div>
            <label class="form-label">Procedure Name</label>
            <input type="text" name="procedures[${procedureCount}][procedure_name]" class="form-control" placeholder="e.g., Ultrasound, ECG, CT Scan">
        </div>
        <div style="margin-top: 12px;">
            <label class="form-label">Instructions/Remarks</label>
            <textarea name="procedures[${procedureCount}][instructions]" class="form-control" rows="2" placeholder="Special instructions..."></textarea>
        </div>
    `;
    container.appendChild(item);
    procedureCount++;
}

function removeItem(btn) {
    btn.closest('.dynamic-item').remove();
}

function updateMedicineInfo(index, select) {
    const option = select.options[select.selectedIndex];
    const infoDiv = document.getElementById(`medicineInfo${index}`);
    const descSpan = document.getElementById(`medicineDescription${index}`);
    const stockSpan = document.getElementById(`medicineStock${index}`);
    const priceSpan = document.getElementById(`medicinePrice${index}`);
    
    if (option.value && option.dataset.description) {
        descSpan.textContent = option.dataset.description || 'N/A';
        stockSpan.textContent = option.dataset.quantity || '0';
        priceSpan.textContent = parseFloat(option.dataset.price || 0).toFixed(2);
        infoDiv.style.display = 'block';
    } else {
        infoDiv.style.display = 'none';
    }
}

// Initialize medicine info for first medication
document.addEventListener('DOMContentLoaded', function() {
    const firstSelect = document.querySelector('select[name="medications[0][medicine_name]"]');
    if (firstSelect) {
        firstSelect.addEventListener('change', function() {
            updateMedicineInfo(0, this);
        });
    }
    
    // Initialize lab test info for first lab test
    const firstLabSelect = document.querySelector('select[name="lab_tests[0][test_name]"]');
    if (firstLabSelect) {
        firstLabSelect.addEventListener('change', function() {
            updateLabTestInfo(0, this);
        });
    }
});
</script>

<?= $this->endSection() ?>

