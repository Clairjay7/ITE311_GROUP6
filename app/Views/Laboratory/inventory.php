<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>📦 Inventory Management</h2>
        <div class="actions">
            <a href="<?= base_url('laboratory/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="inventory-section">
        <h4>Laboratory Supplies & Reagents</h4>
        <p>Manage laboratory inventory, supplies, and reagents.</p>
    </div>
</div>
<?= $this->endSection() ?>
