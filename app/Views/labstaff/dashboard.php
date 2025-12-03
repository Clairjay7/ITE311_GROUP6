<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
    Laboratory Staff Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dashboard-header {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(46, 125, 50, 0.2);
        color: white;
    }
    .dashboard-header h1 { 
        margin: 0 0 8px; 
        font-size: 28px; 
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .dashboard-subtitle { 
        margin: 0; 
        opacity: 0.9;
        font-size: 14px;
    }
    .overview-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
        gap: 20px; 
        margin-bottom: 24px;
    }
    .overview-card { 
        background: white; 
        border: 1px solid #e5e7eb; 
        border-radius: 16px; 
        padding: 24px; 
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08); 
        position: relative; 
        overflow: hidden; 
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .overview-card::before { 
        content: ''; 
        position: absolute; 
        top: 0; 
        left: 0; 
        right: 0; 
        height: 4px; 
        background: linear-gradient(90deg, #2e7d32, #43a047); 
    }
    .overview-card:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 8px 24px rgba(16, 24, 40, 0.15); 
    }
    .overview-card.urgent::before {
        background: linear-gradient(90deg, #ef4444, #f59e0b);
    }
    .overview-card.warning::before {
        background: linear-gradient(90deg, #f59e0b, #fbbf24);
    }
    .overview-card.success::before {
        background: linear-gradient(90deg, #10b981, #34d399);
    }
    .card-content { display: flex; justify-content: space-between; align-items: center; }
    .card-content-left { flex: 1; }
    .card-content h3 { 
        margin: 0 0 8px; 
        font-size: 14px; 
        font-weight: 600; 
        color: #64748b; 
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .card-value { 
        font-size: 32px; 
        font-weight: 800; 
        color: #1f2937;
        line-height: 1;
    }
    .card-icon {
        font-size: 40px;
        opacity: 0.2;
        color: #2e7d32;
    }
    .overview-card.urgent .card-icon { color: #ef4444; }
    .overview-card.warning .card-icon { color: #f59e0b; }
    .overview-card.success .card-icon { color: #10b981; }
    
    .modern-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
    }
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f1f5f9;
    }
    .card-header h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }
    .table-modern th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px;
        text-align: left;
        border-bottom: 2px solid #e5e7eb;
    }
    .table-modern td {
        padding: 14px 12px;
        border-bottom: 1px solid #f1f5f9;
        color: #1f2937;
        font-size: 14px;
    }
    .table-modern tr:hover {
        background: #f8fafc;
    }
    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-in-progress { background: #dbeafe; color: #1e40af; }
    .badge-completed { background: #d1fae5; color: #065f46; }
    .badge-routine { background: #f1f5f9; color: #64748b; }
    .badge-urgent { background: #fee2e2; color: #991b1b; }
    .badge-stat { background: #fef2f2; color: #dc2626; font-weight: 700; }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 24px;
    }
    .action-btn {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }
    .action-btn:hover {
        border-color: #2e7d32;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.15);
    }
    .action-btn i {
        font-size: 32px;
        color: #2e7d32;
    }
    .action-btn strong {
        color: #1f2937;
        font-size: 14px;
    }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #64748b;
    }
    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.3;
    }
</style>

<div class="dashboard-header">
    <div>
        <h1><i class="fas fa-flask"></i> Laboratory Staff Dashboard</h1>
        <p class="dashboard-subtitle">Manage laboratory tests and specimens</p>
    </div>
</div>

<!-- Overview Cards -->
<div class="overview-grid">
    <div class="overview-card urgent" onclick="window.location.href='<?= site_url('labstaff/test-requests') ?>?priority=urgent'">
        <div class="card-content">
            <div class="card-content-left">
                <h3>Urgent Tests</h3>
                <div class="card-value" id="urgentTests">0</div>
            </div>
            <div class="card-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    <div class="overview-card" onclick="window.location.href='<?= site_url('labstaff/test-requests') ?>'">
        <div class="card-content">
            <div class="card-content-left">
                <h3>Pending Tests</h3>
                <div class="card-value" id="pendingTests"><?= is_array($pendingTests) ? count($pendingTests) : (int)$pendingTests ?></div>
            </div>
            <div class="card-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="overview-card warning" onclick="window.location.href='<?= site_url('labstaff/pending-specimens') ?>'">
        <div class="card-content">
            <div class="card-content-left">
                <h3>Pending Specimens</h3>
                <div class="card-value" id="pendingSpecimens"><?= isset($pendingSpecimens) ? (is_array($pendingSpecimens) ? count($pendingSpecimens) : (int)$pendingSpecimens) : '0' ?></div>
            </div>
            <div class="card-icon">
                <i class="fas fa-vial"></i>
            </div>
        </div>
    </div>
    <div class="overview-card success" onclick="window.location.href='<?= site_url('labstaff/completed-tests') ?>'">
        <div class="card-content">
            <div class="card-content-left">
                <h3>Completed Today</h3>
                <div class="card-value" id="completedToday"><?= is_array($completedToday) ? count($completedToday) : (int)$completedToday ?></div>
            </div>
            <div class="card-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="overview-card">
        <div class="card-content">
            <div class="card-content-left">
                <h3>Total This Month</h3>
                <div class="card-value" id="monthlyTests"><?= is_array($monthlyTests) ? count($monthlyTests) : (int)$monthlyTests ?></div>
            </div>
            <div class="card-icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Pending Tests -->
<div class="modern-card">
    <div class="card-header">
        <h2><i class="fas fa-list-ul"></i> Recent Pending Tests</h2>
        <a href="<?= site_url('labstaff/test-requests') ?>" style="color: #2e7d32; text-decoration: none; font-weight: 600; font-size: 14px;">
            View All <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div id="pendingTestsList">
        <div class="empty-state">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading...</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <a href="<?= site_url('labstaff/test-requests') ?>" class="action-btn">
        <i class="fas fa-flask"></i>
        <strong>Test Requests</strong>
    </a>
    <a href="<?= site_url('labstaff/pending-specimens') ?>" class="action-btn">
        <i class="fas fa-vial"></i>
        <strong>Pending Specimens</strong>
    </a>
    <a href="<?= site_url('labstaff/completed-tests') ?>" class="action-btn">
        <i class="fas fa-check-circle"></i>
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
            setText('urgentTests', data.urgent_tests ?? '0');
            
            // Update pending tests list
            updatePendingTestsList(data.pending_tests_list || []);
        } catch (error) {
            console.error('Error fetching Lab Staff Dashboard stats:', error);
        }
    }
    
    function updatePendingTestsList(tests) {
        const container = document.getElementById('pendingTestsList');
        if (!container) return;
        
        if (!tests || tests.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>No pending tests</p>
                </div>
            `;
            return;
        }
        
        let html = '<table class="table-modern"><thead><tr>';
        html += '<th>Patient</th><th>Test Name</th><th>Priority</th><th>Requested By</th><th>Date</th><th>Status</th>';
        html += '</tr></thead><tbody>';
        
        tests.forEach(test => {
            const patientName = (test.firstname || '') + ' ' + (test.lastname || '');
            const priorityClass = test.priority === 'stat' ? 'badge-stat' : 
                                 test.priority === 'urgent' ? 'badge-urgent' : 'badge-routine';
            const statusClass = test.status === 'pending' ? 'badge-pending' : 
                              test.status === 'in_progress' ? 'badge-in-progress' : 'badge-completed';
            const requestedBy = test.requested_by === 'doctor' ? 
                              (test.doctor_name ? 'Dr. ' + test.doctor_name : 'Doctor') :
                              (test.nurse_name || 'Nurse');
            const requestedDate = test.requested_date ? new Date(test.requested_date).toLocaleDateString() : 'N/A';
            
            html += `<tr onclick="window.location.href='<?= site_url('labstaff/test-requests') ?>'" style="cursor: pointer;">`;
            html += `<td><strong>${escapeHtml(patientName.trim() || 'Unknown')}</strong></td>`;
            html += `<td>${escapeHtml(test.test_name || 'N/A')}</td>`;
            html += `<td><span class="badge ${priorityClass}">${escapeHtml(test.priority || 'routine')}</span></td>`;
            html += `<td>${escapeHtml(requestedBy)}</td>`;
            html += `<td>${escapeHtml(requestedDate)}</td>`;
            html += `<td><span class="badge ${statusClass}">${escapeHtml(test.status || 'pending')}</span></td>`;
            html += `</tr>`;
        });
        
        html += '</tbody></table>';
        container.innerHTML = html;
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Initial fetch
    refreshLabDashboard();
    
    // Refresh every 10 seconds for real-time updates
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
