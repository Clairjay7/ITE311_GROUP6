<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Medication Billing<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .page-header h1 {
        margin: 0;
        color: #2e7d32;
        font-size: 28px;
    }
    .status-filter {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
    }
    .filter-btn {
        padding: 8px 16px;
        border: 2px solid #e5e7eb;
        background: white;
        border-radius: 8px;
        text-decoration: none;
        color: #64748b;
        font-weight: 600;
        transition: all 0.3s;
    }
    .filter-btn:hover, .filter-btn.active {
        background: #2e7d32;
        color: white;
        border-color: #2e7d32;
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
        text-transform: uppercase;
    }
    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .status-paid {
        background: #d1fae5;
        color: #065f46;
    }
    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }
    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
        margin-right: 4px;
        display: inline-block;
    }
    .btn-view {
        background: #0288d1;
        color: white;
    }
    .btn-invoice {
        background: #f59e0b;
        color: white;
    }
    .btn-pay {
        background: #10b981;
        color: white;
    }
    .btn-cancel {
        background: #ef4444;
        color: white;
    }
    .read-only-badge {
        background: #dbeafe;
        color: #1e40af;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-pills"></i> Medication Billing</h1>
    <?php if ($isReadOnly): ?>
        <span class="read-only-badge"><i class="fas fa-eye"></i> Read-Only View</span>
    <?php endif; ?>
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

<div class="status-filter">
    <a href="<?= site_url('accounting/medication-billing') ?>" 
       class="filter-btn <?= $currentStatus === 'all' ? 'active' : '' ?>">
        All (<?= count($bills) ?>)
    </a>
    <a href="<?= site_url('accounting/medication-billing?status=pending') ?>" 
       class="filter-btn <?= $currentStatus === 'pending' ? 'active' : '' ?>">
        Pending
    </a>
    <a href="<?= site_url('accounting/medication-billing?status=paid') ?>" 
       class="filter-btn <?= $currentStatus === 'paid' ? 'active' : '' ?>">
        Paid
    </a>
    <a href="<?= site_url('accounting/medication-billing?status=cancelled') ?>" 
       class="filter-btn <?= $currentStatus === 'cancelled' ? 'active' : '' ?>">
        Cancelled
    </a>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Patient</th>
                <th>Medicine</th>
                <th>Dosage</th>
                <th>Unit Price</th>
                <th>Admin Fee</th>
                <th>Total Amount</th>
                <th>Nurse</th>
                <th>Administered At</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bills)): ?>
                <?php foreach ($bills as $bill): ?>
                    <tr>
                        <td><strong><?= esc($bill['invoice_number'] ?? 'N/A') ?></strong></td>
                        <td><?= esc(($bill['firstname'] ?? '') . ' ' . ($bill['lastname'] ?? '') ?: 'N/A') ?></td>
                        <td><strong><?= esc($bill['medicine_name'] ?? 'N/A') ?></strong></td>
                        <td><?= esc($bill['dosage'] ?? 'N/A') ?></td>
                        <td>₱<?= number_format($bill['unit_price'] ?? 0, 2) ?></td>
                        <td>₱<?= number_format($bill['administration_fee'] ?? 0, 2) ?></td>
                        <td style="font-weight: 700; color: #2e7d32;">₱<?= number_format($bill['amount'] ?? 0, 2) ?></td>
                        <td><?= esc($bill['nurse_name'] ?? 'N/A') ?></td>
                        <td><?= $bill['administered_at'] ? date('M d, Y h:i A', strtotime($bill['administered_at'])) : 'N/A' ?></td>
                        <td>
                            <span class="status-badge status-<?= $bill['status'] ?>">
                                <?= ucfirst($bill['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= site_url('accounting/medication-billing/view/' . $bill['id']) ?>" 
                               class="btn-action btn-view">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="<?= site_url('accounting/medication-billing/invoice/' . $bill['id']) ?>" 
                               class="btn-action btn-invoice" target="_blank">
                                <i class="fas fa-file-invoice"></i> Invoice
                            </a>
                            <?php if (($canProcessPayment ?? !$isReadOnly) && $bill['status'] === 'pending'): ?>
                                <button onclick="processPayment(<?= $bill['id'] ?>)" 
                                        class="btn-action btn-pay">
                                    <i class="fas fa-check"></i> Mark Paid
                                </button>
                                <button onclick="cancelBill(<?= $bill['id'] ?>)" 
                                        class="btn-action btn-cancel">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block; color: #cbd5e1;"></i>
                        No medication bills found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
async function processPayment(billId) {
    if (!confirm('Are you sure you want to mark this bill as paid? This action cannot be undone.')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('action', 'process_payment');

        const response = await fetch(`<?= site_url('accounting/medication-billing/process-payment/') ?>${billId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            alert(data.message || 'Payment processed successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to process payment'));
        }
    } catch (error) {
        console.error('Error processing payment:', error);
        alert('Failed to process payment. Please try again or contact support.');
    }
}

async function cancelBill(billId) {
    if (!confirm('Are you sure you want to cancel this bill? This action cannot be undone.')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('action', 'cancel');

        const response = await fetch(`<?= site_url('accounting/medication-billing/cancel/') ?>${billId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
            alert(data.message || 'Bill cancelled successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to cancel bill'));
        }
    } catch (error) {
        console.error('Error cancelling bill:', error);
        alert('Failed to cancel bill. Please try again or contact support.');
    }
}
</script>

<?= $this->endSection() ?>

