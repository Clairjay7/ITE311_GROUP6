<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receptionist Dashboard</title>
    <link rel="stylesheet" href="/GROUP6/public/css/receptionist_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>🧑‍💼 Receptionist Dashboard</h2>
            <a href="<?= base_url('logout') ?>" class="logout-link">Logout</a>
        </div>

        <!-- Notifications -->
        <div class="notification-card">
            <span class="icon">🔔</span>
            <strong>Notification:</strong> New patient arrival for Dr. Smith at 10:00 AM.
        </div>

        <div class="widgets-row">
            <!-- New Patient Registration Form -->
            <div class="widget-card">
                <div class="widget-title">📝 New Patient Registration</div>
                <form>
                    <input type="text" class="form-input" placeholder="Full Name">
                    <input type="text" class="form-input" placeholder="Contact Number">
                    <input type="date" class="form-input" placeholder="Birthdate">
                    <input type="text" class="form-input" placeholder="Address">
                    <button type="submit" class="btn btn-outline-success btn-sm">Register</button>
                </form>
            </div>
            <!-- Today's Appointments & Queue -->
            <div class="widget-card">
                <div class="widget-title">📅 Today's Appointments & Queue</div>
                <ul>
                    <li>
                        09:00 AM - Jane Smith <span class="badge bg-success">Checked-in</span>
                        <button class="btn btn-outline-primary btn-sm">Check-out</button>
                    </li>
                    <li>
                        10:00 AM - John Doe <span class="badge bg-warning text-dark">Waiting</span>
                        <button class="btn btn-outline-success btn-sm">Check-in</button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="widgets-row">
            <!-- Schedule Management -->
            <div class="widget-card">
                <div class="widget-title">⏰ Schedule Management</div>
                <button class="btn btn-outline-primary btn-sm" style="margin-bottom:10px;">＋ Create Appointment</button>
                <ul>
                    <li>
                        Jane Smith - 09:00 AM
                        <button class="btn btn-outline-warning btn-sm">Update</button>
                        <button class="btn btn-outline-danger btn-sm">Cancel</button>
                    </li>
                    <li>
                        John Doe - 10:00 AM
                        <button class="btn btn-outline-warning btn-sm">Update</button>
                        <button class="btn btn-outline-danger btn-sm">Cancel</button>
                    </li>
                </ul>
            </div>
            <!-- Payment & Billing Status -->
            <div class="widget-card">
                <div class="widget-title">💵 Payment & Billing Status</div>
                <ul>
                    <li>
                        Jane Smith - <span class="badge bg-success">Paid</span>
                    </li>
                    <li>
                        John Doe - <span class="badge bg-danger">Unpaid</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Patient Check-in & Check-out (Quick Actions) -->
        <div class="widgets-row">
            <div class="widget-card" style="flex:2;">
                <div class="widget-title">🚪 Patient Check-in & Check-out</div>
                <button class="btn btn-outline-success btn-sm">Check-in Patient</button>
                <button class="btn btn-outline-danger btn-sm">Check-out Patient</button>
            </div>
        </div>
    </div>
</body>
</html>