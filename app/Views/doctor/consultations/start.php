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
            <form action="<?= site_url('doctor/consultations/save-consultation') ?>" method="post">
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
                
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px;">
                    <a href="<?= site_url('doctor/patients') ?>" class="btn-modern btn-modern-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn-modern btn-modern-primary">
                        <i class="fas fa-save"></i> Save Consultation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Orders Section -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5><i class="fas fa-prescription me-2"></i>Create Orders (Optional)</h5>
        </div>
        <div class="card-body-modern">
            <p style="color: #64748b; margin-bottom: 24px;">
                After recording your consultation findings, you can create orders for this patient. Orders will be routed to the appropriate departments.
            </p>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                <a href="<?= site_url('doctor/orders/create?patient_id=' . esc($patient['patient_id'] ?? $patient['id']) . '&patient_source=' . esc($patient_source)) ?>" 
                   class="btn-modern btn-modern-primary" style="text-align: center; flex-direction: column; padding: 20px;">
                    <i class="fas fa-pills" style="font-size: 32px; margin-bottom: 8px;"></i>
                    <div style="font-weight: 600;">Prescribe Medication</div>
                    <div style="font-size: 12px; opacity: 0.9; margin-top: 4px;">→ Pharmacy Dashboard</div>
                </a>
                
                <a href="<?= site_url('doctor/lab-requests?patient_id=' . esc($patient['patient_id'] ?? $patient['id'])) ?>" 
                   class="btn-modern" style="background: #0288d1; color: white; text-align: center; flex-direction: column; padding: 20px;">
                    <i class="fas fa-flask" style="font-size: 32px; margin-bottom: 8px;"></i>
                    <div style="font-weight: 600;">Request Lab Tests</div>
                    <div style="font-size: 12px; opacity: 0.9; margin-top: 4px;">→ Lab Staff Dashboard</div>
                </a>
                
                <a href="<?= site_url('doctor/orders/create?patient_id=' . esc($patient['patient_id'] ?? $patient['id']) . '&patient_source=' . esc($patient_source) . '&order_type=procedure') ?>" 
                   class="btn-modern" style="background: #f59e0b; color: white; text-align: center; flex-direction: column; padding: 20px;">
                    <i class="fas fa-procedures" style="font-size: 32px; margin-bottom: 8px;"></i>
                    <div style="font-weight: 600;">Request Procedure</div>
                    <div style="font-size: 12px; opacity: 0.9; margin-top: 4px;">→ Nurse Dashboard</div>
                </a>
                
                <a href="<?= site_url('doctor/orders/create?patient_id=' . esc($patient['patient_id'] ?? $patient['id']) . '&patient_source=' . esc($patient_source) . '&order_type=other') ?>" 
                   class="btn-modern" style="background: #64748b; color: white; text-align: center; flex-direction: column; padding: 20px;">
                    <i class="fas fa-tasks" style="font-size: 32px; margin-bottom: 8px;"></i>
                    <div style="font-weight: 600;">Other Orders</div>
                    <div style="font-size: 12px; opacity: 0.9; margin-top: 4px;">→ Various Departments</div>
                </a>
            </div>
            
            <div style="margin-top: 24px; padding: 16px; background: #f8fafc; border-radius: 10px; border-left: 4px solid #2e7d32;">
                <div style="font-weight: 600; color: #1e293b; margin-bottom: 8px;">
                    <i class="fas fa-info-circle me-2"></i>Note:
                </div>
                <ul style="margin: 0; padding-left: 20px; color: #64748b; font-size: 14px;">
                    <li>All orders are linked to this patient record and logged with your doctor ID & timestamp</li>
                    <li>Consultation billing entry will be automatically created when you save the consultation</li>
                    <li>Charges from orders (medications, lab tests, procedures) will be queued for the Accountant</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

