<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Discharge Billing<?= $this->endSection() ?>

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
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        margin-bottom: 24px;
    }
    .card-header-modern {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        padding: 20px 24px;
        border-bottom: 2px solid #f59e0b;
    }
    .card-body-modern {
        padding: 32px;
    }
    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }
    .table-modern th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #92400e;
        background: #fef3c7;
        border-bottom: 2px solid #f59e0b;
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
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-primary {
        background: #f59e0b;
        color: white;
    }
    .btn-primary:hover {
        background: #d97706;
    }
    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-sign-out-alt"></i> Discharge Billing</h1>
    <p style="margin: 8px 0 0; opacity: 0.9;">Finalize billing for patients pending discharge</p>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div class="modern-card">
    <div class="card-header-modern">
        <h5><i class="fas fa-list"></i> Patients Pending Discharge</h5>
    </div>
    <div class="card-body-modern">
        <?php if (!empty($dischargePending)): ?>
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Room</th>
                        <th>Doctor</th>
                        <th>Planned Discharge</th>
                        <th>Pending Charges</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dischargePending as $discharge): ?>
                        <tr>
                            <td>
                                <strong><?= esc(ucwords(trim(($discharge['firstname'] ?? '') . ' ' . ($discharge['lastname'] ?? '')))) ?></strong>
                                <br>
                                <small style="color: #64748b;">Admission #<?= esc($discharge['id']) ?></small>
                            </td>
                            <td>
                                <?= esc($discharge['room_number'] ?? 'N/A') ?>
                                <?php if ($discharge['ward']): ?>
                                    <br><small style="color: #64748b;"><?= esc($discharge['ward']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($discharge['doctor_name'] ?? 'N/A') ?></td>
                            <td>
                                <?php if ($discharge['planned_discharge_date']): ?>
                                    <?= date('M d, Y', strtotime($discharge['planned_discharge_date'])) ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong style="color: #dc2626;">₱<?= number_format($discharge['total_charges'] ?? 0, 2) ?></strong>
                            </td>
                            <td>
                                <a href="<?= site_url('accounting/discharge/finalize/' . $discharge['id']) ?>" class="btn-modern btn-primary">
                                    <i class="fas fa-file-invoice-dollar"></i> Finalize Billing
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #64748b;">
                <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 16px; color: #cbd5e1;"></i>
                <h5>No Patients Pending Discharge</h5>
                <p>All patients have been processed or there are no discharge orders pending billing.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

