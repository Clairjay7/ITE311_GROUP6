<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>⚙️ Accounting Settings</h2>
        <div class="actions">
            <a href="<?= base_url('accountant/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="settings-section">
        <h4>Configure Accounting Settings</h4>
        <p>Manage accounting preferences and system configurations.</p>
    </div>
</div>
<?= $this->endSection() ?>
