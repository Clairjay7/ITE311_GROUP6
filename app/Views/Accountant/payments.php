<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>💳 Payment Management</h2>
        <div class="actions">
            <a href="<?= base_url('accountant/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="payments-section">
        <h4>Recent Payments</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Patient</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>PAY-001</strong></td>
                        <td>Sarah Davis</td>
                        <td>₱3,200</td>
                        <td>Cash</td>
                        <td><?= date('M j, Y') ?></td>
                        <td><span class="status-badge completed">Completed</span></td>
                        <td>
                            <button class="btn btn-sm btn-info">View Receipt</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>PAY-002</strong></td>
                        <td>Mike Brown</td>
                        <td>₱1,800</td>
                        <td>Card</td>
                        <td><?= date('M j, Y') ?></td>
                        <td><span class="status-badge completed">Completed</span></td>
                        <td>
                            <button class="btn btn-sm btn-info">View Receipt</button>
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

.payments-section {
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

.status-badge.completed { background: #f0fdf4; color: #16a34a; }
.status-badge.pending { background: #fefce8; color: #ca8a04; }

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.875rem;
    margin-right: 0.25rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.btn-info { background: #06b6d4; color: white; }
.btn-secondary { background: #6b7280; color: white; }
</style>
<?= $this->endSection() ?>
