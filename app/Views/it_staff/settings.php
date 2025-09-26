<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>⚙️ System Settings</h2>
        <div class="actions">
            <a href="<?= base_url('it/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="settings-section">
        <h4>System Configuration</h4>
        <p>Configure system settings and preferences.</p>
    </div>
</div>
<?= $this->endSection() ?>
