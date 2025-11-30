<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <a href="<?= base_url('admin/pharmacy') ?>" class="btn btn-secondary">Back to List</a>
    </div>

    <form method="POST" action="<?= base_url('admin/pharmacy/store') ?>" class="form-container">
        <div class="form-group">
            <label for="item_name">Item Name *</label>
            <input type="text" id="item_name" name="item_name" class="form-control" value="<?= old('item_name') ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="3"><?= old('description') ?></textarea>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity *</label>
            <input type="number" id="quantity" name="quantity" min="0" class="form-control" value="<?= old('quantity') ?>" required>
        </div>

        <div class="form-group">
            <label for="price">Price *</label>
            <input type="number" id="price" name="price" step="0.01" min="0" class="form-control" value="<?= old('price') ?>" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Item</button>
            <a href="<?= base_url('admin/pharmacy') ?>" class="btn btn-secondary">Cancel</a>
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

