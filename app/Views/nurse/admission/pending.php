<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Pending Admissions<?= $this->endSection() ?>

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
    .card-body-modern {
        padding: 24px;
    }
    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }
    .table-modern th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #dc2626;
        background: #fee2e2;
        border-bottom: 2px solid #dc2626;
    }
    .table-modern td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .btn-modern {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .btn-admit {
        background: #dc2626;
        color: white;
    }
    .btn-admit:hover {
        background: #b91c1c;
        color: white;
        transform: translateY(-2px);
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-hospital"></i> Pending Admissions</h1>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div class="modern-card">
    <div class="card-body-modern">
        <?php if (!empty($pendingAdmissions)): ?>
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Consultation Date</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Diagnosis</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingAdmissions as $consultation): ?>
                        <tr>
                            <td>
                                <strong><?= date('M d, Y', strtotime($consultation['consultation_date'])) ?></strong><br>
                                <small style="color: #64748b;"><?= date('h:i A', strtotime($consultation['consultation_time'])) ?></small>
                            </td>
                            <td>
                                <strong><?= esc(ucwords(trim(($consultation['firstname'] ?? '') . ' ' . ($consultation['lastname'] ?? '')))) ?></strong>
                            </td>
                            <td><?= esc($consultation['doctor_name'] ?? 'N/A') ?></td>
                            <td><?= esc(substr($consultation['diagnosis'] ?? 'No diagnosis', 0, 50)) ?><?= strlen($consultation['diagnosis'] ?? '') > 50 ? '...' : '' ?></td>
                            <td>
                                <a href="<?= site_url('admission/create/' . $consultation['id']) ?>" 
                                   class="btn-modern btn-admit">
                                    <i class="fas fa-hospital"></i> Admit Patient
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; color: #64748b;">
                <i class="fas fa-check-circle" style="font-size: 64px; margin-bottom: 16px; color: #cbd5e1;"></i>
                <h5>No Pending Admissions</h5>
                <p>All patients marked for admission have been processed.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

