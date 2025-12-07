<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
            <div style="display: flex; gap: 8px; align-items: center;">
                <label style="font-weight: 600; color: #2e7d32;">Date:</label>
                <input type="date" id="schedule_date" value="<?= esc($selectedDate ?? date('Y-m-d')) ?>" max="<?= date('Y-m-d', strtotime('+1 year')) ?>" onchange="filterByDate()" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
            <?php if (session()->get('role') === 'admin'): ?>
            <a href="<?= base_url('admin/schedule/create') ?>" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Create Schedule
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Search Bar -->
    <div style="margin-bottom: 20px; background: white; padding: 16px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <input type="text" id="scheduleSearchInput" placeholder="🔍 Search by username, role, specialization, or email..." 
               style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: border-color 0.3s;"
               onkeyup="filterScheduleTable()">
    </div>

    <?php if (empty($usersWithSchedules)): ?>
        <div class="empty-state">
            <i class="fa-solid fa-users" style="font-size: 48px; color: #94a3b8; margin-bottom: 16px;"></i>
            <p style="color: #64748b; font-size: 16px;">No users found.</p>
        </div>
    <?php else: ?>
        <div class="users-schedule-container">
            <?php foreach ($usersWithSchedules as $user): ?>
                <div class="user-schedule-card">
                    <div class="user-header">
                        <div class="user-info">
                            <h3 class="user-name">
                                <i class="fa-solid fa-user-md" style="color: <?= $user['role'] === 'Doctor' ? '#3b82f6' : '#10b981' ?>;"></i>
                                <?= esc($user['username']) ?>
                            </h3>
                            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                <span class="user-role badge-<?= strtolower($user['role']) ?>"><?= esc($user['role']) ?></span>
                                <?php if ($user['role'] === 'Doctor' && !empty($user['specialization'])): ?>
                                    <span class="badge badge-specialization"><?= esc($user['specialization']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px; align-items: flex-end;">
                            <div class="user-email">
                                <i class="fa-solid fa-envelope"></i> <?= esc($user['email']) ?>
                            </div>
                            <a href="<?= (session()->get('role') === 'admin' ? base_url('admin/schedule/view/') : base_url('receptionist/schedule/view/')) . $user['user_id'] . '?role=' . strtolower($user['role']) ?>" class="btn-view-schedule">
                                <i class="fa-solid fa-eye"></i> View Full Schedule
                            </a>
                        </div>
                    </div>

                    <div class="schedule-content">
                        <!-- Working Schedules -->
                        <?php if (!empty($user['schedules'])): ?>
                            <div class="schedule-section">
                                <h4 class="section-title">
                                    <i class="fa-solid fa-calendar-check"></i> Working Schedule
                                    <span class="schedule-count">(<?= count($user['schedules']) ?>)</span>
                                </h4>
                                <div class="schedule-list">
                                    <?php foreach ($user['schedules'] as $schedule): ?>
                                        <div class="schedule-item">
                                            <div class="schedule-details">
                                                <div class="schedule-date">
                                                    <i class="fa-solid fa-calendar-days"></i>
                                                    <?= date('M d, Y', strtotime($schedule['shift_date'])) ?>
                                                </div>
                                                <div class="schedule-time">
                                                    <i class="fa-solid fa-clock"></i>
                                                    <?php if (isset($schedule['start_time']) && isset($schedule['end_time'])): ?>
                                                        <strong><?= date('h:i A', strtotime($schedule['start_time'])) ?> - <?= date('h:i A', strtotime($schedule['end_time'])) ?></strong>
                                                    <?php endif; ?>
                                                    <?php if (isset($schedule['shift_type'])): ?>
                                                        <span style="margin-left: 8px; padding: 2px 8px; background: #e0e7ff; color: #4338ca; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                            <?= ucfirst(str_replace('_', ' ', $schedule['shift_type'])) ?> Shift
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div style="display: flex; gap: 8px; align-items: center;">
                                                <div class="schedule-status">
                                                    <span class="badge badge-<?= esc(strtolower($schedule['status'] ?? 'active')) ?>">
                                                        <?= esc(ucfirst($schedule['status'] ?? 'Active')) ?>
                                                    </span>
                                                </div>
                                                <?php if (session()->get('role') === 'admin'): ?>
                                                <a href="<?= base_url('admin/schedule/edit/' . $schedule['id'] . '?role=' . strtolower($user['role'])) ?>" class="btn-edit-schedule" title="Edit Schedule">
                                                    <i class="fa-solid fa-edit"></i>
                                                </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="schedule-section">
                                <h4 class="section-title">
                                    <i class="fa-solid fa-calendar-check"></i> Working Schedule
                                </h4>
                                <p class="no-schedule">No working schedule for this date.</p>
                            </div>
                        <?php endif; ?>

                        <!-- Patient Appointments (for Doctors) -->
                        <?php if ($user['role'] === 'Doctor' && !empty($user['appointments'])): ?>
                            <div class="schedule-section">
                                <h4 class="section-title">
                                    <i class="fa-solid fa-calendar-days"></i> Patient Appointments
                                </h4>
                                <div class="appointment-list">
                                    <?php foreach ($user['appointments'] as $appointment): ?>
                                        <div class="appointment-item">
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
                            </div>
                        <?php elseif ($user['role'] === 'Doctor' && empty($user['appointments'])): ?>
                            <div class="schedule-section">
                                <h4 class="section-title">
                                    <i class="fa-solid fa-calendar-days"></i> Patient Appointments
                                </h4>
                                <p class="no-schedule">No appointments scheduled for this date.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Check and reset date picker if it's from previous year
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('schedule_date');
    if (dateInput) {
        const selectedDate = new Date(dateInput.value);
        const currentYear = new Date().getFullYear();
        if (selectedDate.getFullYear() < currentYear) {
            dateInput.value = new Date().toISOString().split('T')[0];
            // Optionally reload with current date
            const viewType = document.getElementById('view_type').value;
            if (viewType === 'year') {
                const baseUrl = '<?= session()->get('role') === 'admin' ? base_url('admin/schedule') : base_url('receptionist/schedule') ?>';
                window.location.href = baseUrl + '?view=year&date=' + dateInput.value;
            }
        }
    }
});

function filterByDate() {
    const date = document.getElementById('schedule_date').value;
    const baseUrl = '<?= session()->get('role') === 'admin' ? base_url('admin/schedule') : base_url('receptionist/schedule') ?>';
    window.location.href = baseUrl + '?date=' + date + '&view=date';
}

function filterScheduleTable() {
    const searchInput = document.getElementById('scheduleSearchInput');
    const searchTerm = searchInput.value.toLowerCase();
    const container = document.querySelector('.users-schedule-container');
    
    if (!container) return;
    
    const cards = container.querySelectorAll('.user-schedule-card');
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}
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
    align-items: center; 
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
}

.btn-primary { 
    background: #2e7d32; 
    color: white; 
}

.btn-primary:hover {
    background: #1e5a22;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
}

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.btn-warning {
    background: #f59e0b;
    color: white;
}

.btn-warning:hover {
    background: #d97706;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.alert { 
    padding: 12px 16px; 
    border-radius: 6px; 
    margin-bottom: 16px; 
    font-weight: 500;
}

.alert-success { 
    background: #d1fae5; 
    color: #047857; 
    border-left: 4px solid #10b981;
}

.alert-error { 
    background: #fee2e2; 
    color: #b91c1c; 
    border-left: 4px solid #ef4444;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.users-schedule-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 24px;
}

.user-schedule-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.3s;
}

.user-schedule-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.user-header {
    background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
    padding: 20px;
    color: white;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.user-name {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-role {
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-doctor {
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.badge-nurse {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.badge-specialization {
    background: rgba(139, 92, 246, 0.2);
    color: #7c3aed;
    border: 1px solid rgba(139, 92, 246, 0.3);
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
    letter-spacing: 0.5px;
}

.user-email {
    font-size: 14px;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 6px;
}

.schedule-content {
    padding: 20px;
}

.schedule-section {
    margin-bottom: 24px;
}

.schedule-section:last-child {
    margin-bottom: 0;
}

.section-title {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 16px 0;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e5e7eb;
}

.schedule-count {
    font-size: 14px;
    color: #64748b;
    font-weight: 500;
}

.schedule-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.schedule-date {
    font-size: 12px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}

.schedule-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 16px;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 4px solid #2e7d32;
    transition: all 0.3s;
}

.schedule-list, .appointment-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.schedule-item, .appointment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 4px solid #2e7d32;
    transition: all 0.3s;
}

.schedule-item:hover, .appointment-item:hover {
    background: #f1f5f9;
    transform: translateX(4px);
}

.schedule-time, .appointment-time {
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.appointment-patient {
    flex: 1;
    margin-left: 16px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.no-schedule {
    color: #94a3b8;
    font-style: italic;
    padding: 12px;
    text-align: center;
    background: #f8fafc;
    border-radius: 8px;
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

.btn-view-schedule {
    padding: 8px 16px;
    background: #3b82f6;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s;
    white-space: nowrap;
}

.btn-view-schedule:hover {
    background: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

@media (max-width: 768px) {
    .users-schedule-container {
        grid-template-columns: 1fr;
    }
    
    .module-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .user-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>
<?= $this->endSection() ?>
