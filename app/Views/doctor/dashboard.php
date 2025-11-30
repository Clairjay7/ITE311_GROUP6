<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Doctor Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dashboard-container { display: grid; gap: 24px; }
    .welcome-section {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 24px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        background-image: linear-gradient(135deg, rgba(76,175,80,0.06), rgba(46,125,50,0.06));
    }
    .welcome-section h2 {
        font-family: 'Playfair Display', serif;
        color: var(--primary-color);
        margin: 0 0 6px;
        font-size: 28px;
        letter-spacing: -0.01em;
    }
    .welcome-section p { color: #64748b; margin: 0; }
    .stats-container { width: 100%; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
    .stat-card {
        background: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb; padding: 20px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08); position: relative; overflow: hidden;
        transition: var(--transition);
    }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--gradient-1), var(--gradient-2)); }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16, 24, 40, 0.12); }
    .stat-title { margin: 0; font-size: 14px; color: #2e7d32; font-weight: 700; }
    .stat-value { margin-top: 10px; font-size: 32px; font-weight: 800; color: #1f2937; }
    @media (max-width: 600px) { .welcome-section { padding: 18px; } .stat-value { font-size: 28px; } }
</style>
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h2>Welcome back, Dr. <?= esc($name ?? 'Doctor') ?></h2>
        <p>Here's what's happening with your patients today</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Today's Appointments</div>
                <div class="stat-value"><?= $appointmentsCount ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Patients Seen</div>
                <div class="stat-value"><?= $patientsSeenToday ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Pending Results</div>
                <div class="stat-value"><?= $pendingLabResults ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Prescriptions</div>
                <div class="stat-value"><?= $prescriptionsCount ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">My Assigned Patients</div>
                <div class="stat-value"><?= $assignedPatientsCount ?? '0' ?></div>
            </div>
        </div>
    </div>

    <!-- Assigned Patients List -->
    <?php if (!empty($assignedPatients ?? [])): ?>
        <div class="patients-section">
            <h3 style="color: #2e7d32; margin-bottom: 16px;">My Assigned Patients</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Birthdate</th>
                            <th>Gender</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignedPatients as $patient): ?>
                            <tr>
                                <td>#<?= esc($patient['id']) ?></td>
                                <td><?= esc($patient['firstname'] . ' ' . $patient['lastname']) ?></td>
                                <td><?= esc($patient['birthdate']) ?></td>
                                <td><?= esc(ucfirst($patient['gender'])) ?></td>
                                <td><?= esc($patient['contact'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.patients-section { margin-top: 24px; }
.table-container { background: white; border-radius: 8px; overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: #e8f5e9; padding: 12px; text-align: left; font-weight: 600; color: #2e7d32; }
.data-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips if any
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?= $this->endSection() ?>
