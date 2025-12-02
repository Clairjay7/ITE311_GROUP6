<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Discharge Order<?= $this->endSection() ?>

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
    .section {
        margin-bottom: 24px;
    }
    .section-title {
        font-weight: 700;
        color: #2e7d32;
        font-size: 16px;
        margin-bottom: 12px;
    }
    .section-content {
        background: #f8fafc;
        padding: 16px;
        border-radius: 8px;
        border-left: 4px solid #4caf50;
        line-height: 1.8;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-file-medical"></i> Discharge Order</h1>
</div>

<div class="modern-card">
    <div class="card-header-modern">
        <h5><i class="fas fa-user-injured"></i> Patient Information</h5>
    </div>
    <div class="card-body-modern">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div>
                <label style="font-size: 12px; color: #64748b;">Patient Name</label>
                <div style="font-weight: 600; color: #1e293b;">
                    <?= esc(ucwords(trim(($order['firstname'] ?? '') . ' ' . ($order['lastname'] ?? '')))) ?>
                </div>
            </div>
            <div>
                <label style="font-size: 12px; color: #64748b;">Room</label>
                <div style="font-weight: 600; color: #1e293b;">
                    <?= esc($order['room_number'] ?? 'N/A') ?> - <?= esc($order['ward'] ?? 'N/A') ?>
                </div>
            </div>
            <div>
                <label style="font-size: 12px; color: #64748b;">Planned Discharge</label>
                <div style="font-weight: 600; color: #1e293b;">
                    <?= date('M d, Y', strtotime($order['discharge_date'] ?? 'now')) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modern-card">
    <div class="card-header-modern">
        <h5><i class="fas fa-file-medical"></i> Discharge Details</h5>
    </div>
    <div class="card-body-modern">
        <div class="section">
            <div class="section-title">Final Diagnosis</div>
            <div class="section-content">
                <?= nl2br(esc($order['final_diagnosis'] ?? 'N/A')) ?>
            </div>
        </div>
        
        <?php if (!empty($order['treatment_summary'])): ?>
        <div class="section">
            <div class="section-title">Treatment Summary</div>
            <div class="section-content">
                <?= nl2br(esc($order['treatment_summary'])) ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($order['recommendations'])): ?>
        <div class="section">
            <div class="section-title">Recommendations</div>
            <div class="section-content">
                <?= nl2br(esc($order['recommendations'])) ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($order['follow_up_instructions'])): ?>
        <div class="section">
            <div class="section-title">Follow-up Instructions</div>
            <div class="section-content">
                <?= nl2br(esc($order['follow_up_instructions'])) ?>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($order['medications_prescribed'])): ?>
        <div class="section">
            <div class="section-title">Medications Prescribed</div>
            <div class="section-content">
                <?= nl2br(esc($order['medications_prescribed'])) ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div style="margin-top: 32px; padding-top: 24px; border-top: 2px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong>Status:</strong> 
                    <span style="background: <?= $order['status'] === 'completed' ? '#10b981' : ($order['status'] === 'approved' ? '#f59e0b' : '#dc2626') ?>; color: white; padding: 4px 12px; border-radius: 4px; font-size: 12px;">
                        <?= esc(ucfirst($order['status'] ?? 'pending')) ?>
                    </span>
                </div>
                <a href="<?= site_url('doctor/discharge') ?>" class="btn-modern btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

