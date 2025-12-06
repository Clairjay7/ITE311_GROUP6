<?php helper('form'); ?>
<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Pediatric Consultation<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .consultation-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
    }
    
    .page-header {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(245, 158, 11, 0.2);
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
    
    .form-section {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
    }
    
    .form-section h3 {
        color: #92400e;
        margin: 0 0 20px 0;
        font-size: 20px;
        font-weight: 700;
        padding-bottom: 12px;
        border-bottom: 2px solid #fde68a;
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    
    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }
    
    .form-group.full-width {
        grid-column: 1 / -1;
    }
    
    .btn-submit {
        background: #f59e0b;
        color: white;
        padding: 14px 32px;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-submit:hover {
        background: #d97706;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    
    .btn-back {
        background: #6b7280;
        color: white;
        padding: 14px 32px;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-right: 12px;
    }
    
    .error-message {
        color: #b91c1c;
        font-size: 13px;
        margin-top: 6px;
    }
    
    .patient-info-card {
        background: #fffbeb;
        border-left: 4px solid #f59e0b;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
    }
    
    .patient-info-card h4 {
        margin: 0 0 12px 0;
        color: #92400e;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
    }
    
    .info-item {
        font-size: 14px;
    }
    
    .info-item strong {
        color: #64748b;
        display: block;
        margin-bottom: 4px;
    }
</style>

<div class="consultation-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-stethoscope"></i> Pediatric Consultation Form
        </h1>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (isset($validation) && $validation->hasError('patient_id')): ?>
        <div style="background: #fee2e2; color: #b91c1c; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
            <?= $validation->getError('patient_id') ?>
        </div>
    <?php endif; ?>

    <!-- Patient Information Card -->
    <div class="patient-info-card">
        <h4><i class="fas fa-user"></i> Patient Information</h4>
        <div class="info-grid">
            <div class="info-item">
                <strong>Patient Name:</strong>
                <?= esc($patient['full_name'] ?? ($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? '')) ?>
            </div>
            <div class="info-item">
                <strong>Age:</strong>
                <?= $age ?> years old
            </div>
            <div class="info-item">
                <strong>Date of Birth:</strong>
                <?= esc($patient['date_of_birth'] ?? 'N/A') ?>
            </div>
            <div class="info-item">
                <strong>Sex:</strong>
                <?= esc(ucfirst($patient['gender'] ?? 'N/A')) ?>
            </div>
            <div class="info-item">
                <strong>Contact:</strong>
                <?= esc($patient['contact'] ?? 'N/A') ?>
            </div>
            <div class="info-item">
                <strong>Address:</strong>
                <?= esc($patient['address'] ?? 'N/A') ?>
            </div>
        </div>
    </div>

    <form method="post" action="<?= site_url('doctor/consultations/pediatrics/save') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="patient_id" value="<?= esc($patient['patient_id'] ?? $patient['id']) ?>">

        <!-- 1. Patient Information (Pre-filled from card above) -->
        <div class="form-section">
            <h3>1. Patient Information</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Parent/Guardian Name</label>
                    <input type="text" name="parent_guardian_name" value="<?= esc($patient['emergency_name'] ?? '') ?>" placeholder="Enter parent/guardian name">
                </div>
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" name="parent_contact" value="<?= esc($patient['emergency_contact'] ?? $patient['contact'] ?? '') ?>" placeholder="Enter contact number">
                </div>
                <div class="form-group full-width">
                    <label>Address</label>
                    <input type="text" name="address" value="<?= esc($patient['address'] ?? $patient['emergency_address'] ?? '') ?>" placeholder="Enter address">
                </div>
            </div>
        </div>

        <!-- 2. Consultation Information -->
        <div class="form-section">
            <h3>2. Consultation Information</h3>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Reason for Consultation <span style="color: #b91c1c;">*</span></label>
                    <textarea name="reason_for_consultation" placeholder="Enter reason for consultation" required><?= set_value('reason_for_consultation') ?></textarea>
                    <?php if (isset($validation) && $validation->hasError('reason_for_consultation')): ?>
                        <div class="error-message"><?= $validation->getError('reason_for_consultation') ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group full-width">
                    <label>Symptoms</label>
                    <textarea name="symptoms" placeholder="Enter symptoms"><?= set_value('symptoms') ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label>Allergies</label>
                    <textarea name="allergies" placeholder="Enter known allergies"><?= set_value('allergies', $patient['allergies'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- 3. Vital Signs -->
        <div class="form-section">
            <h3>3. Vital Signs</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Temperature (°C)</label>
                    <input type="number" name="temperature" step="0.1" placeholder="e.g., 37.5" value="<?= set_value('temperature') ?>">
                </div>
                <div class="form-group">
                    <label>Weight (kg)</label>
                    <input type="number" name="weight" step="0.1" placeholder="e.g., 25.5" value="<?= set_value('weight') ?>">
                </div>
                <div class="form-group">
                    <label>Pulse Rate (bpm)</label>
                    <input type="number" name="pulse_rate" placeholder="e.g., 80" value="<?= set_value('pulse_rate') ?>">
                </div>
            </div>
        </div>

        <!-- 4. Diagnosis -->
        <div class="form-section">
            <h3>4. Diagnosis</h3>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Diagnosis Notes</label>
                    <textarea name="diagnosis_notes" placeholder="Enter diagnosis notes"><?= set_value('diagnosis_notes') ?></textarea>
                </div>
            </div>
        </div>

        <!-- 5. Lab Test Request Section -->
        <div class="form-section" style="margin-bottom: 24px; padding-top: 24px; border-top: 2px solid #e5e7eb;">
            <h3 style="font-size: 16px; margin-bottom: 16px;">
                <i class="fas fa-vial me-2"></i>
                Request Lab Test (Optional)
            </h3>
            <p style="color: #64748b; font-size: 13px; margin-bottom: 16px;">
                <i class="fas fa-info-circle me-2"></i>
                You can request laboratory tests for this patient. This is optional and depends on the patient's condition.
            </p>
            
            <div style="display: flex; gap: 16px; margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 12px 20px; background: #f8fafc; border-radius: 8px; border: 2px solid #e5e7eb; flex: 1;">
                    <input type="radio" name="request_lab_test" value="yes" id="lab_test_yes" onchange="toggleLabTestForm(true)" style="width: 18px; height: 18px; cursor: pointer;">
                    <span style="font-weight: 600; color: #1e293b;">Yes, request lab test</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 12px 20px; background: #f8fafc; border-radius: 8px; border: 2px solid #e5e7eb; flex: 1;">
                    <input type="radio" name="request_lab_test" value="no" id="lab_test_no" onchange="toggleLabTestForm(false)" checked style="width: 18px; height: 18px; cursor: pointer;">
                    <span style="font-weight: 600; color: #1e293b;">No lab test needed</span>
                </label>
            </div>

            <div id="labTestFields" style="display: none;">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="lab_test_name">
                        <i class="fas fa-flask me-2"></i>
                        Select Lab Test
                    </label>
                    <select name="lab_test_name" id="lab_test_name" class="form-control-modern" onchange="updateLabTestInfo()" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        <option value="">-- Select Lab Test --</option>
                        <?php
                        $categoryLabels = [
                            'with_specimen' => '🔬 With Specimen (Requires Physical Specimen)',
                            'without_specimen' => '📋 Without Specimen (No Physical Specimen Needed)'
                        ];
                        
                        if (!empty($labTests ?? [])):
                            foreach ($labTests as $category => $testTypes):
                                if (is_array($testTypes)):
                                    foreach ($testTypes as $testType => $tests):
                                        if (is_array($tests)):
                                            $categoryLabel = $categoryLabels[$category] ?? ucfirst(str_replace('_', ' ', $category));
                                            ?>
                                            <optgroup label="<?= esc($categoryLabel) ?>">
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
                                                            <span style="color: #64748b;"> - ₱<?= number_format($test['price'] ?? 0, 2) ?></span>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </optgroup>
                                            <?php
                                        endif;
                                    endforeach;
                                endif;
                            endforeach;
                        else:
                        ?>
                            <option value="" disabled>No lab tests available</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div id="lab_test_info" style="display: none; padding: 16px; background: #f8fafc; border-radius: 10px; border: 2px solid #e5e7eb; margin-bottom: 20px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                        <div>
                            <div style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 4px;">Test Type</div>
                            <div style="font-weight: 600; color: #1e293b;" id="test_type_display">-</div>
                        </div>
                        <div>
                            <div style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 4px;">Price</div>
                            <div style="font-weight: 600; color: #1e293b;" id="test_price_display">-</div>
                        </div>
                        <div>
                            <div style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 4px;">Normal Range</div>
                            <div style="font-weight: 600; color: #1e293b;" id="test_range_display">-</div>
                        </div>
                    </div>
                    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e5e7eb;">
                        <div style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 4px;">Description</div>
                        <div style="color: #1e293b; font-size: 14px;" id="test_description_display">-</div>
                    </div>
                </div>

                <div id="nurse_field_group" class="form-group" style="margin-bottom: 20px; display: none;">
                    <label for="lab_nurse_id">
                        <i class="fas fa-user-nurse me-2"></i>
                        Assign Nurse <span id="nurse_required_indicator" style="color: #ef4444; display: none;">*</span> <span id="nurse_label_text">(Will collect specimen)</span>
                    </label>
                    <select name="lab_nurse_id" id="lab_nurse_id" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        <option value="">-- Select Nurse --</option>
                        <?php if (!empty($nurses)): ?>
                            <?php foreach ($nurses as $nurse): ?>
                                <option value="<?= esc($nurse['id']) ?>">
                                    <?= esc($nurse['username']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No nurses available</option>
                        <?php endif; ?>
                    </select>
                    <small id="nurse_help_text" style="color: #64748b; font-size: 13px; margin-top: 4px; display: block;">
                        <i class="fas fa-info-circle"></i> Select a nurse who will collect the specimen from the patient
                    </small>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="lab_test_remarks">
                        <i class="fas fa-sticky-note me-2"></i>
                        Remarks / Instructions (Optional)
                    </label>
                    <textarea name="lab_test_remarks" id="lab_test_remarks" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; min-height: 100px; resize: vertical;" rows="3" placeholder="Add any special instructions or remarks for the lab test..."></textarea>
                </div>
            </div>
        </div>

        <!-- 6. Medication Prescription Section -->
        <div class="form-section" style="margin-bottom: 24px; padding-top: 24px; border-top: 2px solid #e5e7eb;">
            <h3 style="font-size: 16px; margin-bottom: 16px;">
                <i class="fas fa-pills me-2"></i>
                Prescribe Medication (After Consultation)
            </h3>
            <p style="color: #64748b; font-size: 13px; margin-bottom: 16px;">
                <i class="fas fa-info-circle me-2"></i>
                After completing the consultation, you can prescribe medication here. The patient will be asked where they want to purchase the medication.
            </p>
            
            <div style="display: flex; gap: 16px; margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 12px 20px; background: #f8fafc; border-radius: 8px; border: 2px solid #e5e7eb; flex: 1;">
                    <input type="radio" name="prescribe_medication" value="yes" id="prescribe_yes" onchange="toggleMedicationForm(true)" style="width: 18px; height: 18px; cursor: pointer;">
                    <span style="font-weight: 600; color: #1e293b;">Yes, prescribe medication</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 12px 20px; background: #f8fafc; border-radius: 8px; border: 2px solid #e5e7eb; flex: 1;">
                    <input type="radio" name="prescribe_medication" value="no" id="prescribe_no" onchange="toggleMedicationForm(false)" checked style="width: 18px; height: 18px; cursor: pointer;">
                    <span style="font-weight: 600; color: #1e293b;">No medication needed</span>
                </label>
            </div>

            <div id="medicationFields" style="display: none;">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="medicine_name">
                        <i class="fas fa-capsules me-2"></i>
                        Select Medicine <span class="text-danger">*</span>
                    </label>
                    <select name="medicine_name" id="medicine_name" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        <option value="">-- Select Medicine --</option>
                        <?php if (!empty($medicines)): ?>
                            <?php foreach ($medicines as $medicine): ?>
                                <option value="<?= esc($medicine['item_name']) ?>" 
                                    data-price="<?= esc($medicine['price']) ?>"
                                    data-stock="<?= esc($medicine['quantity']) ?>">
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
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label for="dosage">
                            <i class="fas fa-syringe me-2"></i>
                            Dosage <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="dosage" id="dosage" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="e.g., 500mg, 1 tablet">
                    </div>
                    <div class="form-group">
                        <label for="frequency">
                            <i class="fas fa-clock me-2"></i>
                            Frequency <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="frequency" id="frequency" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="e.g., Every 8 hours, Twice daily">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="duration">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Duration <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="duration" id="duration" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" placeholder="e.g., 7 days, 2 weeks">
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label>
                        <i class="fas fa-shopping-cart me-2"></i>
                        Where will the patient purchase the medication? <span class="text-danger">*</span>
                    </label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 8px;">
                        <label style="display: flex; align-items: center; gap: 12px; padding: 16px; background: #dbeafe; border-radius: 10px; border: 2px solid #3b82f6; cursor: pointer;">
                            <input type="radio" name="purchase_location" value="hospital_pharmacy" id="purchase_hospital" onchange="toggleNurseField(true)" style="width: 20px; height: 20px; cursor: pointer;">
                            <div>
                                <div style="font-weight: 700; color: #1e40af; margin-bottom: 4px;">
                                    <i class="fas fa-hospital me-2"></i>Hospital Pharmacy
                                </div>
                                <div style="font-size: 13px; color: #64748b;">
                                    Patient will buy from hospital pharmacy. Medication will be dispensed by pharmacy staff.
                                </div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: center; gap: 12px; padding: 16px; background: #fef3c7; border-radius: 10px; border: 2px solid #f59e0b; cursor: pointer;">
                            <input type="radio" name="purchase_location" value="outside" id="purchase_outside" onchange="toggleNurseField(false)" style="width: 20px; height: 20px; cursor: pointer;">
                            <div>
                                <div style="font-weight: 700; color: #92400e; margin-bottom: 4px;">
                                    <i class="fas fa-store me-2"></i>Outside Hospital
                                </div>
                                <div style="font-size: 13px; color: #64748b;">
                                    Patient will buy from external pharmacy/drugstore. Prescription will be provided.
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="nurseField" class="form-group" style="margin-bottom: 20px; display: none;">
                    <label for="nurse_id">
                        <i class="fas fa-user-nurse me-2"></i>
                        Assign to Nurse <span class="text-danger">*</span>
                    </label>
                    <select name="nurse_id" id="nurse_id" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        <option value="">-- Select Nurse --</option>
                        <?php if (!empty($nurses)): ?>
                            <?php foreach ($nurses as $nurse): ?>
                                <option value="<?= esc($nurse['id']) ?>">
                                    <?= esc($nurse['username']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No nurses available</option>
                        <?php endif; ?>
                    </select>
                    <small style="color: #64748b; font-size: 13px; margin-top: 4px; display: block;">
                        <i class="fas fa-info-circle"></i> Nurse will administer medication after pharmacy dispenses it.
                    </small>
                </div>
            </div>
        </div>

        <!-- 7. Follow-Up -->
        <div class="form-section">
            <h3>7. Follow-Up</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Follow-up Date</label>
                    <input type="date" name="follow_up_date" value="<?= set_value('follow_up_date') ?>" min="<?= date('Y-m-d') ?>">
                </div>
            </div>
        </div>

        <!-- 8. Submit -->
        <div style="text-align: center; margin-top: 32px;">
            <a href="<?= site_url('doctor/consultations/pediatrics') ?>" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="fas fa-save"></i> Save Consultation
            </button>
        </div>
    </form>
</div>

<script>
function toggleMedicationForm(show) {
    const medicationFields = document.getElementById('medicationFields');
    if (show) {
        medicationFields.style.display = 'block';
    } else {
        medicationFields.style.display = 'none';
        // Clear medication fields when hidden
        document.getElementById('medicine_name').value = '';
        document.getElementById('dosage').value = '';
        document.getElementById('frequency').value = '';
        document.getElementById('duration').value = '';
        document.getElementById('nurse_id').value = '';
        document.querySelectorAll('input[name="purchase_location"]').forEach(radio => radio.checked = false);
        // Hide nurse field
        document.getElementById('nurseField').style.display = 'none';
    }
}

function toggleNurseField(show) {
    const nurseField = document.getElementById('nurseField');
    if (show) {
        nurseField.style.display = 'block';
        document.getElementById('nurse_id').setAttribute('required', 'required');
    } else {
        nurseField.style.display = 'none';
        document.getElementById('nurse_id').removeAttribute('required');
        document.getElementById('nurse_id').value = '';
    }
}

function toggleLabTestForm(show) {
    const labTestFields = document.getElementById('labTestFields');
    if (show) {
        labTestFields.style.display = 'block';
    } else {
        labTestFields.style.display = 'none';
        // Clear lab test fields when hidden
        document.getElementById('lab_test_name').value = '';
        document.getElementById('lab_nurse_id').value = '';
        document.getElementById('lab_test_remarks').value = '';
        document.getElementById('lab_test_info').style.display = 'none';
        document.getElementById('nurse_field_group').style.display = 'none';
    }
}

function updateLabTestInfo() {
    const select = document.getElementById('lab_test_name');
    const selectedOption = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('lab_test_info');
    const nurseField = document.getElementById('lab_nurse_id');
    const nurseFieldGroup = document.getElementById('nurse_field_group');
    const nurseRequiredIndicator = document.getElementById('nurse_required_indicator');
    const nurseLabelText = document.getElementById('nurse_label_text');
    const nurseHelpText = document.getElementById('nurse_help_text');
    
    if (!selectedOption || !selectedOption.value || selectedOption.value === '') {
        infoDiv.style.display = 'none';
        // Reset nurse field to default
        nurseField.removeAttribute('required');
        nurseRequiredIndicator.style.display = 'none';
        nurseLabelText.textContent = '(Will collect specimen)';
        nurseHelpText.innerHTML = '<i class="fas fa-info-circle"></i> Select a nurse who will collect the specimen from the patient';
        nurseFieldGroup.style.display = 'none';
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
        nurseRequiredIndicator.style.display = 'none';
        nurseLabelText.textContent = '(Will collect specimen)';
        nurseHelpText.innerHTML = '<i class="fas fa-info-circle"></i> Select a nurse who will collect the specimen from the patient';
    }
}

// Form validation before submission
document.querySelector('form').addEventListener('submit', function(e) {
    const prescribeMedication = document.querySelector('input[name="prescribe_medication"]:checked');
    
    if (prescribeMedication && prescribeMedication.value === 'yes') {
        // Validate medication fields
        const medicineName = document.getElementById('medicine_name').value;
        const dosage = document.getElementById('dosage').value;
        const frequency = document.getElementById('frequency').value;
        const duration = document.getElementById('duration').value;
        const purchaseLocation = document.querySelector('input[name="purchase_location"]:checked');
        
        if (!medicineName || !dosage || !frequency || !duration || !purchaseLocation) {
            e.preventDefault();
            alert('Please fill in all medication prescription fields and select where the patient will purchase the medication.');
            return false;
        }
        
        // Only require nurse_id if hospital pharmacy is selected
        if (purchaseLocation.value === 'hospital_pharmacy') {
            const nurseId = document.getElementById('nurse_id').value;
            if (!nurseId) {
                e.preventDefault();
                alert('Please assign a nurse for hospital pharmacy medication orders.');
                return false;
            }
        }
    }
    
    // Handle lab test validation
    const requestLabTest = document.querySelector('input[name="request_lab_test"]:checked');
    if (requestLabTest && requestLabTest.value === 'yes') {
        const labTestName = document.getElementById('lab_test_name').value;
        if (!labTestName) {
            e.preventDefault();
            alert('Please select a lab test.');
            return false;
        }
        
        // Check if test requires specimen
        const labTestSelect = document.getElementById('lab_test_name');
        const selectedOption = labTestSelect.options[labTestSelect.selectedIndex];
        const specimenCategory = selectedOption ? (selectedOption.dataset.specimenCategory || 'with_specimen') : 'with_specimen';
        const requiresSpecimen = (specimenCategory === 'with_specimen');
        
        // Only require nurse if test requires specimen
        if (requiresSpecimen) {
            const labNurseId = document.getElementById('lab_nurse_id').value;
            if (!labNurseId) {
                e.preventDefault();
                alert('Please assign a nurse for lab tests that require specimen collection.');
                return false;
            }
        }
    }
});
</script>

<?= $this->endSection() ?>

