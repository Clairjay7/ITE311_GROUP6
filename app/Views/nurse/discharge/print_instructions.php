<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Discharge Instructions<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    @media print {
        body { margin: 0; padding: 20px; }
        .no-print { display: none; }
        .print-header { page-break-after: avoid; }
    }
    .print-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 40px;
    }
    .print-header {
        text-align: center;
        border-bottom: 3px solid #0288d1;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    .print-header h1 {
        color: #0288d1;
        margin: 0 0 10px;
    }
    .section {
        margin-bottom: 30px;
        page-break-inside: avoid;
    }
    .section-title {
        font-weight: 700;
        color: #0288d1;
        font-size: 16px;
        margin-bottom: 12px;
        border-bottom: 2px solid #e3f2fd;
        padding-bottom: 8px;
    }
    .section-content {
        line-height: 1.8;
        color: #1e293b;
    }
</style>

<div class="no-print" style="text-align: center; margin-bottom: 20px;">
    <button onclick="window.print()" style="background: #0288d1; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
        <i class="fas fa-print"></i> Print
    </button>
    <a href="<?= site_url('nurse/discharge/view/' . $admission['id']) ?>" style="background: #64748b; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-left: 12px;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="print-container">
    <div class="print-header">
        <h1>DISCHARGE INSTRUCTIONS</h1>
        <p style="color: #64748b; margin: 0;">Hospital Management System</p>
    </div>
    
    <div class="section">
        <div class="section-title">Patient Information</div>
        <div class="section-content">
            <strong>Name:</strong> <?= esc(ucwords(trim(($admission['firstname'] ?? '') . ' ' . ($admission['lastname'] ?? '')))) ?><br>
            <strong>Room:</strong> <?= esc($admission['room_number'] ?? 'N/A') ?> - <?= esc($admission['ward'] ?? 'N/A') ?><br>
            <strong>Attending Physician:</strong> <?= esc($admission['doctor_name'] ?? 'N/A') ?><br>
            <strong>Discharge Date:</strong> <?= date('F d, Y', strtotime($admission['discharge_date'] ?? 'now')) ?>
        </div>
    </div>
    
    <div class="section">
        <div class="section-title">Final Diagnosis</div>
        <div class="section-content">
            <?= nl2br(esc($admission['final_diagnosis'] ?? 'N/A')) ?>
        </div>
    </div>
    
    <?php if (!empty($admission['treatment_summary'])): ?>
    <div class="section">
        <div class="section-title">Treatment Summary</div>
        <div class="section-content">
            <?= nl2br(esc($admission['treatment_summary'])) ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($admission['recommendations'])): ?>
    <div class="section">
        <div class="section-title">Recommendations</div>
        <div class="section-content">
            <?= nl2br(esc($admission['recommendations'])) ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($admission['follow_up_instructions'])): ?>
    <div class="section">
        <div class="section-title">Follow-up Instructions</div>
        <div class="section-content">
            <?= nl2br(esc($admission['follow_up_instructions'])) ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($admission['medications_prescribed'])): ?>
    <div class="section">
        <div class="section-title">Medications Prescribed</div>
        <div class="section-content">
            <?= nl2br(esc($admission['medications_prescribed'])) ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb;">
        <div style="text-align: right;">
            <div style="margin-bottom: 40px;">
                <div style="border-top: 2px solid #1e293b; width: 200px; margin-left: auto; padding-top: 8px;">
                    <strong><?= esc($admission['doctor_name'] ?? 'Doctor') ?></strong><br>
                    Attending Physician
                </div>
            </div>
            <div style="font-size: 12px; color: #64748b;">
                Printed on: <?= date('F d, Y \a\t h:i A') ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

