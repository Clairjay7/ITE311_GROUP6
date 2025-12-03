<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Prescription<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    @media print {
        body { margin: 0; padding: 20px; background: white; }
        .no-print { display: none !important; }
        .print-header { page-break-after: avoid; }
        .prescription-section { page-break-inside: avoid; }
    }
    
    .print-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 40px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.1);
    }
    
    .print-header {
        text-align: center;
        border-bottom: 3px solid #2e7d32;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    
    .print-header h1 {
        color: #2e7d32;
        margin: 0 0 10px;
        font-size: 32px;
        font-weight: 700;
    }
    
    .hospital-info {
        color: #64748b;
        font-size: 14px;
        margin-top: 8px;
    }
    
    .prescription-section {
        margin-bottom: 30px;
        page-break-inside: avoid;
    }
    
    .section-title {
        font-weight: 700;
        color: #2e7d32;
        font-size: 16px;
        margin-bottom: 12px;
        border-bottom: 2px solid #c8e6c9;
        padding-bottom: 8px;
    }
    
    .section-content {
        line-height: 1.8;
        color: #1e293b;
    }
    
    .patient-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    
    .info-item {
        padding: 12px;
        background: #f8fafc;
        border-radius: 8px;
    }
    
    .info-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 4px;
    }
    
    .info-value {
        font-size: 14px;
        color: #1e293b;
        font-weight: 600;
    }
    
    .medication-box {
        background: #f0fdf4;
        border: 2px solid #2e7d32;
        border-radius: 12px;
        padding: 24px;
        margin: 20px 0;
    }
    
    .medication-title {
        font-size: 18px;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 16px;
        text-align: center;
    }
    
    .medication-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }
    
    .medication-item {
        padding: 12px;
        background: white;
        border-radius: 8px;
        border-left: 4px solid #2e7d32;
    }
    
    .medication-label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 4px;
    }
    
    .medication-value {
        font-size: 16px;
        color: #1e293b;
        font-weight: 700;
    }
    
    .signature-section {
        margin-top: 60px;
        padding-top: 20px;
        border-top: 2px solid #e5e7eb;
    }
    
    .signature-box {
        text-align: right;
        margin-top: 40px;
    }
    
    .signature-line {
        border-top: 2px solid #1e293b;
        width: 250px;
        margin-left: auto;
        padding-top: 8px;
        margin-bottom: 4px;
    }
    
    .doctor-name {
        font-weight: 700;
        font-size: 16px;
        color: #1e293b;
    }
    
    .doctor-title {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
    }
    
    .footer-info {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
        font-size: 11px;
        color: #94a3b8;
        text-align: center;
    }
    
    .prescription-number {
        background: #f0fdf4;
        padding: 8px 16px;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 16px;
        font-weight: 600;
        color: #2e7d32;
    }
</style>

<div class="no-print" style="text-align: center; margin-bottom: 20px; padding: 20px; background: #f8fafc; border-radius: 12px;">
    <button onclick="window.print()" style="background: #2e7d32; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 16px;">
        <i class="fas fa-print"></i> Print Prescription
    </button>
    <a href="<?= site_url('doctor/orders/view/' . $order['id']) ?>" style="background: #64748b; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-left: 12px; display: inline-block;">
        <i class="fas fa-arrow-left"></i> Back to Order
    </a>
</div>

<div class="print-container">
    <div class="print-header">
        <h1><i class="fas fa-prescription"></i> PRESCRIPTION</h1>
        <div class="hospital-info">
            <strong>Hospital Management System</strong><br>
            Medical Prescription
        </div>
    </div>
    
    <div style="text-align: center; margin-bottom: 24px;">
        <div class="prescription-number">
            Prescription #<?= esc($order['id']) ?> | Date: <?= date('F d, Y', strtotime($order['created_at'])) ?>
        </div>
    </div>
    
    <div class="prescription-section">
        <div class="section-title">
            <i class="fas fa-user-injured"></i> Patient Information
        </div>
        <div class="patient-info">
            <div class="info-item">
                <div class="info-label">Patient Name</div>
                <div class="info-value"><?= esc(ucwords(trim(($order['firstname'] ?? '') . ' ' . ($order['lastname'] ?? '')))) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Age</div>
                <div class="info-value">
                    <?php
                    if (!empty($order['birthdate'])) {
                        $birth = new \DateTime($order['birthdate']);
                        $today = new \DateTime();
                        $age = $today->diff($birth)->y;
                        echo $age . ' years old';
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Gender</div>
                <div class="info-value"><?= esc(ucfirst($order['gender'] ?? 'N/A')) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Contact</div>
                <div class="info-value"><?= esc($order['contact'] ?? 'N/A') ?></div>
            </div>
        </div>
    </div>
    
    <?php if (!empty($consultation)): ?>
    <div class="prescription-section">
        <div class="section-title">
            <i class="fas fa-stethoscope"></i> Consultation Details
        </div>
        <div class="section-content">
            <?php if (!empty($consultation['diagnosis'])): ?>
                <div style="margin-bottom: 12px;">
                    <strong>Diagnosis:</strong> <?= esc($consultation['diagnosis']) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($consultation['observations'])): ?>
                <div>
                    <strong>Observations:</strong> <?= esc($consultation['observations']) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="prescription-section">
        <div class="section-title">
            <i class="fas fa-pills"></i> Medication Prescribed
        </div>
        <div class="medication-box">
            <div class="medication-title">
                <?= esc($order['medicine_name']) ?>
            </div>
            <div class="medication-details">
                <?php if (!empty($order['dosage'])): ?>
                <div class="medication-item">
                    <div class="medication-label">Dosage</div>
                    <div class="medication-value"><?= esc($order['dosage']) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($order['frequency'])): ?>
                <div class="medication-item">
                    <div class="medication-label">Frequency</div>
                    <div class="medication-value"><?= esc($order['frequency']) ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($order['duration'])): ?>
                <div class="medication-item">
                    <div class="medication-label">Duration</div>
                    <div class="medication-value"><?= esc($order['duration']) ?></div>
                </div>
                <?php endif; ?>
                
                <div class="medication-item">
                    <div class="medication-label">Purchase Location</div>
                    <div class="medication-value">Outside Hospital Pharmacy</div>
                </div>
            </div>
            
            <?php if (!empty($order['order_description'])): ?>
            <div style="margin-top: 16px; padding: 12px; background: white; border-radius: 8px; border-left: 4px solid #2e7d32;">
                <div class="medication-label">Instructions</div>
                <div style="color: #1e293b; line-height: 1.6; margin-top: 8px;">
                    <?= nl2br(esc($order['order_description'])) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($order['instructions'])): ?>
    <div class="prescription-section">
        <div class="section-title">
            <i class="fas fa-clipboard-list"></i> Additional Instructions
        </div>
        <div class="section-content">
            <?= nl2br(esc($order['instructions'])) ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">
                <div class="doctor-name"><?= esc($order['doctor_name'] ?? 'Dr. ' . (session()->get('username') ?? 'Doctor')) ?></div>
                <div class="doctor-title">Attending Physician</div>
            </div>
        </div>
    </div>
    
    <div class="footer-info">
        <div style="margin-bottom: 8px;">
            <strong>Important:</strong> This prescription is valid for purchase at any licensed pharmacy outside the hospital.
        </div>
        <div>
            Prescription #<?= esc($order['id']) ?> | Printed on: <?= date('F d, Y \a\t h:i A') ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


