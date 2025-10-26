<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .stat-number {
        font-size: 32px;
        font-weight: 700;
        color: #2563eb;
        margin-bottom: 8px;
        display: block;
    }
    .stat-label {
        color: #64748b;
        font-size: 14px;
    }
    .management-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    .management-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .management-card h3 {
        margin: 0 0 12px 0;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 18px;
    }
    .management-card p {
        color: #64748b;
        margin: 0 0 16px 0;
        line-height: 1.5;
    }
    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0 0 20px 0;
    }
    .feature-list li {
        padding: 6px 0;
        color: #64748b;
        font-size: 14px;
    }
    .feature-list li:before {
        content: "✓";
        color: #10b981;
        font-weight: bold;
        margin-right: 8px;
    }
    .coming-soon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    .alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #f59e0b;
        background: #fef3c7;
        color: #92400e;
    }
    .btn {
        background: #2563eb;
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
        display: inline-block;
        transition: background 0.2s;
    }
    .btn:hover {
        background: #1d4ed8;
    }
    .btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }
    .btn-secondary {
        background: #6b7280;
    }
    .btn-secondary:hover {
        background: #4b5563;
    }
    .actions-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin-top: 16px;
    }
    .table-container {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 20px;
    }
    .table-header {
        background: #f8fafc;
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
        font-weight: 600;
        color: #1f2937;
    }
    .table-content {
        padding: 20px;
        text-align: center;
        color: #64748b;
    }
</style>

<div class="page-header">
    <h1 class="page-title">🔬 Laboratory Management</h1>
    <div class="actions-row">
        <a href="<?= base_url('super-admin/unified') ?>" class="btn btn-secondary">
            ← Back to Dashboard
        </a>
    </div>
</div>

<div class="alert">
    <strong>SuperAdmin Access:</strong> You are viewing the laboratory management overview. For detailed laboratory operations, individual laboratory staff accounts should be used.
</div>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number" id="pending-tests">0</div>
        <div class="stat-label">Pending Tests</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" id="completed-today">0</div>
        <div class="stat-label">Completed Today</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" id="in-progress">0</div>
        <div class="stat-label">In Progress</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" id="equipment-active">0</div>
        <div class="stat-label">Equipment Active</div>
    </div>
</div>

<!-- Management Sections -->
<div class="management-grid">
    <div class="management-card">
        <h3>
            🧪 Test Management
            <span class="coming-soon">Coming Soon</span>
        </h3>
        <p>Manage laboratory tests, results, and patient samples.</p>
        <ul class="feature-list">
            <li>Process test requests</li>
            <li>Record test results</li>
            <li>Sample tracking</li>
            <li>Quality control</li>
        </ul>
        <a class="btn" href="<?= base_url('super-admin/tests') ?>">Manage Tests</a>
    </div>

    <div class="management-card">
        <h3>
            🔬 Equipment Management
            <span class="coming-soon">Coming Soon</span>
        </h3>
        <p>Monitor laboratory equipment, maintenance, and calibration.</p>
        <ul class="feature-list">
            <li>Equipment status monitoring</li>
            <li>Maintenance scheduling</li>
            <li>Calibration tracking</li>
            <li>Usage statistics</li>
        </ul>
        <a class="btn" href="<?= base_url('super-admin/equipment') ?>">Manage Equipment</a>
    </div>

    <div class="management-card">
        <h3>
            📊 Lab Reports & Analytics
            <span class="coming-soon">Coming Soon</span>
        </h3>
        <p>Generate laboratory reports and analyze test performance.</p>
        <ul class="feature-list">
            <li>Test result reports</li>
            <li>Performance analytics</li>
            <li>Quality metrics</li>
            <li>Turnaround time analysis</li>
        </ul>
        <a class="btn" href="<?= base_url('super-admin/lab-reports') ?>">View Reports</a>
    </div>

    <div class="management-card">
        <h3>
            ⚙️ Laboratory Settings
            <span class="coming-soon">Coming Soon</span>
        </h3>
        <p>Configure laboratory operations and system preferences.</p>
        <ul class="feature-list">
            <li>Test parameters</li>
            <li>Reference ranges</li>
            <li>Staff permissions</li>
            <li>Integration settings</li>
        </ul>
        <button class="btn" onclick="showLabSettings()">Manage Settings</button>
    </div>
</div>

<!-- Quick Actions -->
<div class="management-card">
    <h3>🚀 Quick Actions</h3>
    <p>Common laboratory management tasks and shortcuts.</p>
    <div class="quick-actions-grid">
        <button class="btn btn-secondary" onclick="addNewTest()">
            Add New Test
        </button>
        <button class="btn btn-secondary" onclick="processResults()">
            Process Results
        </button>
        <button class="btn btn-secondary" onclick="checkEquipment()">
            Check Equipment
        </button>
        <button class="btn btn-secondary" onclick="generateLabReport()">
            Generate Report
        </button>
    </div>
</div>

<!-- Recent Activity Table -->
<div class="table-container">
    <div class="table-header">
        🔬 Recent Laboratory Activity
    </div>
    <div class="table-content" id="recent-lab-activity">
        Loading recent laboratory activities...
    </div>
</div>

<script>
// Laboratory Management Functions
document.addEventListener('DOMContentLoaded', function() {
    console.log('Laboratory Management loaded - SuperAdmin view');
    loadLabStats();
    loadRecentLabActivity();
});

function loadLabStats() {
    // Simulate loading laboratory statistics
    setTimeout(() => {
        document.getElementById('pending-tests').textContent = '12';
        document.getElementById('completed-today').textContent = '28';
        document.getElementById('in-progress').textContent = '7';
        document.getElementById('equipment-active').textContent = '15';
    }, 500);
}

function loadRecentLabActivity() {
    // Simulate loading recent activity
    setTimeout(() => {
        const activities = [
            { time: '11:15 AM', action: 'Test completed', test: 'Complete Blood Count', patient: 'John Doe', technician: 'Lab Tech A' },
            { time: '11:00 AM', action: 'Sample received', test: 'Lipid Profile', patient: 'Jane Smith', status: 'Processing' },
            { time: '10:45 AM', action: 'Equipment calibrated', equipment: 'Hematology Analyzer', technician: 'Lab Tech B' },
            { time: '10:30 AM', action: 'Results verified', test: 'Liver Function Test', patient: 'Mike Johnson', doctor: 'Dr. Wilson' },
            { time: '10:15 AM', action: 'Quality control', test: 'Chemistry Panel', status: 'Passed', batch: 'QC-2024-001' }
        ];
        
        const activityHtml = `
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8fafc; text-align: left;">
                        <th style="padding: 12px; border-bottom: 1px solid #e2e8f0;">Time</th>
                        <th style="padding: 12px; border-bottom: 1px solid #e2e8f0;">Action</th>
                        <th style="padding: 12px; border-bottom: 1px solid #e2e8f0;">Details</th>
                    </tr>
                </thead>
                <tbody>
                    ${activities.map(activity => `
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #f1f5f9; color: #64748b;">${activity.time}</td>
                            <td style="padding: 12px; border-bottom: 1px solid #f1f5f9; font-weight: 500;">${activity.action}</td>
                            <td style="padding: 12px; border-bottom: 1px solid #f1f5f9; color: #64748b;">
                                ${activity.test ? `Test: ${activity.test}` : ''}
                                ${activity.patient ? ` - Patient: ${activity.patient}` : ''}
                                ${activity.equipment ? `Equipment: ${activity.equipment}` : ''}
                                ${activity.technician ? ` - ${activity.technician}` : ''}
                                ${activity.doctor ? ` - ${activity.doctor}` : ''}
                                ${activity.status ? ` - Status: ${activity.status}` : ''}
                                ${activity.batch ? ` - Batch: ${activity.batch}` : ''}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
        
        document.getElementById('recent-lab-activity').innerHTML = activityHtml;
    }, 800);
}

// Management Functions
function showTestManagement() {
    window.location.href = '<?= base_url('super-admin/tests') ?>';
}

function showEquipmentManagement() {
    window.location.href = '<?= base_url('super-admin/equipment') ?>';
}

function showLabReports() {
    window.location.href = '<?= base_url('super-admin/lab-reports') ?>';
}

function showLabSettings() {
    const popup = window.open('', '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
    popup.document.write(`
        <!DOCTYPE html>
        <html>
            <head>
                <title>Laboratory Settings</title>
                <style>
                    body { font-family: 'Segoe UI', Arial, sans-serif; padding: 20px; background: #f8fafc; }
                    .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                    .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; }
                    .title { font-size: 28px; font-weight: 700; color: #2563eb; margin: 0; }
                    .coming-soon { background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 20px; text-align: center; color: #92400e; }
                    .btn { background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1 class="title">⚙️ Laboratory Settings</h1>
                    </div>
                    <div class="coming-soon">
                        <h3>🚧 Feature Under Development</h3>
                        <p>The laboratory settings system is currently being developed. This will include:</p>
                        <ul style="text-align: left; max-width: 400px; margin: 0 auto;">
                            <li>Test parameters and reference ranges</li>
                            <li>Staff permissions and access control</li>
                            <li>Integration with external systems</li>
                            <li>Workflow configuration options</li>
                            <li>Quality control settings</li>
                        </ul>
                        <p style="margin-top: 20px;"><strong>Expected completion:</strong> Next development phase</p>
                    </div>
                    <div style="text-align: center; margin-top: 30px;">
                        <button class="btn" onclick="window.close()">✖️ Close Window</button>
                    </div>
                </div>
            </body>
        </html>
    `);
}

// Quick Action Functions
function addNewTest() {
    alert('Add New Test\\n\\nThis feature will allow you to:\\n• Create new test requests\\n• Assign to laboratory staff\\n• Set priority and urgency\\n• Track sample collection\\n\\nFeature coming in next development phase!');
}

function processResults() {
    alert('Process Results\\n\\nThis feature will allow you to:\\n• Enter test results\\n• Verify and validate data\\n• Generate result reports\\n• Notify requesting physicians\\n\\nFeature coming in next development phase!');
}

function checkEquipment() {
    alert('Check Equipment\\n\\nThis feature will show:\\n• Equipment status and availability\\n• Maintenance schedules\\n• Calibration due dates\\n• Performance metrics\\n\\nFeature coming in next development phase!');
}

function generateLabReport() {
    alert('Generate Lab Report\\n\\nThis feature will provide:\\n• Test result summaries\\n• Performance analytics\\n• Quality control reports\\n• Workload statistics\\n\\nFeature coming in next development phase!');
}
</script>

<?= $this->endSection() ?>
