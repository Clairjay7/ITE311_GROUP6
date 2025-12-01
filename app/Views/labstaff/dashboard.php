<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
    Laboratory Staff Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dashboard-header {
        background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px;
        box-shadow: 0 2px 6px rgba(15,23,42,.08);
        background-image: linear-gradient(135deg, rgba(76,175,80,.06), rgba(46,125,50,.06));
        margin-bottom: 16px;
    }
    .dashboard-header h1 { margin: 0 0 6px; color: #2e7d32; font-family: 'Playfair Display', serif; letter-spacing: -0.01em; font-size: 26px; }
    .dashboard-subtitle { margin: 0; color: #64748b; }
    .overview-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
    .overview-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; box-shadow: 0 2px 6px rgba(15,23,42,.08); position: relative; overflow: hidden; transition: all .25s ease; }
    .overview-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #2e7d32, #43a047); }
    .overview-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,24,40,.12); }
    .card-content h3 { margin: 0; font-size: 14px; font-weight: 700; color: #2e7d32; }
    .card-value { margin-top: 10px; font-size: 28px; font-weight: 800; color: #1f2937; }
</style>
    <div class="dashboard-header">
        <h1>Laboratory Staff Dashboard</h1>
        <p class="dashboard-subtitle">Laboratory Test Management</p>
    </div>

    <!-- Overview Cards -->
    <div class="overview-grid">
        <div class="overview-card" onclick="window.location.href='<?= site_url('labstaff/test-requests') ?>'" style="cursor: pointer;">
            <div class="card-content">
                <h3>Pending Tests</h3>
                <div class="card-value" id="pendingTests"><?= is_array($pendingTests) ? count($pendingTests) : (int)$pendingTests ?></div>
            </div>
        </div>
        <div class="overview-card" onclick="window.location.href='<?= site_url('labstaff/pending-specimens') ?>'" style="cursor: pointer;">
            <div class="card-content">
                <h3>Pending Specimens</h3>
                <div class="card-value" id="pendingSpecimens"><?= isset($pendingSpecimens) ? (is_array($pendingSpecimens) ? count($pendingSpecimens) : (int)$pendingSpecimens) : '0' ?></div>
            </div>
        </div>
        <div class="overview-card" onclick="window.location.href='<?= site_url('labstaff/completed-tests') ?>'" style="cursor: pointer;">
            <div class="card-content">
                <h3>Completed Today</h3>
                <div class="card-value" id="completedToday"><?= is_array($completedToday) ? count($completedToday) : (int)$completedToday ?></div>
            </div>
        </div>
        <div class="overview-card">
            <div class="card-content">
                <h3>Total Tests This Month</h3>
                <div class="card-value" id="monthlyTests"><?= is_array($monthlyTests) ? count($monthlyTests) : (int)$monthlyTests ?></div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="margin-top: 32px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <a href="<?= site_url('labstaff/test-requests') ?>" class="btn btn-primary btn-lg" style="padding: 16px; text-align: center; text-decoration: none; border-radius: 12px;">
            <i class="fas fa-flask fa-2x mb-2"></i><br>
            <strong>Test Requests</strong>
        </a>
        <a href="<?= site_url('labstaff/pending-specimens') ?>" class="btn btn-warning btn-lg" style="padding: 16px; text-align: center; text-decoration: none; border-radius: 12px;">
            <i class="fas fa-vial fa-2x mb-2"></i><br>
            <strong>Pending Specimens</strong>
        </a>
        <a href="<?= site_url('labstaff/completed-tests') ?>" class="btn btn-success btn-lg" style="padding: 16px; text-align: center; text-decoration: none; border-radius: 12px;">
            <i class="fas fa-check-circle fa-2x mb-2"></i><br>
            <strong>Completed Tests</strong>
        </a>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const labStatsEndpoint = '<?= site_url('labstaff/dashboard/stats') ?>';
    
    async function refreshLabDashboard() {
        try {
            const response = await fetch(labStatsEndpoint, {
                headers: { 'Accept': 'application/json' }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            // Update stat cards
            const setText = (id, value) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value ?? '0';
                }
            };
            
            setText('pendingTests', data.pending_tests ?? '0');
            setText('pendingSpecimens', data.pending_specimens ?? '0');
            setText('completedToday', data.completed_today ?? '0');
            setText('monthlyTests', data.monthly_tests ?? '0');
        } catch (error) {
            console.error('Error fetching Lab Staff Dashboard stats:', error);
        }
    }
    
    // Initial fetch
    refreshLabDashboard();
    
    // Refresh every 10 seconds
    setInterval(refreshLabDashboard, 10000);
    
    // Refresh when page becomes visible again
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            refreshLabDashboard();
        }
    });
});
</script>
<?= $this->endSection() ?>
