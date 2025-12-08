<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>My Schedule<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .doctor-page-container {
        padding: 0;
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
    }
    
    .card-body-modern {
        padding: 24px;
    }
    
    .btn-modern {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        cursor: pointer;
    }
    
    .btn-modern-primary {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
        color: white;
    }
    
    .btn-modern-success {
        background: #10b981;
        color: white;
    }
    
    .btn-modern-success:hover {
        background: #059669;
        color: white;
        transform: translateY(-2px);
    }
    
    .alert-modern {
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .alert-modern-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        border-left: 4px solid #10b981;
    }
    
    .alert-modern-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
    
    /* Month Navigation */
    .month-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 24px;
        padding: 16px;
        background: #f8fafc;
        border-radius: 12px;
    }
    
    .month-btn {
        padding: 10px 16px;
        border: 2px solid #e5e7eb;
        background: white;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #64748b;
    }
    
    .month-btn:hover {
        border-color: #2e7d32;
        color: #2e7d32;
    }
    
    .month-btn.active {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        border-color: #2e7d32;
        color: white;
    }
    
    /* Schedule Table */
    .schedule-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .schedule-table thead {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
    }
    
    .schedule-table th {
        padding: 16px 12px;
        text-align: left;
        font-weight: 700;
        color: white;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .schedule-table td {
        padding: 14px 12px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
    }
    
    .schedule-table tbody tr {
        transition: all 0.2s ease;
    }
    
    .schedule-table tbody tr:hover {
        background: #f0fdf4;
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
        width: 36px;
        height: 36px;
        border-radius: 50%;
        font-weight: 700;
        font-size: 15px;
        margin-right: 8px;
    }
    
    .day-badge.working {
        background: #d1fae5;
        color: #065f46;
    }
    
    .day-badge.weekend {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .time-slot {
        display: inline-block;
        padding: 6px 12px;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 1px solid #a7f3d0;
        border-radius: 6px;
        color: #065f46;
        font-weight: 600;
        font-size: 13px;
        margin: 2px 4px 2px 0;
    }
    
    .rest-badge {
        display: inline-block;
        padding: 6px 12px;
        background: #fef3c7;
        border-radius: 6px;
        color: #92400e;
        font-weight: 600;
        font-size: 12px;
    }
    
    .status-working {
        color: #065f46;
        font-weight: 600;
    }
    
    .status-rest {
        color: #991b1b;
        font-weight: 600;
    }
    
    .month-section {
        display: none;
    }
    
    .month-section.active {
        display: block;
    }
    
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
        margin: 0 0 24px;
        color: #94a3b8;
        font-size: 15px;
    }
    
    /* Summary Stats */
    .schedule-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .summary-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        transition: all 0.2s ease;
    }
    
    .summary-card:hover {
        border-color: #2e7d32;
        transform: translateY(-2px);
    }
    
    .summary-card .number {
        font-size: 28px;
        font-weight: 800;
        color: #2e7d32;
    }
    
    .summary-card .label {
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 600;
        margin-top: 4px;
    }
</style>

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-calendar-alt"></i>
            My Working Schedule - <?= $currentYear ?? date('Y') ?>
        </h1>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="<?= site_url('doctor/consultations/upcoming') ?>" class="btn-modern btn-modern-success">
                <i class="fas fa-clock"></i>
                Upcoming Consultations
            </a>
            <a href="<?= site_url('doctor/consultations/create') ?>" class="btn-modern btn-modern-primary">
                <i class="fas fa-plus"></i>
                New Consultation
            </a>
        </div>
    </div>
    
    <div class="modern-card">
        <div class="card-body-modern">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert-modern alert-modern-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert-modern alert-modern-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($scheduleByMonth ?? [])): ?>
                <!-- Info Box -->
                <div class="info-box">
                    <h4><i class="fas fa-info-circle"></i> Working Schedule Information</h4>
                    <p>
                        Your working schedule has been created by the administrator. Below is your schedule for the year.
                    </p>
                </div>

                <!-- Summary Stats -->
                <?php 
                $totalWorkingDays = 0;
                $totalRestDays = 0;
                $totalSchedules = 0;
                foreach ($scheduleByMonth as $month => $days) {
                    foreach ($days as $day => $dayData) {
                        if (!empty($dayData['time_slots'])) {
                            $totalWorkingDays++;
                            $totalSchedules += count($dayData['time_slots']);
                        } elseif (in_array($dayData['day_name'], ['Saturday', 'Sunday'])) {
                            $totalRestDays++;
                        }
                    }
                }
                ?>
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
                        <div class="number" style="color: #0288d1;"><?= $totalSchedules ?></div>
                        <div class="label">Total Shifts</div>
                    </div>
                </div>

                <!-- Month Navigation -->
                <div class="month-nav">
                    <?php 
                    $monthIndex = 0;
                    $currentMonth = date('F Y');
                    foreach ($scheduleByMonth as $month => $days): 
                        $isCurrentMonth = ($month === $currentMonth);
                    ?>
                        <button class="month-btn <?= $isCurrentMonth ? 'active' : '' ?>" onclick="showMonth('month-<?= $monthIndex ?>', this)">
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
                foreach ($scheduleByMonth as $month => $days): 
                    $isCurrentMonth = ($month === $currentMonth);
                    ksort($days);
                ?>
                    <div id="month-<?= $monthIndex ?>" class="month-section <?= $isCurrentMonth ? 'active' : '' ?>">
                        <h2 style="color: #2e7d32; margin-bottom: 20px; font-size: 22px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-calendar-alt"></i> <?= esc($month) ?>
                        </h2>
                        
                        <div style="overflow-x: auto;">
                            <table class="schedule-table">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">Day</th>
                                        <th style="width: 120px;">Date</th>
                                        <th style="width: 100px;">Status</th>
                                        <th>Working Hours</th>
                                        <th style="min-width: 250px;">Appointments</th>
                                        <th style="min-width: 250px;">Admitted Patients</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($days as $day => $dayData): 
                                        $isWeekend = in_array($dayData['day_name'], ['Saturday', 'Sunday']);
                                        $admittedPatients = $dayData['admitted_patients'] ?? [];
                                        $consultations = $dayData['consultations'] ?? [];
                                        $hasSchedule = !empty($dayData['time_slots']);
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
                                                <?php elseif ($hasSchedule): ?>
                                                    <span class="status-working"><i class="fas fa-check-circle"></i> Working</span>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8;">No schedule</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($hasSchedule): ?>
                                                    <?php foreach ($dayData['time_slots'] as $timeSlot): ?>
                                                        <span class="time-slot">
                                                            <i class="fas fa-clock"></i> <?= esc($timeSlot) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                <?php elseif ($isWeekend): ?>
                                                    <span class="rest-badge">
                                                        <i class="fas fa-bed"></i> No Schedule - Rest Day
                                                    </span>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8;">No schedule</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($consultations)): ?>
                                                    <div style="display: flex; flex-direction: column; gap: 8px;">
                                                        <?php foreach ($consultations as $consult): ?>
                                                            <div style="padding: 8px 12px; background: #e0f2fe; border-left: 3px solid #0288d1; border-radius: 6px;">
                                                                <div style="font-weight: 600; color: #0288d1; font-size: 13px; margin-bottom: 4px;">
                                                                    <i class="fas fa-user-md"></i> <?= esc($consult['patient_name']) ?>
                                                                </div>
                                                                <div style="font-size: 12px; color: #64748b;">
                                                                    <i class="fas fa-clock"></i> <?= date('g:i A', strtotime($consult['consultation_time'])) ?>
                                                                </div>
                                                                <?php if (!empty($consult['notes'])): ?>
                                                                    <div style="font-size: 11px; color: #94a3b8; margin-top: 4px; font-style: italic;">
                                                                        <?= esc(substr($consult['notes'], 0, 50)) ?><?= strlen($consult['notes']) > 50 ? '...' : '' ?>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8; font-size: 13px;">No appointments</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($admittedPatients)): ?>
                                                    <div style="display: flex; flex-direction: column; gap: 8px;">
                                                        <?php foreach ($admittedPatients as $admitted): ?>
                                                            <div style="background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%); padding: 10px 12px; border-radius: 8px; border-left: 3px solid #0288d1;">
                                                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                                                    <i class="fas fa-user-injured" style="color: #0288d1;"></i>
                                                                    <strong style="color: #1e293b; font-size: 13px;">
                                                                        <?= esc($admitted['name']) ?>
                                                                    </strong>
                                                                </div>
                                                                <div style="font-size: 12px; color: #64748b; margin-left: 24px;">
                                                                    <i class="fas fa-bed"></i> Room: <?= esc($admitted['room_number']) ?>
                                                                    <?php if (!empty($admitted['ward']) && $admitted['ward'] !== 'N/A'): ?>
                                                                        - <?= esc($admitted['ward']) ?>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span style="color: #94a3b8; font-size: 13px;">—</span>
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

            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h5>No Schedule Found</h5>
                    <p>Your working schedule has not been created yet.</p>
                    <p style="color: #94a3b8; font-size: 13px; margin-top: 8px;">
                        Please contact the administrator to create your working schedule.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showMonth(monthId, button) {
    // Hide all month sections
    document.querySelectorAll('.month-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.month-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected month
    document.getElementById(monthId).classList.add('active');
    
    // Add active class to clicked button
    button.classList.add('active');
}

// Auto-scroll to current month button if exists
document.addEventListener('DOMContentLoaded', function() {
    const activeBtn = document.querySelector('.month-btn.active');
    if (activeBtn) {
        activeBtn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
});
</script>

<?= $this->endSection() ?>
