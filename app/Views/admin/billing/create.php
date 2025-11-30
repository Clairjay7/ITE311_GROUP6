<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <a href="<?= base_url('admin/billing') ?>" class="btn btn-secondary">Back to List</a>
    </div>

    <form method="POST" action="<?= base_url('admin/billing/store') ?>" class="form-container">
        <div class="form-group">
            <label for="patient_id">Patient *</label>
            <select id="patient_id" name="patient_id" class="form-control" required>
                <option value="">Select Patient</option>
                <?php foreach ($patients as $patient): ?>
                    <option value="<?= esc($patient['id']) ?>"><?= esc($patient['firstname'] . ' ' . $patient['lastname']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="service">Service *</label>
            <input type="text" id="service" name="service" class="form-control" value="<?= old('service') ?>" required>
        </div>

        <div class="form-group">
            <label for="amount">Amount *</label>
            <input type="number" id="amount" name="amount" step="0.01" class="form-control" value="<?= old('amount') ?>" required>
        </div>

        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" class="form-control" required>
                <option value="pending" <?= old('status') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="paid" <?= old('status') === 'paid' ? 'selected' : '' ?>>Paid</option>
                <option value="cancelled" <?= old('status') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Bill</button>
            <a href="<?= base_url('admin/billing') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
.admin-module { padding: 24px; }
.module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.module-header h2 { margin: 0; color: #2e7d32; }
.btn { padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; border: none; cursor: pointer; }
.btn-primary { background: #2e7d32; color: white; }
.btn-secondary { background: #6b7280; color: white; }
.form-container { background: white; padding: 24px; border-radius: 8px; max-width: 600px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #374151; }
.form-control { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
.form-actions { display: flex; gap: 12px; margin-top: 24px; }
</style>
<?= $this->endSection() ?>

