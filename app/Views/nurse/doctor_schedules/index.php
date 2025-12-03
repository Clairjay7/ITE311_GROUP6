<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Doctor Schedules<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .nurse-page-container {
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
    
    .date-filter {
        display: flex;
        gap: 12px;
        align-items: center;
        background: rgba(255, 255, 255, 0.2);
        padding: 12px 20px;
        border-radius: 10px;
    }
    
    .date-filter input {
        padding: 8px 12px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 8px;
        background: white;
        color: #1e293b;
        font-weight: 600;
    }
    
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .card-header-modern {
        padding: 20px 24px;
        border-bottom: 2px solid #e5e7eb;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    
    .card-header-modern h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .card-body-modern {
        padding: 24px;
    }
    
    .doctor-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
        transition: all 0.3s ease;
    }
    
    .doctor-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .doctor-card.available {
        border-left: 4px solid #10b981;
    }
    
    .doctor-card.busy {
        border-left: 4px solid #f59e0b;
    }
    
    .doctor-card.full {
        border-left: 4px solid #ef4444;
    }
    
    .doctor-card.off_duty {
        border-left: 4px solid #94a3b8;
    }
    
    .doctor-card.no_schedule {
        border-left: 4px solid #f59e0b;
    }
    
    .doctor-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 16px;
    }
    
    .doctor-info h6 {
        margin: 0 0 4px;
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
    }
    
    .doctor-info p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-available {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-busy {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-full {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .status-off_duty {
        background: #f1f5f9;
        color: #64748b;
    }
    
    .status-no_schedule {
        background: #fef3c7;
        color: #92400e;
    }
    
    .schedule-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }
    
    .schedule-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .schedule-label {
        font-size: 11px;
        color: #94a3b8;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .schedule-value {
        font-size: 14px;
        color: #1e293b;
        font-weight: 600;
    }
    
    .progress-bar-container {
        margin-top: 8px;
        background: #f1f5f9;
        border-radius: 8px;
        height: 8px;
        overflow: hidden;
    }
    
    .progress-bar {
        height: 100%;
        border-radius: 8px;
        transition: width 0.3s ease;
    }
    
    .progress-available {
        background: #10b981;
    }
    
    .progress-busy {
        background: #f59e0b;
    }
    
    .progress-full {
        background: #ef4444;
    }
</style>

<div class="nurse-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-calendar-alt"></i>
            Doctor Schedules & Availability
        </h1>
        <div class="date-filter">
            <label style="color: white; font-weight: 600;">Date:</label>
            <input type="date" id="schedule_date" value="<?= esc($selected_date) ?>" onchange="filterByDate()">
        </div>
    </div>

    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-user-md"></i>
                Doctor Availability for <?= date('F d, Y', strtotime($selected_date)) ?>
            </h5>
        </div>
        <div class="card-body-modern">
            <?php if (!empty($doctors)): ?>
                <?php foreach ($doctors as $doctorAvail): ?>
                    <?php 
                    $doctor = $doctorAvail['doctor'];
                    $status = $doctorAvail['status'];
                    $schedules = $doctorAvail['schedules'];
                    $appointments = $doctorAvail['appointments'];
                    $usedSlots = $doctorAvail['used_slots'];
                    $availableSlots = $doctorAvail['available_slots'];
                    $maxSlots = $doctorAvail['max_slots'] ?? 0;
                    $percentage = $maxSlots > 0 ? ($usedSlots / $maxSlots) * 100 : 0;
                    ?>
                    <div class="doctor-card <?= $status ?>">
                        <div class="doctor-header">
                            <div class="doctor-info">
                                <h6>
                                    <i class="fas fa-user-md" style="color: #0288d1; margin-right: 8px;"></i>
                                    <?= esc($doctor['username'] ?? 'Dr. ' . $doctor['id']) ?>
                                </h6>
                                <p><?= esc($doctor['email'] ?? '') ?></p>
                            </div>
                            <span class="status-badge status-<?= $status ?>">
                                <?php
                                switch($status) {
                                    case 'available': echo '<i class="fas fa-check-circle"></i> Available'; break;
                                    case 'busy': echo '<i class="fas fa-exclamation-triangle"></i> Busy'; break;
                                    case 'full': echo '<i class="fas fa-times-circle"></i> Full'; break;
                                    case 'off_duty': echo '<i class="fas fa-moon"></i> Off Duty'; break;
                                    case 'no_schedule': echo '<i class="fas fa-question-circle"></i> No Schedule'; break;
                                    default: echo 'Unknown';
                                }
                                ?>
                            </span>
                        </div>
                        
                        <div class="schedule-details">
                            <?php if (!empty($schedules)): ?>
                                <?php foreach ($schedules as $schedule): ?>
                                    <div class="schedule-item">
                                        <div class="schedule-label">Schedule Time</div>
                                        <div class="schedule-value">
                                            <i class="fas fa-clock"></i> 
                                            <?= date('h:i A', strtotime($schedule['start_time'])) ?> - 
                                            <?= date('h:i A', strtotime($schedule['end_time'])) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="schedule-item">
                                    <div class="schedule-label">Schedule Time</div>
                                    <div class="schedule-value" style="color: #f59e0b;">
                                        <i class="fas fa-exclamation-triangle"></i> No schedule set
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($maxSlots > 0): ?>
                                <div class="schedule-item">
                                    <div class="schedule-label">Appointments</div>
                                    <div class="schedule-value">
                                        <?= $usedSlots ?> / <?= $maxSlots ?> slots
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar progress-<?= $status ?>" 
                                             style="width: <?= min(100, $percentage) ?>%"></div>
                                    </div>
                                </div>
                                
                                <div class="schedule-item">
                                    <div class="schedule-label">Available Slots</div>
                                    <div class="schedule-value" style="color: <?= $availableSlots > 0 ? '#10b981' : '#ef4444' ?>;">
                                        <?= $availableSlots ?> remaining
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="schedule-item">
                                <div class="schedule-label">Current Appointments</div>
                                <div class="schedule-value">
                                    <?= count($appointments) ?> active
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                    <i class="fas fa-user-md" style="font-size: 72px; margin-bottom: 20px; opacity: 0.4; display: block;"></i>
                    <h5 style="margin: 0 0 12px; color: #64748b; font-size: 20px; font-weight: 600;">No Doctors Found</h5>
                    <p style="margin: 0; color: #94a3b8; font-size: 15px;">No active doctors available for this date.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function filterByDate() {
    const date = document.getElementById('schedule_date').value;
    if (date) {
        window.location.href = '<?= site_url('nurse/doctor-schedules') ?>?date=' + date;
    }
}
</script>

<?= $this->endSection() ?>


