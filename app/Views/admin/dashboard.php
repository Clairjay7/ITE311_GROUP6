<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Admin Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .admin-dashboard {
        display: grid;
        gap: 24px;
    }
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }
    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        position: relative;
        overflow: hidden;
        transition: all 0.25s ease;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, #2e7d32, #43a047);
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16, 24, 40, 0.12); }
    .stat-card h4 {
        margin: 0;
        font-size: 14px;
        color: #2e7d32;
        font-weight: 600;
    }
    .stat-card .value {
        margin-top: 12px;
        font-size: 32px;
        font-weight: 700;
        color: #1f2937;
    }
    .recent-activity {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
    }
    .recent-activity h3 {
        margin: 0 0 16px;
        font-size: 20px;
        color: #2e7d32;
        font-family: 'Playfair Display', serif;
    }
    .activity-table {
        width: 100%;
        border-collapse: collapse;
    }
    .activity-table th,
    .activity-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 14px;
        text-align: left;
    }
    .activity-table th {
        background: #e8f5e9;
        color: #2e7d32;
        font-weight: 700;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-completed { background: #d1fae5; color: #047857; }
    .status-cancelled { background: #fee2e2; color: #b91c1c; }
</style>

<div class="admin-dashboard">
    <div class="stat-grid">
        <div class="stat-card">
            <h4>Total Doctors</h4>
            <div class="value" id="totalDoctors"><?= esc($totalDoctors) ?></div>
        </div>
        <div class="stat-card">
            <h4>Total Patients</h4>
            <div class="value" id="totalPatients"><?= esc($totalPatients) ?></div>
        </div>
        <div class="stat-card">
            <h4>Today's Appointments</h4>
            <div class="value" id="todaysAppointments"><?= esc($todaysAppointments) ?></div>
        </div>
        <div class="stat-card">
            <h4>Pending Bills</h4>
            <div class="value" id="pendingBills"><?= esc($pendingBills) ?></div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #f59e0b;">
            <h4>Pending Lab Requests</h4>
            <div class="value" id="pendingLabRequestsCount" style="color: #f59e0b;"><?= esc($pendingLabRequestsCount ?? 0) ?></div>
        </div>
    </div>

    <div class="recent-activity">
        <h3>Pending Lab Requests from Nurses</h3>
        <div id="labRequestsContainer">
            <?php if (!empty($pendingLabRequests ?? [])): ?>
                <div class="table-responsive">
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Test Type</th>
                                <th>Test Name</th>
                                <th>Priority</th>
                                <th>Requested By</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="labRequestsTableBody">
                            <?php foreach ($pendingLabRequests as $request): ?>
                                <tr>
                                    <td>#<?= esc($request['id']) ?></td>
                                    <td><?= esc(ucfirst($request['firstname']) . ' ' . ucfirst($request['lastname'])) ?></td>
                                    <td><?= esc($request['test_type']) ?></td>
                                    <td><?= esc($request['test_name']) ?></td>
                                    <td>
                                        <span class="status-badge" style="background: <?= 
                                            $request['priority'] == 'stat' ? '#fee2e2' : 
                                            ($request['priority'] == 'urgent' ? '#fef3c7' : '#d1fae5'); 
                                        ?>; color: <?= 
                                            $request['priority'] == 'stat' ? '#991b1b' : 
                                            ($request['priority'] == 'urgent' ? '#92400e' : '#065f46'); 
                                        ?>;">
                                            <?= esc(ucfirst($request['priority'])) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($request['nurse_name'] ?? 'N/A') ?></td>
                                    <td><?= esc(date('M d, Y', strtotime($request['created_at']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: #6b7280;">No pending lab requests from nurses.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="recent-activity" style="margin-top: 24px;">
        <h3>Recent Appointment Activity</h3>
        <div id="recentActivityContainer">
            <?php if (empty($recentActivity)): ?>
                <p style="color: #6b7280;">No recent appointments available.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="recentActivityTableBody">
                            <?php foreach ($recentActivity as $activity): ?>
                                <tr>
                                    <td>#<?= esc($activity['id']) ?></td>
                                    <td><?= esc($activity['patient_first_name'] . ' ' . $activity['patient_last_name']) ?></td>
                                    <td><?= esc($activity['doctor'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php 
                                        $dateStr = $activity['date'] ?? '';
                                        $timeStr = $activity['time'] ?? '';
                                        if (!empty($dateStr) && strtotime($dateStr) !== false) {
                                            echo esc(date('M d, Y', strtotime($dateStr)));
                                            if (!empty($timeStr) && strtotime($timeStr) !== false) {
                                                echo ' ' . esc(date('h:i A', strtotime($timeStr)));
                                            }
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= esc(strtolower($activity['status'])) ?>">
                                            <?= esc(ucfirst($activity['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statsEndpoint = '<?= site_url('admin/dashboard/stats') ?>';
    let lastUpdate = null;

    async function fetchDashboardStats() {
        try {
            const response = await fetch(statsEndpoint, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();

            // Update statistics
            document.getElementById('totalDoctors').textContent = data.totalDoctors ?? '0';
            document.getElementById('totalPatients').textContent = data.totalPatients ?? '0';
            document.getElementById('todaysAppointments').textContent = data.todaysAppointments ?? '0';
            document.getElementById('pendingBills').textContent = data.pendingBills ?? '0';
            document.getElementById('pendingLabRequestsCount').textContent = data.pendingLabRequestsCount ?? '0';

            // Update lab requests table
            const labRequestsTableBody = document.getElementById('labRequestsTableBody');
            const labRequestsContainer = document.getElementById('labRequestsContainer');
            
            if (data.pendingLabRequests && data.pendingLabRequests.length > 0) {
                let tableHTML = `
                    <div class="table-responsive">
                        <table class="activity-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Patient</th>
                                    <th>Test Type</th>
                                    <th>Test Name</th>
                                    <th>Priority</th>
                                    <th>Requested By</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="labRequestsTableBody">
                `;
                
                data.pendingLabRequests.forEach(request => {
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
                            <td>${request.firstname} ${request.lastname}</td>
                            <td>${request.test_type}</td>
                            <td>${request.test_name}</td>
                            <td>
                                <span class="status-badge" style="background: ${priorityBg}; color: ${priorityColor};">
                                    ${request.priority.charAt(0).toUpperCase() + request.priority.slice(1)}
                                </span>
                            </td>
                            <td>${request.nurse_name || 'N/A'}</td>
                            <td>${date}</td>
                        </tr>
                    `;
                });
                
                tableHTML += '</tbody></table></div>';
                labRequestsContainer.innerHTML = tableHTML;
            } else {
                labRequestsContainer.innerHTML = '<p style="color: #6b7280;">No pending lab requests from nurses.</p>';
            }

            // Update recent activity table
            const recentActivityTableBody = document.getElementById('recentActivityTableBody');
            const recentActivityContainer = document.getElementById('recentActivityContainer');
            
            if (data.recentActivity && data.recentActivity.length > 0) {
                let tableHTML = `
                    <div class="table-responsive">
                        <table class="activity-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentActivityTableBody">
                `;
                
                data.recentActivity.forEach(activity => {
                    // Format date safely
                    let dateStr = 'N/A';
                    if (activity.date) {
                        try {
                            const dateObj = new Date(activity.date);
                            if (!isNaN(dateObj.getTime())) {
                                dateStr = dateObj.toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                });
                            }
                        } catch (e) {
                            console.error('Invalid date:', activity.date);
                        }
                    }
                    
                    // Format time safely
                    let timeStr = '';
                    if (activity.time) {
                        try {
                            // Handle time string (HH:MM:SS or HH:MM)
                            const timeParts = activity.time.split(':');
                            if (timeParts.length >= 2) {
                                const hours = parseInt(timeParts[0]);
                                const minutes = parseInt(timeParts[1]);
                                const ampm = hours >= 12 ? 'PM' : 'AM';
                                const displayHours = hours % 12 || 12;
                                timeStr = `${displayHours}:${minutes.toString().padStart(2, '0')} ${ampm}`;
                            }
                        } catch (e) {
                            console.error('Invalid time:', activity.time);
                        }
                    }
                    
                    tableHTML += `
                        <tr>
                            <td>#${activity.id}</td>
                            <td>${activity.patient_first_name} ${activity.patient_last_name}</td>
                            <td>${activity.doctor || 'N/A'}</td>
                            <td>${dateStr}${timeStr ? ' ' + timeStr : ''}</td>
                            <td>
                                <span class="status-badge status-${activity.status.toLowerCase()}">
                                    ${activity.status.charAt(0).toUpperCase() + activity.status.slice(1)}
                                </span>
                            </td>
                        </tr>
                    `;
                });
                
                tableHTML += '</tbody></table></div>';
                recentActivityContainer.innerHTML = tableHTML;
            } else {
                recentActivityContainer.innerHTML = '<p style="color: #6b7280;">No recent appointments available.</p>';
            }

            lastUpdate = new Date();
        } catch (error) {
            console.error('Error fetching dashboard stats:', error);
        }
    }

    // Initial fetch
    fetchDashboardStats();

    // Refresh every 10 seconds
    setInterval(fetchDashboardStats, 10000);

    // Refresh when page becomes visible again
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            fetchDashboardStats();
        }
    });
});
</script>

<?= $this->endSection() ?>