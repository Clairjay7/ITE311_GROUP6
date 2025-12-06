<?php
$type = $filterType ?? '';
$roomType = $roomType ?? '';
$query = $query ?? '';
?>
<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <a href="<?= base_url('admin/patients/create') ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Add New Patient
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Search and Filter Form -->
    <form method="get" class="search-filter-form" id="filterForm">
        <div class="search-filter-row">
            <div class="filter-group">
                <label>Patient Type:</label>
                <select name="type" class="form-select" id="patientTypeFilter" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="In-Patient" <?= $type === 'In-Patient' ? 'selected' : '' ?>>In-Patient</option>
                    <option value="Out-Patient" <?= $type === 'Out-Patient' ? 'selected' : '' ?>>Out-Patient</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Room Type:</label>
                <select name="room_type" class="form-select" id="roomTypeFilter" onchange="this.form.submit()">
                    <option value="">All Room Types</option>
                    <option value="Private" <?= $roomType === 'Private' ? 'selected' : '' ?>>Private</option>
                    <option value="Semi-Private" <?= $roomType === 'Semi-Private' ? 'selected' : '' ?>>Semi-Private</option>
                    <option value="Ward" <?= $roomType === 'Ward' ? 'selected' : '' ?>>Ward</option>
                    <option value="ICU" <?= $roomType === 'ICU' ? 'selected' : '' ?>>ICU</option>
                    <option value="Isolation" <?= $roomType === 'Isolation' ? 'selected' : '' ?>>Isolation</option>
                    <option value="NICU" <?= $roomType === 'NICU' ? 'selected' : '' ?>>NICU</option>
                    <option value="ER" <?= $roomType === 'ER' ? 'selected' : '' ?>>ER</option>
                </select>
            </div>
            <div class="search-group">
                <input type="text" name="q" class="form-input" placeholder="Search by patient name, doctor name, or room number..." value="<?= esc($query ?? '') ?>" id="searchInput" />
            </div>
            <div class="button-group">
                <button type="submit" class="btn btn-filter">Search</button>
                <a href="<?= base_url('admin/patients') ?>" class="btn btn-reset">Reset</a>
            </div>
        </div>
    </form>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Birthdate</th>
                    <th>Gender</th>
                    <th>Contact</th>
                    <th>Assigned Doctor</th>
                    <th>Room Type</th>
                    <th>Room Number</th>
                    <th>Bed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($patients)): ?>
                    <tr>
                        <td colspan="11" class="text-center">No patients found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($patients as $patient): ?>
                        <tr>
                            <td>#<?= esc($patient['patient_id'] ?? $patient['id']) ?></td>
                            <td><?= esc($patient['full_name'] ?? ($patient['first_name'] ?? $patient['firstname'] ?? '') . ' ' . ($patient['last_name'] ?? $patient['lastname'] ?? '')) ?></td>
                            <td>
                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; 
                                    <?php if (($patient['type'] ?? '') === 'In-Patient'): ?>
                                        background: #dbeafe; color: #1e40af;
                                    <?php else: ?>
                                        background: #fef3c7; color: #92400e;
                                    <?php endif; ?>">
                                    <?= esc($patient['type'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td><?= esc($patient['date_of_birth'] ?? $patient['birthdate'] ?? '-') ?></td>
                            <td><?= esc(ucfirst($patient['gender'] ?? '-')) ?></td>
                            <td><?= esc($patient['contact'] ?? '-') ?></td>
                            <td><?= esc($patient['doctor_full_name'] ?? $patient['doctor_name'] ?? 'Not Assigned') ?></td>
                            <td><?= esc($patient['room_type'] ?? '-') ?></td>
                            <td><?= esc($patient['room_num'] ?? '-') ?></td>
                            <td><?= esc($patient['bed_number'] ?? '-') ?></td>
                            <td>
                                <a href="<?= base_url('admin/patients/show/' . ($patient['patient_id'] ?? $patient['id'])) ?>" class="btn btn-sm btn-view">View</a>
                                <a href="<?= base_url('admin/patients/edit/' . ($patient['patient_id'] ?? $patient['id'])) ?>" class="btn btn-sm btn-edit">Edit</a>
                                <a href="<?= base_url('admin/patients/delete/' . ($patient['patient_id'] ?? $patient['id'])) ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.admin-module { padding: 24px; }
.module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.module-header h2 { margin: 0; color: #2e7d32; }
.btn { padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; border: none; cursor: pointer; font-size: 14px; position: relative; z-index: 1; }
.btn-primary { background: #2e7d32; color: white; }
.btn-sm { padding: 6px 12px; font-size: 13px; }
.btn-view { background: #10b981; color: white; margin-right: 6px; }
.btn-edit { background: #3b82f6; color: white; margin-right: 6px; }
.btn-delete { background: #ef4444; color: white; }
.btn-filter { background: #2e7d32; color: white; }
.btn-reset { background: #6b7280; color: white; }
.search-filter-form { background: white; padding: 16px; border-radius: 8px; margin-bottom: 20px; }
.search-filter-row { display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; }
.filter-group, .search-group { flex: 1; min-width: 200px; }
.filter-group label { display: block; margin-bottom: 6px; font-weight: 500; color: #374151; font-size: 14px; }
.form-select, .form-input { width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
.form-input:focus, .form-select:focus { outline: none; border-color: #2e7d32; box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1); }
.button-group { display: flex; gap: 8px; }
.table-container { background: white; border-radius: 8px; overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; min-width: 1200px; }
.data-table th { background: #e8f5e9; padding: 12px; text-align: left; font-weight: 600; color: #2e7d32; font-size: 13px; white-space: nowrap; }
.data-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
.text-center { text-align: center; }
.alert { padding: 12px; border-radius: 6px; margin-bottom: 16px; }
.alert-success { background: #d1fae5; color: #047857; }
.alert-error { background: #fee2e2; color: #b91c1c; }
</style>
<script>
// Auto-submit form when Enter key is pressed in search input
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterForm.submit();
            }
        });
    }
});
</script>
<?= $this->endSection() ?>

