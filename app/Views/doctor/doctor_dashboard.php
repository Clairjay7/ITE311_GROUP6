<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Doctor Dashboard' ?> - Hospital Management System</title>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.9);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(5px);
        }
        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 0.5rem;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            min-height: 100vh;
        }
        .user-info {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        .user-info h5 {
            margin: 0;
            color: white;
            font-weight: 600;
        }
        .user-info small {
            color: rgba(255, 255, 255, 0.8);
        }
        .sidebar .nav {
            padding: 1rem;
        }
        .logout-link {
            position: absolute;
            bottom: 1rem;
            left: 1rem;
            right: 1rem;
        }
        .dashboard-content {
            padding: 20px;
        }
        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .welcome-section h2 {
            margin: 0 0 10px 0;
            font-size: 2rem;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-right: 15px;
        }
        .stat-content h3 {
            margin: 0;
            font-size: 1.8rem;
            color: #2563eb;
            font-weight: bold;
        }
        .stat-content p {
            margin: 5px 0 0 0;
            color: #6b7280;
        }
        .dashboard-widget {
            background: white;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .widget-title {
            background: #f8fafc;
            padding: 15px 20px;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }
        .widget-content {
            padding: 20px;
        }
        .appointment-item, .update-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .appointment-item:last-child, .update-item:last-child {
            border-bottom: none;
        }
        .appointment-time {
            background: #2563eb;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            font-weight: 600;
            margin-right: 15px;
            min-width: 80px;
            text-align: center;
        }
        .appointment-details {
            flex: 1;
        }
        .appointment-details strong {
            display: block;
            color: #374151;
        }
        .appointment-type {
            color: #6b7280;
            font-size: 0.9rem;
        }
        .update-icon {
            font-size: 1.5rem;
            margin-right: 15px;
            width: 30px;
            text-align: center;
        }
        .update-details strong {
            display: block;
            color: #374151;
        }
        .update-details span {
            color: #6b7280;
            font-size: 0.9rem;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .action-btn {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8fafc;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            text-decoration: none;
            color: #374151;
            transition: all 0.2s;
        }
        .action-btn:hover {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
            transform: translateY(-2px);
        }
        .action-icon {
            font-size: 1.5rem;
            margin-right: 10px;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                min-height: auto;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="user-info">
                    <h5><?= esc($user['full_name'] ?? 'User') ?></h5>
                    <small><?= esc($user['role'] ?? 'Role') ?></small>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= base_url($rolePath . '/dashboard') ?>">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url($rolePath . '/patients') ?>">
                            <i class="bi bi-person-lines-fill"></i> Patients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url($rolePath . '/appointments') ?>">
                            <i class="bi bi-calendar-check"></i> Appointments
                        </a>
                    </li>
                </ul>
                <div class="logout-link">
                    <a class="nav-link text-danger" href="<?= base_url('logout') ?>">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <div class="dashboard-content">
                    <div class="row">
                        <!-- Welcome Section -->
                        <div class="col-12">
                            <div class="welcome-section">
                                <h2>👨‍⚕️ Welcome, Dr. <?= $user['full_name'] ?? 'Doctor' ?></h2>
                                <p>Manage your patients, appointments, and medical records from your dashboard.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Quick Stats -->
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon">👥</div>
                                <div class="stat-content">
                                    <h3>25</h3>
                                    <p>Active Patients</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon">📅</div>
                                <div class="stat-content">
                                    <h3>8</h3>
                                    <p>Today's Appointments</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon">📋</div>
                                <div class="stat-content">
                                    <h3>12</h3>
                                    <p>Pending Reports</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon">🏥</div>
                                <div class="stat-content">
                                    <h3>3</h3>
                                    <p>Emergency Cases</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Today's Appointments -->
                        <div class="col-md-6">
                            <div class="dashboard-widget">
                                <div class="widget-title">📅 Today's Appointments</div>
                                <div class="widget-content">
                                    <div class="appointment-item">
                                        <div class="appointment-time">09:00 AM</div>
                                        <div class="appointment-details">
                                            <strong>John Smith</strong>
                                            <span class="appointment-type">Follow-up</span>
                                        </div>
                                    </div>
                                    <div class="appointment-item">
                                        <div class="appointment-time">10:30 AM</div>
                                        <div class="appointment-details">
                                            <strong>Maria Garcia</strong>
                                            <span class="appointment-type">Consultation</span>
                                        </div>
                                    </div>
                                    <div class="appointment-item">
                                        <div class="appointment-time">02:00 PM</div>
                                        <div class="appointment-details">
                                            <strong>Robert Johnson</strong>
                                            <span class="appointment-type">Surgery Prep</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Patient Updates -->
                        <div class="col-md-6">
                            <div class="dashboard-widget">
                                <div class="widget-title">📊 Recent Patient Updates</div>
                                <div class="widget-content">
                                    <div class="update-item">
                                        <div class="update-icon">💊</div>
                                        <div class="update-details">
                                            <strong>Sarah Wilson</strong>
                                            <span>Medication updated</span>
                                        </div>
                                    </div>
                                    <div class="update-item">
                                        <div class="update-icon">🔬</div>
                                        <div class="update-details">
                                            <strong>Mike Davis</strong>
                                            <span>Lab results ready</span>
                                        </div>
                                    </div>
                                    <div class="update-item">
                                        <div class="update-icon">📝</div>
                                        <div class="update-details">
                                            <strong>Lisa Brown</strong>
                                            <span>Discharge summary completed</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Quick Actions -->
                        <div class="col-12">
                            <div class="dashboard-widget">
                                <div class="widget-title">⚡ Quick Actions</div>
                                <div class="widget-content">
                                    <div class="quick-actions">
                                        <a href="<?= base_url('doctor/patients') ?>" class="action-btn">
                                            <span class="action-icon">👥</span>
                                            <span>View Patients</span>
                                        </a>
                                        <a href="<?= base_url('doctor/appointments') ?>" class="action-btn">
                                            <span class="action-icon">📅</span>
                                            <span>Manage Appointments</span>
                                        </a>
                                        <a href="#" class="action-btn">
                                            <span class="action-icon">📋</span>
                                            <span>Write Reports</span>
                                        </a>
                                        <a href="#" class="action-btn">
                                            <span class="action-icon">💊</span>
                                            <span>Prescribe Medication</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
