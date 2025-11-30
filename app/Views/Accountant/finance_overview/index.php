<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Finance Overview<?= $this->endSection() ?>

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
    .btn-primary:hover {
        background: #1b5e20;
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
    <h1>Finance Overview</h1>
    <a href="<?= site_url('accounting/finance/create') ?>" class="btn-primary">
        <i class="fas fa-plus"></i> Create New
    </a>
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

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Period Type</th>
                <th>Period Start</th>
                <th>Period End</th>
                <th>Total Revenue</th>
                <th>Total Expenses</th>
                <th>Net Profit</th>
                <th>Total Bills</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($finance_overviews)): ?>
                <?php foreach ($finance_overviews as $overview): ?>
                    <tr>
                        <td><?= esc(ucfirst($overview['period_type'])) ?></td>
                        <td><?= esc(date('M d, Y', strtotime($overview['period_start']))) ?></td>
                        <td><?= esc(date('M d, Y', strtotime($overview['period_end']))) ?></td>
                        <td>₱<?= number_format($overview['total_revenue'], 2) ?></td>
                        <td>₱<?= number_format($overview['total_expenses'], 2) ?></td>
                        <td style="color: <?= $overview['net_profit'] >= 0 ? '#10b981' : '#ef4444' ?>; font-weight: 600;">
                            ₱<?= number_format($overview['net_profit'], 2) ?>
                        </td>
                        <td><?= esc($overview['total_bills']) ?></td>
                        <td>
                            <a href="<?= site_url('accounting/finance/edit/' . $overview['id']) ?>" style="color: #0288d1; margin-right: 8px;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="<?= site_url('accounting/finance/delete/' . $overview['id']) ?>" 
                               onclick="return confirm('Are you sure you want to delete this finance overview?')"
                               style="color: #ef4444;">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
                        No finance overview records found. <a href="<?= site_url('accounting/finance/create') ?>">Create one now</a>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Cross-Role Financial Data -->
<?php if (!empty($cross_role_data)): ?>
    <div style="margin-top: 32px;">
        <h2 style="color: #2e7d32; margin-bottom: 16px; font-size: 20px;">Cross-Role Financial Data</h2>
        
        <!-- Receptionist → Patient Payments -->
        <?php if (!empty($cross_role_data['receptionist_payments'])): ?>
            <div style="margin-bottom: 24px;">
                <h3 style="color: #10b981; margin-bottom: 12px; font-size: 16px;">
                    <i class="fas fa-user-tie"></i> Receptionist → Patient Payments
                </h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cross_role_data['receptionist_payments'] as $payment): ?>
                                <tr>
                                    <td><?= esc(date('M d, Y', strtotime($payment['report_date'] ?? $payment['created_at']))) ?></td>
                                    <td><?= esc(($payment['firstname'] ?? '') . ' ' . ($payment['lastname'] ?? '') ?: 'N/A') ?></td>
                                    <td style="font-weight: 600;">₱<?= number_format($payment['amount'] ?? 0, 2) ?></td>
                                    <td>
                                        <span class="status-badge" style="background: #d1fae5; color: #065f46;">
                                            <?= esc(ucfirst($payment['status'] ?? 'completed')) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Doctor/Nurse → Treatment Charges -->
        <?php if (!empty($cross_role_data['consultation_charges'])): ?>
            <div style="margin-bottom: 24px;">
                <h3 style="color: #0288d1; margin-bottom: 12px; font-size: 16px;">
                    <i class="fas fa-user-doctor"></i> Doctor/Nurse → Treatment Charges
                </h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cross_role_data['consultation_charges'] as $consultation): ?>
                                <tr>
                                    <td><?= esc(date('M d, Y', strtotime($consultation['consultation_date'] ?? $consultation['created_at']))) ?></td>
                                    <td><?= esc(($consultation['firstname'] ?? '') . ' ' . ($consultation['lastname'] ?? '') ?: 'N/A') ?></td>
                                    <td><?= esc($consultation['doctor_name'] ?? 'N/A') ?></td>
                                    <td><?= esc(ucfirst($consultation['type'] ?? 'consultation')) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lab Staff → Lab Test Charges -->
        <?php if (!empty($cross_role_data['lab_test_charges'])): ?>
            <div style="margin-bottom: 24px;">
                <h3 style="color: #ec4899; margin-bottom: 12px; font-size: 16px;">
                    <i class="fas fa-flask"></i> Lab Staff → Lab Test Charges
                </h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Test Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cross_role_data['lab_test_charges'] as $lab): ?>
                                <tr>
                                    <td><?= esc(date('M d, Y', strtotime($lab['created_at']))) ?></td>
                                    <td><?= esc(($lab['firstname'] ?? '') . ' ' . ($lab['lastname'] ?? '') ?: 'N/A') ?></td>
                                    <td><?= esc($lab['test_name'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="status-badge" style="background: #d1fae5; color: #065f46;">
                                            <?= esc(ucfirst($lab['status'] ?? 'completed')) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Pharmacy → Medication Expenses -->
        <?php if (!empty($cross_role_data['pharmacy_expenses'])): ?>
            <div style="margin-bottom: 24px;">
                <h3 style="color: #ef4444; margin-bottom: 12px; font-size: 16px;">
                    <i class="fas fa-pills"></i> Pharmacy → Medication Inventory
                </h3>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cross_role_data['pharmacy_expenses'] as $pharmacy): ?>
                                <tr>
                                    <td><?= esc($pharmacy['item_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($pharmacy['quantity'] ?? 0) ?></td>
                                    <td>₱<?= number_format($pharmacy['price'] ?? 0, 2) ?></td>
                                    <td style="font-weight: 600;">₱<?= number_format(($pharmacy['quantity'] ?? 0) * ($pharmacy['price'] ?? 0), 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

