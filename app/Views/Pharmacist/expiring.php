<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>⏰ Expiring Items</h2>
        <div class="actions">
            <a href="<?= base_url('pharmacist/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="expiring-section">
        <h4>Drugs Expiring Soon</h4>
        <p>Monitor drugs that are approaching their expiry dates.</p>
    </div>
</div>
<?= $this->endSection() ?>
