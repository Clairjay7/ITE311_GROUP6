<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
    Accountant Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dashboard-header { 
        background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; 
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        background-image: linear-gradient(135deg, rgba(76,175,80,0.06), rgba(46,125,50,0.06));
        margin-bottom: 16px;
    }
    .dashboard-header h1 { 
        margin: 0 0 6px; color: #2e7d32; font-family: 'Playfair Display', serif; letter-spacing: -0.01em; 
        font-size: 26px;
    }
    .dashboard-subtitle { margin: 0; color: #64748b; }
    .overview-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
    .overview-card { 
        background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; 
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08); position: relative; overflow: hidden; 
        transition: all 0.25s ease;
    }
    .overview-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #2e7d32, #43a047); }
    .overview-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,24,40,0.12); }
    .card-content h3 { margin: 0; font-size: 14px; font-weight: 700; color: #2e7d32; }
    .card-value { margin-top: 10px; font-size: 28px; font-weight: 800; color: #1f2937; }
    @media (max-width: 600px) { .card-value { font-size: 24px; } }
    /* Print friendly */
    @media print { .overview-card { box-shadow: none; } }
</style>
    <div class="dashboard-header">
        <h1>Accountant Dashboard</h1>
        <p class="dashboard-subtitle">Financial Management & Billing</p>
    </div>

    <!-- Overview Cards -->
    <div class="overview-grid">
        <div class="overview-card">
            <div class="card-content">
                <h3>Today's Revenue</h3>
                <div class="card-value">₱<?= number_format($todayRevenue, 2) ?></div>
            </div>
        </div>
        <div class="overview-card">
            <div class="card-content">
                <h3>Pending Bills</h3>
                <div class="card-value"><?= is_array($pendingBills) ? count($pendingBills) : (int)$pendingBills ?></div>
            </div>
        </div>
        <div class="overview-card">
            <div class="card-content">
                <h3>Insurance Claims</h3>
                <div class="card-value"><?= is_array($insuranceClaims) ? count($insuranceClaims) : (int)$insuranceClaims ?></div>
            </div>
        </div>
        <div class="overview-card">
            <div class="card-content">
                <h3>Outstanding Balance</h3>
                <div class="card-value">₱<?= number_format($outstandingBalance, 2) ?></div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
