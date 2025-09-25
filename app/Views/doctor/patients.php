<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>👥 Assigned Patients</h1>
<p>Listahan ng mga pasyenteng naka-assign sa iyo.</p>

<div class="grid grid-3">
    <div class="card">
        <h5>John Doe</h5>
        <small>Age: 45 • Room 301-B • Status: Stable</small>
        <div class="actions-row">
            <a href="<?= base_url('doctor/emr') ?>" class="btn btn-secondary">Open EMR</a>
        </div>
    </div>
    <div class="card">
        <h5>Maria Garcia</h5>
        <small>Age: 36 • Room 204-A • Status: Under Observation</small>
        <div class="actions-row">
            <a href="<?= base_url('doctor/emr') ?>" class="btn btn-secondary">Open EMR</a>
        </div>
    </div>
    <div class="card">
        <h5>Robert Johnson</h5>
        <small>Age: 52 • ICU-2 • Status: Critical</small>
        <div class="actions-row">
            <a href="<?= base_url('doctor/emr') ?>" class="btn btn-secondary">Open EMR</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>


