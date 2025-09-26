<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>👨‍⚕️ Doctor Dashboard</h1>
<p>Welcome back, Dr. <?= session()->get('username') ?? 'Doctor' ?>! Today is <?= date('F j, Y') ?></p>

<div class="spacer"></div>
<div class="grid grid-4">
    <div class="card">
        <h5>👥 Total Patients</h5>
        <h3><?= esc($totalPatients ?? 125) ?></h3>
    </div>
    <div class="card">
        <h5>📅 Today's Appointments</h5>
        <h3><?= esc($todaysAppointments ?? 8) ?></h3>
    </div>
    <div class="card">
        <h5>💊 Prescriptions</h5>
        <h3><?= esc($pendingPrescriptions ?? 15) ?></h3>
    </div>
    <div class="card">
        <h5>🧪 Lab Requests</h5>
        <h3><?= esc($pendingLabRequests ?? 6) ?></h3>
    </div>
</div>

<h2 class="section-title">Quick Actions</h2>
<div class="grid grid-4">
    <div class="card">
        <h5>👥 My Patients</h5>
        <p>View and manage patient records</p>
        <div class="actions-row">
            <a href="<?= base_url('doctor/patients') ?>" class="btn">View Patients</a>
        </div>
    </div>
    <div class="card">
        <h5>📅 Appointments</h5>
        <p>Manage today's appointments</p>
        <div class="actions-row">
            <a href="<?= base_url('doctor/appointments') ?>" class="btn">View Schedule</a>
        </div>
    </div>
    <div class="card">
        <h5>💊 Prescriptions</h5>
        <p>Create and manage prescriptions</p>
        <div class="actions-row">
            <a href="<?= base_url('doctor/prescriptions') ?>" class="btn">Manage</a>
        </div>
    </div>
    <div class="card">
        <h5>🧪 Lab Requests</h5>
        <p>Order laboratory tests</p>
        <div class="actions-row">
            <a href="<?= base_url('doctor/lab-requests') ?>" class="btn">Order Tests</a>
        </div>
    </div>
</div>

<div class="spacer"></div>
<div class="grid grid-4">
    <div class="card">
        <h5>📋 Medical Records</h5>
        <p>Access patient medical history</p>
        <div class="actions-row">
            <a href="<?= base_url('doctor/medical-records') ?>" class="btn">View Records</a>
        </div>
    </div>
    <div class="card">
        <h5>🩺 Consultations</h5>
        <p>Conduct patient consultations</p>
        <div class="actions-row">
            <a href="<?= base_url('doctor/consultations') ?>" class="btn">Start Consultation</a>
        </div>
    </div>
    <div class="card">
        <h5>📊 Reports</h5>
        <p>Generate medical reports</p>
        <div class="actions-row">
            <a href="<?= base_url('doctor/reports') ?>" class="btn">Generate</a>
        </div>
    </div>
    <div class="card">
        <h5>⚙️ Settings</h5>
        <p>Configure preferences</p>
        <div class="actions-row">
            <a href="<?= base_url('doctor/settings') ?>" class="btn">Configure</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
