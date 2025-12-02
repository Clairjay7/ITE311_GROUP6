<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Charge Details<?= $this->endSection() ?>

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
    .info-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .info-item {
        padding: 16px;
        background: #f8fafc;
        border-radius: 8px;
        border-left: 4px solid #dc2626;
    }
    .info-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 8px;
    }
    .info-value {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
    }
    .status-badge {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-approved { background: #dbeafe; color: #1e40af; }
    .status-paid { background: #d1fae5; color: #065f46; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
    .btn-action {
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        margin-right: 8px;
        display: inline-block;
        border: none;
        cursor: pointer;
    }
    .btn-back { background: #f1f5f9; color: #475569; }
    .btn-invoice { background: #f59e0b; color: white; }
    .btn-approve { background: #3b82f6; color: white; }
    .btn-pay { background: #10b981; color: white; }
    .btn-cancel { background: #ef4444; color: white; }
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
    }
    .items-table th {
        background: #fee2e2;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #dc2626;
    }
    .items-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-file-invoice-dollar"></i> Charge Details - <?= esc($charge['charge_number']) ?></h1>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div class="info-card">
    <h2 style="color: #dc2626; margin-top: 0; margin-bottom: 20px;">Charge Information</h2>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Charge Number</div>
            <div class="info-value"><?= esc($charge['charge_number']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Patient</div>
            <div class="info-value"><?= esc(($charge['firstname'] ?? '') . ' ' . ($charge['lastname'] ?? '')) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Total Amount</div>
            <div class="info-value" style="color: #dc2626;">₱<?= number_format($charge['total_amount'], 2) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Status</div>
            <div class="info-value">
                <span class="status-badge status-<?= $charge['status'] ?>">
                    <?= ucfirst($charge['status']) ?>
                </span>
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Created At</div>
            <div class="info-value"><?= date('M d, Y h:i A', strtotime($charge['created_at'])) ?></div>
        </div>
        <?php if ($charge['doctor_name']): ?>
        <div class="info-item">
            <div class="info-label">Doctor</div>
            <div class="info-value"><?= esc($charge['doctor_name']) ?></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="info-card">
    <h2 style="color: #dc2626; margin-top: 0; margin-bottom: 20px;">Billing Items</h2>
    <table class="items-table">
        <thead>
            <tr>
                <th>Item Type</th>
                <th>Item Name</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($billingItems)): ?>
                <?php foreach ($billingItems as $item): ?>
                    <tr>
                        <td><span style="text-transform: capitalize; font-weight: 600;"><?= esc($item['item_type']) ?></span></td>
                        <td><?= esc($item['item_name']) ?></td>
                        <td><?= esc($item['description'] ?? 'N/A') ?></td>
                        <td><?= number_format($item['quantity'], 2) ?></td>
                        <td>₱<?= number_format($item['unit_price'], 2) ?></td>
                        <td style="font-weight: 700;">₱<?= number_format($item['total_price'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr style="background: #fee2e2; font-weight: 700;">
                    <td colspan="5" style="text-align: right; padding: 16px;">Total Amount:</td>
                    <td style="color: #dc2626; font-size: 18px; padding: 16px;">₱<?= number_format($charge['total_amount'], 2) ?></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #64748b;">
                        No billing items found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div style="margin-top: 24px;">
    <a href="<?= site_url('accounting/charges') ?>" class="btn-action btn-back">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
    <a href="<?= site_url('accounting/charges/invoice/' . $charge['id']) ?>" class="btn-action btn-invoice" target="_blank">
        <i class="fas fa-file-invoice"></i> Print Invoice
    </a>
    <?php if ($charge['status'] === 'pending'): ?>
        <button onclick="approveCharge(<?= $charge['id'] ?>)" class="btn-action btn-approve">
            <i class="fas fa-check"></i> Approve
        </button>
    <?php endif; ?>
    <?php if (in_array($charge['status'], ['pending', 'approved'])): ?>
        <button onclick="processPayment(<?= $charge['id'] ?>)" class="btn-action btn-pay">
            <i class="fas fa-money-bill-wave"></i> Mark as Paid
        </button>
        <button onclick="cancelCharge(<?= $charge['id'] ?>)" class="btn-action btn-cancel">
            <i class="fas fa-times"></i> Cancel
        </button>
    <?php endif; ?>
</div>

<script>
async function approveCharge(chargeId) {
    if (!confirm('Are you sure you want to approve this charge?')) return;
    try {
        const response = await fetch(`<?= site_url('accounting/charges/approve/') ?>${chargeId}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if (data.success) {
            alert(data.message || 'Charge approved successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to approve charge'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to approve charge. Please try again.');
    }
}

async function processPayment(chargeId) {
    if (!confirm('Are you sure you want to mark this charge as paid?')) return;
    try {
        const formData = new FormData();
        formData.append('payment_method', 'cash');
        const response = await fetch(`<?= site_url('accounting/charges/process-payment/') ?>${chargeId}`, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if (data.success) {
            alert(data.message || 'Payment processed successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to process payment'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to process payment. Please try again.');
    }
}

async function cancelCharge(chargeId) {
    if (!confirm('Are you sure you want to cancel this charge?')) return;
    try {
        const response = await fetch(`<?= site_url('accounting/charges/cancel/') ?>${chargeId}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if (data.success) {
            alert(data.message || 'Charge cancelled successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to cancel charge'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to cancel charge. Please try again.');
    }
}
</script>

<?= $this->endSection() ?>

