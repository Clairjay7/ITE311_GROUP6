<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Pediatrics Consultations<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .page-header {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(245, 158, 11, 0.2);
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
    
    .patients-table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .patients-table table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .patients-table th {
        background: #fef3c7;
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: #92400e;
        border-bottom: 2px solid #fde68a;
    }
    
    .patients-table td {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .patients-table tr:hover {
        background: #fffbeb;
    }
    
    .btn-consult {
        background: #f59e0b;
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s;
    }
    
    .btn-consult:hover {
        background: #d97706;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    
    .age-badge {
        background: #dbeafe;
        color: #1e40af;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1>
                <i class="fas fa-child"></i> Pediatrics Consultations
            </h1>
            <p style="margin: 8px 0 0 0; opacity: 0.95;">All pediatric patients (0-17 years old) assigned to you</p>
        </div>
        <a href="<?= site_url('doctor/dashboard') ?>" style="background: rgba(255, 255, 255, 0.2); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255, 255, 255, 0.3);">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div style="background: #d1fae5; color: #047857; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fee2e2; color: #b91c1c; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="patients-table">
    <table>
        <thead>
            <tr>
                <th>Patient ID</th>
                <th>Patient Name</th>
                <th>Age</th>
                <th>Date of Birth</th>
                <th>Sex</th>
                <th>Contact</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($patients)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                        <i class="fas fa-child" style="font-size: 48px; opacity: 0.3; margin-bottom: 16px; display: block;"></i>
                        <p style="margin: 0;">No pediatric patients found (0-17 years old)</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($patients as $patient): ?>
                    <?php
                    // Calculate age
                    $age = $patient['calculated_age'] ?? null;
                    if ($age === null && !empty($patient['date_of_birth'])) {
                        $birthDate = new \DateTime($patient['date_of_birth']);
                        $today = new \DateTime();
                        $age = $today->diff($birthDate)->y;
                    } elseif ($age === null && !empty($patient['age'])) {
                        $age = (int)$patient['age'];
                    }
                    ?>
                    <tr>
                        <td><strong>#<?= esc($patient['patient_id'] ?? $patient['id']) ?></strong></td>
                        <td><?= esc($patient['full_name'] ?? ($patient['first_name'] ?? '') . ' ' . ($patient['last_name'] ?? '')) ?></td>
                        <td>
                            <span class="age-badge"><?= $age !== null ? $age . ' years' : 'N/A' ?></span>
                        </td>
                        <td><?= esc($patient['date_of_birth'] ?? 'N/A') ?></td>
                        <td><?= esc(ucfirst($patient['gender'] ?? 'N/A')) ?></td>
                        <td><?= esc($patient['contact'] ?? 'N/A') ?></td>
                        <td>
                            <a href="<?= site_url('doctor/consultations/pediatrics/consult/' . ($patient['patient_id'] ?? $patient['id'])) ?>" class="btn-consult">
                                <i class="fas fa-stethoscope"></i> Consult
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>

