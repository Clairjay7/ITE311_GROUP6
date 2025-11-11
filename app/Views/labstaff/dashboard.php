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
        <div class="overview-card">
            <div class="card-content">
                <h3>Pending Tests</h3>
                <div class="card-value"><?= is_array($pendingTests) ? count($pendingTests) : (int)$pendingTests ?></div>
            </div>
        </div>
        <div class="overview-card">
            <div class="card-content">
                <h3>Completed Today</h3>
                <div class="card-value"><?= is_array($completedToday) ? count($completedToday) : (int)$completedToday ?></div>
            </div>
        </div>
        <div class="overview-card">
            <div class="card-content">
                <h3>Total Tests This Month</h3>
                <div class="card-value"><?= is_array($monthlyTests) ? count($monthlyTests) : (int)$monthlyTests ?></div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
