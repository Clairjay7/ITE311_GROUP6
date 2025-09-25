<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>🔬 Lab & Imaging Results</h1>
<p>View real-time lab/imaging results.</p>

<div class="card">
    <h5>Recent Results</h5>
    <div class="actions-row"><span>John Doe • CBC • Normal</span></div>
    <div class="actions-row"><span>Maria Garcia • Chest X-ray • Clear</span></div>
    <div class="actions-row"><span>Robert Johnson • Potassium • Low</span></div>
</div>
<?= $this->endSection() ?>


