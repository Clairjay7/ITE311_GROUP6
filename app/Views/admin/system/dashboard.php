        <?= $this->extend('template/header') ?>

<?= $this->section('title') ?>System Control Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dashboard-container { display: grid; gap: 24px; }
    .welcome-section { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; box-shadow: 0 2px 6px rgba(15,23,42,.08); background-image: linear-gradient(135deg, rgba(76,175,80,.06), rgba(46,125,50,.06)); }
    .welcome-section h2 { font-family: 'Playfair Display', serif; color: #2e7d32; margin: 0 0 6px; font-size: 28px; letter-spacing: -0.01em; }
    .welcome-section p { color: #64748b; margin: 0; }
    .stats-container { width: 100%; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
    .stat-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 2px 6px rgba(15,23,42,.08); position: relative; overflow: hidden; transition: all .25s ease; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #2e7d32, #43a047); }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,24,40,.12); }
    .stat-title { margin: 0; font-size: 14px; color: #2e7d32; font-weight: 700; }
    .stat-value { margin-top: 10px; font-size: 32px; font-weight: 800; color: #1f2937; }
    @media (max-width: 600px) { .welcome-section { padding: 18px; } .stat-value { font-size: 28px; } }
</style>
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h2>Welcome, <?= esc($name ?? 'Administrator') ?></h2>
        <p>System Administration & Security Overview - Monitoring all hospital modules and ensuring system stability across all roles</p>
        <div style="margin-top: 16px; padding: 16px; background: rgba(255,255,255,0.5); border-radius: 8px; font-size: 13px; color: #475569; line-height: 1.8;">
            <strong style="color: #2e7d32;">Your Role:</strong> Admin acts as the backbone of the Hospital Management System, ensuring all modules—Admin, Finance, Reception, Pharmacy, Lab, Doctor, and Nurse—remain connected, active, and stable. You handle system configuration, user management, security, maintenance, and troubleshooting for every role.
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">System Uptime</div>
                <div class="stat-value"><?= $systemUptime ?? '99.8%' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Active Users</div>
                <div class="stat-value"><?= $activeUsers ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">System Alerts</div>
                <div class="stat-value"><?= $systemAlerts ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Recent Logs (24h)</div>
                <div class="stat-value"><?= $recentLogsCount ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Error Logs (7d)</div>
                <div class="stat-value"><?= $errorLogsCount ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Total Backups</div>
                <div class="stat-value"><?= $totalBackups ?? '0' ?></div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="welcome-section" style="margin-top: 24px;">
        <h3 style="color: #2e7d32; margin-bottom: 16px;">Quick Actions</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <a href="<?= base_url('admin/system/logs') ?>" class="stat-card" style="text-decoration: none; color: inherit; display: block;">
                <div class="stat-title"><i class="fas fa-file-alt me-2"></i>System Logs</div>
                <div style="margin-top: 8px; color: #64748b; font-size: 14px;">View and filter system logs</div>
            </a>
            <a href="<?= base_url('admin/users') ?>" class="stat-card" style="text-decoration: none; color: inherit; display: block;">
                <div class="stat-title"><i class="fas fa-users-cog me-2"></i>User Management</div>
                <div style="margin-top: 8px; color: #64748b; font-size: 14px;">Manage system users</div>
            </a>
            <a href="<?= base_url('admin/system/backup') ?>" class="stat-card" style="text-decoration: none; color: inherit; display: block;">
                <div class="stat-title"><i class="fas fa-database me-2"></i>Create Backup</div>
                <div style="margin-top: 8px; color: #64748b; font-size: 14px;">Backup database or files</div>
            </a>
            <a href="<?= base_url('admin/system/restore') ?>" class="stat-card" style="text-decoration: none; color: inherit; display: block;">
                <div class="stat-title"><i class="fas fa-rotate-left me-2"></i>Restore System</div>
                <div style="margin-top: 8px; color: #64748b; font-size: 14px;">Restore from backup</div>
            </a>
        </div>
    </div>

    <!-- System-Wide Statistics -->
    <div class="welcome-section" style="margin-top: 24px;">
        <h3 style="color: #2e7d32; margin-bottom: 16px;">
            <i class="fas fa-chart-line me-2"></i>
            System-Wide Statistics
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px;">
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border-left: 4px solid #0288d1;">
                <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 4px;">Total Patients</div>
                <div style="font-size: 24px; font-weight: 800; color: #1e293b;"><?= number_format($totalPatients ?? 0) ?></div>
            </div>
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border-left: 4px solid #10b981;">
                <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 4px;">Today's Appointments</div>
                <div style="font-size: 24px; font-weight: 800; color: #1e293b;"><?= number_format($totalAppointments ?? 0) ?></div>
            </div>
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border-left: 4px solid #8b5cf6;">
                <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 4px;">Pending Bills</div>
                <div style="font-size: 24px; font-weight: 800; color: #1e293b;"><?= number_format($totalBills ?? 0) ?></div>
            </div>
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border-left: 4px solid #ec4899;">
                <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 4px;">Pending Lab Requests</div>
                <div style="font-size: 24px; font-weight: 800; color: #1e293b;"><?= number_format($totalLabRequests ?? 0) ?></div>
            </div>
            <div style="background: #f8fafc; padding: 16px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 4px;">Pending Doctor Orders</div>
                <div style="font-size: 24px; font-weight: 800; color: #1e293b;"><?= number_format($totalOrders ?? 0) ?></div>
            </div>
        </div>
    </div>

    <!-- Module Health & Role Connections -->
    <div class="welcome-section" style="margin-top: 24px;">
        <h3 style="color: #2e7d32; margin-bottom: 16px;">
            <i class="fas fa-network-wired me-2"></i>
            Module Health & Role Connections
        </h3>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">
            Monitor the health and activity of all system modules. Admin ensures all roles have stable access to their respective modules.
        </p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
            <?php if (!empty($roleStats ?? [])): ?>
                <?php foreach ($roleStats as $roleKey => $role): ?>
                    <div style="background: white; border: 2px solid <?= $role['health'] == 'operational' ? '#d1fae5' : '#fef3c7' ?>; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 48px; height: 48px; background: <?= $role['color'] ?>20; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas <?= $role['icon'] ?>" style="font-size: 24px; color: <?= $role['color'] ?>;"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 16px; color: #1e293b;"><?= esc($role['name']) ?></div>
                                    <div style="font-size: 12px; color: #64748b;">
                                        <span style="display: inline-flex; align-items: center; gap: 4px;">
                                            <span style="width: 8px; height: 8px; border-radius: 50%; background: <?= $role['health'] == 'operational' ? '#10b981' : '#f59e0b' ?>;"></span>
                                            <?= esc(ucfirst($role['health'])) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 16px;">
                            <div style="background: #f8fafc; padding: 12px; border-radius: 8px;">
                                <div style="font-size: 11px; color: #64748b; margin-bottom: 4px;">Active Users</div>
                                <div style="font-size: 20px; font-weight: 800; color: #1e293b;"><?= number_format($role['active_users']) ?></div>
                            </div>
                            <div style="background: #f8fafc; padding: 12px; border-radius: 8px;">
                                <div style="font-size: 11px; color: #64748b; margin-bottom: 4px;">Activity (24h)</div>
                                <div style="font-size: 20px; font-weight: 800; color: #1e293b;"><?= number_format($role['activity_24h']) ?></div>
                            </div>
                        </div>
                        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e5e7eb;">
                            <div style="font-size: 11px; color: #64748b; line-height: 1.6;">
                                <?php
                                $descriptions = [
                                    'admin' => 'System config, user accounts, access levels, security permissions',
                                    'doctor' => 'EMR access, patient history, lab results, medical orders',
                                    'nurse' => 'Vitals updates, order execution, patient charts, lab requests',
                                    'receptionist' => 'Patient registration, appointments, queue management',
                                    'finance' => 'Billing modules, payment processing, financial reports',
                                    'pharmacy' => 'Inventory system, medication database, dispensing logs',
                                    'lab_staff' => 'Lab requests, result uploads, device integrations',
                                ];
                                echo esc($descriptions[$roleKey] ?? 'Module monitoring and support');
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Users by Role -->
    <?php if (!empty($usersByRole ?? [])): ?>
    <div class="welcome-section" style="margin-top: 24px;">
        <h3 style="color: #2e7d32; margin-bottom: 16px;">
            <i class="fas fa-users me-2"></i>
            Active Users by Role
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
            <?php foreach ($usersByRole as $roleData): ?>
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px;">
                    <div style="font-size: 12px; color: #64748b; margin-bottom: 8px; text-transform: capitalize;">
                        <?= esc(ucfirst(str_replace('_', ' ', $roleData['role_name']))) ?>
                    </div>
                    <div style="display: flex; align-items: baseline; gap: 8px;">
                        <div style="font-size: 28px; font-weight: 800; color: #1e293b;">
                            <?= number_format($roleData['active_count']) ?>
                        </div>
                        <div style="font-size: 14px; color: #64748b;">
                            / <?= number_format($roleData['user_count']) ?> total
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Activity -->
    <?php if (!empty($recentLogs ?? [])): ?>
    <div class="welcome-section" style="margin-top: 24px;">
        <h3 style="color: #2e7d32; margin-bottom: 16px;">Recent System Logs</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #e8f5e9; border-bottom: 2px solid #c8e6c9;">
                        <th style="padding: 12px; text-align: left; font-size: 12px; color: #2e7d32; font-weight: 700;">Level</th>
                        <th style="padding: 12px; text-align: left; font-size: 12px; color: #2e7d32; font-weight: 700;">Message</th>
                        <th style="padding: 12px; text-align: left; font-size: 12px; color: #2e7d32; font-weight: 700;">Module</th>
                        <th style="padding: 12px; text-align: left; font-size: 12px; color: #2e7d32; font-weight: 700;">User</th>
                        <th style="padding: 12px; text-align: left; font-size: 12px; color: #2e7d32; font-weight: 700;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentLogs as $log): ?>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px;">
                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; background: <?= 
                                    $log['level'] == 'error' || $log['level'] == 'critical' ? '#fee2e2' : 
                                    ($log['level'] == 'warning' ? '#fef3c7' : '#dbeafe'); 
                                ?>; color: <?= 
                                    $log['level'] == 'error' || $log['level'] == 'critical' ? '#991b1b' : 
                                    ($log['level'] == 'warning' ? '#92400e' : '#1e40af'); 
                                ?>;">
                                    <?= esc(ucfirst($log['level'])) ?>
                                </span>
                            </td>
                            <td style="padding: 12px; font-size: 13px;"><?= esc(substr($log['message'], 0, 60)) ?><?= strlen($log['message']) > 60 ? '...' : '' ?></td>
                            <td style="padding: 12px; font-size: 13px;"><?= esc($log['module'] ?? 'N/A') ?></td>
                            <td style="padding: 12px; font-size: 13px;"><?= esc($log['user_name'] ?? 'System') ?></td>
                            <td style="padding: 12px; font-size: 13px;"><?= esc(date('M d, Y h:i A', strtotime($log['created_at']))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top: 16px; text-align: right;">
            <a href="<?= base_url('admin/system/logs') ?>" style="color: #2e7d32; text-decoration: none; font-weight: 600;">View All Logs →</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips if any
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<?= $this->endSection() ?>

