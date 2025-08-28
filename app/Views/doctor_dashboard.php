<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="/GROUP6/public/css/doctor_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>👨‍⚕️ Doctor Dashboard</h2>
            <a href="/GROUP6/public/logout" class="logout-link">Logout</a>
        </div>

        <!-- Notifications -->
        <div class="widget-card" style="background:#fef9c3;">
            <div class="widget-title" style="color:#b45309;">
                ⚠️ Urgent Notification
            </div>
            <div>
                <strong>Urgent:</strong> Lab result for John Doe is ready for review.
            </div>
        </div>

        <div class="widgets-row">
            <!-- Today's Appointments -->
            <div class="widget-card">
                <div class="widget-title">📅 Today's Appointments</div>
                <ul>
                    <li><strong>09:00 AM</strong> - Jane Smith (Consultation)</li>
                    <li><strong>10:30 AM</strong> - John Doe (Follow-up)</li>
                    <li><strong>01:00 PM</strong> - Maria Garcia (Check-up)</li>
                </ul>
            </div>
            <!-- Pending Lab Results -->
            <div class="widget-card">
                <div class="widget-title">🧪 Pending Lab Results</div>
                <ul>
                    <li>
                        John Doe - CBC
                        <button class="btn btn-outline-success">View</button>
                    </li>
                    <li>
                        Maria Garcia - X-Ray
                        <button class="btn btn-outline-success">View</button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="widgets-row">
            <!-- Prescriptions Panel -->
            <div class="widget-card">
                <div class="widget-title">💊 Prescriptions Panel</div>
                <button class="btn btn-primary btn-sm" style="margin-bottom:10px;">＋ Create Prescription</button>
                <ul>
                    <li>Jane Smith - Amoxicillin 500mg (Active)</li>
                    <li>John Doe - Paracetamol 500mg (Completed)</li>
                </ul>
            </div>
            <!-- Patient History (EHR) -->
            <div class="widget-card">
                <div class="widget-title">📖 Patient History (EHR)</div>
                <input type="text" style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:10px;" placeholder="Search patient...">
                <ul>
                    <li>
                        Jane Smith - Last visit: 2024-08-01
                        <button class="btn btn-outline-info" style="float:right;">View</button>
                    </li>
                    <li>
                        John Doe - Last visit: 2024-07-28
                        <button class="btn btn-outline-info" style="float:right;">View</button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="widgets-row">
            <!-- Diagnostic Requests -->
            <div class="widget-card">
                <div class="widget-title">📝 Diagnostic Requests</div>
                <button class="btn btn-outline-secondary btn-sm" style="margin-bottom:10px;">＋ New Request</button>
                <ul>
                    <li>Jane Smith - Blood Test (Sent)</li>
                </ul>
            </div>
            <!-- Medical Notes & Treatment Plans -->
            <div class="widget-card">
                <div class="widget-title">🩺 Medical Notes & Treatment Plans</div>
                <textarea style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:10px;" rows="2" placeholder="Add new note..."></textarea>
                <ul>
                    <li>John Doe - Continue antibiotics for 5 days.</li>
                    <li>Maria Garcia - Schedule follow-up in 2 weeks.</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>