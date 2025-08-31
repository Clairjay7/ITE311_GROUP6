<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - Hospital Management System</title>
    <link rel="stylesheet" href="/ITE311-GROUP6/public/css/superadmin_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <h2 class="dashboard-title">Welcome, User!</h2>
        <div class="widgets-row">
            <!-- Total Patients -->
            <div class="widget-card">
                <div class="widget-icon" style="color:#2563eb;">👥</div>
                <div class="widget-title">Total Patients</div>
                <div class="widget-info">Admitted: <strong>12</strong></div>
                <div class="widget-info">Discharged: <strong>5</strong></div>
                <div class="widget-info">Current: <strong>7</strong></div>
            </div>
            <!-- Appointments Overview -->
            <div class="widget-card">
                <div class="widget-icon" style="color:#22c55e;">📅</div>
                <div class="widget-title">Appointments</div>
                <div class="widget-info">Today: <strong>2</strong></div>
                <div class="widget-info">Upcoming: <strong>4</strong></div>
                <div class="widget-info">Canceled: <strong>1</strong></div>
            </div>
            <!-- Laboratory Reports Overview -->
            <div class="widget-card">
                <div class="widget-icon" style="color:#facc15;">🧪</div>
                <div class="widget-title">Lab Reports</div>
                <div class="widget-info">Pending: <strong>1</strong></div>
                <div class="widget-info">Completed: <strong>3</strong></div>
            </div>
        </div>
        <div class="widgets-row">
            <!-- Billing Summary -->
            <div class="widget-card">
                <div class="widget-icon" style="color:#38bdf8;">💵</div>
                <div class="widget-title">Billing Summary</div>
                <div class="widget-info">Today: <strong>₱1,500</strong></div>
                <div class="widget-info">This Month: <strong>₱12,000</strong></div>
                <div class="widget-info">This Year: <strong>₱120,000</strong></div>
            </div>
            <!-- Pharmacy & Inventory Alerts -->
            <div class="widget-card">
                <div class="widget-icon" style="color:#ef4444;">💊</div>
                <div class="widget-title">Pharmacy Alerts</div>
                <div class="widget-info text-danger">Low Stock: <strong>Paracetamol</strong></div>
                <div class="widget-info text-danger">Expired: <strong>Ibuprofen</strong></div>
            </div>
            <!-- System Security & Backups -->
            <div class="widget-card">
                <div class="widget-icon" style="color:#64748b;">🛡️</div>
                <div class="widget-title">System Security</div>
                <div class="widget-info">Your data is secure.</div>
                <div class="widget-info">Last backup: <strong>Today</strong></div>
            </div>
        </div>
        <div class="logout-row">
            <a href="/ITE311-GROUP6/public/logout" class="logout-btn">Logout</a>
        </div>
    </div>
</body>
</html>