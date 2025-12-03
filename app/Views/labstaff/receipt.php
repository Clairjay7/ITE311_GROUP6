<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Receipt - <?= esc($charge['charge_number']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    @media print {
        .no-print { display: none; }
        body { background: white; }
        .receipt-container { box-shadow: none; }
    }
    .receipt-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        padding: 40px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .receipt-header {
        border-bottom: 3px solid #2e7d32;
        padding-bottom: 20px;
        margin-bottom: 30px;
        text-align: center;
    }
    .receipt-title {
        font-size: 32px;
        font-weight: 700;
        color: #2e7d32;
        margin: 0;
    }
    .receipt-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }
    .info-section h3 {
        color: #2e7d32;
        margin-bottom: 10px;
        font-size: 14px;
        text-transform: uppercase;
    }
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin: 30px 0;
    }
    .items-table th {
        background: #e8f5e9;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #2e7d32;
        border-bottom: 2px solid #2e7d32;
    }
    .items-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
    }
    .total-row {
        background: #e8f5e9;
        font-weight: 700;
        font-size: 18px;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        background: #d1fae5;
        color: #065f46;
    }
</style>

<div class="no-print" style="margin-bottom: 20px;">
    <a href="<?= site_url('labstaff/dashboard') ?>" style="color: #2e7d32; text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
    <button onclick="window.print()" style="float: right; background: #2e7d32; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
        <i class="fas fa-print"></i> Print Receipt
    </button>
</div>

<div class="receipt-container">
    <div class="receipt-header">
        <h1 class="receipt-title">PAYMENT RECEIPT</h1>
        <p style="color: #64748b; margin: 5px 0;">Receipt Number: <strong><?= esc($charge['charge_number']) ?></strong></p>
        <p style="color: #64748b; margin: 5px 0;">
            Status: <span class="status-badge">PAID</span>
        </p>
    </div>

    <div class="receipt-info">
        <div class="info-section">
            <h3>Patient Information</h3>
            <p style="font-weight: 600; margin: 5px 0;"><?= esc(($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '')) ?></p>
            <?php if ($patient['contact']): ?>
                <p style="margin: 5px 0; color: #64748b;">Contact: <?= esc($patient['contact']) ?></p>
            <?php endif; ?>
            <?php if ($patient['address']): ?>
                <p style="margin: 5px 0; color: #64748b;"><?= esc($patient['address']) ?></p>
            <?php endif; ?>
        </div>
        <div class="info-section">
            <h3>Payment Details</h3>
            <p style="margin: 5px 0;"><strong>Date:</strong> <?= date('F d, Y', strtotime($charge['paid_at'] ?? $charge['created_at'])) ?></p>
            <p style="margin: 5px 0;"><strong>Time:</strong> <?= date('h:i A', strtotime($charge['paid_at'] ?? $charge['created_at'])) ?></p>
            <p style="margin: 5px 0;"><strong>Test:</strong> <?= esc($labRequest['test_name']) ?></p>
            <p style="margin: 5px 0;"><strong>Test Type:</strong> <?= esc($labRequest['test_type']) ?></p>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($billingItems as $item): ?>
                <tr>
                    <td style="text-transform: capitalize; font-weight: 600;"><?= esc($item['item_type']) ?></td>
                    <td>
                        <strong><?= esc($item['item_name']) ?></strong><br>
                        <small style="color: #64748b;"><?= esc($item['description'] ?? '') ?></small>
                    </td>
                    <td><?= number_format($item['quantity'], 2) ?></td>
                    <td>₱<?= number_format($item['unit_price'], 2) ?></td>
                    <td style="text-align: right; font-weight: 600;">₱<?= number_format($item['total_price'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="4" style="text-align: right; padding: 20px;">TOTAL AMOUNT:</td>
                <td style="text-align: right; padding: 20px; color: #2e7d32; font-size: 24px;">₱<?= number_format($charge['total_amount'], 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 40px; padding: 20px; background: #f0fdf4; border-radius: 8px; border-left: 4px solid #2e7d32;">
        <h3 style="color: #2e7d32; margin-top: 0;">Payment Confirmation</h3>
        <p style="color: #065f46; margin: 5px 0;">
            <i class="fas fa-check-circle"></i> Payment received and processed successfully.
        </p>
        <p style="color: #065f46; margin: 5px 0;">
            <i class="fas fa-flask"></i> Lab test can now proceed.
        </p>
    </div>

    <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb; text-align: center; color: #64748b; font-size: 12px;">
        <p>This is an official receipt from the Hospital Management System.</p>
        <p>For inquiries, please contact the billing department.</p>
        <p style="margin-top: 10px;"><strong>Thank you for your payment!</strong></p>
    </div>
</div>

<?= $this->endSection() ?>

