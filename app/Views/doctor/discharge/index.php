<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Discharge Patients<?= $this->endSection() ?>

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
    }
    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }
    .table-modern th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #2e7d32;
        background: #e8f5e9;
        border-bottom: 2px solid #4caf50;
    }
    .table-modern td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .btn-modern {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
    }
    .btn-primary {
        background: #2e7d32;
        color: white;
    }
    .btn-primary:hover {
        background: #1b5e20;
        color: white;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-sign-out-alt"></i> Discharge Patients</h1>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="modern-card">
    <?php if (!empty($admissions)): ?>
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Room</th>
                    <th>Admission Date</th>
                    <th>Diagnosis</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admissions as $admission): ?>
                    <tr>
                        <td>
                            <strong><?= esc(ucwords(trim(($admission['firstname'] ?? '') . ' ' . ($admission['lastname'] ?? '')))) ?></strong><br>
                            <small style="color: #64748b;"><?= esc(ucfirst($admission['gender'] ?? 'N/A')) ?></small>
                        </td>
                        <td>
                            <?= esc($admission['room_number'] ?? 'N/A') ?><br>
                            <small style="color: #64748b;"><?= esc($admission['ward'] ?? 'N/A') ?></small>
                        </td>
                        <td><?= date('M d, Y', strtotime($admission['admission_date'])) ?></td>
                        <td><?= esc(substr($admission['diagnosis'] ?? 'N/A', 0, 50)) ?><?= strlen($admission['diagnosis'] ?? '') > 50 ? '...' : '' ?></td>
                        <td>
                            <a href="<?= site_url('doctor/discharge/create/' . $admission['id']) ?>" class="btn-modern btn-primary">
                                <i class="fas fa-file-medical"></i> Create Discharge Order
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; color: #64748b;">
            <i class="fas fa-check-circle" style="font-size: 64px; margin-bottom: 16px; color: #cbd5e1;"></i>
            <h5>No Active Admissions</h5>
            <p>You have no patients currently admitted under your care.</p>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

