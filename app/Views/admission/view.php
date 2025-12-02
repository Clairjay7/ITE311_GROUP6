<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Admission Details<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .page-header {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(220, 38, 38, 0.2);
        color: white;
    }
    .page-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
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
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        padding: 20px 24px;
        border-bottom: 2px solid #dc2626;
    }
    .card-header-modern h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #dc2626;
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
        border-left: 4px solid #dc2626;
    }
    .info-item label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .info-item .value {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
    }
    .status-badge {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-admitted { background: #d1fae5; color: #065f46; }
    .status-discharged { background: #dbeafe; color: #1e40af; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
</style>

<div class="page-header">
    <h1><i class="fas fa-hospital"></i> Admission Details</h1>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div class="modern-card">
    <div class="card-header-modern">
        <h5><i class="fas fa-user-injured"></i> Patient Information</h5>
    </div>
    <div class="card-body-modern">
        <div class="info-grid">
            <div class="info-item">
                <label>Patient Name</label>
                <div class="value"><?= esc(ucwords(trim(($admission['firstname'] ?? '') . ' ' . ($admission['lastname'] ?? '')))) ?></div>
            </div>
            <div class="info-item">
                <label>Contact</label>
                <div class="value"><?= esc($admission['contact'] ?? 'N/A') ?></div>
            </div>
            <div class="info-item">
                <label>Address</label>
                <div class="value"><?= esc($admission['address'] ?? 'N/A') ?></div>
            </div>
        </div>
    </div>
</div>

<div class="modern-card">
    <div class="card-header-modern">
        <h5><i class="fas fa-bed"></i> Admission Information</h5>
    </div>
    <div class="card-body-modern">
        <div class="info-grid">
            <div class="info-item">
                <label>Room</label>
                <div class="value"><?= esc($admission['room_number'] ?? 'N/A') ?> - <?= esc($admission['ward'] ?? 'N/A') ?></div>
            </div>
            <div class="info-item">
                <label>Room Type</label>
                <div class="value"><?= esc($admission['room_type_name'] ?? $admission['room_type'] ?? 'Ward') ?></div>
            </div>
            <?php if ($admission['bed_number']): ?>
            <div class="info-item">
                <label>Bed Number</label>
                <div class="value"><?= esc($admission['bed_number']) ?></div>
            </div>
            <?php endif; ?>
            <div class="info-item">
                <label>Status</label>
                <div class="value">
                    <span class="status-badge status-<?= $admission['status'] ?>">
                        <?= ucfirst($admission['status']) ?>
                    </span>
                </div>
            </div>
            <div class="info-item">
                <label>Admission Date</label>
                <div class="value"><?= date('M d, Y h:i A', strtotime($admission['admission_date'])) ?></div>
            </div>
            <div class="info-item">
                <label>Attending Physician</label>
                <div class="value"><?= esc($admission['attending_physician_name'] ?? 'N/A') ?></div>
            </div>
        </div>
        
        <div style="margin-top: 24px;">
            <label class="form-label-modern">Admission Reason</label>
            <div style="background: #f8fafc; padding: 16px; border-radius: 10px; border-left: 4px solid #dc2626;">
                <?= nl2br(esc($admission['admission_reason'] ?? 'N/A')) ?>
            </div>
        </div>
        
        <?php if ($admission['initial_notes']): ?>
        <div style="margin-top: 24px;">
            <label class="form-label-modern">Initial Notes</label>
            <div style="background: #f8fafc; padding: 16px; border-radius: 10px; border-left: 4px solid #dc2626;">
                <?= nl2br(esc($admission['initial_notes'])) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div style="margin-top: 24px;">
    <a href="<?= site_url('doctor/consultations/my-schedule') ?>" class="btn-modern" style="background: #f1f5f9; color: #475569;">
        <i class="fas fa-arrow-left"></i> Back to Consultations
    </a>
</div>

<?= $this->endSection() ?>

