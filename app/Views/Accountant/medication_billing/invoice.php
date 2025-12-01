<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Invoice #<?= esc($bill['invoice_number'] ?? 'N/A') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    @media print {
        .no-print { display: none; }
        body { background: white; }
    }
    .invoice-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 40px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.1);
    }
    .invoice-header {
        border-bottom: 3px solid #2e7d32;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    .invoice-title {
        font-size: 32px;
        font-weight: 700;
        color: #2e7d32;
        margin: 0;
    }
    .invoice-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-bottom: 30px;
    }
    .invoice-details {
        background: #f8fafc;
        padding: 20px;
        border-radius: 8px;
    }
    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    .invoice-table th {
        background: #2e7d32;
        color: white;
        padding: 12px;
        text-align: left;
        font-weight: 600;
    }
    .invoice-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
    }
    .invoice-total {
        text-align: right;
        margin-top: 20px;
    }
    .total-amount {
        font-size: 24px;
        font-weight: 700;
        color: #2e7d32;
    }
    .btn-print {
        background: #2e7d32;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 20px;
    }
</style>

<div class="no-print" style="text-align: center; margin-bottom: 20px;">
    <button onclick="window.print()" class="btn-print">
        <i class="fas fa-print"></i> Print Invoice
    </button>
    <a href="<?= site_url('accounting/medication-billing/view/' . $bill['id']) ?>" 
       style="background: #64748b; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-left: 12px;">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="invoice-container">
    <div class="invoice-header">
        <h1 class="invoice-title">INVOICE</h1>
        <p style="margin: 8px 0; color: #64748b;">Invoice #<?= esc($bill['invoice_number'] ?? 'N/A') ?></p>
        <p style="margin: 0; color: #64748b;">Date: <?= date('F d, Y', strtotime($bill['created_at'])) ?></p>
    </div>

    <div class="invoice-info">
        <div>
            <h3 style="color: #2e7d32; margin-bottom: 12px;">Bill To:</h3>
            <p style="margin: 4px 0; font-weight: 600;">
                <?= esc(ucfirst($bill['firstname'] ?? '') . ' ' . ucfirst($bill['lastname'] ?? '')) ?>
            </p>
            <p style="margin: 4px 0; color: #64748b;">
                Patient ID: #<?= esc($bill['patient_id'] ?? 'N/A') ?>
            </p>
        </div>
        <div>
            <h3 style="color: #2e7d32; margin-bottom: 12px;">Service Details:</h3>
            <p style="margin: 4px 0;"><strong>Service:</strong> Medication Administration</p>
            <p style="margin: 4px 0;"><strong>Doctor:</strong> <?= esc($bill['doctor_name'] ?? 'N/A') ?></p>
            <p style="margin: 4px 0;"><strong>Nurse:</strong> <?= esc($bill['nurse_name'] ?? 'N/A') ?></p>
            <p style="margin: 4px 0;"><strong>Administered:</strong> <?= $bill['administered_at'] ? date('M d, Y h:i A', strtotime($bill['administered_at'])) : 'N/A' ?></p>
        </div>
    </div>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong><?= esc($bill['medicine_name'] ?? 'N/A') ?></strong><br>
                    <small style="color: #64748b;">Dosage: <?= esc($bill['dosage'] ?? 'N/A') ?></small>
                </td>
                <td><?= esc($bill['quantity'] ?? 1) ?></td>
                <td>₱<?= number_format($bill['unit_price'] ?? 0, 2) ?></td>
                <td>₱<?= number_format(($bill['unit_price'] ?? 0) * ($bill['quantity'] ?? 1), 2) ?></td>
            </tr>
            <tr>
                <td><strong>Administration Fee</strong></td>
                <td>1</td>
                <td>₱<?= number_format($bill['administration_fee'] ?? 0, 2) ?></td>
                <td>₱<?= number_format($bill['administration_fee'] ?? 0, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="invoice-total">
        <div style="display: flex; justify-content: space-between; padding: 16px; background: #f8fafc; border-radius: 8px;">
            <strong style="font-size: 18px;">Total Amount:</strong>
            <span class="total-amount">₱<?= number_format($bill['amount'] ?? 0, 2) ?></span>
        </div>
    </div>

    <?php if ($bill['status'] === 'paid'): ?>
        <div style="text-align: center; margin-top: 30px; padding: 20px; background: #d1fae5; border-radius: 8px;">
            <p style="margin: 0; color: #065f46; font-weight: 600; font-size: 18px;">
                <i class="fas fa-check-circle"></i> PAID
            </p>
            <p style="margin: 8px 0 0; color: #065f46;">
                Processed by: <?= esc($bill['processed_by_name'] ?? 'N/A') ?><br>
                Paid on: <?= $bill['paid_at'] ? date('M d, Y h:i A', strtotime($bill['paid_at'])) : 'N/A' ?>
            </p>
        </div>
    <?php else: ?>
        <div style="text-align: center; margin-top: 30px; padding: 20px; background: #fef3c7; border-radius: 8px;">
            <p style="margin: 0; color: #92400e; font-weight: 600; font-size: 18px;">
                <i class="fas fa-clock"></i> PENDING PAYMENT
            </p>
        </div>
    <?php endif; ?>

    <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb; text-align: center; color: #64748b; font-size: 12px;">
        <p>This is an automatically generated invoice for medication administration services.</p>
        <p>For inquiries, please contact the billing department.</p>
    </div>
</div>

<?= $this->endSection() ?>

