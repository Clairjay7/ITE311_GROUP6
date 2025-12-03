<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Start Consultation<?= $this->endSection() ?>

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
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .card-header-modern {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 20px 24px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .card-header-modern h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
    }
    
    .card-body-modern {
        padding: 32px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    
    .info-item {
        background: #f8fafc;
        padding: 16px;
        border-radius: 10px;
        border-left: 4px solid #2e7d32;
    }
    
    .info-item label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    
    .info-item .value {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
    }
    
    .queue-badge {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        font-size: 24px;
        font-weight: 700;
        text-align: center;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    
    .form-label-modern {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        font-size: 14px;
        display: block;
    }
    
    .form-control-modern,
    .form-select-modern,
    textarea.form-control-modern {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: white;
        font-family: inherit;
    }
    
    .form-control-modern:focus,
    .form-select-modern:focus,
    textarea.form-control-modern:focus {
        outline: none;
        border-color: #2e7d32;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }
    
    textarea.form-control-modern {
        min-height: 120px;
        resize: vertical;
    }
    
    .btn-modern {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
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
    
    .visit-type-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
    }
    
    .visit-type-consultation {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .visit-type-checkup {
        background: #fef3c7;
        color: #92400e;
    }
    
    .visit-type-followup {
        background: #d1fae5;
        color: #065f46;
    }
</style>

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-stethoscope"></i>
            Start Consultation
        </h1>
    </div>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Patient Information Card -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5><i class="fas fa-user-injured me-2"></i>Patient Information</h5>
        </div>
        <div class="card-body-modern">
            <div class="info-grid">
                <div class="info-item">
                    <label>Patient Name</label>
                    <div class="value"><?= esc(ucwords(trim(($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '')))) ?></div>
                </div>
                
                <div class="info-item">
                    <label>Age</label>
                    <div class="value"><?= esc($patient['age'] ?? 'N/A') ?> <?= !empty($patient['age']) ? 'years old' : '' ?></div>
                </div>
                
                <div class="info-item">
                    <label>Gender</label>
                    <div class="value"><?= esc(ucfirst($patient['gender'] ?? 'N/A')) ?></div>
                </div>
                
                <div class="info-item">
                    <label>Visit Type</label>
                    <div class="value">
                        <?php 
                        $visitType = $patient['visit_type'] ?? 'Consultation';
                        $visitTypeClass = '';
                        if ($visitType === 'Consultation') $visitTypeClass = 'visit-type-consultation';
                        elseif ($visitType === 'Check-up') $visitTypeClass = 'visit-type-checkup';
                        elseif ($visitType === 'Follow-up') $visitTypeClass = 'visit-type-followup';
                        ?>
                        <span class="visit-type-badge <?= $visitTypeClass ?>">
                            <?= esc($visitType) ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 24px;">
                <div class="queue-badge">
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 4px;">Queue Number</div>
                    <div>#<?= esc($queue_number ?? 'N/A') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Consultation Form -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5><i class="fas fa-notes-medical me-2"></i>Consultation Details</h5>
        </div>
        <div class="card-body-modern">
            <form id="consultationForm" action="<?= site_url('doctor/consultations/save-consultation') ?>" method="post">
                <?= csrf_field() ?>
                
                <input type="hidden" name="patient_id" value="<?= esc($patient['patient_id'] ?? $patient['id']) ?>">
                <input type="hidden" name="patient_source" value="<?= esc($patient_source) ?>">
                <input type="hidden" name="queue_number" value="<?= esc($queue_number ?? '') ?>">
                
                <div class="form-group-modern" style="margin-bottom: 24px;">
                    <label class="form-label-modern" for="consultation_date">
                        <i class="fas fa-calendar me-2"></i>Consultation Date <span class="text-danger">*</span>
                    </label>
                    <input type="date" 
                           name="consultation_date" 
                           id="consultation_date" 
                           class="form-control-modern" 
                           value="<?= old('consultation_date', date('Y-m-d')) ?>" 
                           required>
                    <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('consultation_date')): ?>
                        <div class="text-danger" style="margin-top: 4px;">
                            <?= session()->getFlashdata('validation')->getError('consultation_date') ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group-modern" style="margin-bottom: 24px;">
                    <label class="form-label-modern" for="consultation_time">
                        <i class="fas fa-clock me-2"></i>Consultation Time <span class="text-danger">*</span>
                    </label>
                    <input type="time" 
                           name="consultation_time" 
                           id="consultation_time" 
                           class="form-control-modern" 
                           value="<?= old('consultation_time', date('H:i')) ?>" 
                           required>
                    <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('consultation_time')): ?>
                        <div class="text-danger" style="margin-top: 4px;">
                            <?= session()->getFlashdata('validation')->getError('consultation_time') ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group-modern" style="margin-bottom: 24px;">
                    <label class="form-label-modern" for="observations">
                        <i class="fas fa-clipboard-list me-2"></i>Observations / Findings
                    </label>
                    <textarea name="observations" 
                              id="observations" 
                              class="form-control-modern" 
                              rows="5" 
                              placeholder="Record your observations and findings during the consultation..."><?= old('observations') ?></textarea>
                    <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('observations')): ?>
                        <div class="text-danger" style="margin-top: 4px;">
                            <?= session()->getFlashdata('validation')->getError('observations') ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group-modern" style="margin-bottom: 24px;">
                    <label class="form-label-modern" for="diagnosis">
                        <i class="fas fa-diagnoses me-2"></i>Diagnosis
                    </label>
                    <textarea name="diagnosis" 
                              id="diagnosis" 
                              class="form-control-modern" 
                              rows="4" 
                              placeholder="Enter diagnosis..."><?= old('diagnosis') ?></textarea>
                    <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('diagnosis')): ?>
                        <div class="text-danger" style="margin-top: 4px;">
                            <?= session()->getFlashdata('validation')->getError('diagnosis') ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group-modern" style="margin-bottom: 24px;">
                    <label class="form-label-modern" for="notes">
                        <i class="fas fa-sticky-note me-2"></i>Notes / Remarks
                    </label>
                    <textarea name="notes" 
                              id="notes" 
                              class="form-control-modern" 
                              rows="4" 
                              placeholder="Additional notes or remarks..."><?= old('notes') ?></textarea>
                    <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('notes')): ?>
                        <div class="text-danger" style="margin-top: 4px;">
                            <?= session()->getFlashdata('validation')->getError('notes') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group-modern" style="margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 12px; padding: 16px; background: #f8fafc; border-radius: 10px; border: 2px solid #e5e7eb;">
                        <input type="checkbox" 
                               name="for_admission" 
                               id="for_admission" 
                               value="1"
                               <?= old('for_admission') ? 'checked' : '' ?>
                               style="width: 20px; height: 20px; cursor: pointer;">
                        <label for="for_admission" style="margin: 0; font-weight: 600; color: #1e293b; cursor: pointer; flex: 1;">
                            <i class="fas fa-hospital me-2" style="color: #dc2626;"></i>
                            Mark patient for admission
                        </label>
                    </div>
                    <p style="margin-top: 8px; font-size: 13px; color: #64748b;">
                        <i class="fas fa-info-circle me-2"></i>
                        Check this box if the patient needs to be admitted to the hospital. A Nurse or Receptionist will process the admission and assign a room/bed.
                    </p>
                </div>

                <!-- Lab Test Request Section -->
                <div class="form-group-modern" style="margin-bottom: 24px; padding-top: 24px; border-top: 2px solid #e5e7eb;">
                    <label class="form-label-modern" style="font-size: 16px; margin-bottom: 16px;">
                        <i class="fas fa-vial me-2"></i>
                        Request Lab Test (Optional)
                    </label>
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
                        <div class="form-group-modern" style="margin-bottom: 20px;">
                            <label class="form-label-modern" for="lab_test_name">
                                <i class="fas fa-flask me-2"></i>
                                Select Lab Test
                            </label>
                            <select name="lab_test_name" id="lab_test_name" class="form-control-modern" onchange="updateLabTestInfo()">
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

                        <div id="nurse_field_group" class="form-group-modern" style="margin-bottom: 20px; display: none;">
                            <label class="form-label-modern" for="lab_nurse_id">
                                <i class="fas fa-user-nurse me-2"></i>
                                Assign Nurse <span id="nurse_required_indicator" style="color: #ef4444; display: none;">*</span> <span id="nurse_label_text">(Will collect specimen)</span>
                            </label>
                            <select name="lab_nurse_id" id="lab_nurse_id" class="form-control-modern">
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

                        <div class="form-group-modern" style="margin-bottom: 20px;">
                            <label class="form-label-modern" for="lab_test_remarks">
                                <i class="fas fa-sticky-note me-2"></i>
                                Remarks / Instructions (Optional)
                            </label>
                            <textarea name="lab_test_remarks" id="lab_test_remarks" class="form-control-modern" rows="3" placeholder="Add any special instructions or remarks for the lab test..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Medication Prescription Section -->
                <div class="form-group-modern" style="margin-bottom: 24px; padding-top: 24px; border-top: 2px solid #e5e7eb;">
                    <label class="form-label-modern" style="font-size: 16px; margin-bottom: 16px;">
                        <i class="fas fa-pills me-2"></i>
                        Prescribe Medication (After Consultation)
                    </label>
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
                        <div class="form-group-modern" style="margin-bottom: 20px;">
                            <label class="form-label-modern" for="medicine_name">
                                <i class="fas fa-capsules me-2"></i>
                                Select Medicine <span class="text-danger">*</span>
                            </label>
                            <select name="medicine_name" id="medicine_name" class="form-control-modern">
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
                            <div class="form-group-modern">
                                <label class="form-label-modern" for="dosage">
                                    <i class="fas fa-syringe me-2"></i>
                                    Dosage <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="dosage" id="dosage" class="form-control-modern" placeholder="e.g., 500mg, 1 tablet">
                            </div>
                            <div class="form-group-modern">
                                <label class="form-label-modern" for="frequency">
                                    <i class="fas fa-clock me-2"></i>
                                    Frequency <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="frequency" id="frequency" class="form-control-modern" placeholder="e.g., Every 8 hours, Twice daily">
                            </div>
                        </div>

                        <div class="form-group-modern" style="margin-bottom: 20px;">
                            <label class="form-label-modern" for="duration">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Duration <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="duration" id="duration" class="form-control-modern" placeholder="e.g., 7 days, 2 weeks">
                        </div>

                        <div class="form-group-modern" style="margin-bottom: 24px;">
                            <label class="form-label-modern">
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

                        <div id="nurseField" class="form-group-modern" style="margin-bottom: 20px; display: none;">
                            <label class="form-label-modern" for="nurse_id">
                                <i class="fas fa-user-nurse me-2"></i>
                                Assign to Nurse <span class="text-danger">*</span>
                            </label>
                            <select name="nurse_id" id="nurse_id" class="form-control-modern">
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
                
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px;">
                    <a href="<?= site_url('doctor/patients') ?>" class="btn-modern btn-modern-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn-modern btn-modern-primary">
                        <i class="fas fa-save"></i> Save Consultation & Prescription
                    </button>
                </div>
            </form>
        </div>
    </div>

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
    
    // Update nurse field based on specimen category (matching admin logic)
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
document.getElementById('consultationForm').addEventListener('submit', function(e) {
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
    
    // Handle lab test validation (matching admin logic)
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
        
        // Only require nurse if test requires specimen (matching admin logic)
        if (requiresSpecimen) {
            const labNurseId = document.getElementById('lab_nurse_id').value;
            if (!labNurseId) {
                e.preventDefault();
                alert('Please assign a nurse for lab tests that require specimen collection.');
                return false;
            }
        }
        
        // For without_specimen, clear nurse_id value to avoid validation issues (matching admin logic)
        if (!requiresSpecimen) {
            const labNurseField = document.getElementById('lab_nurse_id');
            if (labNurseField) {
                labNurseField.value = ''; // Clear value
            }
        }
    }
    
    return true;
});
</script>

<?= $this->endSection() ?>

