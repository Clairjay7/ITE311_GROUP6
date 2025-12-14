<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Consultation Details<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .print-content, .print-content * {
            visibility: visible;
        }
        .print-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
        .print-header {
            border-bottom: 3px solid #000;
            margin-bottom: 20px;
            padding-bottom: 15px;
        }
        .print-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .print-table {
            width: 100%;
            border-collapse: collapse;
        }
        .print-table th,
        .print-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .print-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
    }
    
    .consultation-view {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .print-content {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .print-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 3px solid #0288d1;
    }
    
    .print-header h1 {
        color: #0288d1;
        margin: 0 0 10px 0;
        font-size: 28px;
    }
    
    .print-header p {
        margin: 5px 0;
        color: #64748b;
        font-size: 14px;
    }
    
    .print-section {
        margin-bottom: 25px;
        padding: 15px;
        background: #f8fafc;
        border-radius: 8px;
        border-left: 4px solid #0288d1;
    }
    
    .print-section h3 {
        color: #0288d1;
        margin: 0 0 15px 0;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 15px;
    }
    
    .info-item {
        display: flex;
        flex-direction: column;
    }
    
    .info-label {
        font-weight: 600;
        color: #475569;
        font-size: 12px;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .info-value {
        color: #1e293b;
        font-size: 14px;
    }
    
    .prescription-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }
    
    .prescription-table th,
    .prescription-table td {
        border: 1px solid #e5e7eb;
        padding: 12px;
        text-align: left;
    }
    
    .prescription-table th {
        background-color: #0288d1;
        color: white;
        font-weight: 600;
    }
    
    .prescription-table tr:nth-child(even) {
        background-color: #f8fafc;
    }
    
    .btn-print {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
    }
    
    .btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(2, 136, 209, 0.4);
    }
    
    .btn-back {
        background: #f1f5f9;
        color: #475569;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-back:hover {
        background: #e2e8f0;
    }
    
    .action-buttons {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        justify-content: flex-end;
    }
</style>

<div class="consultation-view">
    <!-- Success Message -->
    <?php if (session()->getFlashdata('success')): ?>
        <div style="background: #10b981; color: white; padding: 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
            <i class="fas fa-check-circle" style="font-size: 20px;"></i>
            <span style="font-weight: 600;"><?= session()->getFlashdata('success') ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background: #ef4444; color: white; padding: 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
            <i class="fas fa-exclamation-circle" style="font-size: 20px;"></i>
            <span style="font-weight: 600;"><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>
    
    <div class="action-buttons no-print">
        <a href="<?= site_url('doctor/patients/view/' . ($patient['id'] ?? $patient['patient_id'] ?? '')) ?>" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Patient
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> Print Consultation
        </button>
    </div>

    <div class="print-content">
        <!-- Header -->
        <div class="print-header">
            <h1><i class="fas fa-file-medical"></i> CONSULTATION REPORT</h1>
            <p style="font-size: 16px; font-weight: 600; color: #1e293b;">Hospital Management System</p>
            <p>Consultation Date: <?= date('F d, Y', strtotime($consultation['consultation_date'])) ?> at <?= date('h:i A', strtotime($consultation['consultation_time'])) ?></p>
        </div>

        <!-- Patient Information -->
        <div class="print-section">
            <h3><i class="fas fa-user"></i> Patient Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Patient Name</span>
                    <span class="info-value">
                        <?= esc(($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? '')) ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Patient ID</span>
                    <span class="info-value"><?= esc($patient['id'] ?? $patient['patient_id'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date of Birth</span>
                    <span class="info-value">
                        <?= !empty($patient['date_of_birth']) ? date('F d, Y', strtotime($patient['date_of_birth'])) : 'N/A' ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Age</span>
                    <span class="info-value">
                        <?php
                        if (!empty($patient['date_of_birth'])) {
                            $birthDate = new DateTime($patient['date_of_birth']);
                            $today = new DateTime();
                            $age = $today->diff($birthDate)->y;
                            echo $age . ' years old';
                        } else {
                            echo 'N/A';
                        }
                        ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Gender</span>
                    <span class="info-value"><?= esc($patient['gender'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Contact Number</span>
                    <span class="info-value"><?= esc($patient['contact'] ?? $patient['phone'] ?? 'N/A') ?></span>
                </div>
            </div>
        </div>

        <!-- Doctor Information -->
        <div class="print-section">
            <h3><i class="fas fa-user-md"></i> Doctor Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Doctor Name</span>
                    <span class="info-value">
                        <?= esc($doctor['doctor_name'] ?? $doctor['username'] ?? 'N/A') ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Consultation Date</span>
                    <span class="info-value">
                        <?= date('F d, Y', strtotime($consultation['consultation_date'])) ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Consultation Time</span>
                    <span class="info-value">
                        <?= date('h:i A', strtotime($consultation['consultation_time'])) ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status</span>
                    <span class="info-value" style="text-transform: uppercase; font-weight: 600; color: #10b981;">
                        <?= esc($consultation['status'] ?? 'COMPLETED') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Chief Complaint -->
        <div class="print-section">
            <h3><i class="fas fa-comment-medical"></i> Chief Complaint</h3>
            <p style="margin: 0; color: #1e293b; font-size: 14px; line-height: 1.6;">
                <?= esc($consultation['chief_complaint'] ?? 'N/A') ?>
            </p>
        </div>

        <!-- Prescriptions -->
        <?php if (!empty($prescriptions)): ?>
        <div class="print-section">
            <h3><i class="fas fa-pills"></i> Prescriptions</h3>
            <table class="prescription-table">
                <thead>
                    <tr>
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                        <th>When to Take</th>
                        <th>Instructions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prescriptions as $prescription): ?>
                        <tr>
                            <td>
                                <strong><?= esc($prescription['medicine']['item_name'] ?? 'N/A') ?></strong>
                                <?php if (!empty($prescription['medicine']['generic_name'])): ?>
                                    <br><small style="color: #64748b;"><?= esc($prescription['medicine']['generic_name']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($prescription['details']['dosage'] ?? 'N/A') ?></td>
                            <td><?= esc($prescription['details']['frequency'] ?? 'N/A') ?></td>
                            <td><?= esc($prescription['details']['duration'] ?? 'N/A') ?></td>
                            <td>
                                <?php
                                $whenToTake = $prescription['details']['when_to_take'] ?? '';
                                $whenToTakeLabels = [
                                    'before_meal' => 'Before Meal',
                                    'after_meal' => 'After Meal',
                                    'with_meal' => 'With Meal',
                                    'empty_stomach' => 'Empty Stomach',
                                    'as_needed' => 'As Needed (PRN)'
                                ];
                                echo esc($whenToTakeLabels[$whenToTake] ?? $whenToTake ?: 'N/A');
                                ?>
                            </td>
                            <td><?= esc($prescription['details']['instructions'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Lab Tests -->
        <?php if (!empty($labTests)): ?>
        <div class="print-section">
            <h3><i class="fas fa-vial"></i> Laboratory Tests</h3>
            <table class="prescription-table">
                <thead>
                    <tr>
                        <th>Test Name</th>
                        <th>Test Type</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($labTests as $test): ?>
                        <tr>
                            <td><strong><?= esc($test['test_name'] ?? 'N/A') ?></strong></td>
                            <td><?= esc($test['test_type'] ?? 'N/A') ?></td>
                            <td>₱<?= number_format($test['price'] ?? 0, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Follow-up -->
        <?php if (!empty($consultation['follow_up']) && $consultation['follow_up'] == 1): ?>
        <div class="print-section">
            <h3><i class="fas fa-calendar-check"></i> Follow-up Consultation</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Follow-up Date</span>
                    <span class="info-value">
                        <?= !empty($consultation['follow_up_date']) ? date('F d, Y', strtotime($consultation['follow_up_date'])) : 'N/A' ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Follow-up Time</span>
                    <span class="info-value">
                        <?= !empty($consultation['follow_up_time']) ? date('h:i A', strtotime($consultation['follow_up_time'])) : 'N/A' ?>
                    </span>
                </div>
                <?php if (!empty($consultation['follow_up_reason'])): ?>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <span class="info-label">Reason for Follow-up</span>
                    <span class="info-value"><?= esc($consultation['follow_up_reason']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb; text-align: center;">
            <p style="color: #64748b; font-size: 12px; margin: 5px 0;">
                Generated on <?= date('F d, Y \a\t h:i A') ?>
            </p>
            <p style="color: #64748b; font-size: 12px; margin: 5px 0;">
                This is a computer-generated document. No signature required.
            </p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

