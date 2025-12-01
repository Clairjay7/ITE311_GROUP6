<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Payment Reports<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .page-header h1 {
        margin: 0;
        color: #2e7d32;
        font-size: 28px;
    }
    .btn-primary {
        background: #2e7d32;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
    }
    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table th {
        background: #e8f5e9;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #2e7d32;
    }
    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
    }
    .status-badge {
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="page-header">
    <h1>Payment Reports</h1>
    <a href="<?= site_url('accounting/payments/create') ?>" class="btn-primary">
        <i class="fas fa-plus"></i> Create New
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<!-- Medication Payments Section -->
<?php if (!empty($medication_payments)): ?>
    <div style="margin-bottom: 32px;">
        <h2 style="color: #10b981; margin-bottom: 16px; font-size: 20px;">
            <i class="fas fa-pills"></i> Medication Administration Payments
        </h2>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Payment Date</th>
                        <th>Patient</th>
                        <th>Medicine</th>
                        <th>Invoice #</th>
                        <th>Payment Method</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Processed By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medication_payments as $report): ?>
                        <tr style="background: <?= $report['status'] === 'completed' ? '#f0fdf4' : ''; ?>">
                            <td><?= esc(date('M d, Y h:i A', strtotime($report['payment_date'] ?? $report['created_at']))) ?></td>
                            <td><?= esc(($report['firstname'] ?? '') . ' ' . ($report['lastname'] ?? '') ?: 'N/A') ?></td>
                            <td>
                                <strong><?= esc($report['medicine_name'] ?? 'N/A') ?></strong>
                                <?php if (!empty($report['billing_invoice'])): ?>
                                    <br><small style="color: #64748b;">Invoice: <?= esc($report['billing_invoice']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($report['reference_number'] ?: 'N/A') ?></td>
                            <td><?= esc(ucfirst(str_replace('_', ' ', $report['payment_method']))) ?></td>
                            <td style="font-weight: 700; color: #10b981;">₱<?= number_format($report['amount'], 2) ?></td>
                            <td>
                                <span class="status-badge" style="background: <?= 
                                    $report['status'] == 'completed' ? '#d1fae5' : 
                                    ($report['status'] == 'pending' ? '#fef3c7' : 
                                    ($report['status'] == 'failed' ? '#fee2e2' : '#dbeafe')); 
                                ?>; color: <?= 
                                    $report['status'] == 'completed' ? '#065f46' : 
                                    ($report['status'] == 'pending' ? '#92400e' : 
                                    ($report['status'] == 'failed' ? '#991b1b' : '#1e40af')); 
                                ?>;">
                                    <?= esc(ucfirst($report['status'])) ?>
                                </span>
                            </td>
                            <td><?= esc($report['processed_by_name'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (($userRole ?? 'finance') === 'finance'): ?>
                                    <a href="<?= site_url('accounting/payments/edit/' . $report['id']) ?>" style="color: #0288d1; margin-right: 8px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                <?php endif; ?>
                                <a href="<?= site_url('accounting/medication-billing/view/' . $report['billing_id']) ?>" style="color: #10b981; margin-right: 8px;">
                                    <i class="fas fa-eye"></i> View Bill
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Other Payment Reports -->
<div style="margin-bottom: 32px;">
    <h2 style="color: #2e7d32; margin-bottom: 16px; font-size: 20px;">
        <i class="fas fa-file-invoice-dollar"></i> All Payment Reports
    </h2>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Report Date</th>
                    <th>Patient</th>
                    <th>Payment Method</th>
                    <th>Amount</th>
                    <th>Reference Number</th>
                    <th>Status</th>
                    <th>Processed By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($payment_reports)): ?>
                    <?php foreach ($payment_reports as $report): ?>
                        <tr>
                            <td><?= esc(date('M d, Y', strtotime($report['report_date']))) ?></td>
                            <td><?= esc(($report['firstname'] ?? '') . ' ' . ($report['lastname'] ?? '') ?: 'N/A') ?></td>
                            <td><?= esc(ucfirst(str_replace('_', ' ', $report['payment_method']))) ?></td>
                            <td style="font-weight: 600;">₱<?= number_format($report['amount'], 2) ?></td>
                            <td><?= esc($report['reference_number'] ?: 'N/A') ?></td>
                            <td>
                                <span class="status-badge" style="background: <?= 
                                    $report['status'] == 'completed' ? '#d1fae5' : 
                                    ($report['status'] == 'pending' ? '#fef3c7' : 
                                    ($report['status'] == 'failed' ? '#fee2e2' : '#dbeafe')); 
                                ?>; color: <?= 
                                    $report['status'] == 'completed' ? '#065f46' : 
                                    ($report['status'] == 'pending' ? '#92400e' : 
                                    ($report['status'] == 'failed' ? '#991b1b' : '#1e40af')); 
                                ?>;">
                                    <?= esc(ucfirst($report['status'])) ?>
                                </span>
                            </td>
                            <td><?= esc($report['processed_by_name'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (($userRole ?? 'finance') === 'finance'): ?>
                                    <a href="<?= site_url('accounting/payments/edit/' . $report['id']) ?>" style="color: #0288d1; margin-right: 8px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?= site_url('accounting/payments/delete/' . $report['id']) ?>" 
                                       onclick="return confirm('Are you sure you want to delete this payment report?')"
                                       style="color: #ef4444;">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                <?php else: ?>
                                    <span style="color: #64748b; font-size: 12px;">Read-Only</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
                            No payment reports found. <?php if (($userRole ?? 'finance') === 'finance'): ?>
                                <a href="<?= site_url('accounting/payments/create') ?>">Create one now</a>.
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Receptionist → Patient Payments from Billing -->
<?php if (!empty($billing_payments)): ?>
    <div style="margin-top: 32px;">
        <h2 style="color: #2e7d32; margin-bottom: 16px; font-size: 20px;">
            <i class="fas fa-user-tie"></i> Patient Payments from Receptionist (Billing)
        </h2>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($billing_payments as $billing): ?>
                        <tr>
                            <td><?= esc(date('M d, Y', strtotime($billing['updated_at'] ?? $billing['created_at']))) ?></td>
                            <td><?= esc(($billing['firstname'] ?? '') . ' ' . ($billing['lastname'] ?? '') ?: 'N/A') ?></td>
                            <td><?= esc($billing['service'] ?? 'N/A') ?></td>
                            <td style="font-weight: 600;">₱<?= number_format($billing['amount'] ?? 0, 2) ?></td>
                            <td>
                                <span class="status-badge" style="background: #d1fae5; color: #065f46;">
                                    <?= esc(ucfirst($billing['status'] ?? 'paid')) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

