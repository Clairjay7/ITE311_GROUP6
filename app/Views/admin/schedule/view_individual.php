<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <div>
            <h2><?= esc($title) ?></h2>
            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px;">
                <?php if ($role === 'doctor' && isset($user['specialization'])): ?>
                    <span class="badge badge-info"><?= esc($user['specialization']) ?></span>
                <?php endif; ?>
                <span style="color: #64748b; font-size: 14px;">
                    <i class="fa-solid fa-envelope"></i> <?= esc($user['email']) ?>
                </span>
            </div>
        </div>
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <?php
            // Determine the correct back URL based on user role
            $currentRole = session()->get('role');
            $backUrl = ($currentRole === 'admin') 
                ? base_url('admin/schedule')
                : base_url('receptionist/doctor-schedules');
            ?>
            <a href="<?= $backUrl ?>" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to All Schedules
            </a>
        </div>
    </div>

    <!-- Month Navigation Buttons -->
    <?php if (!empty($allMonths)): ?>
        <?php
        // Determine the correct base URL based on user role
        $currentRole = session()->get('role');
        $scheduleBaseUrl = ($currentRole === 'admin') 
            ? base_url('admin/schedule/view/' . $user['id'])
            : base_url('receptionist/schedule/view/' . $user['id']);
        ?>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); margin-bottom: 24px;">
            <h3 style="margin: 0 0 16px 0; color: #2e7d32; font-size: 18px; font-weight: 700;">
                <i class="fa-solid fa-calendar-alt"></i> Filter by Month
            </h3>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <a href="<?= $scheduleBaseUrl . '?role=' . $role ?>" 
                   class="month-btn <?= empty($selectedMonth) ? 'active' : '' ?>">
                    All Months
                </a>
                <?php foreach ($allMonths as $month): ?>
                    <?php
                    $monthDate = new \DateTime($month . '-01');
                    $monthName = $monthDate->format('F Y');
                    ?>
                    <a href="<?= $scheduleBaseUrl . '?role=' . $role . '&month=' . $month ?>" 
                       class="month-btn <?= $selectedMonth === $month ? 'active' : '' ?>">
                        <?= esc($monthName) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($selectedMonth)): ?>
        <div style="padding: 12px 16px; background: #e8f5e9; border-radius: 8px; margin-bottom: 24px; color: #2e7d32; font-weight: 600;">
            <i class="fa-solid fa-calendar-range"></i> 
            Viewing schedules for: <strong><?= date('F Y', strtotime($selectedMonth . '-01')) ?></strong>
        </div>
    <?php else: ?>
        <div style="padding: 12px 16px; background: #e8f5e9; border-radius: 8px; margin-bottom: 24px; color: #2e7d32; font-weight: 600;">
            <i class="fa-solid fa-calendar-range"></i> 
            Viewing all schedules
        </div>
    <?php endif; ?>

    <div class="schedule-summary">
        <div class="summary-card">
            <div class="summary-icon" style="background: #dbeafe;">
                <i class="fa-solid fa-calendar-check" style="color: #3b82f6;"></i>
            </div>
            <div class="summary-content">
                <div class="summary-label">Total Schedules</div>
                <div class="summary-value"><?= count($schedules) ?></div>
            </div>
        </div>
        <?php if ($role === 'doctor'): ?>
            <div class="summary-card">
                <div class="summary-icon" style="background: #fef3c7;">
                    <i class="fa-solid fa-calendar-days" style="color: #f59e0b;"></i>
                </div>
                <div class="summary-content">
                    <div class="summary-label">Appointments</div>
                    <div class="summary-value"><?= count($appointments) ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if (empty($schedules)): ?>
        <div class="empty-state">
            <i class="fa-solid fa-calendar-xmark" style="font-size: 48px; color: #94a3b8; margin-bottom: 16px;"></i>
            <p style="color: #64748b; font-size: 16px;">No schedules found<?= !empty($selectedMonth) ? ' for the selected month.' : '.' ?></p>
        </div>
    <?php else: ?>
        <?php if (!empty($selectedMonth)): ?>
            <!-- Show schedules for selected month grouped by date -->
            <div class="schedule-timeline">
                <?php foreach ($schedulesByDate as $date => $daySchedules): ?>
                    <div class="timeline-day">
                        <div class="day-header">
                            <div class="day-date">
                                <i class="fa-solid fa-calendar-days"></i>
                                <?= date('l, F d, Y', strtotime($date)) ?>
                            </div>
                            <div class="day-count">
                                <?= count($daySchedules) ?> schedule(s)
                            </div>
                        </div>
                        <div class="day-schedules">
                            <?php foreach ($daySchedules as $schedule): ?>
                                <div class="schedule-card">
                                    <div class="schedule-time-badge">
                                        <i class="fa-solid fa-clock"></i>
                                        <strong><?= date('h:i A', strtotime($schedule['start_time'])) ?> - <?= date('h:i A', strtotime($schedule['end_time'])) ?></strong>
                                    </div>
                                    <div class="schedule-info">
                                        <div style="display: flex; flex-direction: column; gap: 8px;">
                                            <?php if (isset($schedule['shift_type'])): ?>
                                                <div class="schedule-type">
                                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                                    <span style="padding: 4px 10px; background: #e0e7ff; color: #4338ca; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                        <?= ucfirst(str_replace('_', ' ', $schedule['shift_type'])) ?> Shift
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if ($role === 'nurse' && isset($schedule['station_assignment'])): ?>
                                                <div style="font-size: 13px; color: #64748b;">
                                                    <i class="fa-solid fa-hospital"></i> Station: <strong><?= esc($schedule['station_assignment']) ?></strong>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div style="display: flex; gap: 8px; align-items: center;">
                                            <div class="schedule-status">
                                                <span class="badge badge-<?= esc(strtolower($schedule['status'] ?? 'active')) ?>">
                                                    <?= esc(ucfirst($schedule['status'] ?? 'Active')) ?>
                                                </span>
                                            </div>
                                            <?php if (session()->get('role') === 'admin'): ?>
                                            <a href="<?= base_url('admin/schedule/edit/' . $schedule['id'] . '?role=' . $role) ?>" class="btn-edit-schedule" title="Edit Schedule">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if ($role === 'doctor' && isset($appointmentsByDate[$date])): ?>
                            <div class="day-appointments">
                                <div class="appointments-header">
                                    <i class="fa-solid fa-user-injured"></i> Patient Appointments
                                </div>
                                <?php foreach ($appointmentsByDate[$date] as $appointment): ?>
                                    <div class="appointment-card">
                                        <div class="appointment-time">
                                            <i class="fa-solid fa-clock"></i>
                                            <?= date('h:i A', strtotime($appointment['time'])) ?>
                                        </div>
                                        <div class="appointment-patient">
                                            <i class="fa-solid fa-user"></i>
                                            <?= esc(($appointment['firstname'] ?? '') . ' ' . ($appointment['lastname'] ?? 'N/A')) ?>
                                        </div>
                                        <div class="appointment-status">
                                            <span class="badge badge-<?= esc(strtolower($appointment['status'])) ?>">
                                                <?= esc(ucfirst($appointment['status'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Show all schedules grouped by month -->
            <div class="schedule-timeline">
                <?php foreach ($schedulesByMonth as $month => $monthSchedules): ?>
                    <?php
                    $monthDate = new \DateTime($month . '-01');
                    $monthName = $monthDate->format('F Y');
                    ?>
                    <div class="timeline-month">
                        <div class="month-header">
                            <div class="month-title">
                                <i class="fa-solid fa-calendar-alt"></i>
                                <?= esc($monthName) ?>
                            </div>
                            <div class="month-count">
                                <?php
                                $totalSchedules = 0;
                                foreach ($monthSchedules as $daySchedules) {
                                    $totalSchedules += count($daySchedules);
                                }
                                ?>
                                <?= $totalSchedules ?> schedule(s)
                            </div>
                            <?php
                            // Determine the correct base URL based on user role
                            $currentRole = session()->get('role');
                            $scheduleBaseUrl = ($currentRole === 'admin') 
                                ? base_url('admin/schedule/view/' . $user['id'])
                                : base_url('receptionist/schedule/view/' . $user['id']);
                            ?>
                            <a href="<?= $scheduleBaseUrl . '?role=' . $role . '&month=' . $month ?>" class="btn-view-month">
                                View Month
                            </a>
                        </div>
                        <div class="month-schedules">
                            <?php foreach ($monthSchedules as $date => $daySchedules): ?>
                                <div class="timeline-day">
                                    <div class="day-header">
                                        <div class="day-date">
                                            <i class="fa-solid fa-calendar-days"></i>
                                            <?= date('l, F d, Y', strtotime($date)) ?>
                                        </div>
                                        <div class="day-count">
                                            <?= count($daySchedules) ?> schedule(s)
                                        </div>
                                    </div>
                                    <div class="day-schedules">
                                        <?php foreach ($daySchedules as $schedule): ?>
                                            <div class="schedule-card">
                                                <div class="schedule-time-badge">
                                                    <i class="fa-solid fa-clock"></i>
                                                    <strong><?= date('h:i A', strtotime($schedule['start_time'])) ?> - <?= date('h:i A', strtotime($schedule['end_time'])) ?></strong>
                                                </div>
                                                <div class="schedule-info">
                                                    <div style="display: flex; flex-direction: column; gap: 8px;">
                                                        <?php if (isset($schedule['shift_type'])): ?>
                                                            <div class="schedule-type">
                                                                <i class="fa-solid fa-clock-rotate-left"></i>
                                                                <span style="padding: 4px 10px; background: #e0e7ff; color: #4338ca; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                                    <?= ucfirst(str_replace('_', ' ', $schedule['shift_type'])) ?> Shift
                                                                </span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if ($role === 'nurse' && isset($schedule['station_assignment'])): ?>
                                                            <div style="font-size: 13px; color: #64748b;">
                                                                <i class="fa-solid fa-hospital"></i> Station: <strong><?= esc($schedule['station_assignment']) ?></strong>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div style="display: flex; gap: 8px; align-items: center;">
                                                        <div class="schedule-status">
                                                            <span class="badge badge-<?= esc(strtolower($schedule['status'] ?? 'active')) ?>">
                                                                <?= esc(ucfirst($schedule['status'] ?? 'Active')) ?>
                                                            </span>
                                                        </div>
                                                        <?php if (session()->get('role') === 'admin'): ?>
                                                        <a href="<?= base_url('admin/schedule/edit/' . $schedule['id'] . '?role=' . $role) ?>" class="btn-edit-schedule" title="Edit Schedule">
                                                            <i class="fa-solid fa-edit"></i>
                                                        </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <?php if ($role === 'doctor' && isset($appointmentsByMonth[$month][$date])): ?>
                                        <div class="day-appointments">
                                            <div class="appointments-header">
                                                <i class="fa-solid fa-user-injured"></i> Patient Appointments
                                            </div>
                                            <?php foreach ($appointmentsByMonth[$month][$date] as $appointment): ?>
                                                <div class="appointment-card">
                                                    <div class="appointment-time">
                                                        <i class="fa-solid fa-clock"></i>
                                                        <?= date('h:i A', strtotime($appointment['time'])) ?>
                                                    </div>
                                                    <div class="appointment-patient">
                                                        <i class="fa-solid fa-user"></i>
                                                        <?= esc(($appointment['firstname'] ?? '') . ' ' . ($appointment['lastname'] ?? 'N/A')) ?>
                                                    </div>
                                                    <div class="appointment-status">
                                                        <span class="badge badge-<?= esc(strtolower($appointment['status'])) ?>">
                                                            <?= esc(ucfirst($appointment['status'])) ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
// No date filtering needed - using month buttons instead
</script>

<style>
.admin-module { 
    padding: 24px; 
    background: #f8fafc;
    min-height: 100vh;
}

.module-header { 
    display: flex; 
    justify-content: space-between; 
    align-items: flex-start; 
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.module-header h2 { 
    margin: 0; 
    color: #2e7d32; 
    font-size: 28px;
}

.btn { 
    padding: 10px 20px; 
    border-radius: 6px; 
    text-decoration: none; 
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.schedule-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.summary-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.summary-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.summary-content {
    flex: 1;
}

.summary-label {
    font-size: 13px;
    color: #64748b;
    margin-bottom: 4px;
}

.summary-value {
    font-size: 28px;
    font-weight: 700;
    color: #1e293b;
}

.schedule-timeline {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.timeline-day {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.day-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid #e5e7eb;
}

.day-date {
    font-size: 18px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.day-count {
    font-size: 14px;
    color: #64748b;
    background: #f1f5f9;
    padding: 6px 12px;
    border-radius: 6px;
}

.day-schedules {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.schedule-card {
    background: #f8fafc;
    border-left: 4px solid #3b82f6;
    border-radius: 8px;
    padding: 16px;
    transition: all 0.3s;
}

.schedule-card:hover {
    background: #f1f5f9;
    transform: translateX(4px);
}

.schedule-time-badge {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.schedule-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.schedule-type {
    font-size: 13px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.day-appointments {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 2px solid #e5e7eb;
}

.appointments-header {
    font-size: 16px;
    font-weight: 700;
    color: #f59e0b;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.appointment-card {
    background: #fffbeb;
    border-left: 4px solid #f59e0b;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}

.appointment-time {
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.appointment-patient {
    flex: 1;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.badge {
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-pending { 
    background: #fef3c7; 
    color: #92400e; 
}

.badge-confirmed { 
    background: #d1fae5; 
    color: #047857; 
}

.badge-completed { 
    background: #dbeafe; 
    color: #1e40af; 
}

.badge-cancelled { 
    background: #fee2e2; 
    color: #b91c1c; 
}

.badge-active {
    background: #d1fae5;
    color: #047857;
}

.badge-info {
    background: #dbeafe;
    color: #1e40af;
    padding: 6px 12px;
    font-size: 13px;
}

.month-btn {
    padding: 10px 20px;
    background: white;
    color: #2e7d32;
    border: 2px solid #2e7d32;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
    display: inline-block;
}

.month-btn:hover {
    background: #e8f5e9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(46, 125, 50, 0.2);
}

.month-btn.active {
    background: #2e7d32;
    color: white;
    border-color: #2e7d32;
}

.timeline-month {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 24px;
}

.month-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 3px solid #2e7d32;
    flex-wrap: wrap;
    gap: 12px;
}

.month-title {
    font-size: 24px;
    font-weight: 700;
    color: #2e7d32;
    display: flex;
    align-items: center;
    gap: 10px;
}

.month-count {
    font-size: 14px;
    color: #64748b;
    background: #f1f5f9;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
}

.btn-view-month {
    padding: 8px 16px;
    background: #2e7d32;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-view-month:hover {
    background: #1e5a22;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
}

.month-schedules {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.btn-edit-schedule {
    padding: 6px 12px;
    background: #3b82f6;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-edit-schedule:hover {
    background: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

@media (max-width: 768px) {
    .module-header {
        flex-direction: column;
    }
    
    .day-schedules {
        grid-template-columns: 1fr;
    }
    
    .appointment-card {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
<?= $this->endSection() ?>

