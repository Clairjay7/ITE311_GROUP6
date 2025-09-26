<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div style="display:flex; gap:20px;">
    <!-- Sidebar -->
    <aside style="width:260px; background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow: var(--shadow); padding:16px; height: fit-content;">
        <h3 style="margin:0 0 12px 0; font-size:18px; color:#0f172a;">Navigation</h3>
        <nav style="display:flex; flex-direction:column; gap:8px;">
            <a class="btn btn-secondary" href="<?= base_url('admin/doctors') ?>">Doctors</a>
            <a class="btn btn-secondary" href="<?= base_url('admin/patients') ?>">Patients</a>
            <a class="btn btn-secondary" href="<?= base_url('admin/appointments') ?>">Appointments</a>
            <a class="btn btn-secondary" href="<?= base_url('admin/billing') ?>">Billing</a>
            <a class="btn btn-secondary" href="<?= base_url('admin/inventory') ?>">Inventory</a>
            <a class="btn btn-secondary" href="<?= base_url('admin/reports') ?>">Reports</a>
        </nav>
        <div class="spacer"></div>
        <div class="actions-row">
            <a class="btn" href="<?= base_url('admin/appointments/new') ?>">+ New Appointment</a>
            <a class="btn" href="<?= base_url('admin/patients/new') ?>">+ Add Patient</a>
        </div>
    </aside>

    <!-- Main Content -->
    <section style="flex:1 1 auto;">
        <h2 class="section-title">Admin Dashboard</h2>

        <!-- Summary Cards -->
        <div class="grid grid-4">
            <div class="card"><h5>Total Doctors</h5><h3><?= esc($totalDoctors) ?></h3></div>
            <div class="card"><h5>Total Patients</h5><h3><?= esc($totalPatients) ?></h3></div>
            <div class="card"><h5>Today's Appointments</h5><h3><?= esc($todaysAppointments) ?></h3></div>
            <div class="card"><h5>Pending Bills</h5><h3><?= esc($pendingBills) ?></h3></div>
        </div>

        <div class="spacer"></div>

        <!-- Recent Activity Table -->
        <div class="card">
            <h5>Recent Activity</h5>
            <div style="overflow:auto;">
                <table style="width:100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Date</th>
                            <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Patient</th>
                            <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Doctor</th>
                            <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Status</th>
                            <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentActivity)): ?>
                            <?php foreach ($recentActivity as $row): ?>
                                <tr>
                                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><?= esc(date('Y-m-d H:i', strtotime($row['appointment_date'] ?? 'now'))) ?></td>
                                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><?= esc(($row['patient_first_name'] ?? '') . ' ' . ($row['patient_last_name'] ?? '')) ?></td>
                                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><?= esc($row['doctor_name'] ?? '-') ?></td>
                                    <td style="padding:8px; border-bottom:1px solid #f1f5f9; text-transform:capitalize;"><?= esc($row['status'] ?? '-') ?></td>
                                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn btn-secondary" href="<?= base_url('admin/appointments/view/' . ($row['id'] ?? 0)) ?>">View</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="padding:10px; text-align:center; color:#64748b;">No recent activity.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="spacer"></div>

        <!-- Quick Actions -->
        <div class="actions-row">
            <a class="btn" href="<?= base_url('admin/billing/collect') ?>">Collect Payment</a>
            <a class="btn" href="<?= base_url('admin/reports/generate') ?>">Generate Report</a>
            <a class="btn" href="<?= base_url('admin/inventory/low-stock') ?>">Check Low Stock</a>
        </div>
    </section>
</div>

<?= $this->endSection() ?>


