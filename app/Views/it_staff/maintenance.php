<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>🔧 System Maintenance</h2>
        <div class="actions">
            <a href="<?= base_url('it/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="maintenance-section">
        <h4>Maintenance Tasks</h4>
        <p>Perform system maintenance and updates.</p>
    </div>
</div>
<?= $this->endSection() ?>
