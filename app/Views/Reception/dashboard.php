<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>
<style>
    .dashboard-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
    .mini-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; box-shadow: 0 2px 6px rgba(15,23,42,.08); position: relative; overflow: hidden; transition: all .25s ease; }
    .mini-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #2e7d32, #43a047); }
    .mini-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,24,40,.12); }
    .mini-title { margin: 0; font-size: 14px; font-weight: 700; color: #2e7d32; }
    .mini-value { margin-top: 8px; font-size: 28px; font-weight: 800; color: #1f2937; }
    .mini-subtext { margin-top: 4px; font-size: 12px; color: #64748b; }
    @media (max-width: 600px) { .mini-value { font-size: 24px; } }
</style>
<div class="dashboard-summary">
    <div class="mini-card">
        <div class="mini-title">Today's Appointments</div>
        <div class="mini-value">24</div>
        <div class="mini-subtext">+3 from yesterday</div>
    </div>
    
    <div class="mini-card">
        <div class="mini-title">Waiting Patients</div>
        <div class="mini-value">8</div>
        <div class="mini-subtext">In queue</div>
    </div>

    <div class="mini-card">
        <div class="mini-title">New Registrations</div>
        <div class="mini-value">5</div>
        <div class="mini-subtext">Today</div>
    </div>

    <div class="mini-card">
        <div class="mini-title">Pending Payments</div>
        <div class="mini-value">₱12,500</div>
        <div class="mini-subtext">3 invoices</div>
    </div>
</div>
<?= $this->endSection() ?>