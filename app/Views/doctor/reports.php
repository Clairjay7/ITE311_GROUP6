<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>📊 Doctor Reports</h1>
<p>Daily number of patients seen, treatment stats (optional).</p>

<div class="grid grid-3">
    <div class="card"><h5>Patients Seen Today</h5><h3>14</h3></div>
    <div class="card"><h5>Treatment Success Rate</h5><h3>92%</h3></div>
    <div class="card"><h5>Avg. Consult Time</h5><h3>18m</h3></div>
</div>
<?= $this->endSection() ?>


