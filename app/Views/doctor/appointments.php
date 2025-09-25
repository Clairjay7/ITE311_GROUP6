<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>📅 Appointments</h1>
<p>Daily at upcoming appointments.</p>

<div class="grid grid-2">
    <div class="card">
        <h5>Today</h5>
        <div class="actions-row"><span>09:00 • John Smith • Follow-up</span></div>
        <div class="actions-row"><span>10:30 • Maria Garcia • Consultation</span></div>
    </div>
    <div class="card">
        <h5>Upcoming</h5>
        <div class="actions-row"><span>Tomorrow • Robert Johnson • Surgery Prep</span></div>
        <div class="actions-row"><span>Fri • Anne Cruz • New Patient</span></div>
    </div>
</div>

<div class="card">
    <h5>🗓️ Calendar</h5>
    <div class="actions-row"><a href="<?= base_url('doctor/calendar') ?>" class="btn">Open Calendar View</a></div>
</div>
<?= $this->endSection() ?>


