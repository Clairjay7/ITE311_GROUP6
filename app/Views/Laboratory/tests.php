<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>🧪 All Tests</h2>
        <div class="actions">
            <a href="<?= base_url('laboratory/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="tests-overview">
        <div class="grid grid-4">
            <div class="stat-card">
                <h5>Total Tests</h5>
                <h3><?= count($tests ?? []) ?></h3>
            </div>
            <div class="stat-card">
                <h5>Pending</h5>
                <h3 style="color: #f59e0b;">12</h3>
            </div>
            <div class="stat-card">
                <h5>In Progress</h5>
                <h3 style="color: #3b82f6;">8</h3>
            </div>
            <div class="stat-card">
                <h5>Completed</h5>
                <h3 style="color: #16a34a;">24</h3>
            </div>
        </div>
    </div>

    <div class="tests-table">
        <h4>Laboratory Tests</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Test ID</th>
                        <th>Patient</th>
                        <th>Test Type</th>
                        <th>Doctor</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>LAB-001</strong></td>
                        <td>John Doe</td>
                        <td>CBC</td>
                        <td>Dr. Smith</td>
                        <td><span class="priority-badge urgent">Urgent</span></td>
                        <td><span class="status-badge pending">Pending</span></td>
                        <td><?= date('M j, Y') ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary">Start</button>
                            <button class="btn btn-sm btn-info">View</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>LAB-002</strong></td>
                        <td>Jane Smith</td>
                        <td>Blood Sugar</td>
                        <td>Dr. Johnson</td>
                        <td><span class="priority-badge routine">Routine</span></td>
                        <td><span class="status-badge in-progress">In Progress</span></td>
                        <td><?= date('M j, Y') ?></td>
                        <td>
                            <button class="btn btn-sm btn-success">Complete</button>
                            <button class="btn btn-sm btn-info">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
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

.tests-overview {
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

.tests-table {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

.priority-badge, .status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.priority-badge.urgent { background: #fef2f2; color: #dc2626; }
.priority-badge.routine { background: #f0fdf4; color: #16a34a; }

.status-badge.pending { background: #fefce8; color: #ca8a04; }
.status-badge.in-progress { background: #dbeafe; color: #2563eb; }
.status-badge.completed { background: #f0fdf4; color: #16a34a; }

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
.btn-success { background: #16a34a; color: white; }
</style>
<?= $this->endSection() ?>
