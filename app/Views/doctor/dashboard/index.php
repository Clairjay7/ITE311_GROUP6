<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Doctor Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dashboard-container { 
        display: grid; 
        gap: 24px; 
        padding: 0;
    }
    
    .welcome-section {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(46, 125, 50, 0.2);
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .welcome-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    
    .welcome-section h2 {
        font-family: 'Playfair Display', serif;
        margin: 0 0 8px;
        font-size: 32px;
        font-weight: 700;
        position: relative;
        z-index: 1;
    }
    
    .welcome-section p { 
        margin: 0;
        opacity: 0.95;
        font-size: 16px;
        position: relative;
        z-index: 1;
    }
    
    .refresh-indicator {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        z-index: 2;
    }
    
    .refresh-indicator .spinner {
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .stats-container { 
        width: 100%; 
    }
    
    .stats-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
        gap: 20px; 
    }
    
    .stat-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2e7d32, #4caf50);
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(46, 125, 50, 0.15);
    }
    
    .stat-card.primary::before { background: linear-gradient(90deg, #1976d2, #42a5f5); }
    .stat-card.warning::before { background: linear-gradient(90deg, #f57c00, #ff9800); }
    .stat-card.info::before { background: linear-gradient(90deg, #0288d1, #03a9f4); }
    .stat-card.success::before { background: linear-gradient(90deg, #388e3c, #66bb6a); }
    .stat-card.danger::before { background: linear-gradient(90deg, #d32f2f, #ef5350); }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        font-size: 24px;
    }
    
    .stat-card.primary .stat-icon { background: rgba(25, 118, 210, 0.1); color: #1976d2; }
    .stat-card.warning .stat-icon { background: rgba(245, 124, 0, 0.1); color: #f57c00; }
    .stat-card.info .stat-icon { background: rgba(2, 136, 209, 0.1); color: #0288d1; }
    .stat-card.success .stat-icon { background: rgba(56, 142, 60, 0.1); color: #388e3c; }
    .stat-card.danger .stat-icon { background: rgba(211, 47, 47, 0.1); color: #d32f2f; }
    
    .stat-title {
        margin: 0 0 8px;
        font-size: 14px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-value {
        margin: 0;
        font-size: 36px;
        font-weight: 800;
        color: #1e293b;
        line-height: 1;
    }
    
    .patients-section {
        margin-top: 8px;
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .patients-section h3 {
        color: #2e7d32;
        margin: 0 0 20px;
        font-size: 20px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .patients-section h3::before {
        content: '';
        width: 4px;
        height: 24px;
        background: #2e7d32;
        border-radius: 2px;
    }
    
    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table thead {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f4 100%);
    }
    
    .data-table th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #2e7d32;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #c8e6c9;
    }
    
    .data-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
    }
    
    .data-table tbody tr {
        transition: background 0.2s ease;
    }
    
    .data-table tbody tr:hover {
        background: #f8fafc;
    }
    
    .data-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .btn {
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        margin-right: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary {
        background: #2e7d32;
        color: white;
    }
    
    .btn-primary:hover {
        background: #1b5e20;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .btn-warning {
        background: #f59e0b;
        color: white;
    }
    
    .btn-warning:hover {
        background: #d97706;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    
    .btn-info {
        background: #0288d1;
        color: white;
    }
    
    .btn-info:hover {
        background: #0277bd;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }
    
    .empty-state i {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
    
    .empty-state h4 {
        margin: 0 0 8px;
        color: #64748b;
    }
    
    @media (max-width: 768px) {
        .welcome-section { padding: 24px; }
        .welcome-section h2 { font-size: 24px; }
        .stat-value { font-size: 28px; }
        .stats-grid { grid-template-columns: 1fr; }
        .data-table { font-size: 14px; }
        .data-table th,
        .data-table td { padding: 12px; }
    }
</style>

<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="refresh-indicator" id="refreshIndicator">
            <div class="spinner" id="refreshSpinner" style="display: none;"></div>
            <span id="lastUpdate">Auto-updating every 5 seconds...</span>
        </div>
        <h2>Welcome back, Dr. <?= esc($name ?? 'Doctor') ?></h2>
        <p>Here's what's happening with your patients today</p>
    </div>

    <!-- Quick Actions -->
    <?php if (!empty($isPediatricsDoctor) && $isPediatricsDoctor): ?>
    <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 12px; padding: 20px; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); border: 2px solid #f59e0b;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <h3 style="margin: 0 0 4px 0; color: #92400e; font-size: 18px; font-weight: 700;">
                    <i class="fas fa-child"></i> Pediatric Patients
                </h3>
                <p style="margin: 0; color: #78350f; font-size: 14px;">Manage consultations for patients aged 0-17 years old</p>
            </div>
            <a href="<?= site_url('doctor/consultations/pediatrics') ?>" style="background: #f59e0b; color: white; padding: 14px 28px; border-radius: 10px; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4); transition: all 0.3s; font-size: 15px;">
                <i class="fas fa-stethoscope"></i> View Pediatrics Consultations
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Notifications Section -->
    <?php if (!empty($unreadNotifications ?? []) && $totalUnreadNotifications > 0): ?>
        <div class="patients-section" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="color: #92400e; margin: 0;">
                    <i class="fas fa-bell"></i>
                    Notifications
                    <span style="background: #f59e0b; color: white; padding: 4px 12px; border-radius: 20px; font-size: 14px; margin-left: 12px;">
                        <?= $totalUnreadNotifications ?> Unread
                    </span>
                </h3>
                <button onclick="markAllNotificationsRead()" class="btn btn-warning" style="padding: 8px 16px; font-size: 13px;">
                    <i class="fas fa-check-double"></i> Mark All as Read
                </button>
            </div>
            <div style="display: grid; gap: 12px;">
                <?php foreach ($unreadNotifications as $notification): ?>
                    <div class="notification-item" data-notification-id="<?= $notification['id'] ?>" 
                         style="background: white; border-radius: 8px; padding: 16px; border-left: 4px solid #f59e0b; cursor: pointer;"
                         onclick="markNotificationRead(<?= $notification['id'] ?>)">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                    <i class="fas fa-<?= 
                                        $notification['type'] === 'lab_result_ready' ? 'flask' : 
                                        ($notification['type'] === 'lab_request_pending' ? 'vial' : 
                                        ($notification['type'] === 'order_completed' ? 'check-circle' : 'bell'))
                                    ?>" style="color: #f59e0b;"></i>
                                    <strong style="color: #92400e; font-size: 14px;"><?= esc($notification['title']) ?></strong>
                                    <span style="background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">
                                        New
                                    </span>
                                </div>
                                <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.5;">
                                    <?= esc($notification['message']) ?>
                                </p>
                                <div style="margin-top: 8px; font-size: 11px; color: #94a3b8;">
                                    <i class="fas fa-clock"></i> <?= esc(date('M d, Y h:i A', strtotime($notification['created_at']))) ?>
                                </div>
                            </div>
                            <button onclick="event.stopPropagation(); markNotificationRead(<?= $notification['id'] ?>)" 
                                    style="background: #f59e0b; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">
                                <i class="fas fa-check"></i> Mark Read
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Stats Grid -->
    <div class="stats-container">
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-title">Today's Appointments</div>
                <div class="stat-value" id="appointments_count"><?= $appointmentsCount ?? '0' ?></div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-title">My Assigned Patients</div>
                <div class="stat-value" id="assigned_patients_count"><?= ($assignedPatientsCount ?? 0) + (count($hmsPatients ?? [])) ?></div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-title">Patients Seen Today</div>
                <div class="stat-value" id="patients_seen_today"><?= $patientsSeenToday ?? '0' ?></div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-title">Pending Consultations</div>
                <div class="stat-value" id="pending_consultations">0</div>
            </div>
            
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-title">Upcoming (7 days)</div>
                <div class="stat-value" id="upcoming_consultations">0</div>
            </div>
            
            <div class="stat-card success" style="cursor: pointer;" onclick="document.getElementById('patientsTableContainer').scrollIntoView({behavior: 'smooth'});">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-title">My Assigned Patients</div>
                <div class="stat-value" id="assigned_patients_count"><?= ($assignedPatientsCount ?? 0) + (count($hmsPatients ?? [])) ?></div>
                <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                    <i class="fas fa-info-circle"></i> Click to view list
                </div>
            </div>
            
            <div class="stat-card warning" style="border-left: 4px solid #f59e0b;">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-vial"></i>
                </div>
                <div class="stat-title">Pending Lab Requests</div>
                <div class="stat-value" id="pending_lab_requests_count" style="color: #f59e0b;"><?= $pendingLabRequestsCount ?? '0' ?></div>
            </div>
            
            <div class="stat-card" style="border-left: 4px solid #0288d1;">
                <div class="stat-icon" style="background: rgba(2, 136, 209, 0.1); color: #0288d1;">
                    <i class="fas fa-prescription"></i>
                </div>
                <div class="stat-title">Total Orders</div>
                <div class="stat-value" id="total_orders" style="color: #0288d1;"><?= $totalOrders ?? '0' ?></div>
            </div>
            
            <div class="stat-card" style="border-left: 4px solid #f59e0b;">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-title">Pending Orders</div>
                <div class="stat-value" id="pending_orders" style="color: #f59e0b;"><?= $pendingOrders ?? '0' ?></div>
            </div>
            
            <div class="stat-card" style="border-left: 4px solid #10b981;">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-title">Completed Orders</div>
                <div class="stat-value" id="completed_orders" style="color: #10b981;"><?= $completedOrders ?? '0' ?></div>
            </div>
        </div>
    </div>

    <!-- Pending Lab Requests Section -->
    <?php if (!empty($pendingLabRequests ?? [])): ?>
        <div class="patients-section">
            <h3>
                <i class="fas fa-vial"></i>
                Pending Lab Requests
            </h3>
            <div class="table-container">
                <div id="labRequestsTableContainer">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Test Type</th>
                                <th>Test Name</th>
                                <th>Priority</th>
                                <th>Requested By</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="labRequestsTableBody">
                            <?php foreach ($pendingLabRequests as $request): ?>
                                <tr>
                                    <td>#<?= esc($request['id']) ?></td>
                                    <td><strong><?= esc(ucfirst($request['firstname']) . ' ' . ucfirst($request['lastname'])) ?></strong></td>
                                    <td><?= esc($request['test_type']) ?></td>
                                    <td><?= esc($request['test_name']) ?></td>
                                    <td>
                                        <span style="background: <?= 
                                            $request['priority'] == 'stat' ? '#fee2e2' : 
                                            ($request['priority'] == 'urgent' ? '#fef3c7' : '#d1fae5'); 
                                        ?>; color: <?= 
                                            $request['priority'] == 'stat' ? '#991b1b' : 
                                            ($request['priority'] == 'urgent' ? '#92400e' : '#065f46'); 
                                        ?>; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                            <?= esc(ucfirst($request['priority'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($request['requested_by'] === 'doctor'): ?>
                                            <span style="color: #0288d1; font-weight: 600;">
                                                <i class="fas fa-user-md"></i> <?= esc($request['doctor_name'] ?? 'Doctor') ?>
                                            </span>
                                        <?php else: ?>
                                            <?= esc($request['nurse_name'] ?? 'N/A') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc(date('M d, Y', strtotime($request['created_at']))) ?></td>
                                    <td>
                                        <a href="<?= site_url('doctor/lab-requests') ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">
                                            <i class="fas fa-eye"></i>
                                            Review
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Recent Orders Section -->
    <?php if (!empty($recentOrders ?? [])): ?>
        <div class="patients-section">
            <h3>
                <i class="fas fa-prescription"></i>
                Recent Medical Orders
                <a href="<?= site_url('doctor/orders') ?>" style="margin-left: auto; font-size: 14px; color: #2e7d32; text-decoration: none;">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Order Type</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td>#<?= esc($order['id']) ?></td>
                                <td><strong><?= esc(ucfirst($order['firstname']) . ' ' . ucfirst($order['lastname'])) ?></strong></td>
                                <td>
                                    <span style="background: #e0f2fe; color: #0369a1; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                        <?= esc(ucfirst(str_replace('_', ' ', $order['order_type']))) ?>
                                    </span>
                                </td>
                                <td><?= esc(substr($order['order_description'], 0, 50)) ?><?= strlen($order['order_description']) > 50 ? '...' : '' ?></td>
                                <td>
                                    <span style="background: <?= 
                                        $order['status'] == 'completed' ? '#d1fae5' : 
                                        ($order['status'] == 'in_progress' ? '#fef3c7' : 
                                        ($order['status'] == 'cancelled' ? '#fee2e2' : '#dbeafe')); 
                                    ?>; color: <?= 
                                        $order['status'] == 'completed' ? '#065f46' : 
                                        ($order['status'] == 'in_progress' ? '#92400e' : 
                                        ($order['status'] == 'cancelled' ? '#991b1b' : '#1e40af')); 
                                    ?>; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                        <?= esc(ucfirst(str_replace('_', ' ', $order['status']))) ?>
                                    </span>
                                </td>
                                <td><?= esc(date('M d, Y', strtotime($order['created_at']))) ?></td>
                                <td>
                                    <a href="<?= site_url('doctor/orders/view/' . $order['id']) ?>" class="btn btn-info" style="padding: 6px 12px; font-size: 12px;">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Emergency Cases Section -->
    <div class="patients-section" id="emergencyCasesSection" data-section="emergency" style="margin-top: 24px; background: #fee2e2; border-left: 4px solid #ef4444; padding: 20px; border-radius: 8px; <?= empty($emergencyCases ?? []) ? 'display: none;' : '' ?>">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="color: #991b1b; margin: 0;">
                <i class="fas fa-exclamation-triangle"></i> Emergency Cases (Critical Triage)
            </h3>
            <a href="<?= site_url('doctor/er-beds') ?>" class="btn btn-danger" style="padding: 8px 16px; font-size: 13px; text-decoration: none;">
                <i class="fas fa-bed"></i> ER Bed Management
            </a>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Triage Level</th>
                        <th>Destination</th>
                        <th>Status</th>
                        <th>Chief Complaint</th>
                        <th>Triage Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($emergencyCases ?? [])): ?>
                        <?php foreach ($emergencyCases as $case): ?>
                            <?php
                            $disposition = $case['disposition'] ?? 'ER';
                            $triageLevel = $case['triage_level'] ?? 'Critical';
                            $forAdmission = $case['for_admission'] ?? 0;
                            $opdQueueNumber = $case['opd_queue_number'] ?? null;
                            
                            // Determine destination
                            $destination = 'Emergency Room (ER)';
                            $destinationIcon = 'fas fa-hospital';
                            $destinationBadge = 'badge-danger';
                            
                            if ($disposition === 'OPD') {
                                $destination = 'Out-Patient Department (OPD)';
                                $destinationIcon = 'fas fa-clinic-medical';
                                $destinationBadge = 'badge-warning';
                            } elseif ($disposition === 'Admission' || $forAdmission) {
                                $destination = 'For Admission';
                                $destinationIcon = 'fas fa-bed';
                                $destinationBadge = 'badge-success';
                            }
                            
                            // Status
                            $statusText = 'Assigned to You';
                            if ($opdQueueNumber) {
                                $statusText = "OPD Queue #{$opdQueueNumber}";
                            }
                            ?>
                            <tr>
                                <td><strong><?= esc($case['patient_name'] ?? 'N/A') ?></strong></td>
                                <td>
                                    <span style="background: #ef4444; color: white; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                        <?= esc($triageLevel) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-modern <?= $destinationBadge ?>" style="display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="<?= $destinationIcon ?>"></i>
                                        <?= esc($destination) ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 12px; color: #64748b; font-weight: 500;">
                                        <?= esc($statusText) ?>
                                    </span>
                                    <?php if ($opdQueueNumber): ?>
                                        <br><small style="color: #0288d1; font-weight: 600;">Queue #<?= $opdQueueNumber ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($case['chief_complaint'] ?? 'N/A') ?></td>
                                <td><?= esc(date('M d, Y H:i', strtotime($case['created_at']))) ?></td>
                                <td>
                                    <a href="<?= site_url('doctor/consultations/view/' . ($case['patient_id'] ?? '')) ?>" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">
                                        <i class="fas fa-user-md"></i> Attend Now
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Awaiting Consultation Section -->
    <div class="patients-section" id="awaitingConsultationSection" data-section="awaiting" style="margin-top: 24px; background: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 8px; <?= empty($awaitingConsultation ?? []) ? 'display: none;' : '' ?>">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="color: #92400e; margin: 0;">
                <i class="fas fa-clock"></i> Awaiting Consultation
            </h3>
            <a href="<?= site_url('doctor/er-beds') ?>" class="btn btn-warning" style="padding: 8px 16px; font-size: 13px; text-decoration: none;">
                <i class="fas fa-bed"></i> ER Bed Management
            </a>
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Destination</th>
                        <th>Triage Level</th>
                        <th>Consultation Date</th>
                        <th>Consultation Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($awaitingConsultation ?? [])): ?>
                        <?php foreach ($awaitingConsultation as $consultation): ?>
                            <?php
                            $triageLevel = $consultation['triage_level'] ?? null;
                            $disposition = $consultation['disposition'] ?? null;
                            
                            // Determine destination from triage info
                            $destination = 'Consultation';
                            $destinationIcon = 'fas fa-user-md';
                            $destinationBadge = 'badge-info';
                            
                            if ($disposition === 'ER' || $triageLevel === 'Critical') {
                                $destination = 'Emergency Room (ER)';
                                $destinationIcon = 'fas fa-hospital';
                                $destinationBadge = 'badge-danger';
                            } elseif ($disposition === 'OPD') {
                                $destination = 'Out-Patient Department (OPD)';
                                $destinationIcon = 'fas fa-clinic-medical';
                                $destinationBadge = 'badge-warning';
                            } elseif ($disposition === 'Admission') {
                                $destination = 'For Admission';
                                $destinationIcon = 'fas fa-bed';
                                $destinationBadge = 'badge-success';
                            }
                            ?>
                            <tr>
                                <td>
                                    <strong><?= esc(($consultation['firstname'] ?? '') . ' ' . ($consultation['lastname'] ?? '')) ?></strong>
                                    <?php if (!empty($consultation['from_triage'])): ?>
                                        <span style="background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-left: 8px;">
                                            <i class="fas fa-stethoscope"></i> From Triage
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge-modern <?= $destinationBadge ?>" style="display: inline-flex; align-items: center; gap: 6px; font-size: 11px;">
                                        <i class="<?= $destinationIcon ?>"></i>
                                        <?= esc($destination) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($triageLevel): ?>
                                        <?php
                                        $levelBadge = 'badge-info';
                                        if ($triageLevel === 'Critical') $levelBadge = 'badge-danger';
                                        elseif ($triageLevel === 'Moderate') $levelBadge = 'badge-warning';
                                        ?>
                                        <span class="badge-modern <?= $levelBadge ?>" style="font-size: 11px;">
                                            <?= esc($triageLevel) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #64748b; font-size: 12px;">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc(date('M d, Y', strtotime($consultation['consultation_date']))) ?></td>
                                <td><?= esc(date('h:i A', strtotime($consultation['consultation_time']))) ?></td>
                                <td>
                                    <span style="background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                        Awaiting Consultation
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($consultation['from_triage']) && !empty($consultation['triage_id'])): ?>
                                        <!-- For triage cases without consultation, link to patient directly -->
                                        <a href="<?= site_url('doctor/consultations/view/' . ($consultation['patient_id'] ?? '')) ?>" class="btn btn-info" style="padding: 6px 12px; font-size: 12px;">
                                            <i class="fas fa-user-md"></i> Attend Patient
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= site_url('doctor/consultations/view/' . ($consultation['id'] ?? $consultation['patient_id'] ?? '')) ?>" class="btn btn-info" style="padding: 6px 12px; font-size: 12px;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Assigned Patients List -->
    <div class="patients-section" style="margin-top: 32px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">
                <i class="fas fa-users"></i>
                My Assigned Patients
                <span style="background: #2e7d32; color: white; padding: 4px 12px; border-radius: 20px; font-size: 14px; margin-left: 12px;">
                    Total: <?= ($assignedPatientsCount ?? 0) + (count($hmsPatients ?? [])) ?>
                </span>
            </h3>
            <a href="<?= site_url('doctor/patients') ?>" class="btn-modern btn-modern-primary" style="text-decoration: none;">
                <i class="fas fa-list"></i> View All Patients
            </a>
        </div>
        <div class="table-container">
            <div id="patientsTableContainer">
                <?php if (!empty($assignedPatients ?? []) || !empty($hmsPatients ?? [])): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Birthdate</th>
                                <th>Gender</th>
                                <th>Visit Type</th>
                                <th>Room</th>
                                <th>Contact</th>
                                <th>Source</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="patientsTableBody">
                            <?php 
                            // Use merged allAssignedPatients if available, otherwise merge manually
                            $displayPatients = $allAssignedPatients ?? array_merge($assignedPatients ?? [], $hmsPatients ?? []);
                            foreach ($displayPatients as $patient): ?>
                                <?php
                                // Format patient name - handle both admin_patients and patients table structures
                                $nameParts = [];
                                if (!empty($patient['firstname'])) $nameParts[] = $patient['firstname'];
                                if (!empty($patient['lastname'])) $nameParts[] = $patient['lastname'];
                                if (empty($nameParts) && !empty($patient['first_name'])) $nameParts[] = $patient['first_name'];
                                if (empty($nameParts) && !empty($patient['last_name'])) $nameParts[] = $patient['last_name'];
                                if (empty($nameParts) && !empty($patient['full_name'])) {
                                    $parts = explode(' ', $patient['full_name'], 2);
                                    $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                                }
                                $patientName = !empty($patient['full_name']) ? $patient['full_name'] : implode(' ', $nameParts);
                                $patientId = $patient['patient_id'] ?? $patient['id'] ?? null;
                                $visitType = $patient['visit_type'] ?? 'N/A';
                                $visitTypeBg = $visitType === 'Emergency' ? '#fee2e2' : 
                                              ($visitType === 'Consultation' ? '#dbeafe' : 
                                              ($visitType === 'Check-up' ? '#fef3c7' : 
                                              ($visitType === 'Follow-up' ? '#d1fae5' : '#f1f5f9')));
                                $visitTypeColor = $visitType === 'Emergency' ? '#991b1b' : 
                                                 ($visitType === 'Consultation' ? '#1e40af' : 
                                                 ($visitType === 'Check-up' ? '#92400e' : 
                                                 ($visitType === 'Follow-up' ? '#065f46' : '#64748b')));
                                $isReceptionist = isset($patient['source']) && $patient['source'] === 'receptionist';
                                $birthdate = $patient['birthdate'] ?? $patient['date_of_birth'] ?? null;
                                $patientType = $patient['type'] ?? 'Out-Patient';
                                $isInPatient = ($patientType === 'In-Patient');
                                $roomNumber = $patient['room_number'] ?? null;
                                ?>
                                <tr style="<?= $isReceptionist ? 'background: #f0fdf4;' : '' ?>">
                                    <td>#<?= esc($patientId) ?></td>
                                    <td><strong><?= esc($patientName) ?></strong></td>
                                    <td>
                                        <?php if ($isInPatient): ?>
                                            <span style="background: #0288d1; color: white; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fas fa-bed"></i> In-Patient
                                            </span>
                                        <?php else: ?>
                                            <span style="background: #10b981; color: white; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fas fa-user-md"></i> Out-Patient
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= !empty($birthdate) ? esc(date('M d, Y', strtotime($birthdate))) : 'N/A' ?></td>
                                    <td><?= esc(ucfirst($patient['gender'] ?? 'N/A')) ?></td>
                                    <td>
                                        <span style="background: <?= $visitTypeBg ?>; color: <?= $visitTypeColor ?>; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                            <?= esc($visitType) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($isInPatient && !empty($roomNumber)): ?>
                                            <span style="background: #e0f2fe; color: #0288d1; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                                <i class="fas fa-door-open"></i> <?= esc($roomNumber) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #94a3b8;">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($patient['contact'] ?? 'N/A') ?></td>
                                    <td>
                                        <span style="background: <?= $isReceptionist ? '#d1fae5' : '#dbeafe' ?>; color: <?= $isReceptionist ? '#065f46' : '#1e40af' ?>; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                                            <?= $isReceptionist ? 'Receptionist' : 'Admin Panel' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('doctor/patients/view/' . $patientId) ?>" class="btn btn-info" style="padding: 6px 12px; font-size: 12px;">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="<?= site_url('doctor/patients/edit/' . $patientId) ?>" class="btn btn-warning" style="padding: 6px 12px; font-size: 12px;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-user-injured"></i>
                        <h4>No Patients Assigned</h4>
                        <p>You don't have any assigned patients yet. Patients assigned from the receptionist or admin panel will appear here.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const endpoint = '<?= site_url('doctor/dashboard/stats') ?>';

async function refreshDashboard() {
    const spinner = document.getElementById('refreshSpinner');
    const lastUpdate = document.getElementById('lastUpdate');
    
    try {
        spinner.style.display = 'block';
        lastUpdate.textContent = 'Updating...';
        
        const res = await fetch(endpoint, { 
            headers: { 'Accept': 'application/json' } 
        });
        
        if (!res.ok) throw new Error('Network error');
        
        const data = await res.json();
        
        // Update statistics
        const setText = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val ?? '0';
        };
        
        setText('appointments_count', data.appointments_count);
        setText('patients_seen_today', data.patients_seen_today);
        // Update assigned patients count (includes both admin_patients and patients table)
        const totalPatients = (data.assigned_patients_count || 0) + (data.hms_patients ? data.hms_patients.length : 0);
        setText('assigned_patients_count', totalPatients);
        setText('pending_consultations', data.pending_consultations);
        setText('upcoming_consultations', data.upcoming_consultations);
        setText('pending_lab_requests_count', data.pending_lab_requests_count);
        setText('total_orders', data.total_orders);
        setText('pending_orders', data.pending_orders);
        setText('completed_orders', data.completed_orders);
        
        // Update emergency cases count if available
        if (data.emergency_cases_count !== undefined) {
            const emergencyCountEl = document.getElementById('emergency_cases_count');
            if (emergencyCountEl) {
                emergencyCountEl.textContent = data.emergency_cases_count;
            }
        }
        
        // Update awaiting consultation count if available
        if (data.awaiting_consultation_count !== undefined) {
            const awaitingCountEl = document.getElementById('awaiting_consultation_count');
            if (awaitingCountEl) {
                awaitingCountEl.textContent = data.awaiting_consultation_count;
            }
        }
        
        // Update patients table
        const tableBody = document.getElementById('patientsTableBody');
        const tableContainer = document.getElementById('patientsTableContainer');
        
        // Use merged all_assigned_patients if available, otherwise merge manually
        const allPatients = data.all_assigned_patients || [...(data.assigned_patients || []), ...(data.hms_patients || [])];
        
        if (allPatients.length > 0) {
            let tableHTML = `
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Birthdate</th>
                            <th>Gender</th>
                            <th>Visit Type</th>
                            <th>Contact</th>
                            <th>Source</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="patientsTableBody">
            `;
            
            allPatients.forEach(patient => {
                const birthdate = patient.birthdate ? new Date(patient.birthdate).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                }) : 'N/A';
                
                const visitType = patient.visit_type || 'N/A';
                const visitTypeBg = visitType === 'Emergency' ? '#fee2e2' : 
                                   (visitType === 'Consultation' ? '#dbeafe' : 
                                   (visitType === 'Check-up' ? '#fef3c7' : 
                                   (visitType === 'Follow-up' ? '#d1fae5' : '#f1f5f9')));
                const visitTypeColor = visitType === 'Emergency' ? '#991b1b' : 
                                     (visitType === 'Consultation' ? '#1e40af' : 
                                     (visitType === 'Check-up' ? '#92400e' : 
                                     (visitType === 'Follow-up' ? '#065f46' : '#64748b')));
                
                const patientId = patient.patient_id || patient.id;
                const isReceptionist = patient.source === 'receptionist';
                
                tableHTML += `
                    <tr style="${isReceptionist ? 'background: #f0fdf4;' : ''}">
                        <td>#${patientId}</td>
                        <td><strong>${patient.firstname || ''} ${patient.lastname || ''}</strong></td>
                        <td>${birthdate}</td>
                        <td>${patient.gender ? patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1) : 'N/A'}</td>
                        <td>
                            <span style="background: ${visitTypeBg}; color: ${visitTypeColor}; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                ${visitType}
                            </span>
                        </td>
                        <td>${patient.contact || 'N/A'}</td>
                        <td>
                            ${isReceptionist ? 
                                '<span style="background: #d1fae5; color: #065f46; padding: 4px 8px; border-radius: 4px; font-size: 11px;">Receptionist</span>' : 
                                '<span style="background: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 4px; font-size: 11px;">Admin Panel</span>'
                            }
                        </td>
                        <td>
                            <a href="<?= site_url('doctor/patients/view/') ?>${patientId}" class="btn btn-info" style="padding: 6px 12px; font-size: 12px;">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="<?= site_url('doctor/patients/edit/') ?>${patientId}" class="btn btn-warning" style="padding: 6px 12px; font-size: 12px;">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                `;
            });
            
            tableHTML += '</tbody></table>';
            tableContainer.innerHTML = tableHTML;
        } else {
            tableContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-user-injured"></i>
                    <h4>No Patients Assigned</h4>
                    <p>You don't have any assigned patients yet. Patients assigned from the receptionist or admin panel will appear here.</p>
                </div>
            `;
        }
        
        // Update lab requests table
        const labRequestsTableBody = document.getElementById('labRequestsTableBody');
        const labRequestsTableContainer = document.getElementById('labRequestsTableContainer');
        
        if (data.pending_lab_requests && data.pending_lab_requests.length > 0 && labRequestsTableContainer) {
            let tableHTML = `
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Test Type</th>
                            <th>Test Name</th>
                            <th>Priority</th>
                            <th>Requested By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="labRequestsTableBody">
            `;
            
            data.pending_lab_requests.forEach(request => {
                const priorityBg = request.priority == 'stat' ? '#fee2e2' : 
                                  (request.priority == 'urgent' ? '#fef3c7' : '#d1fae5');
                const priorityColor = request.priority == 'stat' ? '#991b1b' : 
                                     (request.priority == 'urgent' ? '#92400e' : '#065f46');
                const date = new Date(request.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                
                tableHTML += `
                    <tr>
                        <td>#${request.id}</td>
                        <td><strong>${request.firstname} ${request.lastname}</strong></td>
                        <td>${request.test_type}</td>
                        <td>${request.test_name}</td>
                        <td>
                            <span style="background: ${priorityBg}; color: ${priorityColor}; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                ${request.priority.charAt(0).toUpperCase() + request.priority.slice(1)}
                            </span>
                        </td>
                        <td>
                            ${request.requested_by === 'doctor' 
                                ? `<span style="color: #0288d1; font-weight: 600;"><i class="fas fa-user-md"></i> ${request.doctor_name || 'Doctor'}</span>`
                                : (request.nurse_name || 'N/A')
                            }
                        </td>
                        <td>${date}</td>
                        <td>
                            <a href="<?= site_url('doctor/lab-requests') ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">
                                <i class="fas fa-eye"></i>
                                Review
                            </a>
                        </td>
                    </tr>
                `;
            });
            
            tableHTML += '</tbody></table>';
            labRequestsTableContainer.innerHTML = tableHTML;
        } else if (labRequestsTableContainer && (!data.pending_lab_requests || data.pending_lab_requests.length === 0)) {
            // Hide the section if no lab requests
            const labRequestsSection = labRequestsTableContainer.closest('.patients-section');
            if (labRequestsSection) {
                labRequestsSection.style.display = 'none';
            }
        }
        
        // Show lab requests section if there are requests
        if (data.pending_lab_requests && data.pending_lab_requests.length > 0) {
            const existingSection = document.querySelector('.patients-section:has(#labRequestsTableContainer)');
            if (!existingSection) {
                // Create the section if it doesn't exist
                const patientsSection = document.querySelector('.patients-section');
                if (patientsSection) {
                    const labSection = document.createElement('div');
                    labSection.className = 'patients-section';
                    labSection.innerHTML = `
                        <h3>
                            <i class="fas fa-vial"></i>
                            Pending Lab Requests
                        </h3>
                        <div class="table-container">
                            <div id="labRequestsTableContainer"></div>
                        </div>
                    `;
                    patientsSection.parentNode.insertBefore(labSection, patientsSection.nextSibling);
                }
            }
        }
        
        // Update awaiting consultation section dynamically
        const awaitingSection = document.getElementById('awaitingConsultationSection');
        if (data.awaiting_consultation && data.awaiting_consultation.length > 0) {
            if (awaitingSection) {
                awaitingSection.style.display = 'block';
                const tbody = awaitingSection.querySelector('tbody');
                if (tbody) {
                    let html = '';
                    data.awaiting_consultation.forEach(consult => {
                        const consultDate = new Date(consult.consultation_date).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric'
                        });
                        const consultTime = new Date('2000-01-01 ' + consult.consultation_time).toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });
                        const visitType = consult.visit_type || 'N/A';
                        const visitTypeBg = visitType === 'Emergency' ? '#fee2e2' : 
                                           (visitType === 'Consultation' ? '#dbeafe' : 
                                           (visitType === 'Check-up' ? '#fef3c7' : 
                                           (visitType === 'Follow-up' ? '#d1fae5' : '#f1f5f9')));
                        const visitTypeColor = visitType === 'Emergency' ? '#991b1b' : 
                                              (visitType === 'Consultation' ? '#1e40af' : 
                                              (visitType === 'Check-up' ? '#92400e' : 
                                              (visitType === 'Follow-up' ? '#065f46' : '#64748b')));
                        html += `
                            <tr>
                                <td><strong>${consult.firstname || ''} ${consult.lastname || ''}</strong></td>
                                <td>${consultDate}</td>
                                <td>${consultTime}</td>
                                <td>
                                    <span style="background: ${visitTypeBg}; color: ${visitTypeColor}; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                        ${visitType}
                                    </span>
                                </td>
                                <td>
                                    <span style="background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                        Awaiting Consultation
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= site_url('doctor/consultations/view/') ?>${consult.id}" class="btn btn-info" style="padding: 6px 12px; font-size: 12px;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                }
            }
        } else if (awaitingSection) {
            awaitingSection.style.display = 'none';
        }
        
        // Update emergency cases section dynamically
        const emergencySection = document.getElementById('emergencyCasesSection');
        if (data.emergency_cases && data.emergency_cases.length > 0) {
            if (emergencySection) {
                emergencySection.style.display = 'block';
                const tbody = emergencySection.querySelector('tbody');
                if (tbody) {
                    let html = '';
                    data.emergency_cases.forEach(case_ => {
                        const triageTime = new Date(case_.created_at).toLocaleString('en-US', {
                            year: 'numeric',
                            month: 'short',
                            day: 'numeric',
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });
                        const disposition = case_.disposition || 'ER';
                        const forAdmission = case_.for_admission || 0;
                        const opdQueueNumber = case_.opd_queue_number || null;
                        
                        let destination = 'Emergency Room (ER)';
                        let destinationIcon = 'fas fa-hospital';
                        let destinationBadge = 'badge-danger';
                        
                        if (disposition === 'OPD') {
                            destination = 'Out-Patient Department (OPD)';
                            destinationIcon = 'fas fa-clinic-medical';
                            destinationBadge = 'badge-warning';
                        } else if (disposition === 'Admission' || forAdmission) {
                            destination = 'For Admission';
                            destinationIcon = 'fas fa-bed';
                            destinationBadge = 'badge-success';
                        }
                        
                        let statusText = 'Assigned to You';
                        if (opdQueueNumber) {
                            statusText = `OPD Queue #${opdQueueNumber}`;
                        }
                        
                        html += `
                            <tr>
                                <td><strong>${case_.patient_name || 'N/A'}</strong></td>
                                <td>
                                    <span style="background: #ef4444; color: white; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                        ${case_.triage_level || 'Critical'}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-modern ${destinationBadge}" style="display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="${destinationIcon}"></i>
                                        ${destination}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 12px; color: #64748b; font-weight: 500;">
                                        ${statusText}
                                    </span>
                                    ${opdQueueNumber ? `<br><small style="color: #0288d1; font-weight: 600;">Queue #${opdQueueNumber}</small>` : ''}
                                </td>
                                <td>${case_.chief_complaint || 'N/A'}</td>
                                <td>${triageTime}</td>
                                <td>
                                    <a href="<?= site_url('doctor/consultations/view/') ?>${case_.patient_id || ''}" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">
                                        <i class="fas fa-user-md"></i> Attend Now
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = html;
                }
            }
        } else if (emergencySection) {
            emergencySection.style.display = 'none';
        }
        
        // Update notifications if available
        if (data.unread_notifications !== undefined) {
            updateNotifications(data.unread_notifications, data.total_unread_notifications || 0);
        }
        
        // Update last refresh time
        const now = new Date();
        lastUpdate.textContent = `Updated: ${now.toLocaleTimeString()}`;
        
    } catch (e) {
        console.error('Dashboard refresh error:', e);
        lastUpdate.textContent = 'Update failed';
    } finally {
        spinner.style.display = 'none';
    }
}

function updateNotifications(notifications, totalUnread) {
    const notificationsSection = document.querySelector('.patients-section:has(.notification-item)');
    if (!notificationsSection && totalUnread > 0) {
        // Create notifications section if it doesn't exist
        const welcomeSection = document.querySelector('.welcome-section');
        if (welcomeSection && welcomeSection.nextElementSibling) {
            const newSection = document.createElement('div');
            newSection.className = 'patients-section';
            newSection.style.cssText = 'background: #fef3c7; border-left: 4px solid #f59e0b;';
            newSection.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="color: #92400e; margin: 0;">
                        <i class="fas fa-bell"></i>
                        Notifications
                        <span style="background: #f59e0b; color: white; padding: 4px 12px; border-radius: 20px; font-size: 14px; margin-left: 12px;">
                            ${totalUnread} Unread
                        </span>
                    </h3>
                    <button onclick="markAllNotificationsRead()" class="btn btn-warning" style="padding: 8px 16px; font-size: 13px;">
                        <i class="fas fa-check-double"></i> Mark All as Read
                    </button>
                </div>
                <div id="notificationsContainer" style="display: grid; gap: 12px;"></div>
            `;
            welcomeSection.parentNode.insertBefore(newSection, welcomeSection.nextElementSibling);
        }
    }
    
    const container = document.getElementById('notificationsContainer') || notificationsSection?.querySelector('div[style*="grid"]');
    if (container && notifications && notifications.length > 0) {
        let html = '';
        notifications.forEach(notif => {
            const icon = notif.type === 'lab_result_ready' ? 'flask' : 
                       (notif.type === 'lab_request_pending' ? 'vial' : 
                       (notif.type === 'order_completed' ? 'check-circle' : 'bell'));
            const date = new Date(notif.created_at).toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            html += `
                <div class="notification-item" data-notification-id="${notif.id}" 
                     style="background: white; border-radius: 8px; padding: 16px; border-left: 4px solid #f59e0b; cursor: pointer;"
                     onclick="markNotificationRead(${notif.id})">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <i class="fas fa-${icon}" style="color: #f59e0b;"></i>
                                <strong style="color: #92400e; font-size: 14px;">${notif.title || 'Notification'}</strong>
                                <span style="background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">
                                    New
                                </span>
                            </div>
                            <p style="margin: 0; color: #64748b; font-size: 13px; line-height: 1.5;">
                                ${notif.message || ''}
                            </p>
                            <div style="margin-top: 8px; font-size: 11px; color: #94a3b8;">
                                <i class="fas fa-clock"></i> ${date}
                            </div>
                        </div>
                        <button onclick="event.stopPropagation(); markNotificationRead(${notif.id})" 
                                style="background: #f59e0b; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">
                            <i class="fas fa-check"></i> Mark Read
                        </button>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    } else if (container && (!notifications || notifications.length === 0)) {
        container.innerHTML = '<p style="text-align: center; color: #94a3b8; padding: 20px;">No unread notifications</p>';
    }
}

function markNotificationRead(notificationId) {
    fetch('<?= site_url('doctor/notifications/mark-read') ?>/' + notificationId, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove notification from UI
            const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.style.opacity = '0.5';
                notificationItem.style.textDecoration = 'line-through';
                setTimeout(() => {
                    notificationItem.remove();
                    // Refresh notifications count
                    refreshDashboard();
                }, 500);
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function markAllNotificationsRead() {
    if (!confirm('Mark all notifications as read?')) return;
    
    fetch('<?= site_url('doctor/notifications/mark-all-read') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove all notifications from UI
            document.querySelectorAll('.notification-item').forEach(item => {
                item.style.opacity = '0.5';
                setTimeout(() => item.remove(), 500);
            });
            // Refresh dashboard
            refreshDashboard();
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}

// Initialize on page load
let refreshInterval;
window.addEventListener('DOMContentLoaded', () => {
    refreshDashboard();
    // Auto-refresh every 5 seconds for real-time updates
    refreshInterval = setInterval(refreshDashboard, 5000);
});

// Pause refresh when tab is hidden, resume when visible
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    } else {
        refreshDashboard();
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
        refreshInterval = setInterval(refreshDashboard, 5000);
    }
});
</script>

<?= $this->endSection() ?>
