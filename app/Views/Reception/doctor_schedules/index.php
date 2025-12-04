<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Doctor Schedules<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .reception-page-container {
        padding: 0;
    }
    
    .page-header {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(2, 136, 209, 0.2);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .page-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .page-header h1 i {
        font-size: 32px;
    }
    
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .card-body-modern {
        padding: 24px;
    }
    
    /* Filter Section */
    .filter-section {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        border: 1px solid #e5e7eb;
    }
    
    .filter-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        color: #1e293b;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .filter-select:focus {
        outline: none;
        border-color: #0288d1;
        box-shadow: 0 0 0 3px rgba(2, 136, 209, 0.1);
    }
    
    .filter-btn {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
    }
    
    /* Doctor Card */
    .doctor-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        margin-bottom: 32px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .doctor-card-header {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        color: white;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .doctor-name {
        font-size: 22px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .doctor-card-body {
        padding: 24px;
    }
    
    /* Info Box */
    .info-box {
        padding: 16px 20px;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-radius: 12px;
        border-left: 4px solid #0288d1;
        margin-bottom: 24px;
    }
    
    .info-box h4 {
        margin: 0 0 8px;
        color: #0288d1;
        font-size: 16px;
    }
    
    .info-box p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }
    
    .info-box .highlight {
        display: inline-block;
        padding: 2px 8px;
        background: white;
        border-radius: 4px;
        font-weight: 600;
        margin: 0 4px;
    }
    
    /* Summary Stats */
    .schedule-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }
    
    .summary-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px;
        text-align: center;
        transition: all 0.2s ease;
    }
    
    .summary-card:hover {
        border-color: #0288d1;
        transform: translateY(-2px);
    }
    
    .summary-card .number {
        font-size: 24px;
        font-weight: 800;
        color: #0288d1;
    }
    
    .summary-card .label {
        font-size: 11px;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
        margin-top: 2px;
    }
    
    /* Month Navigation */
    .month-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 20px;
        padding: 12px;
        background: #f8fafc;
        border-radius: 10px;
    }
    
    .month-btn {
        padding: 8px 12px;
        border: 2px solid #e5e7eb;
        background: white;
        border-radius: 6px;
        font-weight: 600;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #64748b;
    }
    
    .month-btn:hover {
        border-color: #0288d1;
        color: #0288d1;
    }
    
    .month-btn.active {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        border-color: #0288d1;
        color: white;
    }
    
    /* Schedule Table */
    .schedule-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        font-size: 13px;
    }
    
    .schedule-table thead {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
    }
    
    .schedule-table th {
        padding: 14px 10px;
        text-align: left;
        font-weight: 700;
        color: white;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .schedule-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .schedule-table tbody tr {
        transition: all 0.2s ease;
    }
    
    .schedule-table tbody tr:hover {
        background: #f0f9ff;
    }
    
    .schedule-table tbody tr.weekend {
        background: #fef2f2;
    }
    
    .schedule-table tbody tr.weekend:hover {
        background: #fee2e2;
    }
    
    .day-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-weight: 700;
        font-size: 14px;
    }
    
    .day-badge.working {
        background: #dbeafe;
        color: #1d4ed8;
    }
    
    .day-badge.weekend {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .time-slot {
        display: inline-block;
        padding: 5px 10px;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 1px solid #a7f3d0;
        border-radius: 5px;
        color: #065f46;
        font-weight: 600;
        font-size: 12px;
        margin: 2px 4px 2px 0;
    }
    
    .rest-badge {
        display: inline-block;
        padding: 5px 10px;
        background: #fef3c7;
        border-radius: 5px;
        color: #92400e;
        font-weight: 600;
        font-size: 11px;
    }
    
    .status-working {
        color: #065f46;
        font-weight: 600;
        font-size: 12px;
    }
    
    .status-rest {
        color: #991b1b;
        font-weight: 600;
        font-size: 12px;
    }
    
    .month-section {
        display: none;
    }
    
    .month-section.active {
        display: block;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }
    
    .empty-state i {
        font-size: 72px;
        margin-bottom: 20px;
        opacity: 0.4;
        color: #cbd5e1;
    }
    
    .empty-state h5 {
        margin: 0 0 12px;
        color: #64748b;
        font-size: 20px;
        font-weight: 600;
    }
    
    .empty-state p {
        margin: 0;
        color: #94a3b8;
        font-size: 15px;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .schedule-table {
            font-size: 12px;
        }
        
        .schedule-table th,
        .schedule-table td {
            padding: 10px 8px;
        }
        
        .time-slot {
            padding: 4px 8px;
            font-size: 11px;
        }
    }
</style>

<div class="reception-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-calendar-alt"></i>
            Doctor Schedules - <?= $currentYear ?? date('Y') ?>
        </h1>
        <a href="<?= site_url('receptionist/dashboard') ?>" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="modern-card">
        <div class="card-body-modern">
            <!-- Filter Section -->
            <div class="filter-section">
                <form method="get" action="<?= site_url('receptionist/doctor-schedules') ?>" style="display: flex; gap: 12px; align-items: end; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 250px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">
                            <i class="fas fa-user-md"></i> Select Doctor
                        </label>
                        <select name="doctor_id" class="filter-select" onchange="this.form.submit()">
                            <option value="">-- All Doctors --</option>
                            <?php foreach ($doctors ?? [] as $doctor): ?>
                                <option value="<?= $doctor['id'] ?>" <?= ($selectedDoctorId ?? null) == $doctor['id'] ? 'selected' : '' ?>>
                                    Dr. <?= esc($doctor['username']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <h4><i class="fas fa-info-circle"></i> Schedule Information</h4>
                <p>
                    <span class="highlight"><i class="fas fa-calendar-week"></i> Mon - Fri</span>
                    <span class="highlight"><i class="fas fa-sun"></i> 9:00 AM - 12:00 PM</span>
                    <span class="highlight"><i class="fas fa-cloud-sun"></i> 1:00 PM - 4:00 PM</span>
                    <span class="highlight" style="background: #fef3c7;"><i class="fas fa-moon"></i> Sat & Sun - Rest</span>
                </p>
            </div>

            <?php if (!empty($scheduleByDoctor ?? [])): ?>
                <?php foreach ($scheduleByDoctor as $doctorSchedule): 
                    $doctorIndex = array_search($doctorSchedule, $scheduleByDoctor);
                    
                    // Calculate stats for this doctor
                    $totalWorkingDays = 0;
                    $totalRestDays = 0;
                    foreach ($doctorSchedule['months'] as $month => $days) {
                        foreach ($days as $day => $dayData) {
                            if (in_array($dayData['day_name'], ['Saturday', 'Sunday'])) {
                                $totalRestDays++;
                            } else {
                                $totalWorkingDays++;
                            }
                        }
                    }
                ?>
                    <div class="doctor-card">
                        <div class="doctor-card-header">
                            <div class="doctor-name">
                                <i class="fas fa-user-md"></i>
                                Dr. <?= esc($doctorSchedule['doctor_name']) ?>
                            </div>
                            <div style="display: flex; gap: 16px; font-size: 13px;">
                                <span><i class="fas fa-briefcase"></i> <?= $totalWorkingDays ?> Working Days</span>
                                <span><i class="fas fa-moon"></i> <?= $totalRestDays ?> Rest Days</span>
                            </div>
                        </div>
                        
                        <div class="doctor-card-body">
                            <!-- Summary Stats -->
                            <div class="schedule-summary">
                                <div class="summary-card">
                                    <div class="number"><?= $totalWorkingDays ?></div>
                                    <div class="label">Working Days</div>
                                </div>
                                <div class="summary-card">
                                    <div class="number" style="color: #f59e0b;"><?= $totalRestDays ?></div>
                                    <div class="label">Rest Days</div>
                                </div>
                                <div class="summary-card">
                                    <div class="number" style="color: #10b981;">6</div>
                                    <div class="label">Hours/Day</div>
                                </div>
                                <div class="summary-card">
                                    <div class="number" style="color: #7c3aed;">12</div>
                                    <div class="label">Months</div>
                                </div>
                            </div>

                            <!-- Month Navigation -->
                            <div class="month-nav">
                                <?php 
                                $monthIndex = 0;
                                $currentMonth = date('F Y');
                                foreach ($doctorSchedule['months'] as $month => $days): 
                                    $isCurrentMonth = ($month === $currentMonth);
                                ?>
                                    <button type="button" class="month-btn <?= $isCurrentMonth ? 'active' : '' ?>" onclick="showMonth('doctor-<?= $doctorIndex ?>-month-<?= $monthIndex ?>', this, 'doctor-<?= $doctorIndex ?>')">
                                        <?= esc(substr($month, 0, 3)) ?>
                                    </button>
                                <?php 
                                    $monthIndex++;
                                endforeach; 
                                ?>
                            </div>

                            <!-- Month Sections -->
                            <?php 
                            $monthIndex = 0;
                            foreach ($doctorSchedule['months'] as $month => $days): 
                                $isCurrentMonth = ($month === $currentMonth);
                                ksort($days);
                            ?>
                                <div id="doctor-<?= $doctorIndex ?>-month-<?= $monthIndex ?>" class="month-section doctor-<?= $doctorIndex ?>-month <?= $isCurrentMonth ? 'active' : '' ?>">
                                    <h3 style="color: #0288d1; margin-bottom: 16px; font-size: 18px; display: flex; align-items: center; gap: 10px;">
                                        <i class="fas fa-calendar-alt"></i> <?= esc($month) ?>
                                    </h3>
                                    
                                    <div style="overflow-x: auto;">
                                        <table class="schedule-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 60px;">Day</th>
                                                    <th style="width: 100px;">Date</th>
                                                    <th style="width: 80px;">Status</th>
                                                    <th>Working Hours</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($days as $day => $dayData): 
                                                    $isWeekend = in_array($dayData['day_name'], ['Saturday', 'Sunday']);
                                                ?>
                                                    <tr class="<?= $isWeekend ? 'weekend' : '' ?>">
                                                        <td>
                                                            <span class="day-badge <?= $isWeekend ? 'weekend' : 'working' ?>">
                                                                <?= esc($day) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong><?= esc($dayData['day_name']) ?></strong>
                                                        </td>
                                                        <td>
                                                            <?php if ($isWeekend): ?>
                                                                <span class="status-rest"><i class="fas fa-moon"></i> Rest</span>
                                                            <?php else: ?>
                                                                <span class="status-working"><i class="fas fa-check-circle"></i> Work</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if (!$isWeekend && !empty($dayData['time_slots'])): ?>
                                                                <?php foreach ($dayData['time_slots'] as $timeSlot): ?>
                                                                    <span class="time-slot">
                                                                        <i class="fas fa-clock"></i> <?= esc($timeSlot) ?>
                                                                    </span>
                                                                <?php endforeach; ?>
                                                            <?php elseif ($isWeekend): ?>
                                                                <span class="rest-badge">
                                                                    <i class="fas fa-bed"></i> No Schedule
                                                                </span>
                                                            <?php else: ?>
                                                                <span style="color: #94a3b8;">No schedule</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php 
                                $monthIndex++;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h5>No Schedules Found</h5>
                    <p>
                        <?= ($selectedDoctorId ?? null) ? 'This doctor has no schedule for this year.' : 'No doctor schedules found for this year.' ?>
                    </p>
                    <p style="margin-top: 12px; font-size: 13px;">
                        Doctors need to access their "My Schedule" page first to generate their schedules.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showMonth(monthId, button, doctorClass) {
    // Hide all month sections for this doctor
    document.querySelectorAll('.' + doctorClass + '-month').forEach(section => {
        section.classList.remove('active');
    });
    
    // Remove active class from all buttons in this doctor's nav
    button.parentElement.querySelectorAll('.month-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected month
    document.getElementById(monthId).classList.add('active');
    
    // Add active class to clicked button
    button.classList.add('active');
}

// Auto-scroll to current month button if exists
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.month-nav').forEach(nav => {
        const activeBtn = nav.querySelector('.month-btn.active');
        if (activeBtn) {
            activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    });
});
</script>

<?= $this->endSection() ?>
