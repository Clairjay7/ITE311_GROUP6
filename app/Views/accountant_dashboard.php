<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Accountant Dashboard</title>
    <link rel="stylesheet" href="/GROUP6/public/css/accountant_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>Accountant Dashboard</h2>
            <a href="/GROUP6/public/logout" style="color:#e53e3e;text-decoration:none;font-weight:bold;">Logout</a>
        </div>
        <div class="widgets-row">
            <!-- Total Income & Expenses -->
            <div class="widget-card">
                <div class="widget-title">
                    <span class="icon">📈</span> Total Income & Expenses
                </div>
                <ul>
                    <li><strong>Today:</strong> Income ₱5,000 / Expenses ₱2,000</li>
                    <li><strong>This Week:</strong> Income ₱30,000 / Expenses ₱12,000</li>
                    <li><strong>This Month:</strong> Income ₱120,000 / Expenses ₱50,000</li>
                </ul>
            </div>
            <!-- Pending Bills & Payments -->
            <div class="widget-card">
                <div class="widget-title">
                    <span class="icon">🧾</span> Pending Bills & Payments
                </div>
                <ul>
                    <li>
                        Jane Smith - ₱1,200 <span class="badge bg-danger">Unpaid</span>
                        <button class="btn btn-outline-success">Mark Paid</button>
                    </li>
                    <li>
                        John Doe - ₱800 <span class="badge bg-warning text-dark">Pending</span>
                        <button class="btn btn-outline-success">Mark Paid</button>
                    </li>
                </ul>
            </div>
            <!-- Insurance Claim Management -->
            <div class="widget-card">
                <div class="widget-title">
                    <span class="icon">🛡️</span> Insurance Claims
                </div>
                <ul>
                    <li>
                        Maria Garcia - ₱2,000 <span class="badge bg-warning text-dark">Processing</span>
                        <button class="btn btn-outline-primary">Update</button>
                    </li>
                    <li>
                        Alex Cruz - ₱1,500 <span class="badge bg-success">Approved</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="widgets-row">
            <!-- Refunds & Adjustments -->
            <div class="widget-card">
                <div class="widget-title">
                    <span class="icon">🔄</span> Refunds & Adjustments
                </div>
                <ul>
                    <li>
                        John Doe - Refund ₱200 <span class="badge bg-info text-dark">Processed</span>
                    </li>
                    <li>
                        Jane Smith - Adjustment ₱100 <span class="badge bg-warning text-dark">Pending</span>
                    </li>
                </ul>
            </div>
            <!-- Financial Reports Export -->
            <div class="widget-card">
                <div class="widget-title">
                    <span class="icon">📄</span> Financial Reports
                </div>
                <button class="btn btn-outline-info">Export PDF</button>
                <button class="btn btn-outline-success">Export Excel</button>
            </div>
        </div>

        <div class="widgets-row">
            <!-- Branch-wise Revenue Reports -->
            <div class="widget-card" style="flex:2;">
                <div class="widget-title">
                    <span class="icon">🏢</span> Branch-wise Revenue Reports
                </div>
                <ul>
                    <li>Davao City Branch: ₱80,000</li>
                    <li>Tagum Branch: ₱40,000</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>