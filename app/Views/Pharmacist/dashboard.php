<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pharmacist Dashboard</title>
    <link rel="stylesheet" href="/GROUP6/public/css/pharmacist_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>💊 Pharmacist Dashboard</h2>
            <a href="<?= base_url('logout') ?>" class="logout-link">Logout</a>
        </div>

        <div class="widgets-row">
            <!-- Pending Prescription Requests -->
            <div class="widget-card">
                <div class="widget-title">📝 Pending Prescription Requests</div>
                <ul>
                    <li>
                        Jane Smith - Amoxicillin 500mg
                        <button class="btn btn-outline-success btn-sm">Dispense</button>
                    </li>
                    <li>
                        John Doe - Paracetamol 500mg
                        <button class="btn btn-outline-success btn-sm">Dispense</button>
                    </li>
                </ul>
            </div>
            <!-- Dispensed vs. Pending Medicines -->
            <div class="widget-card">
                <div class="widget-title">✅ Dispensed vs. Pending Medicines</div>
                <ul>
                    <li>
                        Amoxicillin 500mg - <span class="badge bg-success">Dispensed</span>
                    </li>
                    <li>
                        Paracetamol 500mg - <span class="badge bg-warning text-dark">Pending</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="widgets-row">
            <!-- Inventory & Stock Management -->
            <div class="widget-card">
                <div class="widget-title">📦 Inventory & Stock Management</div>
                <ul>
                    <li>
                        Amoxicillin 500mg - <span class="badge bg-danger">Low Stock</span>
                    </li>
                    <li>
                        Paracetamol 500mg - <span class="badge bg-success">In Stock</span>
                    </li>
                </ul>
            </div>
            <!-- Expired & Reorder Alerts -->
            <div class="widget-card">
                <div class="widget-title">⚠️ Expired & Reorder Alerts</div>
                <ul>
                    <li>
                        Ibuprofen 200mg - <span class="badge bg-danger">Expired</span>
                        <button class="btn btn-outline-danger btn-sm">Remove</button>
                    </li>
                    <li>
                        Cetirizine 10mg - <span class="badge bg-warning text-dark">Reorder Needed</span>
                        <button class="btn btn-outline-primary btn-sm">Reorder</button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="widgets-row">
            <!-- Sales & Pharmacy Billing Reports -->
            <div class="widget-card" style="flex:2;">
                <div class="widget-title">📊 Sales & Pharmacy Billing Reports</div>
                <ul>
                    <li>Today: ₱2,500</li>
                    <li>This Month: ₱45,000</li>
                    <li>This Year: ₱520,000</li>
                </ul>
                <button class="btn btn-outline-info btn-sm" style="margin-top:10px;">Export Report</button>
            </div>
        </div>
    </div>
</body>
</html>