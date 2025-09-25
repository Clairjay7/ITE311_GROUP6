<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>🧑‍⚕️ Nurse Dashboard</h1>
<p>Care coordination, tasks, and patient flow.</p>

<div class="spacer"></div>
<div class="grid grid-3">
    <div class="card"><h5>Today's Appointments</h5><h3>12</h3></div>
    <div class="card"><h5>Pending Tasks</h5><h3>5</h3></div>
    <div class="card"><h5>Patients Waiting</h5><h3>3</h3></div>
</div>

<div class="spacer"></div>
<div class="grid grid-2">
    <div class="card">
        <h5>Recent Patients</h5>
        <div class="actions-row"><span>John Doe • 09:30 • In Progress</span></div>
        <div class="actions-row"><span>Jane Smith • 10:15 • Completed</span></div>
        <div class="actions-row"><span>Robert Johnson • 11:00 • Waiting</span></div>
    </div>
    <div class="card">
        <h5>Upcoming Appointments</h5>
        <div class="actions-row"><span>Sarah Williams • 11:30 • Routine Checkup</span></div>
        <div class="actions-row"><span>Michael Brown • 13:15 • Follow-up Visit</span></div>
        <div class="actions-row"><span>Emily Davis • 14:30 • Vaccination</span></div>
    </div>
</div>
<?= $this->endSection() ?>
