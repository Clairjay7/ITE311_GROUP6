<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Pending Charges<?= $this->endSection() ?>

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
        color: #dc2626;
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
        background: #dc2626;
        color: white;
        border-color: #dc2626;
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
        background: #fee2e2;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #dc2626;
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
    .status-approved {
        background: #dbeafe;
        color: #1e40af;
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
        border: none;
        cursor: pointer;
    }
    .btn-view {
        background: #0288d1;
        color: white;
    }
    .btn-invoice {
        background: #f59e0b;
        color: white;
    }
    .btn-approve {
        background: #3b82f6;
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
</style>

<div class="page-header">
    <h1><i class="fas fa-file-invoice-dollar"></i> Pending Charges</h1>
    <p style="color: #64748b; margin: 0;">Auto-generated charges from consultations</p>
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
    <a href="<?= site_url('accounting/charges') ?>" 
       class="filter-btn <?= $currentStatus === 'all' ? 'active' : '' ?>">
        All (<?= count($charges) ?>)
    </a>
    <a href="<?= site_url('accounting/charges?status=pending') ?>" 
       class="filter-btn <?= $currentStatus === 'pending' ? 'active' : '' ?>">
        Pending
    </a>
    <a href="<?= site_url('accounting/charges?status=approved') ?>" 
       class="filter-btn <?= $currentStatus === 'approved' ? 'active' : '' ?>">
        Approved
    </a>
    <a href="<?= site_url('accounting/charges?status=paid') ?>" 
       class="filter-btn <?= $currentStatus === 'paid' ? 'active' : '' ?>">
        Paid
    </a>
    <a href="<?= site_url('accounting/charges?status=cancelled') ?>" 
       class="filter-btn <?= $currentStatus === 'cancelled' ? 'active' : '' ?>">
        Cancelled
    </a>
</div>

<div class="table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Charge #</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Items</th>
                <th>Total Amount</th>
                <th>Created</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($charges)): ?>
                <?php foreach ($charges as $charge): ?>
                    <tr>
                        <td><strong><?= esc($charge['charge_number'] ?? 'N/A') ?></strong></td>
                        <td><?= esc(($charge['firstname'] ?? '') . ' ' . ($charge['lastname'] ?? '') ?: 'N/A') ?></td>
                        <td>
                            <?= esc($charge['doctor_name'] ?? 'N/A') ?>
                        </td>
                        <td><?= esc($charge['item_count'] ?? 0) ?> item(s)</td>
                        <td style="font-weight: 700; color: #dc2626;">₱<?= number_format($charge['total_amount'] ?? 0, 2) ?></td>
                        <td><?= $charge['created_at'] ? date('M d, Y h:i A', strtotime($charge['created_at'])) : 'N/A' ?></td>
                        <td>
                            <span class="status-badge status-<?= $charge['status'] ?>">
                                <?= ucfirst($charge['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= site_url('accounting/charges/view/' . $charge['id']) ?>" 
                               class="btn-action btn-view">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="<?= site_url('accounting/charges/invoice/' . $charge['id']) ?>" 
                               class="btn-action btn-invoice" target="_blank">
                                <i class="fas fa-file-invoice"></i> Invoice
                            </a>
                            <?php if ($charge['status'] === 'pending'): ?>
                                <button onclick="approveCharge(<?= $charge['id'] ?>)" 
                                        class="btn-action btn-approve">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            <?php endif; ?>
                            <?php if (in_array($charge['status'], ['pending', 'approved'])): ?>
                                <button onclick="processPayment(<?= $charge['id'] ?>)" 
                                        class="btn-action btn-pay">
                                    <i class="fas fa-money-bill-wave"></i> Pay
                                </button>
                                <button onclick="cancelCharge(<?= $charge['id'] ?>)" 
                                        class="btn-action btn-cancel">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block; color: #cbd5e1;"></i>
                        No charges found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
async function approveCharge(chargeId) {
    if (!confirm('Are you sure you want to approve this charge?')) {
        return;
    }

    try {
        const response = await fetch(`<?= site_url('accounting/charges/approve/') ?>${chargeId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message || 'Charge approved successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to approve charge'));
        }
    } catch (error) {
        console.error('Error approving charge:', error);
        alert('Failed to approve charge. Please try again.');
    }
}

async function processPayment(chargeId) {
    if (!confirm('Are you sure you want to mark this charge as paid? This action cannot be undone.')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('payment_method', 'cash');

        const response = await fetch(`<?= site_url('accounting/charges/process-payment/') ?>${chargeId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message || 'Payment processed successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to process payment'));
        }
    } catch (error) {
        console.error('Error processing payment:', error);
        alert('Failed to process payment. Please try again.');
    }
}

async function cancelCharge(chargeId) {
    if (!confirm('Are you sure you want to cancel this charge? This action cannot be undone.')) {
        return;
    }

    try {
        const response = await fetch(`<?= site_url('accounting/charges/cancel/') ?>${chargeId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message || 'Charge cancelled successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to cancel charge'));
        }
    } catch (error) {
        console.error('Error cancelling charge:', error);
        alert('Failed to cancel charge. Please try again.');
    }
}
</script>

<?= $this->endSection() ?>

