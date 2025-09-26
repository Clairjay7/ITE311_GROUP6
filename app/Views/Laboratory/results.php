<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>📊 Test Results</h2>
        <div class="actions">
            <a href="<?= base_url('laboratory/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="results-section">
        <h4>Test Results Management</h4>
        <p>Review and approve laboratory test results before releasing to doctors.</p>
    </div>
</div>
<?= $this->endSection() ?>
