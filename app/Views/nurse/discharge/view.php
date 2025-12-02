<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Prepare Patient for Discharge<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .page-header {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(2, 136, 209, 0.2);
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
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        padding: 20px 24px;
        border-bottom: 2px solid #0288d1;
    }
    .card-body-modern {
        padding: 32px;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
    .info-item {
        background: #f8fafc;
        padding: 16px;
        border-radius: 10px;
        border-left: 4px solid #0288d1;
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
        background: #0288d1;
        color: white;
    }
    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    .alert-warning {
        background: #fef3c7;
        color: #92400e;
        padding: 16px;
        border-radius: 8px;
        border-left: 4px solid #f59e0b;
        margin-bottom: 16px;
    }
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        padding: 16px;
        border-radius: 8px;
        border-left: 4px solid #10b981;
        margin-bottom: 16px;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-user-injured"></i> Prepare Patient for Discharge</h1>
</div>

<?php if (!$allChargesPaid): ?>
    <div class="alert-warning">
        <strong><i class="fas fa-exclamation-triangle"></i> Billing Not Finalized:</strong> 
        Patient has pending charges. Billing must be finalized before discharge can be completed.
    </div>
<?php else: ?>
    <div class="alert-success">
        <strong><i class="fas fa-check-circle"></i> Billing Finalized:</strong> 
        All charges have been paid. Patient is ready for discharge.
    </div>
<?php endif; ?>

<div class="modern-card">
    <div class="card-header-modern">
        <h5><i class="fas fa-user-injured"></i> Patient Information</h5>
    </div>
    <div class="card-body-modern">
        <div class="info-grid">
            <div class="info-item">
                <label style="font-size: 12px; color: #64748b;">Patient Name</label>
                <div style="font-weight: 600; color: #1e293b;">
                    <?= esc(ucwords(trim(($admission['firstname'] ?? '') . ' ' . ($admission['lastname'] ?? '')))) ?>
                </div>
            </div>
            <div class="info-item">
                <label style="font-size: 12px; color: #64748b;">Room</label>
                <div style="font-weight: 600; color: #1e293b;">
                    <?= esc($admission['room_number'] ?? 'N/A') ?> - <?= esc($admission['ward'] ?? 'N/A') ?>
                </div>
            </div>
            <div class="info-item">
                <label style="font-size: 12px; color: #64748b;">Attending Physician</label>
                <div style="font-weight: 600; color: #1e293b;">
                    <?= esc($admission['doctor_name'] ?? 'N/A') ?>
                </div>
            </div>
            <div class="info-item">
                <label style="font-size: 12px; color: #64748b;">Planned Discharge Date</label>
                <div style="font-weight: 600; color: #1e293b;">
                    <?= date('M d, Y', strtotime($admission['discharge_date'] ?? 'now')) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modern-card">
    <div class="card-header-modern">
        <h5><i class="fas fa-file-medical"></i> Discharge Instructions</h5>
    </div>
    <div class="card-body-modern">
        <div style="margin-bottom: 24px;">
            <label style="font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Final Diagnosis</label>
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border-left: 4px solid #0288d1;">
                <?= nl2br(esc($admission['final_diagnosis'] ?? 'N/A')) ?>
            </div>
        </div>
        
        <?php if (!empty($admission['treatment_summary'])): ?>
        <div style="margin-bottom: 24px;">
            <label style="font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Treatment Summary</label>
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border-left: 4px solid #0288d1;">
                <?= nl2br(esc($admission['treatment_summary'])) ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($admission['recommendations'])): ?>
        <div style="margin-bottom: 24px;">
            <label style="font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Recommendations</label>
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border-left: 4px solid #0288d1;">
                <?= nl2br(esc($admission['recommendations'])) ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($admission['follow_up_instructions'])): ?>
        <div style="margin-bottom: 24px;">
            <label style="font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Follow-up Instructions</label>
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border-left: 4px solid #0288d1;">
                <?= nl2br(esc($admission['follow_up_instructions'])) ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($admission['medications_prescribed'])): ?>
        <div style="margin-bottom: 24px;">
            <label style="font-weight: 600; color: #1e293b; margin-bottom: 8px; display: block;">Medications Prescribed</label>
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border-left: 4px solid #0288d1;">
                <?= nl2br(esc($admission['medications_prescribed'])) ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px;">
            <a href="<?= site_url('nurse/discharge') ?>" class="btn-modern btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="<?= site_url('nurse/discharge/print/' . $admission['id']) ?>" target="_blank" class="btn-modern btn-primary">
                <i class="fas fa-print"></i> Print Discharge Instructions
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

