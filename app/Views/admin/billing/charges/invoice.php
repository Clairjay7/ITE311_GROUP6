<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= ($charge['status'] === 'paid') ? 'Receipt' : 'Invoice' ?> - <?= esc($charge['charge_number']) ?><?= $this->endSection() ?>

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
    }
    .invoice-header {
        border-bottom: 3px solid #dc2626;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    .invoice-title {
        font-size: 32px;
        font-weight: 700;
        color: #dc2626;
        margin: 0;
    }
    .invoice-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }
    .info-section h3 {
        color: #dc2626;
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
        background: #fee2e2;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #dc2626;
        border-bottom: 2px solid #dc2626;
    }
    .items-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
    }
    .total-row {
        background: #fee2e2;
        font-weight: 700;
        font-size: 18px;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-approved { background: #dbeafe; color: #1e40af; }
    .status-paid { background: #d1fae5; color: #065f46; }
</style>

<div class="no-print" style="margin-bottom: 20px;">
    <a href="<?= base_url('admin/billing/charges/view/' . $charge['id']) ?>" style="color: #dc2626; text-decoration: none;">
        <i class="fas fa-arrow-left"></i> Back to Charge Details
    </a>
    <button onclick="window.print()" style="float: right; background: #dc2626; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
        <i class="fas fa-print"></i> Print <?= ($charge['status'] === 'paid') ? 'Receipt' : 'Invoice' ?>
    </button>
</div>

<div class="invoice-container">
    <div class="invoice-header">
        <h1 class="invoice-title"><?= ($charge['status'] === 'paid') ? 'RECEIPT' : 'INVOICE' ?></h1>
        <p style="color: #64748b; margin: 5px 0;">Charge Number: <strong><?= esc($charge['charge_number']) ?></strong></p>
        <p style="color: #64748b; margin: 5px 0;">
            Status: <span class="status-badge status-<?= $charge['status'] ?>"><?= ucfirst($charge['status']) ?></span>
        </p>
    </div>

    <div class="invoice-info">
        <div class="info-section">
            <h3>Bill To</h3>
            <p style="font-weight: 600; margin: 5px 0;"><?= esc(($charge['firstname'] ?? '') . ' ' . ($charge['lastname'] ?? '')) ?></p>
            <?php if ($charge['contact']): ?>
                <p style="margin: 5px 0; color: #64748b;">Contact: <?= esc($charge['contact']) ?></p>
            <?php endif; ?>
            <?php if ($charge['address']): ?>
                <p style="margin: 5px 0; color: #64748b;"><?= esc($charge['address']) ?></p>
            <?php endif; ?>
        </div>
        <div class="info-section">
            <h3><?= ($charge['status'] === 'paid') ? 'Receipt' : 'Invoice' ?> Details</h3>
            <p style="margin: 5px 0;"><strong>Date:</strong> <?= date('F d, Y', strtotime($charge['created_at'])) ?></p>
            <p style="margin: 5px 0;"><strong>Time:</strong> <?= date('h:i A', strtotime($charge['created_at'])) ?></p>
            <?php if ($charge['doctor_name']): ?>
                <p style="margin: 5px 0;"><strong>Doctor:</strong> <?= esc($charge['doctor_name']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Item Type</th>
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
                <td style="text-align: right; padding: 20px; color: #dc2626; font-size: 24px;">₱<?= number_format($charge['total_amount'], 2) ?></td>
            </tr>
        </tbody>
    </table>

    <?php if ($charge['notes']): ?>
        <div style="margin-top: 30px; padding: 20px; background: #f8fafc; border-radius: 8px;">
            <h3 style="color: #dc2626; margin-top: 0;">Notes</h3>
            <p style="color: #64748b;"><?= esc($charge['notes']) ?></p>
        </div>
    <?php endif; ?>

    <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb; text-align: center; color: #64748b; font-size: 12px;">
        <p>This is an auto-generated invoice from the Hospital Management System.</p>
        <p>For inquiries, please contact the billing department.</p>
    </div>
</div>

<?= $this->endSection() ?>

