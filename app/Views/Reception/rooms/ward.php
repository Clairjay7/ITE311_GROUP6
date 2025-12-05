<?php
/** @var string $wardName */
/** @var array $rooms */
?>
<?= $this->extend('template/header') ?>
<?= $this->section('title') ?><?= esc($wardName) ?> - Rooms<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/room-list.css?v=20251119') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="room-list container py-4">
  <div class="page-header mb-3 d-flex justify-content-between align-items-center">
    <h3 class="page-title mb-0"><?= esc($wardName) ?> - Room Management</h3>
    <a href="<?= site_url('receptionist/dashboard') ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm table-wrap">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Room</th>
              <th>Beds</th>
              <th>Status</th>
              <th>Patient</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rooms)): foreach ($rooms as $room): ?>
              <tr>
                <td><?= esc($room['room_number']) ?></td>
                <td>
                  <?php if (!empty($room['beds']) && is_array($room['beds'])): ?>
                    <div style="display: flex; flex-direction: column; gap: 4px;">
                      <?php foreach ($room['beds'] as $bed): ?>
                        <div style="display: flex; align-items: center; gap: 8px;">
                          <span style="font-weight: 600;">Bed <?= esc($bed['bed_number']) ?>:</span>
                          <?php if (!empty($bed['current_patient_id'])): ?>
                            <span class="badge bg-danger" style="font-size: 11px;">Occupied</span>
                            <?php if (!empty($bed['patient_name'])): ?>
                              <span style="font-size: 12px; color: #6c757d;">(<?= esc($bed['patient_name']) ?>)</span>
                            <?php endif; ?>
                          <?php else: ?>
                            <span class="badge bg-success" style="font-size: 11px;">Available</span>
                          <?php endif; ?>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php else: ?>
                    <span class="text-muted">No beds</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (($room['status'] ?? '') === 'Occupied'): ?>
                    <span class="badge bg-danger">Occupied</span>
                  <?php else: ?>
                    <span class="badge bg-success">Available</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (($room['status'] ?? '') === 'Occupied' && !empty($room['patient_name'])): ?>
                    <?= esc($room['patient_name']) ?>
                  <?php else: ?>
                    <span class="text-muted">—</span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <?php if (($room['status'] ?? '') === 'Occupied'): ?>
                    <?php if (!empty($room['current_patient_id'])): ?>
                      <a href="<?= site_url('receptionist/patients/show/' . $room['current_patient_id']) ?>" class="btn btn-sm btn-outline-primary">View Patient</a>
                    <?php endif; ?>
                  <?php else: ?>
                    <a href="<?= site_url('receptionist/rooms/assign/' . $room['id']) ?>" class="room-link-action">Assign Patient</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No rooms defined for this ward.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
