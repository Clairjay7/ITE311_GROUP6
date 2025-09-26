<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>⚙️ Equipment Management</h2>
        <div class="actions">
            <a href="<?= base_url('laboratory/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="equipment-section">
        <h4>Laboratory Equipment Status</h4>
        <p>Monitor and manage laboratory equipment status and maintenance.</p>
    </div>
</div>
<?= $this->endSection() ?>
