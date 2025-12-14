<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <div>
            <h2><?= esc($title) ?></h2>
            <div style="display: flex; gap: 12px; align-items: center; margin-top: 8px;">
                <?php if ($doctor && isset($doctor['specialization'])): ?>
                    <span class="badge badge-info"><?= esc($doctor['specialization']) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Month Navigation Buttons -->
    <?php if (!empty($allMonths)): ?>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); margin-bottom: 24px;">
            <h3 style="margin: 0 0 16px 0; color: #2e7d32; font-size: 18px; font-weight: 700;">
                <i class="fa-solid fa-calendar-alt"></i> Filter by Month
            </h3>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <a href="<?= base_url('doctor/schedule') ?>" 
                   class="month-btn <?= empty($selectedMonth) ? 'active' : '' ?>">
                    All Months
                </a>
                <?php foreach ($allMonths as $month): ?>
                    <?php
                    $monthDate = new \DateTime($month . '-01');
                    $monthName = $monthDate->format('F Y');
                    ?>
                    <a href="<?= base_url('doctor/schedule?month=' . $month) ?>" 
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
        <div class="summary-card">
            <div class="summary-icon" style="background: #fef3c7;">
                <i class="fa-solid fa-calendar-days" style="color: #f59e0b;"></i>
            </div>
            <div class="summary-content">
                <div class="summary-label">Appointments</div>
                <div class="summary-value"><?= count($appointments) ?></div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon" style="background: #fce7f3;">
                <i class="fa-solid fa-hospital-user" style="color: #ec4899;"></i>
            </div>
            <div class="summary-content">
                <div class="summary-label">In-Patients</div>
                <div class="summary-value"><?= count($inPatients ?? []) ?></div>
            </div>
        </div>
    </div>

    <?php if (empty($schedules) && empty($appointments) && empty($inPatients ?? [])): ?>
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
                                        <strong><?= date('h:i A', strtotime($schedule['start_time'])) ?><br><?= date('h:i A', strtotime($schedule['end_time'])) ?></strong>
                                    </div>
                                    <div class="schedule-info">
                                        <?php if (isset($schedule['shift_type'])): ?>
                                            <div class="schedule-type">
                                                <span style="display: inline-block; padding: 2px 6px; background: #e0e7ff; color: #4338ca; border-radius: 4px; font-size: 8px; font-weight: 600;">
                                                    <?= ucfirst(str_replace('_', ' ', substr($schedule['shift_type'], 0, 8))) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="schedule-status">
                                                <span class="badge badge-<?= esc(strtolower($schedule['status'] ?? 'active')) ?>">
                                                    <?= esc(ucfirst($schedule['status'] ?? 'Active')) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (isset($appointmentsByDate[$date])): ?>
                            <div class="day-appointments">
                                <div class="appointments-header">
                                    <i class="fa-solid fa-user-injured"></i> Patient Appointments
                                </div>
                                <div class="appointment-list">
                                    <?php foreach ($appointmentsByDate[$date] as $appointment): ?>
                                        <div class="appointment-card">
                                            <div class="appointment-time">
                                                <i class="fa-solid fa-clock"></i>
                                                <span><?= date('h:i A', strtotime($appointment['appointment_time'])) ?></span>
                                            </div>
                                            <div class="appointment-patient">
                                                <i class="fa-solid fa-user"></i>
                                                <?= esc(trim(($appointment['first_name'] ?? '') . ' ' . ($appointment['middle_name'] ?? '') . ' ' . ($appointment['last_name'] ?? ''))) ?>
                                            </div>
                                            <div class="appointment-status">
                                                <span class="badge badge-<?= esc(strtolower($appointment['status'] ?? 'scheduled')) ?>">
                                                    <?= esc(ucfirst($appointment['status'] ?? 'Scheduled')) ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($inPatientsByDate[$date])): ?>
                            <div class="day-appointments">
                                <div class="appointments-header" style="background: #fce7f3; color: #ec4899;">
                                    <i class="fa-solid fa-hospital-user"></i> In-Patient Admissions
                                </div>
                                <div class="appointment-list">
                                    <?php foreach ($inPatientsByDate[$date] as $inPatient): ?>
                                        <div class="appointment-card" style="border-left: 4px solid #ec4899;">
                                            <div class="appointment-time">
                                                <i class="fa-solid fa-calendar-check"></i>
                                                <span><?= date('h:i A', strtotime($inPatient['admission_date'] . ' ' . ($inPatient['admission_time'] ?? '00:00:00'))) ?></span>
                                            </div>
                                            <div class="appointment-patient">
                                                <i class="fa-solid fa-user"></i>
                                                <?= esc(trim(($inPatient['patient_first_name'] ?? $inPatient['first_name'] ?? '') . ' ' . ($inPatient['patient_middle_name'] ?? $inPatient['middle_name'] ?? '') . ' ' . ($inPatient['patient_last_name'] ?? $inPatient['last_name'] ?? ''))) ?>
                                            </div>
                                            <div class="appointment-status">
                                                <span class="badge badge-info">
                                                    <?= esc($inPatient['visit_type'] ?? 'In-Patient') ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
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
                            <a href="<?= base_url('doctor/schedule?month=' . $month) ?>" class="btn-view-month">
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
                                                    <strong><?= date('h:i A', strtotime($schedule['start_time'])) ?><br><?= date('h:i A', strtotime($schedule['end_time'])) ?></strong>
                                                </div>
                                                <div class="schedule-info">
                                                    <?php if (isset($schedule['shift_type'])): ?>
                                                        <div class="schedule-type">
                                                            <span style="display: inline-block; padding: 2px 6px; background: #e0e7ff; color: #4338ca; border-radius: 4px; font-size: 8px; font-weight: 600;">
                                                                <?= ucfirst(str_replace('_', ' ', substr($schedule['shift_type'], 0, 8))) ?>
                                                            </span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="schedule-status">
                                                            <span class="badge badge-<?= esc(strtolower($schedule['status'] ?? 'active')) ?>">
                                                                <?= esc(ucfirst($schedule['status'] ?? 'Active')) ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <?php if (isset($appointmentsByMonth[$month][$date])): ?>
                                        <div class="day-appointments">
                                            <div class="appointments-header">
                                                <i class="fa-solid fa-user-injured"></i> Patient Appointments
                                            </div>
                                            <div class="appointment-list">
                                                <?php foreach ($appointmentsByMonth[$month][$date] as $appointment): ?>
                                                    <div class="appointment-card">
                                                        <div class="appointment-time">
                                                            <i class="fa-solid fa-clock"></i>
                                                            <span><?= date('h:i A', strtotime($appointment['appointment_time'])) ?></span>
                                                        </div>
                                                        <div class="appointment-patient">
                                                            <i class="fa-solid fa-user"></i>
                                                            <?= esc(trim(($appointment['first_name'] ?? '') . ' ' . ($appointment['middle_name'] ?? '') . ' ' . ($appointment['last_name'] ?? ''))) ?>
                                                        </div>
                                                        <div class="appointment-status">
                                                            <span class="badge badge-<?= esc(strtolower($appointment['status'] ?? 'scheduled')) ?>">
                                                                <?= esc(ucfirst($appointment['status'] ?? 'Scheduled')) ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($inPatientsByMonth[$month][$date]) && !empty($inPatientsByMonth[$month][$date])): ?>
                                        <div class="day-appointments">
                                            <div class="appointments-header" style="background: #fce7f3; color: #ec4899;">
                                                <i class="fa-solid fa-hospital-user"></i> In-Patient Admissions
                                            </div>
                                            <div class="appointment-list">
                                                <?php foreach ($inPatientsByMonth[$month][$date] as $inPatient): ?>
                                                    <div class="appointment-card" style="border-left: 4px solid #ec4899;">
                                                        <div class="appointment-time">
                                                            <i class="fa-solid fa-calendar-check"></i>
                                                            <span><?= date('h:i A', strtotime($inPatient['admission_date'] . ' ' . ($inPatient['admission_time'] ?? '00:00:00'))) ?></span>
                                                        </div>
                                                        <div class="appointment-patient">
                                                            <i class="fa-solid fa-user"></i>
                                                            <?= esc(trim(($inPatient['patient_first_name'] ?? $inPatient['first_name'] ?? '') . ' ' . ($inPatient['patient_middle_name'] ?? $inPatient['middle_name'] ?? '') . ' ' . ($inPatient['patient_last_name'] ?? $inPatient['last_name'] ?? ''))) ?>
                                                        </div>
                                                        <div class="appointment-status">
                                                            <span class="badge badge-info">
                                                                <?= esc($inPatient['visit_type'] ?? 'In-Patient') ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
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
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
}

.timeline-day {
    background: white;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.day-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e5e7eb;
    flex-wrap: wrap;
    gap: 8px;
}

.day-date {
    font-size: 14px;
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.day-date i {
    font-size: 12px;
}

.day-count {
    font-size: 11px;
    color: #64748b;
    background: #f1f5f9;
    padding: 4px 10px;
    border-radius: 6px;
    white-space: nowrap;
}

.day-schedules {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 10px;
    margin-bottom: 20px;
}

.schedule-card {
    background: #f8fafc;
    border-left: 3px solid #3b82f6;
    border-radius: 6px;
    padding: 10px;
    transition: all 0.3s;
    display: flex;
    flex-direction: column;
    min-height: 75px;
}

.schedule-card:hover {
    background: #f1f5f9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.schedule-time-badge {
    font-size: 11px;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 8px;
    display: flex;
    align-items: flex-start;
    gap: 4px;
    line-height: 1.3;
}

.schedule-time-badge i {
    font-size: 9px;
    margin-top: 2px;
}

.schedule-time-badge strong {
    font-size: 10px;
    display: block;
    line-height: 1.4;
}

.schedule-info {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
}

.schedule-type {
    font-size: 10px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 4px;
}

.schedule-info > div:last-child {
    display: flex;
    gap: 6px;
    align-items: center;
    margin-top: auto;
    padding-top: 6px;
    border-top: 1px solid #e5e7eb;
    flex-wrap: wrap;
}

.day-appointments {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 2px solid #e5e7eb;
}

.appointments-header {
    font-size: 14px;
    font-weight: 700;
    color: #f59e0b;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.day-appointments .appointment-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 10px;
}

.appointment-card {
    background: #fffbeb;
    border-left: 3px solid #f59e0b;
    border-radius: 6px;
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-height: 75px;
    transition: all 0.3s;
}

.appointment-card:hover {
    background: #fef3c7;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
}

.appointment-time {
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: flex-start;
    gap: 4px;
    font-size: 11px;
}

.appointment-time i {
    font-size: 9px;
    margin-top: 2px;
}

.appointment-patient {
    color: #065f46;
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 600;
    margin-top: 2px;
}

.appointment-patient i {
    font-size: 9px;
}

.appointment-status {
    margin-top: auto;
    padding-top: 6px;
    border-top: 1px solid #e5e7eb;
}

.badge {
    padding: 3px 8px;
    border-radius: 999px;
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
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
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}
</style>
<?= $this->endSection() ?>
