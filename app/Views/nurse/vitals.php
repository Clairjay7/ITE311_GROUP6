<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>🩺 Vital Signs</h2>
        <div class="actions">
            <button class="btn btn-primary" onclick="showAddVitalsModal()">Record New Vitals</button>
            <a href="<?= base_url('nurse/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="vitals-overview">
        <div class="grid grid-4">
            <div class="stat-card">
                <h5>Recorded Today</h5>
                <h3>18</h3>
            </div>
            <div class="stat-card">
                <h5>Critical Alerts</h5>
                <h3 style="color: #dc2626;">2</h3>
            </div>
            <div class="stat-card">
                <h5>Pending</h5>
                <h3 style="color: #f59e0b;">5</h3>
            </div>
            <div class="stat-card">
                <h5>Normal Range</h5>
                <h3 style="color: #16a34a;">11</h3>
            </div>
        </div>
    </div>

    <div class="vitals-table">
        <h4>Recent Vital Signs</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Room</th>
                        <th>BP</th>
                        <th>HR</th>
                        <th>Temp</th>
                        <th>RR</th>
                        <th>SpO2</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="critical">
                        <td><strong>08:45</strong></td>
                        <td>John Doe</td>
                        <td>102-B</td>
                        <td class="critical-value">160/105</td>
                        <td>98</td>
                        <td class="critical-value">38.5°C</td>
                        <td>22</td>
                        <td>96%</td>
                        <td><span class="status-badge status-critical">Critical</span></td>
                        <td>
                            <button class="btn btn-sm btn-danger">Alert Doctor</button>
                            <button class="btn btn-sm btn-info">View</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>08:30</strong></td>
                        <td>Jane Smith</td>
                        <td>101-A</td>
                        <td>120/80</td>
                        <td>78</td>
                        <td>36.7°C</td>
                        <td>18</td>
                        <td>98%</td>
                        <td><span class="status-badge status-normal">Normal</span></td>
                        <td>
                            <button class="btn btn-sm btn-info">View</button>
                            <button class="btn btn-sm btn-warning">Edit</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>08:15</strong></td>
                        <td>Mary Johnson</td>
                        <td>103-A</td>
                        <td>115/75</td>
                        <td>72</td>
                        <td>36.5°C</td>
                        <td>16</td>
                        <td>99%</td>
                        <td><span class="status-badge status-normal">Normal</span></td>
                        <td>
                            <button class="btn btn-sm btn-info">View</button>
                            <button class="btn btn-sm btn-warning">Edit</button>
                        </td>
                    </tr>
                    <tr class="warning">
                        <td><strong>08:00</strong></td>
                        <td>Robert Wilson</td>
                        <td>104-A</td>
                        <td class="warning-value">140/90</td>
                        <td>85</td>
                        <td>37.2°C</td>
                        <td>20</td>
                        <td>97%</td>
                        <td><span class="status-badge status-warning">Monitor</span></td>
                        <td>
                            <button class="btn btn-sm btn-warning">Recheck</button>
                            <button class="btn btn-sm btn-info">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="vitals-charts">
        <h4>Vital Signs Trends</h4>
        <div class="grid grid-2">
            <div class="chart-card">
                <h5>Blood Pressure Trends</h5>
                <div class="chart-placeholder">
                    <p>📈 Chart showing BP trends for selected patients</p>
                </div>
            </div>
            <div class="chart-card">
                <h5>Temperature Monitoring</h5>
                <div class="chart-placeholder">
                    <p>🌡️ Temperature trends and fever alerts</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Vitals Modal -->
<div id="addVitalsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Record Vital Signs</h3>
            <button class="close-btn" onclick="closeAddVitalsModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="vitalsForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="patient">Patient *</label>
                        <select id="patient" name="patient" required>
                            <option value="">Select Patient</option>
                            <option value="1">Jane Smith - 101-A</option>
                            <option value="2">John Doe - 102-B</option>
                            <option value="3">Mary Johnson - 103-A</option>
                            <option value="4">Robert Wilson - 104-A</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bp_systolic">Blood Pressure (Systolic) *</label>
                        <input type="number" id="bp_systolic" name="bp_systolic" placeholder="120" required>
                    </div>
                    <div class="form-group">
                        <label for="bp_diastolic">Blood Pressure (Diastolic) *</label>
                        <input type="number" id="bp_diastolic" name="bp_diastolic" placeholder="80" required>
                    </div>
                    <div class="form-group">
                        <label for="heart_rate">Heart Rate (bpm) *</label>
                        <input type="number" id="heart_rate" name="heart_rate" placeholder="72" required>
                    </div>
                    <div class="form-group">
                        <label for="temperature">Temperature (°C) *</label>
                        <input type="number" step="0.1" id="temperature" name="temperature" placeholder="36.5" required>
                    </div>
                    <div class="form-group">
                        <label for="respiratory_rate">Respiratory Rate *</label>
                        <input type="number" id="respiratory_rate" name="respiratory_rate" placeholder="16" required>
                    </div>
                    <div class="form-group">
                        <label for="oxygen_saturation">Oxygen Saturation (%)</label>
                        <input type="number" id="oxygen_saturation" name="oxygen_saturation" placeholder="98">
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Additional observations..."></textarea>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Vitals</button>
                    <button type="button" class="btn btn-secondary" onclick="closeAddVitalsModal()">Cancel</button>
                </div>
            </form>
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

.vitals-overview {
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

.vitals-table, .vitals-charts {
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

.table tr.critical {
    background-color: #fef2f2;
}

.table tr.warning {
    background-color: #fefce8;
}

.critical-value {
    color: #dc2626;
    font-weight: 600;
}

.warning-value {
    color: #f59e0b;
    font-weight: 600;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-critical { background: #fef2f2; color: #dc2626; }
.status-warning { background: #fefce8; color: #ca8a04; }
.status-normal { background: #f0fdf4; color: #16a34a; }

.chart-card {
    background: #f9fafb;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.chart-placeholder {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-style: italic;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
    margin: 0;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #6b7280;
}

.modal-body {
    padding: 1.5rem;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
}

.form-group input,
.form-group select,
.form-group textarea {
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 1rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
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
.btn-warning { background: #f59e0b; color: white; }
.btn-danger { background: #ef4444; color: white; }
</style>

<script>
function showAddVitalsModal() {
    document.getElementById('addVitalsModal').style.display = 'flex';
}

function closeAddVitalsModal() {
    document.getElementById('addVitalsModal').style.display = 'none';
}

document.getElementById('vitalsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Here you would normally send the data to the server
    alert('Vital signs recorded successfully!');
    closeAddVitalsModal();
    
    // Refresh the page or update the table
    location.reload();
});

// Close modal when clicking outside
document.getElementById('addVitalsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddVitalsModal();
    }
});
</script>
<?= $this->endSection() ?>
