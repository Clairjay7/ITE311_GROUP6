<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>👥 My Patients</h2>
        <div class="actions">
            <button class="btn btn-primary" onclick="showAddPatientModal()">Add New Patient</button>
            <a href="<?= base_url('doctor/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="patients-overview">
        <div class="grid grid-4">
            <div class="stat-card">
                <h5>Total Patients</h5>
                <h3><?= count($patients ?? []) ?></h3>
            </div>
            <div class="stat-card">
                <h5>Critical</h5>
                <h3 style="color: #dc2626;">2</h3>
            </div>
            <div class="stat-card">
                <h5>Stable</h5>
                <h3 style="color: #16a34a;">8</h3>
            </div>
            <div class="stat-card">
                <h5>Follow-ups Due</h5>
                <h3 style="color: #f59e0b;">3</h3>
            </div>
        </div>
    </div>

    <div class="patients-table">
        <h4>Patient List</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Last Visit</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($patients)): ?>
                        <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td><strong><?= esc($patient['patient_id']) ?></strong></td>
                            <td><?= esc($patient['first_name'] . ' ' . $patient['last_name']) ?></td>
                            <td><?= date_diff(date_create($patient['date_of_birth']), date_create('today'))->y ?></td>
                            <td><?= ucfirst(esc($patient['gender'])) ?></td>
                            <td><?= esc($patient['phone']) ?></td>
                            <td>
                                <span class="status-badge status-<?= esc($patient['status']) ?>">
                                    <?= ucfirst(esc($patient['status'])) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($patient['updated_at'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= base_url('doctor/emr?patient=' . $patient['id']) ?>" class="btn btn-sm btn-primary" title="View EMR">📋</a>
                                    <button class="btn btn-sm btn-info" onclick="viewPatient(<?= $patient['id'] ?>)" title="View Details">👁️</button>
                                    <a href="<?= base_url('doctor/prescriptions?patient=' . $patient['id']) ?>" class="btn btn-sm btn-warning" title="Prescriptions">💊</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">No patients found</td>
                        </tr>
                    <?php endif; ?>
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

.patients-overview {
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
    color: #1f2937;
    font-size: 2rem;
}

.patients-table {
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

.status-active { background: #f0fdf4; color: #16a34a; }
.status-inactive { background: #f3f4f6; color: #6b7280; }
.status-critical { background: #fef2f2; color: #dc2626; }

.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
    font-size: 0.875rem;
    display: inline-block;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.btn-primary { background: #3b82f6; color: white; }
.btn-secondary { background: #6b7280; color: white; }
.btn-info { background: #06b6d4; color: white; }
.btn-warning { background: #f59e0b; color: white; }

.text-center {
    text-align: center;
}
</style>

<script>
function showAddPatientModal() {
    alert('Add Patient modal would open here.');
}

function viewPatient(patientId) {
    alert('Viewing patient details for ID: ' + patientId);
}
</script>
<?= $this->endSection() ?>


