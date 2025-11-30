<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Appointment Overview<?= $this->endSection() ?>

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
    }
    
    .page-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
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
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 20px 24px;
        border-bottom: 2px solid #e5e7eb;
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
    
    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #e3f2fd 0%, #f1f8ff 100%);
    }
    
    .table-modern th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #0288d1;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #90caf9;
    }
    
    .table-modern td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
    }
    
    .table-modern tbody tr:hover {
        background: #f8fafc;
    }
    
    .badge-modern {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .btn-modern {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        cursor: pointer;
    }
    
    .btn-modern-success {
        background: #10b981;
        color: white;
    }
    
    .btn-modern-warning {
        background: #f59e0b;
        color: white;
    }
    
    .btn-modern-info {
        background: #0288d1;
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }
    
    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.4;
    }
    
    .status-form {
        display: inline-block;
    }
</style>

<div class="nurse-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-calendar-check"></i>
            Appointment Overview
        </h1>
    </div>
    
    <!-- Today's Appointments -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-calendar-day"></i>
                Today's Appointments
            </h5>
        </div>
        <div class="card-body-modern">
            <?php if (!empty($appointments)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td><strong><?= esc(date('h:i A', strtotime($appointment['appointment_time']))) ?></strong></td>
                                    <td><strong><?= esc($appointment['patient_name'] ?? 'N/A') ?></strong></td>
                                    <td><?= esc($appointment['doctor_name'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                                            <?= esc(ucfirst(str_replace('_', ' ', $appointment['appointment_type'] ?? 'consultation'))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-modern" style="background: <?= 
                                            $appointment['status'] == 'completed' ? '#d1fae5' : 
                                            ($appointment['status'] == 'in_progress' ? '#fef3c7' : 
                                            ($appointment['status'] == 'confirmed' ? '#dbeafe' : '#fee2e2')); 
                                        ?>; color: <?= 
                                            $appointment['status'] == 'completed' ? '#065f46' : 
                                            ($appointment['status'] == 'in_progress' ? '#92400e' : 
                                            ($appointment['status'] == 'confirmed' ? '#1e40af' : '#991b1b')); 
                                        ?>;">
                                            <?= esc(ucfirst(str_replace('_', ' ', $appointment['status']))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form action="<?= site_url('nurse/appointments/update-status/' . $appointment['id']) ?>" method="post" class="status-form">
                                            <?= csrf_field() ?>
                                            <select name="status" class="form-select form-select-sm" style="display: inline-block; width: auto; margin-right: 8px;" onchange="this.form.submit()">
                                                <option value="scheduled" <?= $appointment['status'] == 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                                                <option value="confirmed" <?= $appointment['status'] == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                <option value="in_progress" <?= $appointment['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                                <option value="completed" <?= $appointment['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <p style="margin: 0; color: #64748b;">No appointments scheduled for today.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Upcoming Appointments -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-calendar-week"></i>
                Upcoming Appointments (Next 7 Days)
            </h5>
        </div>
        <div class="card-body-modern">
            <?php if (!empty($upcomingAppointments)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcomingAppointments as $appointment): ?>
                                <tr>
                                    <td><strong><?= esc(date('M d, Y', strtotime($appointment['appointment_date']))) ?></strong></td>
                                    <td><?= esc(date('h:i A', strtotime($appointment['appointment_time']))) ?></td>
                                    <td><strong><?= esc($appointment['patient_name'] ?? 'N/A') ?></strong></td>
                                    <td><?= esc($appointment['doctor_name'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                                            <?= esc(ucfirst(str_replace('_', ' ', $appointment['appointment_type'] ?? 'consultation'))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-modern" style="background: #fef3c7; color: #92400e;">
                                            <?= esc(ucfirst(str_replace('_', ' ', $appointment['status']))) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <p style="margin: 0; color: #64748b;">No upcoming appointments in the next 7 days.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

