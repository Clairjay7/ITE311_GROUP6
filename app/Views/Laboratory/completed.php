<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>✅ Completed Tests</h2>
        <div class="actions">
            <a href="<?= base_url('laboratory/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="completed-tests">
        <h4>Recently Completed Tests</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Test ID</th>
                        <th>Patient</th>
                        <th>Test Type</th>
                        <th>Completed</th>
                        <th>Results</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>LAB-005</strong></td>
                        <td>Sarah Davis</td>
                        <td>CBC</td>
                        <td>2 hours ago</td>
                        <td><span class="result-badge normal">Normal</span></td>
                        <td>
                            <button class="btn btn-sm btn-info">View Results</button>
                            <button class="btn btn-sm btn-success">Release</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>LAB-006</strong></td>
                        <td>Mike Brown</td>
                        <td>Glucose</td>
                        <td>1 hour ago</td>
                        <td><span class="result-badge abnormal">Abnormal</span></td>
                        <td>
                            <button class="btn btn-sm btn-info">View Results</button>
                            <button class="btn btn-sm btn-warning">Review</button>
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

.completed-tests {
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

.result-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.result-badge.normal { background: #f0fdf4; color: #16a34a; }
.result-badge.abnormal { background: #fef2f2; color: #dc2626; }

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
.btn-success { background: #16a34a; color: white; }
.btn-warning { background: #f59e0b; color: white; }
.btn-secondary { background: #6b7280; color: white; }
</style>
<?= $this->endSection() ?>
