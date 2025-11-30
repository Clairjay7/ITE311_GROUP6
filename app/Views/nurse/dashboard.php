<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Nurse Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dashboard-container { 
        display: grid; 
        gap: 24px; 
        padding: 0;
    }
    
    .welcome-section {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(2, 136, 209, 0.2);
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
        display: none;
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
        background: linear-gradient(90deg, #0288d1, #03a9f4);
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(2, 136, 209, 0.15);
    }
    
    .stat-card.urgent::before { background: linear-gradient(90deg, #ef4444, #f59e0b); }
    .stat-card.warning::before { background: linear-gradient(90deg, #f57c00, #ff9800); }
    .stat-card.success::before { background: linear-gradient(90deg, #388e3c, #66bb6a); }
    .stat-card.info::before { background: linear-gradient(90deg, #0288d1, #03a9f4); }
    
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
    
    .stat-card.urgent .stat-icon { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .stat-card.warning .stat-icon { background: rgba(245, 124, 0, 0.1); color: #f57c00; }
    .stat-card.success .stat-icon { background: rgba(56, 142, 60, 0.1); color: #388e3c; }
    .stat-card.info .stat-icon { background: rgba(2, 136, 209, 0.1); color: #0288d1; }
    
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
    
    .section-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        margin-top: 8px;
    }
    
    .section-card h3 {
        color: #0288d1;
        margin: 0 0 20px;
        font-size: 20px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .section-card h3::before {
        content: '';
        width: 4px;
        height: 24px;
        background: #0288d1;
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
        background: linear-gradient(135deg, #e3f2fd 0%, #f1f8ff 100%);
    }
    
    .data-table th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #0288d1;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #90caf9;
    }
    
    .data-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
    }
    
    .data-table tbody tr:hover {
        background: #f8fafc;
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
        background: #0288d1;
        color: white;
    }
    
    .btn-primary:hover {
        background: #0277bd;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
    }
    
    .btn-success {
        background: #10b981;
        color: white;
    }
    
    .btn-success:hover {
        background: #059669;
        transform: translateY(-1px);
    }
    
    .badge {
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-urgent { background: #fee2e2; color: #991b1b; }
    .badge-warning { background: #fef3c7; color: #92400e; }
    .badge-success { background: #d1fae5; color: #065f46; }
    .badge-info { background: #dbeafe; color: #1e40af; }
    
    .notifications-panel {
        position: fixed;
        top: 80px;
        right: 20px;
        width: 380px;
        max-height: 600px;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        display: none;
        overflow: hidden;
    }
    
    .notifications-panel.show {
        display: block;
    }
    
    .notifications-header {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        padding: 20px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .notifications-body {
        max-height: 500px;
        overflow-y: auto;
        padding: 16px;
    }
    
    .notification-item {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        transition: background 0.2s;
    }
    
    .notification-item:hover {
        background: #f8fafc;
    }
    
    .notification-item.unread {
        background: #eff6ff;
        border-left: 4px solid #0288d1;
    }
    
    .notification-bell {
        position: relative;
        cursor: pointer;
    }
    
    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }
    
    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
</style>

<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="refresh-indicator">
            <div class="spinner" id="refreshSpinner"></div>
            <span id="lastUpdate">Auto-updating...</span>
        </div>
        <h2>Welcome, Nurse</h2>
        <p>Patient Care & Monitoring Overview</p>
        <div style="position: absolute; top: 20px; left: 20px; z-index: 2;">
            <div class="notification-bell" onclick="toggleNotifications()">
                <i class="fas fa-bell" style="font-size: 24px; color: white;"></i>
                <span class="notification-badge" id="notificationBadge" style="display: <?= ($unreadNotificationsCount ?? 0) > 0 ? 'flex' : 'none' ?>;">
                    <?= $unreadNotificationsCount ?? 0 ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-container">
        <div class="stats-grid">
            <div class="stat-card urgent" onclick="window.location.href='<?= site_url('nurse/patients/view') ?>'">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-title">Critical Patients</div>
                <div class="stat-value" id="criticalPatients"><?= $criticalPatients ?? '0' ?></div>
            </div>
            
            <div class="stat-card info" onclick="window.location.href='<?= site_url('nurse/patients/view') ?>'">
                <div class="stat-icon">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <div class="stat-title">Patients Under Care</div>
                <div class="stat-value" id="patientsUnderCare"><?= $patientsUnderCare ?? '0' ?></div>
            </div>
            
            <div class="stat-card warning" onclick="window.location.href='<?= site_url('nurse/patients/view') ?>'">
                <div class="stat-icon">
                    <i class="fas fa-pills"></i>
                </div>
                <div class="stat-title">Medications Due</div>
                <div class="stat-value" id="medicationsDue"><?= $medicationsDue ?? '0' ?></div>
            </div>
            
            <div class="stat-card success" onclick="window.location.href='<?= site_url('nurse/patients/view') ?>'">
                <div class="stat-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div class="stat-title">Vitals Pending</div>
                <div class="stat-value" id="vitalsPending"><?= $vitalsPending ?? '0' ?></div>
            </div>
        </div>
    </div>

    <!-- Notifications Panel -->
    <div class="notifications-panel" id="notificationsPanel">
        <div class="notifications-header">
            <h4 style="margin: 0; font-size: 18px;">
                <i class="fas fa-bell"></i>
                Notifications
            </h4>
            <div>
                <button onclick="markAllAsRead()" class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white; border: none; padding: 6px 12px; border-radius: 6px;">
                    Mark All Read
                </button>
                <button onclick="toggleNotifications()" style="background: transparent; border: none; color: white; font-size: 20px; margin-left: 12px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="notifications-body" id="notificationsBody">
            <?php if (!empty($unreadNotifications)): ?>
                <?php foreach ($unreadNotifications as $notification): ?>
                    <div class="notification-item unread" onclick="markAsRead(<?= $notification['id'] ?>)">
                        <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">
                            <?= esc($notification['title']) ?>
                        </div>
                        <div style="font-size: 13px; color: #64748b;">
                            <?= esc($notification['message']) ?>
                        </div>
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 8px;">
                            <?= esc(date('M d, Y h:i A', strtotime($notification['created_at']))) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <p>No new notifications</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending Doctor Orders -->
    <div class="section-card">
        <h3>
            <i class="fas fa-prescription"></i>
            Pending Doctor Orders
        </h3>
        <div class="table-container">
            <div id="pendingOrdersContainer">
                <?php if (!empty($pendingOrders)): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Order Type</th>
                                <th>Description</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="pendingOrdersTableBody">
                            <?php foreach ($pendingOrders as $order): ?>
                                <tr>
                                    <td><strong><?= esc(ucfirst($order['firstname']) . ' ' . ucfirst($order['lastname'])) ?></strong></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= esc(ucfirst(str_replace('_', ' ', $order['order_type']))) ?>
                                        </span>
                                    </td>
                                    <td><?= esc(substr($order['order_description'], 0, 60)) ?><?= strlen($order['order_description']) > 60 ? '...' : '' ?></td>
                                    <td><?= esc($order['doctor_name'] ?? 'N/A') ?></td>
                                    <td><?= esc(date('M d, Y', strtotime($order['created_at']))) ?></td>
                                    <td>
                                        <a href="<?= site_url('nurse/patients/details/' . $order['patient_id']) ?>" class="btn btn-primary">
                                            <i class="fas fa-eye"></i>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>No pending doctor orders</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Lab Requests Status -->
    <div class="section-card">
        <h3>
            <i class="fas fa-vial"></i>
            Lab Requests Status
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <!-- Pending Requests -->
            <div>
                <h5 style="color: #f59e0b; margin-bottom: 12px;">
                    <i class="fas fa-clock"></i>
                    Pending (<?= count($pendingLabRequests ?? []) ?>)
                </h5>
                <div id="pendingLabRequestsContainer">
                    <?php if (!empty($pendingLabRequests)): ?>
                        <?php foreach ($pendingLabRequests as $request): ?>
                            <div style="background: #fef3c7; padding: 12px; border-radius: 8px; margin-bottom: 8px;">
                                <div style="font-weight: 600; color: #92400e;">
                                    <?= esc($request['test_name']) ?>
                                </div>
                                <div style="font-size: 12px; color: #78350f; margin-top: 4px;">
                                    Patient: <?= esc(ucfirst($request['firstname']) . ' ' . ucfirst($request['lastname'])) ?>
                                </div>
                                <div style="font-size: 11px; color: #92400e; margin-top: 4px;">
                                    Waiting for doctor confirmation
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #94a3b8; font-size: 14px;">No pending requests</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Approved Requests -->
            <div>
                <h5 style="color: #10b981; margin-bottom: 12px;">
                    <i class="fas fa-check-circle"></i>
                    Approved (<?= count($approvedLabRequests ?? []) ?>)
                </h5>
                <div id="approvedLabRequestsContainer">
                    <?php if (!empty($approvedLabRequests)): ?>
                        <?php foreach ($approvedLabRequests as $request): ?>
                            <div style="background: #d1fae5; padding: 12px; border-radius: 8px; margin-bottom: 8px;">
                                <div style="font-weight: 600; color: #065f46;">
                                    <?= esc($request['test_name']) ?>
                                </div>
                                <div style="font-size: 12px; color: #047857; margin-top: 4px;">
                                    Patient: <?= esc(ucfirst($request['firstname']) . ' ' . ucfirst($request['lastname'])) ?>
                                </div>
                                <div style="font-size: 11px; color: #065f46; margin-top: 4px;">
                                    In progress
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #94a3b8; font-size: 14px;">No approved requests</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Completed Results -->
            <div>
                <h5 style="color: #0288d1; margin-bottom: 12px;">
                    <i class="fas fa-file-medical"></i>
                    Results Ready (<?= count($completedLabResults ?? []) ?>)
                </h5>
                <div id="completedLabResultsContainer">
                    <?php if (!empty($completedLabResults)): ?>
                        <?php foreach ($completedLabResults as $result): ?>
                            <div style="background: #dbeafe; padding: 12px; border-radius: 8px; margin-bottom: 8px; cursor: pointer;" onclick="window.location.href='<?= site_url('nurse/laboratory/testresult') ?>'">
                                <div style="font-weight: 600; color: #1e40af;">
                                    <?= esc($result['test_name']) ?>
                                </div>
                                <div style="font-size: 12px; color: #1e3a8a; margin-top: 4px;">
                                    Patient: <?= esc(ucfirst($result['firstname']) . ' ' . ucfirst($result['lastname'])) ?>
                                </div>
                                <div style="font-size: 11px; color: #1e40af; margin-top: 4px;">
                                    <i class="fas fa-check"></i>
                                    Result available
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #94a3b8; font-size: 14px;">No results available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Appointments -->
    <div class="section-card">
        <h3>
            <i class="fas fa-calendar-day"></i>
            Today's Appointments
        </h3>
        <div class="table-container">
            <div id="todaysAppointmentsContainer">
                <?php if (!empty($todaysAppointments)): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="todaysAppointmentsTableBody">
                            <?php foreach ($todaysAppointments as $appointment): ?>
                                <tr>
                                    <td><strong><?= esc(date('h:i A', strtotime($appointment['appointment_time']))) ?></strong></td>
                                    <td><?= esc($appointment['patient_name'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge badge-info">
                                            <?= esc(ucfirst(str_replace('_', ' ', $appointment['appointment_type'] ?? 'consultation'))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= 
                                            $appointment['status'] == 'completed' ? 'success' : 
                                            ($appointment['status'] == 'in_progress' ? 'warning' : 'info') 
                                        ?>">
                                            <?= esc(ucfirst(str_replace('_', ' ', $appointment['status']))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('nurse/appointments/list') ?>" class="btn btn-primary">
                                            <i class="fas fa-eye"></i>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>No appointments scheduled for today</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const endpoint = '<?= site_url('nurse/dashboard/stats') ?>';

function toggleNotifications() {
    const panel = document.getElementById('notificationsPanel');
    panel.classList.toggle('show');
}

async function markAsRead(id) {
    try {
        const response = await fetch('<?= site_url('nurse/notifications/mark-read/') ?>' + id, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            refreshDashboard();
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
}

async function markAllAsRead() {
    try {
        const response = await fetch('<?= site_url('nurse/notifications/mark-all-read') ?>', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            refreshDashboard();
        }
    } catch (error) {
        console.error('Error marking all as read:', error);
    }
}

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
        document.getElementById('criticalPatients').textContent = data.criticalPatients ?? '0';
        document.getElementById('patientsUnderCare').textContent = data.patientsUnderCare ?? '0';
        document.getElementById('medicationsDue').textContent = data.medicationsDue ?? '0';
        document.getElementById('vitalsPending').textContent = data.vitalsPending ?? '0';

        // Update notification badge
        const badge = document.getElementById('notificationBadge');
        const badgeCount = data.unreadNotificationsCount ?? 0;
        if (badgeCount > 0) {
            badge.textContent = badgeCount;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }

        // Update notifications panel
        const notificationsBody = document.getElementById('notificationsBody');
        if (data.unreadNotifications && data.unreadNotifications.length > 0) {
            let html = '';
            data.unreadNotifications.forEach(notif => {
                const date = new Date(notif.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit'
                });
                html += `
                    <div class="notification-item unread" onclick="markAsRead(${notif.id})">
                        <div style="font-weight: 600; color: #1e293b; margin-bottom: 4px;">
                            ${notif.title}
                        </div>
                        <div style="font-size: 13px; color: #64748b;">
                            ${notif.message}
                        </div>
                        <div style="font-size: 11px; color: #94a3b8; margin-top: 8px;">
                            ${date}
                        </div>
                    </div>
                `;
            });
            notificationsBody.innerHTML = html;
        } else {
            notificationsBody.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <p>No new notifications</p>
                </div>
            `;
        }

        // Update pending orders table
        updateTable('pendingOrdersTableBody', 'pendingOrdersContainer', data.pendingOrders, (order) => {
            return `
                <tr>
                    <td><strong>${order.firstname} ${order.lastname}</strong></td>
                    <td><span class="badge badge-info">${order.order_type.replace('_', ' ')}</span></td>
                    <td>${order.order_description.substring(0, 60)}${order.order_description.length > 60 ? '...' : ''}</td>
                    <td>${order.doctor_name || 'N/A'}</td>
                    <td>${new Date(order.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
                    <td>
                        <a href="<?= site_url('nurse/patients/details/') ?>${order.patient_id}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
            `;
        }, ['Patient', 'Order Type', 'Description', 'Doctor', 'Date', 'Actions']);

        // Update lab requests
        updateLabRequests('pendingLabRequestsContainer', data.pendingLabRequests);
        updateLabRequests('approvedLabRequestsContainer', data.approvedLabRequests, true);
        updateLabResults('completedLabResultsContainer', data.completedLabResults);

        // Update appointments
        updateTable('todaysAppointmentsTableBody', 'todaysAppointmentsContainer', data.todaysAppointments, (apt) => {
            const time = new Date(apt.appointment_time).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit'
            });
            return `
                <tr>
                    <td><strong>${time}</strong></td>
                    <td>${apt.patient_name || 'N/A'}</td>
                    <td><span class="badge badge-info">${apt.appointment_type.replace('_', ' ')}</span></td>
                    <td><span class="badge badge-${apt.status === 'completed' ? 'success' : (apt.status === 'in_progress' ? 'warning' : 'info')}">${apt.status.replace('_', ' ')}</span></td>
                    <td>
                        <a href="<?= site_url('nurse/appointments/list') ?>" class="btn btn-primary">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
            `;
        }, ['Time', 'Patient', 'Type', 'Status', 'Actions']);

        const now = new Date();
        lastUpdate.textContent = `Updated: ${now.toLocaleTimeString()}`;
        
    } catch (e) {
        console.error('Dashboard refresh error:', e);
        lastUpdate.textContent = 'Update failed';
    } finally {
        spinner.style.display = 'none';
    }
}

function updateTable(bodyId, containerId, data, rowTemplate, headers) {
    const tableBody = document.getElementById(bodyId);
    const container = document.getElementById(containerId);
    
    if (data && data.length > 0) {
        let html = `
            <table class="data-table">
                <thead>
                    <tr>
                        ${headers.map(h => `<th>${h}</th>`).join('')}
                    </tr>
                </thead>
                <tbody id="${bodyId}">
        `;
        data.forEach(item => {
            html += rowTemplate(item);
        });
        html += '</tbody></table>';
        container.innerHTML = html;
    } else {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-inbox"></i><p>No data available</p></div>';
    }
}

function updateLabRequests(containerId, requests, isApproved = false) {
    const container = document.getElementById(containerId);
    if (requests && requests.length > 0) {
        let html = '';
        requests.forEach(req => {
            const bg = isApproved ? '#d1fae5' : '#fef3c7';
            const color = isApproved ? '#065f46' : '#92400e';
            html += `
                <div style="background: ${bg}; padding: 12px; border-radius: 8px; margin-bottom: 8px;">
                    <div style="font-weight: 600; color: ${color};">
                        ${req.test_name}
                    </div>
                    <div style="font-size: 12px; color: ${isApproved ? '#047857' : '#78350f'}; margin-top: 4px;">
                        Patient: ${req.firstname} ${req.lastname}
                    </div>
                    <div style="font-size: 11px; color: ${color}; margin-top: 4px;">
                        ${isApproved ? 'In progress' : 'Waiting for doctor confirmation'}
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    } else {
        container.innerHTML = '<p style="color: #94a3b8; font-size: 14px;">No requests</p>';
    }
}

function updateLabResults(containerId, results) {
    const container = document.getElementById(containerId);
    if (results && results.length > 0) {
        let html = '';
        results.forEach(result => {
            html += `
                <div style="background: #dbeafe; padding: 12px; border-radius: 8px; margin-bottom: 8px; cursor: pointer;" onclick="window.location.href='<?= site_url('nurse/laboratory/testresult') ?>'">
                    <div style="font-weight: 600; color: #1e40af;">
                        ${result.test_name}
                    </div>
                    <div style="font-size: 12px; color: #1e3a8a; margin-top: 4px;">
                        Patient: ${result.firstname} ${result.lastname}
                    </div>
                    <div style="font-size: 11px; color: #1e40af; margin-top: 4px;">
                        <i class="fas fa-check"></i> Result available
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    } else {
        container.innerHTML = '<p style="color: #94a3b8; font-size: 14px;">No results available</p>';
    }
}

// Initialize on page load
window.addEventListener('DOMContentLoaded', () => {
    refreshDashboard();
    // Auto-refresh every 10 seconds
    setInterval(refreshDashboard, 10000);
});

// Refresh when page becomes visible again
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        refreshDashboard();
    }
});
</script>

<?= $this->endSection() ?>
