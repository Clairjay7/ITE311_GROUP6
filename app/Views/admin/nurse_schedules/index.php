<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Nurse Schedules<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .admin-module {
        padding: 24px;
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
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
        color: white;
    }
    
    .btn-sm {
        padding: 6px 12px;
        font-size: 13px;
    }
    
    .btn-edit {
        background: #3b82f6;
        color: white;
        margin-right: 8px;
    }
    
    .btn-delete {
        background: #ef4444;
        color: white;
    }
    
    .filter-section {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        align-items: end;
    }
    
    .filter-group {
        flex: 1;
        min-width: 200px;
    }
    
    .filter-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }
    
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #2e7d32;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }
    
    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table thead {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f4 100%);
    }
    
    .data-table th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #2e7d32;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #c8e6c9;
    }
    
    .data-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
    }
    
    .data-table tbody tr:hover {
        background: #f8fafc;
    }
    
    .badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .badge-morning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
    }
    
    .badge-night {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
    }
    
    .badge-active {
        background: #d1fae5;
        color: #065f46;
    }
    
    .badge-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .badge-on_leave {
        background: #fef3c7;
        color: #92400e;
    }
    
    .shift-section {
        margin-bottom: 32px;
    }
    
    .shift-header {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        color: white;
        padding: 16px 20px;
        border-radius: 12px 12px 0 0;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        font-size: 18px;
    }
    
    .shift-header.night {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
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
    }
    
    .text-center {
        text-align: center;
    }
    
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }
    
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
</style>

<div class="admin-module">
    <div class="module-header">
        <h2><i class="fas fa-user-nurse"></i> Nurse Schedules</h2>
        <div style="display: flex; gap: 12px;">
            <a href="<?= base_url('admin/nurse-schedules/bulk-assign') ?>" class="btn btn-primary" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="fas fa-users"></i> Bulk Assign
            </a>
            <a href="<?= base_url('admin/nurse-schedules/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Schedule
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="<?= base_url('admin/nurse-schedules') ?>" style="display: flex; gap: 16px; flex-wrap: wrap; width: 100%;">
            <div class="filter-group">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" class="form-control" value="<?= esc($selectedDate) ?>" required>
            </div>
            <div class="filter-group">
                <label for="nurse_id">Nurse (Optional)</label>
                <select id="nurse_id" name="nurse_id" class="form-control">
                    <option value="">All Nurses</option>
                    <?php foreach ($nurses as $nurse): ?>
                        <option value="<?= esc($nurse['id']) ?>" <?= $selectedNurse == $nurse['id'] ? 'selected' : '' ?>>
                            <?= esc($nurse['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group" style="display: flex; align-items: end;">
                <button type="submit" class="btn btn-primary" style="margin: 0;">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Morning Shift Section -->
    <div class="shift-section">
        <div class="shift-header">
            <i class="fas fa-sun"></i> Morning Shift (6 hours)
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nurse</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schedulesByShift['morning'])): ?>
                        <tr>
                            <td colspan="6" class="text-center empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <p>No morning shift schedules for this date.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($schedulesByShift['morning'] as $schedule): ?>
                            <tr>
                                <td><strong><?= esc($schedule['nurse_name'] ?? 'N/A') ?></strong></td>
                                <td><?= esc(date('M d, Y', strtotime($schedule['shift_date']))) ?></td>
                                <td>
                                    <strong><?= esc(date('h:i A', strtotime($schedule['start_time']))) ?></strong> - 
                                    <?= esc(date('h:i A', strtotime($schedule['end_time']))) ?>
                                </td>
                                <td>
                                    <span class="badge badge-morning">6 hours</span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= esc($schedule['status']) ?>">
                                        <?= esc(ucfirst(str_replace('_', ' ', $schedule['status']))) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/nurse-schedules/edit/' . $schedule['id']) ?>" class="btn btn-sm btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?= base_url('admin/nurse-schedules/delete/' . $schedule['id']) ?>" 
                                       class="btn btn-sm btn-delete" 
                                       onclick="return confirm('Are you sure you want to delete this schedule?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Night Shift Section -->
    <div class="shift-section">
        <div class="shift-header night">
            <i class="fas fa-moon"></i> Night Shift (6 hours)
        </div>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nurse</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($schedulesByShift['night'])): ?>
                        <tr>
                            <td colspan="6" class="text-center empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <p>No night shift schedules for this date.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($schedulesByShift['night'] as $schedule): ?>
                            <tr>
                                <td><strong><?= esc($schedule['nurse_name'] ?? 'N/A') ?></strong></td>
                                <td><?= esc(date('M d, Y', strtotime($schedule['shift_date']))) ?></td>
                                <td>
                                    <strong><?= esc(date('h:i A', strtotime($schedule['start_time']))) ?></strong> - 
                                    <?= esc(date('h:i A', strtotime($schedule['end_time']))) ?>
                                </td>
                                <td>
                                    <span class="badge badge-night">6 hours</span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= esc($schedule['status']) ?>">
                                        <?= esc(ucfirst(str_replace('_', ' ', $schedule['status']))) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/nurse-schedules/edit/' . $schedule['id']) ?>" class="btn btn-sm btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="<?= base_url('admin/nurse-schedules/delete/' . $schedule['id']) ?>" 
                                       class="btn btn-sm btn-delete" 
                                       onclick="return confirm('Are you sure you want to delete this schedule?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Auto-submit form when date changes
    document.getElementById('date').addEventListener('change', function() {
        this.form.submit();
    });
</script>

<?= $this->endSection() ?>

