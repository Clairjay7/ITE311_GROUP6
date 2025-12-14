<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Appointment Tracker<?= $this->endSection() ?>
<?= $this->section('content') ?>
<style>
.appointments-page {
    padding: 24px;
    background: #f8fafc;
    min-height: 100vh;
}

.page-header {
    background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
    border-radius: 16px;
    padding: 24px 32px;
    margin-bottom: 24px;
    box-shadow: 0 4px 20px rgba(46, 125, 50, 0.2);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-header h3 {
    margin: 0;
    font-size: 26px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-header h3 i {
    font-size: 30px;
}

.month-navigation {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 24px;
}

.month-navigation h4 {
    margin: 0 0 16px 0;
    color: #2e7d32;
    font-size: 18px;
    font-weight: 700;
}

.month-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.month-btn {
    padding: 10px 20px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.2s;
}

.month-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #475569;
}

.month-btn.active {
    background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
    color: white;
    border-color: #2e7d32;
}

.viewing-status {
    padding: 12px 16px;
    background: #e8f5e9;
    border-radius: 8px;
    margin-bottom: 24px;
    color: #2e7d32;
    font-weight: 600;
}

.appointments-calendar {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 24px;
}

.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-bottom: 12px;
}

.calendar-day-header {
    text-align: center;
    font-weight: 700;
    font-size: 12px;
    color: #64748b;
    padding: 8px;
    text-transform: uppercase;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
}

.calendar-day {
    min-height: 120px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 8px;
    background: white;
    position: relative;
    overflow-y: auto;
}

.calendar-day.today {
    border-color: #2e7d32;
    background: #f0fdf4;
}

.calendar-day.other-month {
    opacity: 0.4;
    background: #f8fafc;
}

.day-number {
    font-weight: 700;
    font-size: 14px;
    color: #1e293b;
    margin-bottom: 4px;
}

.appointment-list-in-calendar {
    margin-top: 6px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.appointment-item-calendar {
    background: #dbeafe;
    border-left: 3px solid #3b82f6;
    padding: 4px 6px;
    border-radius: 4px;
    font-size: 10px;
    cursor: pointer;
    transition: all 0.2s;
}

.appointment-item-calendar:hover {
    background: #bfdbfe;
    transform: translateX(2px);
}

.appointment-time-small {
    font-weight: 700;
    color: #1e40af;
    display: block;
    margin-bottom: 2px;
}

.appointment-name-small {
    color: #1e3a8a;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.appointment-more {
    font-size: 9px;
    color: #64748b;
    font-weight: 600;
    text-align: center;
    margin-top: 4px;
    padding: 2px;
}

.available-slots-badge {
    margin-top: 6px;
    padding-top: 6px;
    border-top: 1px solid #e5e7eb;
}

.available-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #86efac;
}

.appointments-list {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.appointments-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
}

.appointment-card {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    background: white;
    transition: all 0.2s;
}

.appointment-card:hover {
    border-color: #2e7d32;
    box-shadow: 0 4px 12px rgba(46, 125, 50, 0.1);
}

.appointment-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e5e7eb;
}

.appointment-date {
    font-weight: 700;
    font-size: 16px;
    color: #1e293b;
}

.appointment-time {
    font-size: 14px;
    color: #64748b;
}

.appointment-patient {
    font-weight: 600;
    font-size: 15px;
    color: #1e293b;
    margin-bottom: 8px;
}

.appointment-doctor {
    font-size: 13px;
    color: #64748b;
    margin-bottom: 4px;
}

.appointment-type {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    background: #f1f5f9;
    color: #475569;
    margin-top: 8px;
}

.appointment-status {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    margin-top: 8px;
}

.appointment-status.scheduled {
    background: #dbeafe;
    color: #1e40af;
}

.appointment-status.confirmed {
    background: #d1fae5;
    color: #065f46;
}

.appointment-status.completed {
    background: #fef3c7;
    color: #92400e;
}

.appointment-status.cancelled {
    background: #fee2e2;
    color: #991b1b;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    display: block;
}
</style>

<div class="appointments-page">
    <div class="page-header">
        <h3>
            <i class="fas fa-calendar-check"></i>
            Appointment Tracker
        </h3>
    </div>

    <!-- Month Navigation -->
    <?php if (!empty($allMonths)): ?>
        <div class="month-navigation">
            <h4>
                <i class="fa-solid fa-calendar-alt"></i> Filter by Month
            </h4>
            <div class="month-buttons">
                <a href="<?= base_url('appointments/list') ?>" 
                   class="month-btn <?= empty($selectedMonth) ? 'active' : '' ?>">
                    All Months
                </a>
                <?php foreach ($allMonths as $month): ?>
                    <?php
                    $monthDate = new \DateTime($month . '-01');
                    $monthName = $monthDate->format('F Y');
                    ?>
                    <a href="<?= base_url('appointments/list?month=' . $month) ?>" 
                       class="month-btn <?= $selectedMonth === $month ? 'active' : '' ?>">
                        <?= esc($monthName) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($selectedMonth)): ?>
        <div class="viewing-status">
            <i class="fa-solid fa-calendar-range"></i> 
            Viewing appointments for: <strong><?= date('F Y', strtotime($selectedMonth . '-01')) ?></strong>
        </div>
    <?php else: ?>
        <div class="viewing-status">
            <i class="fa-solid fa-calendar-range"></i> 
            Viewing all appointments
        </div>
    <?php endif; ?>

    <!-- Calendar Display (Landscape) - Show all months with appointments -->
    <?php if (!empty($allMonths)): ?>
        <?php 
        // If month is selected, show only that month
        // If no month selected, show all months with appointments
        $monthsToDisplay = $selectedMonth ? [$selectedMonth] : $allMonths;
        
        foreach ($monthsToDisplay as $displayMonth): 
            $monthAppointments = $appointmentsByMonth[$displayMonth] ?? [];
        ?>
        <div class="appointments-calendar">
            <?php
            $monthStart = new \DateTime($displayMonth . '-01');
            $monthEnd = clone $monthStart;
            $monthEnd->modify('last day of this month');
            
            // Get first day of week for the month
            $firstDay = (int)$monthStart->format('w'); // 0 = Sunday, 1 = Monday, etc.
            // Adjust to Monday = 0
            $firstDay = $firstDay == 0 ? 6 : $firstDay - 1;
            
            // Get last day of month
            $lastDay = (int)$monthEnd->format('d');
            
            // Get previous month's last days for padding
            $prevMonth = clone $monthStart;
            $prevMonth->modify('last day of previous month');
            $prevMonthLastDay = (int)$prevMonth->format('d');
            ?>
            
            <div style="margin-bottom: 20px;">
                <h4 style="color: #2e7d32; font-weight: 700; font-size: 20px; margin: 0 0 16px 0;">
                    <?= $monthStart->format('F Y') ?>
                </h4>
            </div>
            
            <div class="calendar-header">
                <div class="calendar-day-header">Mon</div>
                <div class="calendar-day-header">Tue</div>
                <div class="calendar-day-header">Wed</div>
                <div class="calendar-day-header">Thu</div>
                <div class="calendar-day-header">Fri</div>
                <div class="calendar-day-header">Sat</div>
                <div class="calendar-day-header">Sun</div>
            </div>
            
            <div class="calendar-grid">
                <?php
                // Fill in previous month's days
                for ($i = $firstDay - 1; $i >= 0; $i--) {
                    $day = $prevMonthLastDay - $i;
                    $dateStr = $prevMonth->format('Y-m') . '-' . sprintf('%02d', $day);
                    ?>
                    <div class="calendar-day other-month">
                        <div class="day-number"><?= $day ?></div>
                    </div>
                <?php } ?>
                
                <?php
                // Current month's days
                $today = date('Y-m-d');
                for ($day = 1; $day <= $lastDay; $day++) {
                    $dateStr = $displayMonth . '-' . sprintf('%02d', $day);
                    $isToday = $dateStr === $today;
                    // Use monthAppointments when showing all months, or appointmentsByDate when month is selected
                    $dayAppointments = $selectedMonth 
                        ? ($appointmentsByDate[$dateStr] ?? [])
                        : ($monthAppointments[$dateStr] ?? []);
                    $appointmentCount = count($dayAppointments);
                    ?>
                    <div class="calendar-day <?= $isToday ? 'today' : '' ?>">
                        <div class="day-number"><?= $day ?></div>
                        <?php if ($appointmentCount > 0): ?>
                            <div class="appointment-list-in-calendar">
                                <?php 
                                // Show up to 3 appointments, then show count
                                $displayCount = min(3, $appointmentCount);
                                for ($i = 0; $i < $displayCount; $i++): 
                                    $apt = $dayAppointments[$i];
                                    $patientName = trim(($apt['patient_first_name'] ?? '') . ' ' . ($apt['patient_last_name'] ?? ''));
                                    if (empty($patientName)) {
                                        $patientName = $apt['patient_name'] ?? 'Patient';
                                    }
                                    $time = substr($apt['appointment_time'] ?? '', 0, 5);
                                    ?>
                                    <div class="appointment-item-calendar" title="<?= esc($patientName) . ' - ' . $time ?>">
                                        <span class="appointment-time-small"><?= $time ?></span>
                                        <span class="appointment-name-small"><?= esc(strlen($patientName) > 12 ? substr($patientName, 0, 12) . '...' : $patientName) ?></span>
                                    </div>
                                <?php endfor; ?>
                                <?php if ($appointmentCount > 3): ?>
                                    <div class="appointment-more">+<?= $appointmentCount - 3 ?> more</div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php 
                        // Show available slots if date is today or future
                        $availableSlots = $availableSlotsByDate[$dateStr] ?? 0;
                        if ($availableSlots > 0 && $dateStr >= date('Y-m-d')): 
                        ?>
                            <div class="available-slots-badge">
                                <span class="available-badge"><?= $availableSlots ?> Available</span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php } ?>
                
                <?php
                // Fill in next month's days to complete the grid (42 cells total for 6 weeks)
                $totalCells = 42;
                $cellsUsed = $firstDay + $lastDay;
                $nextMonthDays = $totalCells - $cellsUsed;
                $nextMonth = clone $monthStart;
                $nextMonth->modify('first day of next month');
                
                for ($day = 1; $day <= $nextMonthDays; $day++) {
                    ?>
                    <div class="calendar-day other-month">
                        <div class="day-number"><?= $day ?></div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Appointments List (Landscape Grid) -->
    <div class="appointments-list">
        <h4 style="color: #2e7d32; font-weight: 700; font-size: 18px; margin: 0 0 20px 0;">
            <i class="fas fa-list"></i> All Appointments
        </h4>
        
        <?php if (!empty($appointments)): ?>
            <div class="appointments-grid">
                <?php foreach ($appointments as $apt): ?>
                    <?php
                    $patientName = trim(($apt['patient_first_name'] ?? '') . ' ' . 
                                       ($apt['patient_middle_name'] ?? '') . ' ' . 
                                       ($apt['patient_last_name'] ?? ''));
                    if (empty($patientName)) {
                        $patientName = $apt['patient_name'] ?? 'N/A';
                    }
                    $doctorName = $apt['doctor_name'] ?? 'N/A';
                    $appointmentDate = $apt['appointment_date'] ?? '';
                    $appointmentTime = substr($apt['appointment_time'] ?? '', 0, 5);
                    $appointmentType = $apt['appointment_type'] ?? 'Consultation';
                    $status = strtolower($apt['status'] ?? 'scheduled');
                    ?>
                    <div class="appointment-card">
                        <div class="appointment-card-header">
                            <div>
                                <div class="appointment-date">
                                    <?= date('M d, Y', strtotime($appointmentDate)) ?>
                                </div>
                                <div class="appointment-time">
                                    <i class="fas fa-clock"></i> <?= $appointmentTime ?>
                                </div>
                            </div>
                        </div>
                        <div class="appointment-patient">
                            <i class="fas fa-user"></i> <?= esc($patientName) ?>
                        </div>
                        <div class="appointment-doctor">
                            <i class="fas fa-user-md"></i> Dr. <?= esc($doctorName) ?>
                        </div>
                        <div>
                            <span class="appointment-type"><?= esc($appointmentType) ?></span>
                            <span class="appointment-status <?= esc($status) ?>"><?= esc(ucfirst($status)) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <p>No appointments found<?= !empty($selectedMonth) ? ' for the selected month.' : '.' ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
