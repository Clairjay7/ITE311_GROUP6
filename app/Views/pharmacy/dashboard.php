<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Pharmacy Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dashboard-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
    .mini-card {
        background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px;
        box-shadow: 0 2px 6px rgba(15,23,42,.08); position: relative; overflow: hidden; transition: all .25s ease;
    }
    .mini-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #2e7d32, #43a047); }
    .mini-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,24,40,.12); }
    .mini-title { margin: 0; font-size: 14px; font-weight: 700; color: #2e7d32; }
    .mini-value { margin-top: 8px; font-size: 28px; font-weight: 800; color: #1f2937; }

    .composite-card { grid-column: 1 / -1; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 2px 6px rgba(15,23,42,.08); }
    .composite-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid #e5e7eb; }
    .composite-title { font-family: 'Playfair Display', serif; color: #2e7d32; font-size: 20px; letter-spacing: -0.01em; }
    .btn.btn-sm { background: linear-gradient(135deg, #4caf50, #66bb6a); color: #fff; border: none; padding: 6px 10px; border-radius: 8px; font-size: 12px; text-decoration: none; }
    .metric-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; padding: 16px 18px; }
    .metric-item { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; }
    .metric-title { font-size: 13px; color: #2e7d32; font-weight: 700; margin: 0; }
    .metric-value { margin-top: 6px; font-size: 22px; font-weight: 800; color: #1f2937; }
    .text-danger { color: #b91c1c !important; }
    .text-warning { color: #b45309 !important; }
    @media (max-width: 600px) { .mini-value { font-size: 24px; } .metric-value { font-size: 20px; } }
</style>
<div class="dashboard-summary">
    <div class="mini-card">
        <div class="mini-title">Prescriptions Today</div>
        <div class="mini-value"><?= $prescriptionsToday ?? '0' ?></div>
    </div>
    
    <div class="mini-card">
        <div class="mini-title">Pending Fulfillment</div>
        <div class="mini-value"><?= $pendingFulfillment ?? '0' ?></div>
    </div>
    
    <div class="mini-card">
        <div class="mini-title">Low Stock Items</div>
        <div class="mini-value"><?= $lowStockItems ?? '0' ?></div>
    </div>
    
    <div class="mini-card">
        <div class="mini-title">Total Inventory</div>
        <div class="mini-value"><?= $totalInventory ?? '0' ?></div>
    </div>

    <div class="composite-card inventory-card">
        <div class="composite-header">
            <div class="composite-title">Inventory Status</div>
            <a href="/pharmacy/inventory" class="btn btn-sm">View All</a>
        </div>
        <div class="metric-grid">
            <div class="metric-item">
                <div class="metric-title">Critical Items</div>
                <div class="metric-value text-danger" id="criticalItems"><?= $criticalItems ?? '0' ?></div>
            </div>
            <div class="metric-item">
                <div class="metric-title">Expiring Soon</div>
                <div class="metric-value text-warning" id="expiringSoon"><?= $expiringSoon ?? '0' ?></div>
            </div>
            <div class="metric-item">
                <div class="metric-title">Out of Stock</div>
                <div class="metric-value" id="outOfStock"><?= $outOfStock ?? '0' ?></div>
            </div>
            <div class="metric-item">
                <div class="metric-title">Categories</div>
                <div class="metric-value" id="categoriesCount"><?= $categoriesCount ?? '0' ?></div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pharmacyStatsEndpoint = '<?= site_url('pharmacy/dashboard/stats') ?>';
    
    async function refreshPharmacyDashboard() {
        try {
            const response = await fetch(pharmacyStatsEndpoint, {
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
            
            setText('prescriptionsToday', data.prescriptions_today ?? '0');
            setText('pendingFulfillment', data.pending_fulfillment ?? '0');
            setText('lowStockItems', data.low_stock_items ?? '0');
            setText('totalInventory', data.total_inventory ?? '0');
            setText('criticalItems', data.critical_items ?? '0');
            setText('expiringSoon', data.expiring_soon ?? '0');
            setText('outOfStock', data.out_of_stock ?? '0');
            setText('categoriesCount', data.categories_count ?? '0');
        } catch (error) {
            console.error('Error fetching Pharmacy Dashboard stats:', error);
        }
    }
    
    // Initial fetch
    refreshPharmacyDashboard();
    
    // Refresh every 10 seconds
    setInterval(refreshPharmacyDashboard, 10000);
    
    // Refresh when page becomes visible again
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            refreshPharmacyDashboard();
        }
    });
});
</script>
<?= $this->endSection() ?>
