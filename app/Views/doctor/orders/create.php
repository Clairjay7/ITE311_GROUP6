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
                <label class="form-label-modern" for="order_type">
                    <i class="fas fa-tag me-2"></i>
                    Order Type <span class="text-danger">*</span>
                </label>
                <select name="order_type" id="order_type" class="form-control-modern" required>
                    <option value="">Select Order Type</option>
                    <?php 
                    $selectedOrderType = $selected_order_type ?? old('order_type');
                    ?>
                    <option value="medication" <?= $selectedOrderType == 'medication' ? 'selected' : '' ?>>Medication (Routes to Pharmacy first)</option>
                    <option value="lab_test" <?= $selectedOrderType == 'lab_test' ? 'selected' : '' ?>>Lab Test</option>
                    <option value="procedure" <?= $selectedOrderType == 'procedure' ? 'selected' : '' ?>>Procedure</option>
                    <option value="diet" <?= $selectedOrderType == 'diet' ? 'selected' : '' ?>>Diet</option>
                    <option value="activity" <?= $selectedOrderType == 'activity' ? 'selected' : '' ?>>Activity</option>
                    <option value="other" <?= $selectedOrderType == 'other' ? 'selected' : '' ?>>Other</option>
                </select>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['order_type'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['order_type']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group-modern">
                <label class="form-label-modern" for="nurse_id">
                    <i class="fas fa-user-nurse me-2"></i>
                    Assign to Nurse <span class="text-danger">*</span>
                    <small style="display: block; font-weight: normal; color: #64748b; margin-top: 4px;">
                        <i class="fas fa-info-circle"></i> Nurse will administer medication after Pharmacy dispenses
                    </small>
                </label>
                <select name="nurse_id" id="nurse_id" class="form-control-modern" required>
                    <option value="">Select Nurse</option>
                    <?php foreach ($nurses as $nurse): ?>
                        <option value="<?= esc($nurse['id']) ?>" <?= old('nurse_id') == $nurse['id'] ? 'selected' : '' ?>>
                            <?= esc(ucfirst($nurse['username'])) ?> (<?= esc($nurse['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nurse_id'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['nurse_id']) ?></div>
                <?php endif; ?>
            </div>
            
            <!-- Medication-specific fields (shown when medication is selected) -->
            <div id="medicationFields" style="display: none;">
                <div class="form-group-modern">
                    <label class="form-label-modern" for="medicine_name">
                        <i class="fas fa-pills me-2"></i>
                        Select Medicine <span class="text-danger">*</span>
                    </label>
                    <select name="medicine_name" id="medicine_name" class="form-control-modern" required>
                        <option value="">-- Select Medicine --</option>
                        <?php if (!empty($medicines)): ?>
                            <?php foreach ($medicines as $medicine): ?>
                                <option value="<?= esc($medicine['item_name']) ?>" 
                                    data-description="<?= esc($medicine['description'] ?? '') ?>"
                                    data-quantity="<?= esc($medicine['quantity']) ?>"
                                    data-price="<?= esc($medicine['price']) ?>"
                                    <?= old('medicine_name') == $medicine['item_name'] ? 'selected' : '' ?>>
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
                        <div class="text-danger" style="margin-top: 8px;">
                            <i class="fas fa-exclamation-triangle"></i> No medicines available in pharmacy. Please contact pharmacy staff.
                        </div>
                    <?php endif; ?>
                    <small id="medicineInfo" style="display: none; margin-top: 8px; padding: 8px; background: #f1f5f9; border-radius: 6px; color: #475569;">
                        <strong>Description:</strong> <span id="medicineDescription"></span><br>
                        <strong>Available Stock:</strong> <span id="medicineStock"></span> units<br>
                        <strong>Price:</strong> ₱<span id="medicinePrice"></span>
                    </small>
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['medicine_name'])): ?>
                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['medicine_name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label class="form-label-modern" for="dosage">
                                <i class="fas fa-syringe me-2"></i>
                                Dosage <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="dosage" id="dosage" class="form-control-modern" value="<?= old('dosage') ?>" placeholder="e.g., 500mg, 1 tablet">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['dosage'])): ?>
                                <div class="text-danger"><?= esc(session()->getFlashdata('errors')['dosage']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-modern">
                            <label class="form-label-modern" for="duration">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Duration <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="duration" id="duration" class="form-control-modern" value="<?= old('duration') ?>" placeholder="e.g., 7 days, 2 weeks">
                            <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['duration'])): ?>
                                <div class="text-danger"><?= esc(session()->getFlashdata('errors')['duration']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group-modern">
                <label class="form-label-modern" for="order_description">
                    <i class="fas fa-file-medical me-2"></i>
                    Order Description <span class="text-danger">*</span>
                </label>
                <textarea name="order_description" id="order_description" class="form-control-modern" rows="4" required placeholder="Describe the medical order in detail..."><?= old('order_description') ?></textarea>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['order_description'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['order_description']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="instructions">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Instructions (Optional)
                </label>
                <textarea name="instructions" id="instructions" class="form-control-modern" rows="3" placeholder="Additional instructions for nurses..."><?= old('instructions') ?></textarea>
            </div>

            <div class="form-group-modern">
                <label class="form-label-modern" for="remarks">
                    <i class="fas fa-sticky-note me-2"></i>
                    Remarks (Optional)
                </label>
                <textarea name="remarks" id="remarks" class="form-control-modern" rows="2" placeholder="Additional remarks or notes..."><?= old('remarks') ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
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
                <div class="col-md-6">
                    <div class="form-group-modern">
                        <label class="form-label-modern" for="start_date">
                            <i class="fas fa-calendar me-2"></i>
                            Start Date (Optional)
                        </label>
                        <input type="date" name="start_date" id="start_date" class="form-control-modern" value="<?= old('start_date') ?>">
                    </div>
                </div>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="end_date">
                    <i class="fas fa-calendar-times me-2"></i>
                    End Date (Optional)
                </label>
                <input type="date" name="end_date" id="end_date" class="form-control-modern" value="<?= old('end_date') ?>">
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
document.getElementById('order_type').addEventListener('change', function() {
    const medicationFields = document.getElementById('medicationFields');
    const frequencyRequired = document.getElementById('frequencyRequired');
    const medicineName = document.getElementById('medicine_name');
    const dosage = document.getElementById('dosage');
    const duration = document.getElementById('duration');
    const frequency = document.getElementById('frequency');
    
    if (this.value === 'medication') {
        // Show medication fields
        medicationFields.style.display = 'block';
        frequencyRequired.style.display = 'inline';
        medicineName.setAttribute('required', 'required');
        dosage.setAttribute('required', 'required');
        duration.setAttribute('required', 'required');
        frequency.setAttribute('required', 'required');
    } else {
        // Hide medication fields
        medicationFields.style.display = 'none';
        frequencyRequired.style.display = 'none';
        medicineName.removeAttribute('required');
        dosage.removeAttribute('required');
        duration.removeAttribute('required');
        frequency.removeAttribute('required');
    }
});

// Show medicine info when selected
const medicineNameSelect = document.getElementById('medicine_name');
if (medicineNameSelect) {
    medicineNameSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const medicineInfo = document.getElementById('medicineInfo');
        const medicineDescription = document.getElementById('medicineDescription');
        const medicineStock = document.getElementById('medicineStock');
        const medicinePrice = document.getElementById('medicinePrice');
        
        if (this.value && selectedOption) {
            medicineDescription.textContent = selectedOption.getAttribute('data-description') || 'No description';
            medicineStock.textContent = selectedOption.getAttribute('data-quantity') || '0';
            medicinePrice.textContent = parseFloat(selectedOption.getAttribute('data-price') || 0).toFixed(2);
            medicineInfo.style.display = 'block';
        } else {
            medicineInfo.style.display = 'none';
        }
    });
}

// Trigger on page load if medication is already selected
if (document.getElementById('order_type').value === 'medication') {
    document.getElementById('order_type').dispatchEvent(new Event('change'));
}
</script>
<?= $this->endSection() ?>

