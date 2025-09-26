<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>💊 All Prescriptions</h2>
        <div class="actions">
            <a href="<?= base_url('pharmacist/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="prescriptions-overview">
        <div class="grid grid-4">
            <div class="stat-card">
                <h5>Total Prescriptions</h5>
                <h3><?= count($prescriptions ?? []) ?></h3>
            </div>
            <div class="stat-card">
                <h5>Pending</h5>
                <h3 style="color: #f59e0b;">8</h3>
            </div>
            <div class="stat-card">
                <h5>Dispensed Today</h5>
                <h3 style="color: #16a34a;">24</h3>
            </div>
            <div class="stat-card">
                <h5>Cancelled</h5>
                <h3 style="color: #dc2626;">2</h3>
            </div>
        </div>
    </div>

    <div class="prescriptions-table">
        <h4>Prescription List</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Prescription ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>RX-001</strong></td>
                        <td>Jane Smith</td>
                        <td>Dr. Johnson</td>
                        <td>Amoxicillin</td>
                        <td>500mg x 3/day</td>
                        <td><span class="status-badge pending">Pending</span></td>
                        <td><?= date('M j, Y') ?></td>
                        <td>
                            <button class="btn btn-sm btn-success">Dispense</button>
                            <button class="btn btn-sm btn-info">View</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>RX-002</strong></td>
                        <td>John Doe</td>
                        <td>Dr. Smith</td>
                        <td>Paracetamol</td>
                        <td>500mg x 4/day</td>
                        <td><span class="status-badge dispensed">Dispensed</span></td>
                        <td><?= date('M j, Y') ?></td>
                        <td>
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

.prescriptions-overview {
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

.prescriptions-table {
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
.status-badge.dispensed { background: #f0fdf4; color: #16a34a; }
.status-badge.cancelled { background: #fef2f2; color: #dc2626; }

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
.btn-secondary { background: #6b7280; color: white; }
.btn-info { background: #06b6d4; color: white; }
</style>
<?= $this->endSection() ?>
