<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Nurse Dashboard<?= $this->endSection() ?>

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
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }
    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        position: relative;
        overflow: hidden;
        transition: var(--transition);
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, var(--gradient-1), var(--gradient-2));
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16, 24, 40, 0.12); }
    .stat-title { margin: 0; font-size: 14px; color: #64748b; font-weight: 600; }
    .stat-value { margin-top: 10px; font-size: 32px; font-weight: 800; color: #1f2937; }
    .stat-card.urgent::before { background: linear-gradient(90deg, #ef4444, #f59e0b); }
    @media (max-width: 600px) { .welcome-section { padding: 18px; } .stat-value { font-size: 28px; } }
</style>
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h2>Welcome, Nurse</h2>
        <p>Patient Care & Monitoring Overview</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-container">
        <div class="stats-grid">
            <div class="stat-card urgent">
                <div class="stat-title">Critical Patients</div>
                <div class="stat-value"><?= $criticalPatients ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Patients Under Care</div>
                <div class="stat-value"><?= $patientsUnderCare ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Medications Due</div>
                <div class="stat-value"><?= $medicationsDue ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Vitals Pending</div>
                <div class="stat-value"><?= $vitalsPending ?? '0' ?></div>
            </div>
        </div>
    </div>
</div>

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
