<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<style>
    .admin-dashboard {
        display: grid;
        gap: 24px;
    }
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }
    .stat-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
    }
    .stat-card h4 {
        margin: 0;
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }
    .stat-card .value {
        margin-top: 12px;
        font-size: 32px;
        font-weight: 700;
        color: #1f2937;
    }
    .recent-activity {
        background: white;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
    }
    .recent-activity h3 {
        margin: 0 0 16px;
        font-size: 18px;
        color: #1f2937;
    }
    .activity-table {
        width: 100%;
        border-collapse: collapse;
    }
    .activity-table th,
    .activity-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 14px;
        text-align: left;
    }
    .activity-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-completed { background: #d1fae5; color: #047857; }
    .status-cancelled { background: #fee2e2; color: #b91c1c; }
</style>

<div class="admin-dashboard">
    <div class="stat-grid">
        <div class="stat-card">
            <h4>Total Doctors</h4>
            <div class="value"><?= esc($totalDoctors) ?></div>
        </div>
        <div class="stat-card">
            <h4>Total Patients</h4>
            <div class="value"><?= esc($totalPatients) ?></div>
        </div>
        <div class="stat-card">
            <h4>Today's Appointments</h4>
            <div class="value"><?= esc($todaysAppointments) ?></div>
        </div>
        <div class="stat-card">
            <h4>Pending Bills</h4>
            <div class="value"><?= esc($pendingBills) ?></div>
        </div>
    </div>

    <div class="recent-activity">
        <h3>Recent Appointment Activity</h3>
        <?php if (empty($recentActivity)): ?>
            <p style="color: #6b7280;">No recent appointments available.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentActivity as $activity): ?>
                            <tr>
                                <td>#<?= esc($activity['id']) ?></td>
                                <td><?= esc($activity['patient_first_name'] . ' ' . $activity['patient_last_name']) ?></td>
                                <td><?= esc($activity['doctor_name']) ?></td>
                                <td><?= esc(date('M d, Y h:i A', strtotime($activity['appointment_date']))) ?></td>
                                <td>
                                    <span class="status-badge status-<?= esc(strtolower($activity['status'])) ?>">
                                        <?= esc(ucfirst($activity['status'])) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
