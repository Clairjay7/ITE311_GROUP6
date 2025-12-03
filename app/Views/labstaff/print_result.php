<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    @media print {
        body { margin: 0; padding: 20px; }
        .no-print { display: none !important; }
        .print-container { box-shadow: none; border: none; }
    }
    .print-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 40px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .header {
        text-align: center;
        border-bottom: 3px solid #2e7d32;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    .header h1 {
        color: #2e7d32;
        margin: 0 0 10px;
        font-size: 32px;
    }
    .header p {
        color: #64748b;
        margin: 5px 0;
    }
    .section {
        margin-bottom: 30px;
    }
    .section-title {
        color: #2e7d32;
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 15px;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 8px;
    }
    .info-grid {
        display: grid;
        grid-template-columns: 150px 1fr;
        gap: 12px;
        margin-bottom: 12px;
    }
    .info-label {
        font-weight: 600;
        color: #374151;
    }
    .info-value {
        color: #1f2937;
    }
    .result-box {
        background: #f9fafb;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        margin: 15px 0;
    }
    .result-content {
        color: #1f2937;
        line-height: 1.8;
        white-space: pre-wrap;
    }
    .interpretation-box {
        background: #eff6ff;
        border-left: 4px solid #0288d1;
        padding: 15px;
        margin: 15px 0;
        border-radius: 4px;
    }
    .footer {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 2px solid #e5e7eb;
        text-align: center;
        color: #64748b;
        font-size: 12px;
    }
    .signature-section {
        margin-top: 40px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }
    .signature-box {
        text-align: center;
    }
    .signature-line {
        border-top: 2px solid #1f2937;
        margin-top: 60px;
        padding-top: 10px;
    }
</style>

<div class="print-container">
    <div class="header">
        <h1>LABORATORY RESULT</h1>
        <p>Hospital Laboratory Department</p>
        <p style="font-size: 12px;">Result Report</p>
    </div>

    <!-- Patient Information -->
    <div class="section">
        <div class="section-title">Patient Information</div>
        <div class="info-grid">
            <div class="info-label">Patient Name:</div>
            <div class="info-value">
                <strong><?= esc(($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '')) ?></strong>
            </div>
            <?php if (!empty($patient['contact'])): ?>
                <div class="info-label">Contact:</div>
                <div class="info-value"><?= esc($patient['contact']) ?></div>
            <?php endif; ?>
            <?php if (!empty($patient['birthdate'])): ?>
                <div class="info-label">Date of Birth:</div>
                <div class="info-value"><?= esc(date('M d, Y', strtotime($patient['birthdate']))) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Test Information -->
    <div class="section">
        <div class="section-title">Test Information</div>
        <div class="info-grid">
            <div class="info-label">Test Name:</div>
            <div class="info-value"><strong><?= esc($labRequest['test_name']) ?></strong></div>
            <div class="info-label">Test Type:</div>
            <div class="info-value"><?= esc($labRequest['test_type']) ?></div>
            <div class="info-label">Requested Date:</div>
            <div class="info-value"><?= esc(date('M d, Y', strtotime($labRequest['requested_date']))) ?></div>
            <div class="info-label">Completed Date:</div>
            <div class="info-value">
                <?= !empty($labResult['completed_at']) ? esc(date('M d, Y H:i', strtotime($labResult['completed_at']))) : 'N/A' ?>
            </div>
            <?php if (!empty($testInfo['normal_range'])): ?>
                <div class="info-label">Normal Range:</div>
                <div class="info-value"><?= esc($testInfo['normal_range']) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Test Result -->
    <div class="section">
        <div class="section-title">Test Result</div>
        <div class="result-box">
            <div class="result-content"><?= esc($labResult['result']) ?></div>
        </div>
    </div>

    <!-- Interpretation -->
    <?php if (!empty($labResult['interpretation'])): ?>
        <div class="section">
            <div class="section-title">Interpretation</div>
            <div class="interpretation-box">
                <div class="result-content"><?= esc($labResult['interpretation']) ?></div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                <strong><?= esc($completedBy['username'] ?? 'Laboratory Staff') ?></strong><br>
                Laboratory Staff
            </div>
        </div>
        <div class="signature-box">
            <div class="signature-line">
                <strong>Date:</strong> <?= esc(date('M d, Y', strtotime($labResult['completed_at'] ?? 'now'))) ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is a computer-generated report. No signature required.</p>
        <p>Generated on: <?= date('M d, Y H:i:s') ?></p>
    </div>
</div>

<div class="no-print" style="text-align: center; margin: 20px;">
    <button onclick="window.print()" style="background: #2e7d32; color: white; padding: 12px 24px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600;">
        <i class="fas fa-print"></i> Print Result
    </button>
    <a href="<?= site_url('labstaff/completed-tests') ?>" style="background: #64748b; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block; margin-left: 12px; font-size: 16px; font-weight: 600;">
        <i class="fas fa-arrow-left"></i> Back to Completed Tests
    </a>
</div>

<?= $this->endSection() ?>

