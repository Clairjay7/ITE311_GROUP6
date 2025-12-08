<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Expense Tracking<?= $this->endSection() ?>

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
    <h1>Expense Tracking</h1>
    <a href="<?= base_url('admin/billing/expenses/create') ?>" class="btn-primary">
        <i class="fas fa-plus"></i> Create New
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Expense Date</th>
                <th>Category</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Vendor</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($expenses)): ?>
                <?php foreach ($expenses as $expense): ?>
                    <tr>
                        <td><?= esc(date('M d, Y', strtotime($expense['expense_date']))) ?></td>
                        <td><?= esc(ucfirst(str_replace('_', ' ', $expense['category']))) ?></td>
                        <td><?= esc($expense['description']) ?></td>
                        <td style="font-weight: 600;">₱<?= number_format($expense['amount'], 2) ?></td>
                        <td><?= esc($expense['vendor'] ?: 'N/A') ?></td>
                        <td>
                            <span class="status-badge" style="background: <?= 
                                $expense['status'] == 'paid' ? '#d1fae5' : 
                                ($expense['status'] == 'approved' ? '#dbeafe' : 
                                ($expense['status'] == 'pending' ? '#fef3c7' : '#fee2e2')); 
                            ?>; color: <?= 
                                $expense['status'] == 'paid' ? '#065f46' : 
                                ($expense['status'] == 'approved' ? '#1e40af' : 
                                ($expense['status'] == 'pending' ? '#92400e' : '#991b1b')); 
                            ?>;">
                                <?= esc(ucfirst($expense['status'])) ?>
                            </span>
                        </td>
                        <td><?= esc($expense['created_by_name'] ?? 'N/A') ?></td>
                        <td>
                            <a href="<?= base_url('admin/billing/expenses/edit/' . $expense['id']) ?>" style="color: #0288d1; margin-right: 8px;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="<?= base_url('admin/billing/expenses/delete/' . $expense['id']) ?>" 
                               onclick="return confirm('Are you sure you want to delete this expense?')"
                               style="color: #ef4444;">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
                        No expenses found. <a href="<?= base_url('admin/billing/expenses/create') ?>">Create one now</a>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pharmacy → Medication Expenses -->
<?php if (!empty($pharmacy_expenses)): ?>
    <div style="margin-top: 32px;">
        <h2 style="color: #2e7d32; margin-bottom: 16px; font-size: 20px;">
            <i class="fas fa-pills"></i> Pharmacy → Medication Expenses
        </h2>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Price per Unit</th>
                        <th>Total Value</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pharmacy_expenses as $pharmacy): ?>
                        <tr>
                            <td><?= esc($pharmacy['item_name'] ?? 'N/A') ?></td>
                            <td><?= esc($pharmacy['quantity'] ?? 0) ?></td>
                            <td>₱<?= number_format($pharmacy['price'] ?? 0, 2) ?></td>
                            <td style="font-weight: 600;">₱<?= number_format(($pharmacy['quantity'] ?? 0) * ($pharmacy['price'] ?? 0), 2) ?></td>
                            <td><?= esc($pharmacy['updated_at'] ? date('M d, Y', strtotime($pharmacy['updated_at'])) : 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Lab Staff → Lab Test Expenses -->
<?php if (!empty($lab_test_expenses)): ?>
    <div style="margin-top: 32px;">
        <h2 style="color: #2e7d32; margin-bottom: 16px; font-size: 20px;">
            <i class="fas fa-flask"></i> Lab Staff → Lab Test Expenses
        </h2>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Test Name</th>
                        <th>Test Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lab_test_expenses as $lab): ?>
                        <tr>
                            <td><?= esc(date('M d, Y', strtotime($lab['created_at']))) ?></td>
                            <td><?= esc(($lab['firstname'] ?? '') . ' ' . ($lab['lastname'] ?? '') ?: 'N/A') ?></td>
                            <td><?= esc($lab['test_name'] ?? 'N/A') ?></td>
                            <td><?= esc(ucfirst($lab['test_type'] ?? 'N/A')) ?></td>
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
<?= $this->endSection() ?>

