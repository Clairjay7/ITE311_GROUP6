<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Medication Bill Details<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .page-header {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(46, 125, 50, 0.2);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
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
        border-left: 4px solid #2e7d32;
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
    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .status-paid {
        background: #d1fae5;
        color: #065f46;
    }
    .btn-action {
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        margin-right: 8px;
        display: inline-block;
    }
    .btn-back {
        background: #f1f5f9;
        color: #475569;
    }
    .btn-invoice {
        background: #f59e0b;
        color: white;
    }
    .btn-pay {
        background: #10b981;
        color: white;
    }
    .read-only-badge {
        background: #dbeafe;
        color: #1e40af;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-pills"></i> Medication Bill Details</h1>
    <div>
        <?php if ($isReadOnly): ?>
            <span class="read-only-badge"><i class="fas fa-eye"></i> Read-Only View</span>
        <?php endif; ?>
        <a href="<?= site_url('accounting/medication-billing') ?>" class="btn-action btn-back">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
        <a href="<?= site_url('accounting/medication-billing/invoice/' . $bill['id']) ?>" 
           class="btn-action btn-invoice" target="_blank">
            <i class="fas fa-file-invoice"></i> View Invoice
        </a>
    </div>
</div>

<div class="info-card">
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Invoice Number</div>
            <div class="info-value"><?= esc($bill['invoice_number'] ?? 'N/A') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Status</div>
            <div class="info-value">
                <span class="status-badge status-<?= $bill['status'] ?>">
                    <?= ucfirst($bill['status']) ?>
                </span>
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Patient</div>
            <div class="info-value">
                <?= esc(ucfirst($bill['firstname'] ?? '') . ' ' . ucfirst($bill['lastname'] ?? '')) ?>
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Doctor</div>
            <div class="info-value"><?= esc($bill['doctor_name'] ?? 'N/A') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Medicine Name</div>
            <div class="info-value"><strong><?= esc($bill['medicine_name'] ?? 'N/A') ?></strong></div>
        </div>
        <div class="info-item">
            <div class="info-label">Dosage</div>
            <div class="info-value"><?= esc($bill['dosage'] ?? 'N/A') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Quantity</div>
            <div class="info-value"><?= esc($bill['quantity'] ?? 1) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Unit Price</div>
            <div class="info-value">₱<?= number_format($bill['unit_price'] ?? 0, 2) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Administration Fee</div>
            <div class="info-value">₱<?= number_format($bill['administration_fee'] ?? 0, 2) ?></div>
        </div>
        <div class="info-item" style="border-left-color: #10b981; background: #d1fae5;">
            <div class="info-label">Total Amount</div>
            <div class="info-value" style="color: #065f46; font-size: 24px;">
                ₱<?= number_format($bill['amount'] ?? 0, 2) ?>
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Nurse (Administered By)</div>
            <div class="info-value">
                <i class="fas fa-user-nurse"></i> <?= esc($bill['nurse_name'] ?? 'N/A') ?>
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Administered At</div>
            <div class="info-value">
                <i class="fas fa-clock"></i> <?= $bill['administered_at'] ? date('M d, Y h:i A', strtotime($bill['administered_at'])) : 'N/A' ?>
            </div>
        </div>
        <?php if ($bill['status'] === 'paid'): ?>
            <div class="info-item">
                <div class="info-label">Processed By</div>
                <div class="info-value"><?= esc($bill['processed_by_name'] ?? 'N/A') ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Paid At</div>
                <div class="info-value">
                    <i class="fas fa-check-circle"></i> <?= $bill['paid_at'] ? date('M d, Y h:i A', strtotime($bill['paid_at'])) : 'N/A' ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (($canProcessPayment ?? !$isReadOnly) && $bill['status'] === 'pending'): ?>
<div class="info-card" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
    <h3 style="margin-top: 0; color: #92400e;"><i class="fas fa-info-circle"></i> Payment Actions</h3>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <button onclick="processPayment(<?= $bill['id'] ?>)" class="btn-action btn-pay">
            <i class="fas fa-check"></i> Mark as Paid
        </button>
        <button onclick="cancelBill(<?= $bill['id'] ?>)" class="btn-action" style="background: #ef4444; color: white;">
            <i class="fas fa-times"></i> Cancel Bill
        </button>
    </div>
</div>
<?php endif; ?>

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

