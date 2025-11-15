<?php
helper('text');
?>
<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>In-Patient Rooms<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<style>
.table th, .table td { vertical-align: middle; }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">In-Patient Rooms</h3>
    <a href="<?= site_url('receptionist/patients?type=In-Patient') ?>" class="btn btn-outline-secondary">Back to In-Patients</a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Patient ID</th>
              <th>Full Name</th>
              <th>Admission Date</th>
              <th>Room Number</th>
              <th>Ward</th>
              <th>Private Room</th>
              <th>Doctor</th>
              <th>Department</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!empty($patients)): ?>
            <?php foreach ($patients as $p): ?>
              <tr>
                <td><?= esc($p['patient_id']) ?></td>
                <td><?= esc($p['full_name'] ?? trim(($p['first_name'] ?? '').' '.($p['middle_name'] ?? '').' '.($p['last_name'] ?? ''))) ?></td>
                <td><?= esc($p['admission_date'] ?? 'N/A') ?></td>
                <td><?= esc($p['room_number'] ?? 'N/A') ?></td>
                <td><?= esc($p['ward'] ?? 'N/A') ?></td>
                <td><?= esc($p['private_room'] ?? 'N/A') ?></td>
                <td><?= esc($p['doctor_name'] ?? 'N/A') ?></td>
                <td><?= esc($p['department_name'] ?? 'N/A') ?></td>
                <td>
                  <a class="btn btn-sm btn-primary" href="<?= site_url('receptionist/patients/show/'.($p['patient_id'] ?? '')) ?>">View</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="9" class="text-center text-muted">No in-patient records found.</td>
            </tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
