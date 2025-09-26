<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>👥 My Patients</h2>
        <div class="actions">
            <a href="<?= base_url('nurse/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="patients-overview">
        <div class="grid grid-4">
            <div class="stat-card">
                <h5>Assigned Patients</h5>
                <h3>12</h3>
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
                <h5>Discharged Today</h5>
                <h3>2</h3>
            </div>
        </div>
    </div>

    <div class="patients-table">
        <h4>Patient List</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Room/Bed</th>
                        <th>Patient Name</th>
                        <th>Age</th>
                        <th>Diagnosis</th>
                        <th>Status</th>
                        <th>Last Vitals</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>101-A</strong></td>
                        <td>Jane Smith</td>
                        <td>42</td>
                        <td>Pneumonia</td>
                        <td><span class="status-badge status-stable">Stable</span></td>
                        <td>BP: 120/80, HR: 78, T: 36.7°C</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-info" title="View Details">👁️</button>
                                <button class="btn btn-sm btn-primary" title="Record Vitals">🩺</button>
                                <button class="btn btn-sm btn-warning" title="Add Note">📝</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>102-B</strong></td>
                        <td>John Doe</td>
                        <td>57</td>
                        <td>Hypertension</td>
                        <td><span class="status-badge status-critical">Critical</span></td>
                        <td>BP: 150/100, HR: 98, T: 38.2°C</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-info" title="View Details">👁️</button>
                                <button class="btn btn-sm btn-primary" title="Record Vitals">🩺</button>
                                <button class="btn btn-sm btn-warning" title="Add Note">📝</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>103-A</strong></td>
                        <td>Mary Johnson</td>
                        <td>35</td>
                        <td>Post-Surgery</td>
                        <td><span class="status-badge status-stable">Stable</span></td>
                        <td>BP: 115/75, HR: 72, T: 36.5°C</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-info" title="View Details">👁️</button>
                                <button class="btn btn-sm btn-primary" title="Record Vitals">🩺</button>
                                <button class="btn btn-sm btn-warning" title="Add Note">📝</button>
                            </div>
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

.status-stable { background: #f0fdf4; color: #16a34a; }
.status-critical { background: #fef2f2; color: #dc2626; }
.status-moderate { background: #fefce8; color: #ca8a04; }

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
</style>
<?= $this->endSection() ?>
