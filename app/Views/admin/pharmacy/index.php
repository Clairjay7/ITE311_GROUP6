<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <a href="<?= base_url('admin/pharmacy/create') ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Add Item
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pharmacyItems)): ?>
                    <tr><td colspan="6" class="text-center">No items found.</td></tr>
                <?php else: ?>
                    <?php foreach ($pharmacyItems as $item): ?>
                        <tr>
                            <td>#<?= esc($item['id']) ?></td>
                            <td><?= esc($item['item_name']) ?></td>
                            <td><?= esc(substr($item['description'] ?? 'N/A', 0, 50)) ?></td>
                            <td><?= esc($item['quantity']) ?></td>
                            <td>₱<?= number_format($item['price'], 2) ?></td>
                            <td>
                                <a href="<?= base_url('admin/pharmacy/edit/' . $item['id']) ?>" class="btn btn-sm btn-edit">Edit</a>
                                <a href="<?= base_url('admin/pharmacy/delete/' . $item['id']) ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.admin-module { padding: 24px; }
.module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.module-header h2 { margin: 0; color: #2e7d32; }
.btn { padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; }
.btn-primary { background: #2e7d32; color: white; }
.btn-sm { padding: 6px 12px; font-size: 14px; }
.btn-edit { background: #3b82f6; color: white; margin-right: 8px; }
.btn-delete { background: #ef4444; color: white; }
.table-container { background: white; border-radius: 8px; overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: #e8f5e9; padding: 12px; text-align: left; font-weight: 600; color: #2e7d32; }
.data-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; }
.text-center { text-align: center; }
.alert { padding: 12px; border-radius: 6px; margin-bottom: 16px; }
.alert-success { background: #d1fae5; color: #047857; }
</style>
<?= $this->endSection() ?>

