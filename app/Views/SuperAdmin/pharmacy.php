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
    <h1 class="page-title">💊 Pharmacy Management</h1>
    <div class="actions-row">
        <a href="<?= base_url('super-admin/unified') ?>" class="btn btn-secondary">
            ← Back to Dashboard
        </a>
    </div>
</div>

<div class="alert">
    <strong>SuperAdmin Access:</strong> You are viewing the pharmacy management overview. For detailed pharmacy operations, individual pharmacist accounts should be used.
</div>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number" id="total-medications">0</div>
        <div class="stat-label">Total Medications</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" id="low-stock-items">0</div>
        <div class="stat-label">Low Stock Items</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" id="pending-prescriptions">0</div>
        <div class="stat-label">Pending Prescriptions</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" id="expiring-soon">0</div>
        <div class="stat-label">Expiring Soon</div>
    </div>
</div>

<!-- Management Sections -->
<div class="management-grid">
    <div class="management-card">
        <h3>
            📋 Prescription Management
            <span class="coming-soon">Coming Soon</span>
        </h3>
        <p>Manage patient prescriptions, dispensing, and medication tracking.</p>
        <ul class="feature-list">
            <li>View pending prescriptions</li>
            <li>Dispense medications</li>
            <li>Track prescription history</li>
            <li>Patient medication profiles</li>
        </ul>
        <a class="btn" href="<?= base_url('admin/prescriptions') ?>">Manage Prescriptions</a>
    </div>

    <div class="management-card">
        <h3>
            📦 Inventory Management
            <span class="coming-soon">Coming Soon</span>
        </h3>
        <p>Track medication stock levels, expiration dates, and reorder points.</p>
        <ul class="feature-list">
            <li>Monitor stock levels</li>
            <li>Track expiration dates</li>
            <li>Set reorder alerts</li>
            <li>Manage suppliers</li>
        </ul>
        <a class="btn" href="<?= base_url('super-admin/inventory') ?>">Manage Inventory</a>
    </div>

    <div class="management-card">
        <h3>
            📊 Reports & Analytics
            <span class="coming-soon">Coming Soon</span>
        </h3>
        <p>Generate reports on medication usage, costs, and pharmacy performance.</p>
        <ul class="feature-list">
            <li>Dispensing reports</li>
            <li>Cost analysis</li>
            <li>Usage statistics</li>
            <li>Compliance tracking</li>
        </ul>
        <a class="btn" href="<?= base_url('super-admin/reports') ?>">View Reports</a>
    </div>

    <div class="management-card">
        <h3>
            ⚙️ Pharmacy Settings
            <span class="coming-soon">Coming Soon</span>
        </h3>
        <p>Configure pharmacy operations, staff access, and system preferences.</p>
        <ul class="feature-list">
            <li>Staff permissions</li>
            <li>Notification settings</li>
            <li>Integration settings</li>
            <li>Backup & security</li>
        </ul>
        <a class="btn" href="<?= base_url('super-admin/pharmacy-settings') ?>">Manage Settings</a>
    </div>
</div>

<!-- Quick Actions -->
<div class="management-card">
    <h3>🚀 Quick Actions</h3>
    <p>Common pharmacy management tasks and shortcuts.</p>
    <div class="quick-actions-grid">
        <button class="btn btn-secondary" onclick="addNewMedication()">
            Add New Medication
        </button>
        <button class="btn btn-secondary" onclick="processPrescription()">
            Process Prescription
        </button>
        <button class="btn btn-secondary" onclick="checkStockLevels()">
            Check Stock Levels
        </button>
        <button class="btn btn-secondary" onclick="generateReport()">
            Generate Report
        </button>
    </div>
</div>

<!-- Recent Activity Table -->
<div class="table-container">
    <div class="table-header">
        📋 Recent Pharmacy Activity
    </div>
    <div class="table-content" id="recent-activity">
        Loading recent pharmacy activities...
    </div>
</div>

<script>
// Pharmacy Management Functions
document.addEventListener('DOMContentLoaded', function() {
    console.log('Pharmacy Management loaded - SuperAdmin view');
    loadPharmacyStats();
    loadRecentActivity();
});

function loadPharmacyStats() {
    // Simulate loading pharmacy statistics
    setTimeout(() => {
        document.getElementById('total-medications').textContent = '156';
        document.getElementById('low-stock-items').textContent = '8';
        document.getElementById('pending-prescriptions').textContent = '23';
        document.getElementById('expiring-soon').textContent = '5';
    }, 500);
}

function loadRecentActivity() {
    // Simulate loading recent activity
    setTimeout(() => {
        const activities = [
            { time: '10:30 AM', action: 'Prescription dispensed', patient: 'John Doe', medication: 'Amoxicillin 500mg' },
            { time: '10:15 AM', action: 'Stock updated', item: 'Paracetamol 500mg', quantity: '+50 units' },
            { time: '09:45 AM', action: 'Low stock alert', item: 'Insulin Pen', current: '3 units' },
            { time: '09:30 AM', action: 'Prescription received', patient: 'Jane Smith', doctor: 'Dr. Johnson' },
            { time: '09:00 AM', action: 'Inventory check', status: 'Completed', items: '45 medications verified' }
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
                                ${activity.patient ? `Patient: ${activity.patient}` : ''}
                                ${activity.medication ? ` - ${activity.medication}` : ''}
                                ${activity.item ? `Item: ${activity.item}` : ''}
                                ${activity.quantity ? ` (${activity.quantity})` : ''}
                                ${activity.current ? ` - Current: ${activity.current}` : ''}
                                ${activity.doctor ? ` - ${activity.doctor}` : ''}
                                ${activity.status ? `Status: ${activity.status}` : ''}
                                ${activity.items ? ` - ${activity.items}` : ''}
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
        
        document.getElementById('recent-activity').innerHTML = activityHtml;
    }, 800);
}

// Management Functions
function showPrescriptionManagement() {
    window.location.href = '<?= base_url('admin/prescriptions') ?>';
}

function showInventoryManagement() {
    window.location.href = '<?= base_url('super-admin/inventory') ?>';
}

function showPharmacyReports() {
    window.location.href = '<?= base_url('super-admin/reports') ?>';
}

function showPharmacySettings() {
    window.location.href = '<?= base_url('super-admin/pharmacy-settings') ?>';
}

// Quick Action Functions
function addNewMedication() {
    alert('Add New Medication\\n\\nThis feature will allow you to:\\n• Add new medications to inventory\\n• Set stock levels and reorder points\\n• Configure pricing and supplier info\\n• Set expiration tracking\\n\\nFeature coming in next development phase!');
}

function processPrescription() {
    alert('Process Prescription\\n\\nThis feature will allow you to:\\n• View pending prescriptions\\n• Verify medication availability\\n• Dispense medications to patients\\n• Update prescription status\\n\\nFeature coming in next development phase!');
}

function checkStockLevels() {
    alert('Check Stock Levels\\n\\nThis feature will show:\\n• Current inventory levels\\n• Low stock alerts\\n• Expiring medications\\n• Reorder recommendations\\n\\nFeature coming in next development phase!');
}

function generateReport() {
    alert('Generate Report\\n\\nThis feature will provide:\\n• Dispensing activity reports\\n• Inventory status reports\\n• Financial summaries\\n• Compliance reports\\n\\nFeature coming in next development phase!');
}
</script>

<?= $this->endSection() ?>
