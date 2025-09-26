<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>💊 Medication Management</h2>
        <div class="actions">
            <a href="<?= base_url('nurse/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="medication-overview">
        <div class="grid grid-4">
            <div class="stat-card">
                <h5>Due Now</h5>
                <h3 style="color: #dc2626;">3</h3>
            </div>
            <div class="stat-card">
                <h5>Due in 1 Hour</h5>
                <h3 style="color: #f59e0b;">5</h3>
            </div>
            <div class="stat-card">
                <h5>Completed Today</h5>
                <h3 style="color: #16a34a;">24</h3>
            </div>
            <div class="stat-card">
                <h5>Missed</h5>
                <h3 style="color: #dc2626;">1</h3>
            </div>
        </div>
    </div>

    <div class="medication-schedule">
        <h4>Today's Medication Schedule</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Room</th>
                        <th>Medication</th>
                        <th>Dosage</th>
                        <th>Route</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="overdue">
                        <td><strong>08:00</strong></td>
                        <td>John Doe</td>
                        <td>102-B</td>
                        <td>Lisinopril</td>
                        <td>10mg</td>
                        <td>Oral</td>
                        <td><span class="status-badge status-overdue">Overdue</span></td>
                        <td>
                            <button class="btn btn-sm btn-danger">Give Now</button>
                            <button class="btn btn-sm btn-secondary">Skip</button>
                        </td>
                    </tr>
                    <tr class="due-now">
                        <td><strong>09:00</strong></td>
                        <td>Jane Smith</td>
                        <td>101-A</td>
                        <td>Amoxicillin</td>
                        <td>500mg</td>
                        <td>Oral</td>
                        <td><span class="status-badge status-due">Due Now</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary">Give</button>
                            <button class="btn btn-sm btn-secondary">Delay</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>09:30</strong></td>
                        <td>Mary Johnson</td>
                        <td>103-A</td>
                        <td>Paracetamol</td>
                        <td>500mg</td>
                        <td>Oral</td>
                        <td><span class="status-badge status-upcoming">Upcoming</span></td>
                        <td>
                            <button class="btn btn-sm btn-info">View</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>10:00</strong></td>
                        <td>Robert Wilson</td>
                        <td>104-A</td>
                        <td>Insulin</td>
                        <td>10 units</td>
                        <td>SC</td>
                        <td><span class="status-badge status-upcoming">Upcoming</span></td>
                        <td>
                            <button class="btn btn-sm btn-info">View</button>
                        </td>
                    </tr>
                    <tr class="completed">
                        <td><strong>07:00</strong></td>
                        <td>Jane Smith</td>
                        <td>101-A</td>
                        <td>Omeprazole</td>
                        <td>20mg</td>
                        <td>Oral</td>
                        <td><span class="status-badge status-completed">Completed</span></td>
                        <td>
                            <button class="btn btn-sm btn-info">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="quick-actions">
        <h4>Quick Actions</h4>
        <div class="action-grid">
            <button class="action-card">
                <div class="action-icon">💊</div>
                <div class="action-title">Record PRN Medication</div>
                <div class="action-desc">Give as-needed medication</div>
            </button>
            <button class="action-card">
                <div class="action-icon">⚠️</div>
                <div class="action-title">Report Adverse Reaction</div>
                <div class="action-desc">Document medication reaction</div>
            </button>
            <button class="action-card">
                <div class="action-icon">📋</div>
                <div class="action-title">Medication Reconciliation</div>
                <div class="action-desc">Review patient medications</div>
            </button>
            <button class="action-card">
                <div class="action-icon">🔄</div>
                <div class="action-title">Refresh Schedule</div>
                <div class="action-desc">Update medication times</div>
            </button>
        </div>
    </div>
</div>

<style>
.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.medication-overview {
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-card h5 {
    margin: 0 0 0.5rem 0;
    color: #6b7280;
    font-size: 0.9rem;
}

.stat-card h3 {
    margin: 0;
    font-size: 2rem;
}

.medication-schedule {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.table th,
.table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.table th {
    background-color: #f9fafb;
    font-weight: 600;
    color: #374151;
}

.table tr.overdue {
    background-color: #fef2f2;
}

.table tr.due-now {
    background-color: #fefce8;
}

.table tr.completed {
    opacity: 0.7;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-overdue { background: #fef2f2; color: #dc2626; }
.status-due { background: #fefce8; color: #ca8a04; }
.status-upcoming { background: #eff6ff; color: #2563eb; }
.status-completed { background: #f0fdf4; color: #16a34a; }

.quick-actions {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.action-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}

.action-card:hover {
    background: #f3f4f6;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.action-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.action-title {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.25rem;
}

.action-desc {
    font-size: 0.875rem;
    color: #6b7280;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
    font-size: 0.875rem;
    display: inline-block;
    margin-right: 0.25rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.btn-primary { background: #3b82f6; color: white; }
.btn-secondary { background: #6b7280; color: white; }
.btn-info { background: #06b6d4; color: white; }
.btn-danger { background: #ef4444; color: white; }
</style>
<?= $this->endSection() ?>
