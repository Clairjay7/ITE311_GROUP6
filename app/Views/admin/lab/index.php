<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <a href="<?= base_url('admin/lab/create') ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Create Lab Service
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
                    <th>Patient</th>
                    <th>Test Type</th>
                    <th>Result</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($labServices)): ?>
                    <tr><td colspan="5" class="text-center">No lab services found.</td></tr>
                <?php else: ?>
                    <?php foreach ($labServices as $lab): ?>
                        <tr>
                            <td>#<?= esc($lab['id']) ?></td>
                            <td><?= esc($lab['firstname'] . ' ' . $lab['lastname']) ?></td>
                            <td><?= esc($lab['test_type']) ?></td>
                            <td><?= esc(substr($lab['result'] ?? 'N/A', 0, 50)) ?></td>
                            <td>
                                <a href="<?= base_url('admin/lab/edit/' . $lab['id']) ?>" class="btn btn-sm btn-edit">Edit</a>
                                <a href="<?= base_url('admin/lab/delete/' . $lab['id']) ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
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

