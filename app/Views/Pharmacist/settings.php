<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>⚙️ Pharmacy Settings</h2>
        <div class="actions">
            <a href="<?= base_url('pharmacist/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="settings-section">
        <h4>Pharmacy Configuration</h4>
        <p>Manage pharmacy settings and preferences.</p>
    </div>
</div>
<?= $this->endSection() ?>
