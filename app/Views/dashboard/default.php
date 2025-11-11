<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .default-dashboard { display: grid; gap: 24px; }
    .welcome-card {
        background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;
        box-shadow: 0 2px 6px rgba(15,23,42,.08);
        background-image: linear-gradient(135deg, rgba(76,175,80,.06), rgba(46,125,50,.06));
    }
    .welcome-card h2 { font-family: 'Playfair Display', serif; color: #2e7d32; margin: 0 0 6px; letter-spacing: -0.01em; }
    .welcome-card p { margin: 0; color: #64748b; }

    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
    .stat-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 2px 6px rgba(15,23,42,.08); position: relative; overflow: hidden; transition: all .25s ease; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #2e7d32, #43a047); }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,24,40,.12); }
    .stat-title { margin: 0; font-size: 14px; font-weight: 700; color: #2e7d32; }
    .stat-value { margin-top: 10px; font-size: 28px; font-weight: 800; color: #1f2937; }
</style>

<div class="default-dashboard">
    <div class="welcome-card">
        <h2>Welcome to the Dashboard</h2>
        <p>Your role-specific dashboard was not found, showing a generic overview.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Total Doctors</div>
            <div class="stat-value"><?= esc($totalDoctors ?? 0) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Patients</div>
            <div class="stat-value"><?= esc($totalPatients ?? 0) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Today's Appointments</div>
            <div class="stat-value"><?= esc($todaysAppointments ?? 0) ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Pending Bills</div>
            <div class="stat-value"><?= esc($pendingBills ?? 0) ?></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
