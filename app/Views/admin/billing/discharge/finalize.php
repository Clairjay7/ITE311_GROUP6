<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Finalize Billing for Discharge<?= $this->endSection() ?>

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
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        border: none;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-primary {
        background: #f59e0b;
        color: white;
    }
    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-check-circle"></i> Finalize Billing for Discharge</h1>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="modern-card">
    <div class="card-header-modern">
        <h5><i class="fas fa-user-injured"></i> Patient Information</h5>
    </div>
    <div class="card-body-modern">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div>
                <label style="font-size: 12px; color: #64748b;">Patient Name</label>
                <div style="font-weight: 600; color: #1e293b;">
                    <?= esc(ucwords(trim(($admission['firstname'] ?? '') . ' ' . ($admission['lastname'] ?? '')))) ?>
                </div>
            </div>
            <div>
                <label style="font-size: 12px; color: #64748b;">Admission ID</label>
                <div style="font-weight: 600; color: #1e293b;">
                    #<?= esc($admission['id']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modern-card">
    <div class="card-header-modern">
        <h5><i class="fas fa-file-invoice-dollar"></i> Pending Charges</h5>
    </div>
    <div class="card-body-modern">
        <?php if (!empty($pendingCharges)): ?>
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Charge Number</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalAmount = 0;
                    foreach ($pendingCharges as $charge): 
                        $totalAmount += (float)($charge['total_amount'] ?? 0);
                    ?>
                        <tr>
                            <td><?= esc($charge['charge_number'] ?? 'N/A') ?></td>
                            <td><?= esc($charge['notes'] ?? 'N/A') ?></td>
                            <td>₱<?= number_format($charge['total_amount'] ?? 0, 2) ?></td>
                            <td>
                                <span style="background: #dc2626; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                    <?= esc(ucfirst($charge['status'] ?? 'pending')) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #fef3c7;">
                        <td colspan="2" style="text-align: right; font-weight: 700; color: #92400e;">Total Amount:</td>
                        <td style="font-weight: 700; color: #92400e; font-size: 18px;">₱<?= number_format($totalAmount, 2) ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            
            <form action="<?= base_url('admin/billing/discharge/process/' . $admission['id']) ?>" method="post" style="margin-top: 24px;">
                <?= csrf_field() ?>
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <a href="<?= base_url('admin/billing/dashboard') ?>" class="btn-modern btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn-modern btn-primary" onclick="return confirm('Are you sure? This will mark all charges as paid and discharge the patient. Room and bed will be freed.');">
                        <i class="fas fa-check-circle"></i> Finalize & Discharge Patient
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #64748b;">
                <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 16px; color: #cbd5e1;"></i>
                <h5>No Pending Charges</h5>
                <p>All charges for this patient have been paid. You can proceed with discharge.</p>
                <form action="<?= base_url('admin/billing/discharge/process/' . $admission['id']) ?>" method="post" style="margin-top: 24px;">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-modern btn-primary" onclick="return confirm('Are you sure? This will discharge the patient. Room and bed will be freed.');">
                        <i class="fas fa-check-circle"></i> Complete Discharge
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

