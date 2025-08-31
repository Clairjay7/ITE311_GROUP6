<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Staff Dashboard - Hospital Management System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .dashboard-header {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .dashboard-header h1, .dashboard-header h2 {
            margin: 0;
            color: #2c3e50;
        }
        .widgets-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .widget-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .widget-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            color: white;
        }
        .bg-success {
            background-color: #10b981;
        }
        .bg-warning {
            background-color: #f59e0b;
        }
        .bg-danger {
            background-color: #ef4444;
        }
        .logout-link {
            float: right;
            color: #e74c3c;
            text-decoration: none;
            font-weight: 500;
        }
        .logout-link:hover {
            text-decoration: underline;
        }
        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        li:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Hospital Management System</h1>
            <div style="float: right;">
                <a href="<?= base_url('logout') ?>" class="logout-link">Logout</a>
            </div>
            <div style="clear: both;"></div>
        </div>
        <div class="dashboard-header">
            <h2>💻 IT Staff Dashboard</h2>
            <p style="color: #7f8c8d; margin: 5px 0 0;">System Administration & Support</p>
        </div>

        <div class="widgets-row">
            <!-- System Health Status -->
            <div class="widget-card">
                <div class="widget-title">❤️‍🩹 System Health Status</div>
                <ul>
                    <li>Server: <span class="badge bg-success">Online</span></li>
                    <li>Database: <span class="badge bg-success">Connected</span></li>
                    <li>Uptime: <span class="badge bg-info text-dark">99.99%</span></li>
                </ul>
            </div>
            <!-- User Activity Logs -->
            <div class="widget-card">
                <div class="widget-title">📋 User Activity Logs</div>
                <ul style="max-height: 120px; overflow-y: auto;">
                    <li>admin1 logged in - 08:00 AM</li>
                    <li>doctor1 viewed patient records - 08:15 AM</li>
                    <li>nurse1 updated vitals - 08:30 AM</li>
                </ul>
            </div>
            <!-- Manage Backups & Restore Points -->
            <div class="widget-card">
                <div class="widget-title">💾 Backups & Restore Points</div>
                <ul>
                    <li>Last Backup: <span class="badge bg-success">Today 02:00 AM</span></li>
                    <li>Restore Point: <span class="badge bg-secondary">2024-08-27</span></li>
                </ul>
                <button class="btn btn-outline-success btn-sm">Create Backup</button>
                <button class="btn btn-outline-warning btn-sm">Restore</button>
            </div>
        </div>

        <div class="widgets-row">
            <!-- Security Alerts & Logs -->
            <div class="widget-card">
                <div class="widget-title">🛡️ Security Alerts & Logs</div>
                <ul style="max-height: 100px; overflow-y: auto;">
                    <li>Failed login attempt - user: unknown - 07:55 AM</li>
                    <li>Suspicious activity detected - 08:10 AM</li>
                </ul>
            </div>
            <!-- Manage Role-Based Access Controls -->
            <div class="widget-card">
                <div class="widget-title">👥 Role-Based Access Controls</div>
                <ul>
                    <li>
                        Doctor - <span class="badge bg-success">Active</span>
                        <button class="btn btn-outline-primary btn-sm">Edit</button>
                    </li>
                    <li>
                        Nurse - <span class="badge bg-success">Active</span>
                        <button class="btn btn-outline-primary btn-sm">Edit</button>
                    </li>
                </ul>
                <button class="btn btn-outline-success btn-sm">Add Role</button>
            </div>
        </div>

        <div class="widgets-row">
            <!-- Update Notifications & Version Control -->
            <div class="widget-card" style="flex:2;">
                <div class="widget-title">🔄 Update Notifications & Version Control</div>
                <ul>
                    <li>System Version: <span class="badge bg-info text-dark">v1.2.3</span></li>
                    <li>Last Update: <span class="badge bg-success">2024-08-27</span></li>
                </ul>
                <button class="btn btn-outline-info btn-sm">Check for Updates</button>
            </div>
        </div>
    </div>
</body>
</html>