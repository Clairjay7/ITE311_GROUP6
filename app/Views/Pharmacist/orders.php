<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>🛒 Drug Orders</h2>
        <div class="actions">
            <a href="<?= base_url('pharmacist/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="orders-section">
        <h4>Manage Drug Orders</h4>
        <p>Place and track orders for pharmacy inventory.</p>
    </div>
</div>
<?= $this->endSection() ?>
