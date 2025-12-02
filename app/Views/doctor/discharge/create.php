<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Create Discharge Order<?= $this->endSection() ?>

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
    }
    .card-header-modern {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        padding: 20px 24px;
        border-bottom: 2px solid #4caf50;
    }
    .card-body-modern {
        padding: 32px;
    }
    .form-label-modern {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        font-size: 14px;
        display: block;
    }
    .form-control-modern, textarea.form-control-modern {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 15px;
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
        border: none;
        cursor: pointer;
        text-decoration: none;
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
</style>

<div class="page-header">
    <h1><i class="fas fa-file-medical"></i> Create Discharge Order</h1>
</div>

<?php if ($existingOrder): ?>
    <div style="background: #fef3c7; color: #92400e; padding: 16px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #f59e0b;">
        <strong><i class="fas fa-info-circle"></i> Notice:</strong> A discharge order already exists for this patient. 
        <a href="<?= site_url('doctor/discharge/view/' . $existingOrder['id']) ?>" style="color: #92400e; text-decoration: underline;">View existing order</a>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="modern-card">
    <div class="card-header-modern">
        <h5><i class="fas fa-user-injured"></i> Patient Information</h5>
    </div>
    <div class="card-body-modern">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div>
                <label style="font-size: 12px; color: #64748b;">Patient Name</label>
                <div style="font-weight: 600; color: #1e293b;">
                    <?= esc(ucwords(trim(($admission['firstname'] ?? '') . ' ' . ($admission['lastname'] ?? '')))) ?>
                </div>
            </div>
            <div>
                <label style="font-size: 12px; color: #64748b;">Room</label>
                <div style="font-weight: 600; color: #1e293b;">
                    <?= esc($admission['room_number'] ?? 'N/A') ?> - <?= esc($admission['ward'] ?? 'N/A') ?>
                </div>
            </div>
            <div>
                <label style="font-size: 12px; color: #64748b;">Admission Date</label>
                <div style="font-weight: 600; color: #1e293b;">
                    <?= date('M d, Y', strtotime($admission['admission_date'])) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modern-card">
    <div class="card-header-modern">
        <h5><i class="fas fa-file-medical"></i> Discharge Order Details</h5>
    </div>
    <div class="card-body-modern">
        <form action="<?= site_url('doctor/discharge/store') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="admission_id" value="<?= esc($admission['id']) ?>">
            <input type="hidden" name="patient_id" value="<?= esc($admission['patient_id']) ?>">
            
            <div style="margin-bottom: 24px;">
                <label class="form-label-modern" for="final_diagnosis">
                    Final Diagnosis <span style="color: #dc2626;">*</span>
                </label>
                <textarea name="final_diagnosis" id="final_diagnosis" class="form-control-modern" required><?= old('final_diagnosis', $admission['diagnosis'] ?? '') ?></textarea>
            </div>
            
            <div style="margin-bottom: 24px;">
                <label class="form-label-modern" for="treatment_summary">
                    Treatment Summary
                </label>
                <textarea name="treatment_summary" id="treatment_summary" class="form-control-modern" placeholder="Summary of treatments provided during admission..."><?= old('treatment_summary') ?></textarea>
            </div>
            
            <div style="margin-bottom: 24px;">
                <label class="form-label-modern" for="recommendations">
                    Recommendations
                </label>
                <textarea name="recommendations" id="recommendations" class="form-control-modern" placeholder="Recommendations for continued care..."><?= old('recommendations') ?></textarea>
            </div>
            
            <div style="margin-bottom: 24px;">
                <label class="form-label-modern" for="follow_up_instructions">
                    Follow-up Instructions
                </label>
                <textarea name="follow_up_instructions" id="follow_up_instructions" class="form-control-modern" placeholder="Instructions for follow-up appointments, medications, etc..."><?= old('follow_up_instructions') ?></textarea>
            </div>
            
            <div style="margin-bottom: 24px;">
                <label class="form-label-modern" for="medications_prescribed">
                    Medications Prescribed
                </label>
                <textarea name="medications_prescribed" id="medications_prescribed" class="form-control-modern" placeholder="List of medications prescribed upon discharge..."><?= old('medications_prescribed') ?></textarea>
            </div>
            
            <div style="margin-bottom: 24px;">
                <label class="form-label-modern" for="discharge_date">
                    Planned Discharge Date <span style="color: #dc2626;">*</span>
                </label>
                <input type="date" name="discharge_date" id="discharge_date" class="form-control-modern" value="<?= old('discharge_date', date('Y-m-d')) ?>" required>
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px;">
                <a href="<?= site_url('doctor/discharge') ?>" class="btn-modern btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn-modern btn-primary">
                    <i class="fas fa-check"></i> Create Discharge Order
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

