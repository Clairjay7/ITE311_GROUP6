<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>📋 Billing Management</h2>
        <div class="actions">
            <a href="<?= base_url('accountant/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="billing-overview">
        <div class="grid grid-4">
            <div class="stat-card">
                <h5>Total Bills</h5>
                <h3><?= count($bills ?? []) ?></h3>
            </div>
            <div class="stat-card">
                <h5>Pending</h5>
                <h3 style="color: #f59e0b;">15</h3>
            </div>
            <div class="stat-card">
                <h5>Paid Today</h5>
                <h3 style="color: #16a34a;">8</h3>
            </div>
            <div class="stat-card">
                <h5>Overdue</h5>
                <h3 style="color: #dc2626;">3</h3>
            </div>
        </div>
    </div>

    <div class="billing-table">
        <h4>Patient Bills</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Bill ID</th>
                        <th>Patient</th>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>INV-001</strong></td>
                        <td>John Doe</td>
                        <td>Surgery Consultation</td>
                        <td>₱5,500</td>
                        <td><span class="status-badge overdue">Overdue</span></td>
                        <td><?= date('M j, Y', strtotime('-2 days')) ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning">Send Reminder</button>
                            <button class="btn btn-sm btn-info">View</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>INV-002</strong></td>
                        <td>Jane Smith</td>
                        <td>Laboratory Tests</td>
                        <td>₱2,300</td>
                        <td><span class="status-badge pending">Pending</span></td>
                        <td><?= date('M j, Y') ?></td>
                        <td>
                            <button class="btn btn-sm btn-success">Mark Paid</button>
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

.billing-overview {
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

.billing-table {
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

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.pending { background: #fefce8; color: #ca8a04; }
.status-badge.paid { background: #f0fdf4; color: #16a34a; }
.status-badge.overdue { background: #fef2f2; color: #dc2626; }

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

.btn-success { background: #16a34a; color: white; }
.btn-warning { background: #f59e0b; color: white; }
.btn-secondary { background: #6b7280; color: white; }
.btn-info { background: #06b6d4; color: white; }
</style>
<?= $this->endSection() ?>
