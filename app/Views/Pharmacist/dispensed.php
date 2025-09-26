<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>✅ Dispensed Prescriptions</h2>
        <div class="actions">
            <a href="<?= base_url('pharmacist/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="dispensed-prescriptions">
        <h4>Recently Dispensed Prescriptions</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Prescription ID</th>
                        <th>Patient</th>
                        <th>Medicine</th>
                        <th>Dispensed</th>
                        <th>Dispensed By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>RX-005</strong></td>
                        <td>Sarah Davis</td>
                        <td>Amoxicillin 500mg</td>
                        <td>2 hours ago</td>
                        <td>Pharmacist 1</td>
                        <td>
                            <button class="btn btn-sm btn-info">View Receipt</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>RX-006</strong></td>
                        <td>Mike Brown</td>
                        <td>Paracetamol 500mg</td>
                        <td>1 hour ago</td>
                        <td>Pharmacist 1</td>
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

.dispensed-prescriptions {
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
