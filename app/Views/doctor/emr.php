<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>📁 Electronic Medical Records</h1>
<p>Quick access sa EMR ng pasyente: diagnoses, prescriptions, allergies, treatments.</p>

<div class="card">
    <h5>Sample Patient EMR</h5>
    <div class="actions-row"><span>Diagnoses: Hypertension, Type 2 Diabetes</span></div>
    <div class="actions-row"><span>Allergies: Penicillin</span></div>
    <div class="actions-row"><span>Ongoing: Metformin 500mg, Amlodipine 5mg</span></div>
    <div class="actions-row"><a class="btn" href="<?= base_url('doctor/prescriptions') ?>">Create/Update Prescription</a></div>
    
</div>
<?= $this->endSection() ?>


