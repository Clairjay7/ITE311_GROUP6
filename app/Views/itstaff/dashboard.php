<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>IT Staff Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dashboard-container { display: grid; gap: 24px; }
    .welcome-section { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; box-shadow: 0 2px 6px rgba(15,23,42,.08); background-image: linear-gradient(135deg, rgba(76,175,80,.06), rgba(46,125,50,.06)); }
    .welcome-section h2 { font-family: 'Playfair Display', serif; color: #2e7d32; margin: 0 0 6px; font-size: 28px; letter-spacing: -0.01em; }
    .welcome-section p { color: #64748b; margin: 0; }
    .stats-container { width: 100%; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
    .stat-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 2px 6px rgba(15,23,42,.08); position: relative; overflow: hidden; transition: all .25s ease; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #2e7d32, #43a047); }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,24,40,.12); }
    .stat-title { margin: 0; font-size: 14px; color: #2e7d32; font-weight: 700; }
    .stat-value { margin-top: 10px; font-size: 32px; font-weight: 800; color: #1f2937; }
    @media (max-width: 600px) { .welcome-section { padding: 18px; } .stat-value { font-size: 28px; } }
</style>
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h2>Welcome, IT Staff</h2>
        <p>System Administration & Security Overview</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">System Uptime</div>
                <div class="stat-value"><?= $systemUptime ?? '99.8%' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Active Users</div>
                <div class="stat-value"><?= $activeUsers ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">System Alerts</div>
                <div class="stat-value"><?= $systemAlerts ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Pending Tasks</div>
                <div class="stat-value"><?= $pendingTasks ?? '0' ?></div>
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
