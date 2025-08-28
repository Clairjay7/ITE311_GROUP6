<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Staff Dashboard</title>
    <link rel="stylesheet" href="/GROUP6/public/css/it_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>💻 IT Staff Dashboard</h2>
            <a href="/GROUP6/public/logout" class="logout-link">Logout</a>
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