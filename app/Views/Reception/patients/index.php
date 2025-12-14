<?php
 helper('form');
 $type = $filterType ?? '';
 $query = $query ?? '';
?>
<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Patient Records<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/patient-list.css?v=20251119') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="patient-list container py-4">
  <div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h3 class="page-title mb-0">Patient Records</h3>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>

  <form method="get" class="toolbar row g-2 mb-3">
    <div class="col-12 col-md-3">
      <select class="form-select" name="type">
        <option value="">All Types</option>
        <option value="In-Patient" <?= $type==='In-Patient'?'selected':''; ?>>In-Patient</option>
        <option value="Out-Patient" <?= $type==='Out-Patient'?'selected':''; ?>>Out-Patient</option>
      </select>
    </div>
    <div class="col-12 col-md-6">
      <input type="text" class="form-control" name="q" placeholder="Search by name, ID, or doctor" value="<?= esc($query) ?>" />
    </div>
    <div class="col-12 col-md-3 d-grid">
      <button class="btn btn-secondary" type="submit">Filter</button>
    </div>
  </form>

  <?php
    $inPatients = [];
    $outPatients = [];
    if (!empty($patients)) {
      foreach ($patients as $p) {
        if (($p['type'] ?? '') === 'In-Patient') {
          $inPatients[] = $p;
        } elseif (($p['type'] ?? '') === 'Out-Patient') {
          $outPatients[] = $p;
        }
      }
    }
    
    // Ensure both arrays are sorted by patient_id DESC (newest first)
    usort($inPatients, function($a, $b) {
      return (int)$b['patient_id'] <=> (int)$a['patient_id'];
    });
    usort($outPatients, function($a, $b) {
      return (int)$b['patient_id'] <=> (int)$a['patient_id'];
    });

    $showInPatients  = ($type === '' || $type === 'In-Patient');
    $showOutPatients = ($type === '' || $type === 'Out-Patient');
  ?>
  <?php if ($showInPatients): ?>
    <div class="table-wrap table-responsive mb-4">
      <h5 class="mb-3">In-Patients</h5>
      <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Type</th>
            <th>Blood Type</th>
            <th>Visit Type</th>
            <th>Purpose</th>
            <th>Doctor Assigned</th>
            <th>Contact</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($inPatients)): foreach($inPatients as $p): ?>
            <tr>
              <td><?= esc($p['patient_id']) ?></td>
              <td><?= esc($p['full_name']) ?></td>
              <td><span class="badge <?= $p['type']==='In-Patient'?'bg-info':'bg-success' ?>"><?= esc($p['type']) ?></span></td>
              <td>
                <?php if (!empty($p['blood_type'])): ?>
                  <span class="badge bg-danger"><?= esc($p['blood_type']) ?></span>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($p['visit_type'])): ?>
                  <span class="badge bg-secondary"><?= esc($p['visit_type']) ?></span>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td><?= esc($p['purpose'] ?? '-') ?></td>
              <td><?= esc($p['doctor_name'] ?? 'N/A') ?></td>
              <td><?= esc($p['contact'] ?? '-') ?></td>
              <td>
                <a class="btn btn-sm btn-outline-primary" href="<?= site_url('receptionist/patients/show/'.$p['patient_id']) ?>">View</a>
                <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('receptionist/patients/edit/'.$p['patient_id']) ?>">Edit</a>
                <form action="<?= site_url('receptionist/patients/delete/'.$p['patient_id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Delete this record?');">
                  <?= csrf_field() ?>
                  <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="9" class="text-center text-muted py-4 empty">No in-patient records found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <?php if ($showOutPatients): ?>
    <div class="table-wrap table-responsive">
      <h5 class="mb-3">Out-Patients</h5>
      <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Type</th>
            <th>Blood Type</th>
            <th>Visit Type</th>
            <th>Purpose</th>
            <th>Doctor Assigned</th>
            <th>Contact</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($outPatients)): foreach($outPatients as $p): ?>
            <tr>
              <td><?= esc($p['patient_id']) ?></td>
              <td><?= esc($p['full_name']) ?></td>
              <td><span class="badge <?= $p['type']==='In-Patient'?'bg-info':'bg-success' ?>"><?= esc($p['type']) ?></span></td>
              <td>
                <?php if (!empty($p['blood_type'])): ?>
                  <span class="badge bg-danger"><?= esc($p['blood_type']) ?></span>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($p['visit_type'])): ?>
                  <span class="badge bg-secondary"><?= esc($p['visit_type']) ?></span>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td><?= esc($p['purpose'] ?? '-') ?></td>
              <td><?= esc($p['doctor_name'] ?? 'N/A') ?></td>
              <td><?= esc($p['contact'] ?? '-') ?></td>
              <td>
                <a class="btn btn-sm btn-outline-primary" href="<?= site_url('receptionist/patients/show/'.$p['patient_id']) ?>">View</a>
                <a class="btn btn-sm btn-outline-secondary" href="<?= site_url('receptionist/patients/edit/'.$p['patient_id']) ?>">Edit</a>
                <form action="<?= site_url('receptionist/patients/delete/'.$p['patient_id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Delete this record?');">
                  <?= csrf_field() ?>
                  <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="9" class="text-center text-muted py-4 empty">No out-patient records found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<?= $this->endSection() ?>
