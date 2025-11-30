<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <a href="<?= base_url('admin/system') ?>" class="btn btn-secondary">Back to List</a>
    </div>

    <form method="POST" action="<?= base_url('admin/system/store') ?>" class="form-container">
        <div class="form-group">
            <label for="setting_name">Setting Name *</label>
            <input type="text" id="setting_name" name="setting_name" class="form-control" value="<?= old('setting_name') ?>" required>
        </div>

        <div class="form-group">
            <label for="setting_value">Setting Value *</label>
            <textarea id="setting_value" name="setting_value" class="form-control" rows="4" required><?= old('setting_value') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Setting</button>
            <a href="<?= base_url('admin/system') ?>" class="btn btn-secondary">Cancel</a>
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

