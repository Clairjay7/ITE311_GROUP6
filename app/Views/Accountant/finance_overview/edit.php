<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Edit Finance Overview<?= $this->endSection() ?>

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
    <h1 style="color: #2e7d32; margin-bottom: 24px;">Edit Finance Overview</h1>

    <?php if (session()->getFlashdata('errors')): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <div><?= esc($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= site_url('accounting/finance/update/' . $finance_overview['id']) ?>">
        <div class="form-group">
            <label class="form-label">Period Type <span style="color: red;">*</span></label>
            <select name="period_type" class="form-control" required>
                <option value="daily" <?= $finance_overview['period_type'] == 'daily' ? 'selected' : '' ?>>Daily</option>
                <option value="weekly" <?= $finance_overview['period_type'] == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                <option value="monthly" <?= $finance_overview['period_type'] == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                <option value="yearly" <?= $finance_overview['period_type'] == 'yearly' ? 'selected' : '' ?>>Yearly</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Period Start <span style="color: red;">*</span></label>
            <input type="date" name="period_start" class="form-control" value="<?= old('period_start', $finance_overview['period_start']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Period End <span style="color: red;">*</span></label>
            <input type="date" name="period_end" class="form-control" value="<?= old('period_end', $finance_overview['period_end']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Total Revenue</label>
            <input type="number" name="total_revenue" class="form-control" step="0.01" value="<?= old('total_revenue', $finance_overview['total_revenue']) ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Total Expenses</label>
            <input type="number" name="total_expenses" class="form-control" step="0.01" value="<?= old('total_expenses', $finance_overview['total_expenses']) ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Total Bills</label>
            <input type="number" name="total_bills" class="form-control" value="<?= old('total_bills', $finance_overview['total_bills']) ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Paid Bills</label>
            <input type="number" name="paid_bills" class="form-control" value="<?= old('paid_bills', $finance_overview['paid_bills']) ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Pending Bills</label>
            <input type="number" name="pending_bills" class="form-control" value="<?= old('pending_bills', $finance_overview['pending_bills']) ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Insurance Claims Total</label>
            <input type="number" name="insurance_claims_total" class="form-control" step="0.01" value="<?= old('insurance_claims_total', $finance_overview['insurance_claims_total']) ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-control" rows="4"><?= old('notes', $finance_overview['notes']) ?></textarea>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" class="btn-submit">Update Finance Overview</button>
            <a href="<?= site_url('accounting/finance') ?>" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

