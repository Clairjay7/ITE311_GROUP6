<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>💰 Revenue Analysis</h2>
        <div class="actions">
            <a href="<?= base_url('accountant/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="revenue-section">
        <h4>Analyze Revenue Streams</h4>
        <p>Track and analyze hospital revenue from different departments.</p>
    </div>
</div>
<?= $this->endSection() ?>
