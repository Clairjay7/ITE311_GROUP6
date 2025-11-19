<?php
/** @var array $room */
/** @var array $patients */
?>
<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Assign Patient to Room<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/patient-register.css?v=20251119') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="register-page container py-4">
  <div class="page-header mb-3 d-flex justify-content-between align-items-center">
    <h3 class="page-title mb-0">Assign Patient to Room <?= esc($room['room_number'] ?? '') ?></h3>
    <a href="<?= site_url('receptionist/rooms/ward/' . ($room['ward'] === 'Pedia Ward' ? 'pedia' : ($room['ward'] === 'Male Ward' ? 'male' : 'female'))) ?>" class="btn btn-outline-secondary">Back to Ward</a>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="post" action="<?= site_url('receptionist/rooms/assign/' . $room['id']) ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
          <label class="form-label">Room</label>
          <input type="text" class="form-control" value="<?= esc($room['ward'] . ' - Room ' . $room['room_number']) ?>" disabled>
        </div>
        <div class="mb-3">
          <label class="form-label">Select Patient (In-Patient)</label>
          <select name="patient_id" class="form-select" required>
            <option value="">-- Select Patient --</option>
            <?php foreach ($patients as $p): ?>
              <option value="<?= esc($p['patient_id']) ?>">
                <?= esc($p['patient_id']) ?> - <?= esc($p['full_name'] ?? ($p['first_name'] . ' ' . $p['last_name'])) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="d-flex gap-2 mt-3">
          <button type="submit" class="btn btn-primary">Assign</button>
          <a href="<?= site_url('receptionist/rooms/ward/' . ($room['ward'] === 'Pedia Ward' ? 'pedia' : ($room['ward'] === 'Male Ward' ? 'male' : 'female'))) ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
