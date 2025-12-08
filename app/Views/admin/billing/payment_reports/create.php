<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Create Payment Report<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .form-container {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        max-width: 800px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
    }
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
    }
    .btn-submit {
        background: #2e7d32;
        color: white;
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-cancel {
        background: #6b7280;
        color: white;
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        display: inline-block;
    }
</style>

<div class="form-container">
    <h1 style="color: #2e7d32; margin-bottom: 24px;">Create Payment Report</h1>

    <?php if (session()->getFlashdata('errors')): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <div><?= esc($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('admin/billing/payment_reports/store') ?>">
        <div class="form-group">
            <label class="form-label">Report Date <span style="color: red;">*</span></label>
            <input type="date" name="report_date" class="form-control" value="<?= old('report_date', date('Y-m-d')) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Patient</label>
            <select name="patient_id" class="form-control">
                <option value="">Select Patient (Optional)</option>
                <?php foreach ($patients as $patient): ?>
                    <option value="<?= $patient['id'] ?>" <?= old('patient_id') == $patient['id'] ? 'selected' : '' ?>>
                        <?= esc($patient['firstname'] . ' ' . $patient['lastname']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Billing ID (Optional)</label>
            <input type="number" name="billing_id" class="form-control" value="<?= old('billing_id') ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Payment Method <span style="color: red;">*</span></label>
            <select name="payment_method" class="form-control" required>
                <option value="cash" <?= old('payment_method') == 'cash' ? 'selected' : '' ?>>Cash</option>
                <option value="credit_card" <?= old('payment_method') == 'credit_card' ? 'selected' : '' ?>>Credit Card</option>
                <option value="debit_card" <?= old('payment_method') == 'debit_card' ? 'selected' : '' ?>>Debit Card</option>
                <option value="bank_transfer" <?= old('payment_method') == 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                <option value="check" <?= old('payment_method') == 'check' ? 'selected' : '' ?>>Check</option>
                <option value="insurance" <?= old('payment_method') == 'insurance' ? 'selected' : '' ?>>Insurance</option>
                <option value="other" <?= old('payment_method') == 'other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Amount <span style="color: red;">*</span></label>
            <input type="number" name="amount" class="form-control" step="0.01" value="<?= old('amount') ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Reference Number</label>
            <input type="text" name="reference_number" class="form-control" value="<?= old('reference_number') ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Status <span style="color: red;">*</span></label>
            <select name="status" class="form-control" required>
                <option value="pending" <?= old('status') == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="completed" <?= old('status') == 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="failed" <?= old('status') == 'failed' ? 'selected' : '' ?>>Failed</option>
                <option value="refunded" <?= old('status') == 'refunded' ? 'selected' : '' ?>>Refunded</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Payment Date</label>
            <input type="datetime-local" name="payment_date" class="form-control" value="<?= old('payment_date', date('Y-m-d\TH:i')) ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="4"><?= old('notes') ?></textarea>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn-submit">Create Payment Report</button>
            <a href="<?= base_url('admin/billing/payment_reports') ?>" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

