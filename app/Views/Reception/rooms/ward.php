<?php
/** @var string $wardName */
/** @var array $rooms */
?>
<?= $this->extend('template/header') ?>
<?= $this->section('title') ?><?= esc($wardName) ?> - Rooms<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="container py-4">
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

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Room</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($rooms)): foreach ($rooms as $room): ?>
              <tr>
                <td><?= esc($room['room_number']) ?></td>
                <td>
                  <?php if (($room['status'] ?? '') === 'Occupied'): ?>
                    <span class="badge bg-danger">Occupied</span>
                  <?php else: ?>
                    <span class="badge bg-success">Available</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr>
                <td colspan="2" class="text-center text-muted py-4">No rooms defined for this ward.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
