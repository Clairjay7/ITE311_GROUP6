<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Create Expense<?= $this->endSection() ?>

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
    <h1 style="color: #2e7d32; margin-bottom: 24px;">Create Expense</h1>

    <?php if (session()->getFlashdata('errors')): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <div><?= esc($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('admin/billing/expenses/store') ?>">
        <div class="form-group">
            <label class="form-label">Expense Date <span style="color: red;">*</span></label>
            <input type="date" name="expense_date" class="form-control" value="<?= old('expense_date', date('Y-m-d')) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Category <span style="color: red;">*</span></label>
            <select name="category" class="form-control" required>
                <option value="medical_supplies" <?= old('category') == 'medical_supplies' ? 'selected' : '' ?>>Medical Supplies</option>
                <option value="equipment" <?= old('category') == 'equipment' ? 'selected' : '' ?>>Equipment</option>
                <option value="utilities" <?= old('category') == 'utilities' ? 'selected' : '' ?>>Utilities</option>
                <option value="salaries" <?= old('category') == 'salaries' ? 'selected' : '' ?>>Salaries</option>
                <option value="maintenance" <?= old('category') == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                <option value="office_supplies" <?= old('category') == 'office_supplies' ? 'selected' : '' ?>>Office Supplies</option>
                <option value="insurance" <?= old('category') == 'insurance' ? 'selected' : '' ?>>Insurance</option>
                <option value="rent" <?= old('category') == 'rent' ? 'selected' : '' ?>>Rent</option>
                <option value="other" <?= old('category') == 'other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Description <span style="color: red;">*</span></label>
            <input type="text" name="description" class="form-control" value="<?= old('description') ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Amount <span style="color: red;">*</span></label>
            <input type="number" name="amount" class="form-control" step="0.01" value="<?= old('amount') ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Vendor</label>
            <input type="text" name="vendor" class="form-control" value="<?= old('vendor') ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Invoice Number</label>
            <input type="text" name="invoice_number" class="form-control" value="<?= old('invoice_number') ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Payment Method <span style="color: red;">*</span></label>
            <select name="payment_method" class="form-control" required>
                <option value="cash" <?= old('payment_method') == 'cash' ? 'selected' : '' ?>>Cash</option>
                <option value="check" <?= old('payment_method') == 'check' ? 'selected' : '' ?>>Check</option>
                <option value="bank_transfer" <?= old('payment_method') == 'bank_transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                <option value="credit_card" <?= old('payment_method') == 'credit_card' ? 'selected' : '' ?>>Credit Card</option>
                <option value="other" <?= old('payment_method') == 'other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Status <span style="color: red;">*</span></label>
            <select name="status" class="form-control" required>
                <option value="pending" <?= old('status') == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= old('status') == 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="paid" <?= old('status') == 'paid' ? 'selected' : '' ?>>Paid</option>
                <option value="rejected" <?= old('status') == 'rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="4"><?= old('notes') ?></textarea>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn-submit">Create Expense</button>
            <a href="<?= base_url('admin/billing/expenses') ?>" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

