<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nurse Dashboard</title>
    <link rel="stylesheet" href="/GROUP6/css/nurse_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>🧑‍⚕️ Nurse Dashboard</h2>
            <a href="/GROUP6/logout" class="logout-link">Logout</a>
        </div>

        <!-- Emergency Alerts -->
        <div class="widget-card" style="background:#fee2e2;">
            <div class="widget-title" style="color:#b91c1c;">
                🚨 Emergency Alert
            </div>
            <div>
                <strong>Emergency:</strong> Patient John Doe's blood pressure is critical!
            </div>
        </div>

        <div class="widgets-row">
            <!-- Assigned Patients List -->
            <div class="widget-card">
                <div class="widget-title">👥 Assigned Patients</div>
                <ul>
                    <li>Jane Smith (Room 101)</li>
                    <li>John Doe (Room 102)</li>
                    <li>Maria Garcia (Room 103)</li>
                </ul>
            </div>
            <!-- Vital Signs Monitoring -->
            <div class="widget-card">
                <div class="widget-title">💓 Vital Signs Monitoring</div>
                <ul>
                    <li>
                        Jane Smith - BP: 120/80, Temp: 36.7°C
                        <button class="btn btn-outline-primary btn-sm">Update</button>
                    </li>
                    <li>
                        John Doe - BP: 150/100, Temp: 38.2°C
                        <button class="btn btn-outline-primary btn-sm">Update</button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="widgets-row">
            <!-- Treatment Schedules -->
            <div class="widget-card">
                <div class="widget-title">⏰ Treatment Schedules</div>
                <ul>
                    <li>Jane Smith - 8:00 AM: Antibiotics, 12:00 PM: IV Fluids</li>
                    <li>John Doe - 9:00 AM: Insulin, 3:00 PM: BP Check</li>
                </ul>
            </div>
            <!-- Doctor’s Orders -->
            <div class="widget-card">
                <div class="widget-title">📋 Doctor’s Orders</div>
                <ul>
                    <li>Jane Smith - Continue antibiotics for 5 days.</li>
                    <li>John Doe - Monitor BP every 4 hours.</li>
                </ul>
            </div>
        </div>

        <div class="widgets-row">
            <!-- Patient Status Updates -->
            <div class="widget-card" style="flex:2;">
                <div class="widget-title">ℹ️ Patient Status Updates</div>
                <ul>
                    <li>Jane Smith - <span class="badge bg-success">Admitted</span></li>
                    <li>John Doe - <span class="badge bg-warning text-dark">Transferred</span></li>
                    <li>Maria Garcia - <span class="badge bg-danger">Discharged</span></li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>