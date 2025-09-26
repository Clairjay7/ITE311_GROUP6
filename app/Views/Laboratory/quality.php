<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>🎯 Quality Control</h2>
        <div class="actions">
            <a href="<?= base_url('laboratory/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="quality-section">
        <h4>Quality Control Management</h4>
        <p>Manage quality control tests, calibrations, and compliance.</p>
    </div>
</div>
<?= $this->endSection() ?>
