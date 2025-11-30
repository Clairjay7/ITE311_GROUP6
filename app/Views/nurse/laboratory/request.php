<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Create Lab Request<?= $this->endSection() ?>

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
    
    .card-body-modern {
        padding: 32px;
    }
    
    .form-group {
        margin-bottom: 24px;
    }
    
    .form-label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        display: block;
    }
    
    .form-control, .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #0288d1;
        box-shadow: 0 0 0 3px rgba(2, 136, 209, 0.1);
    }
    
    .btn-modern {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        cursor: pointer;
    }
    
    .btn-modern-primary {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
    }
    
    .btn-modern-secondary {
        background: #64748b;
        color: white;
    }
    
    .text-danger {
        color: #ef4444;
        font-size: 13px;
        margin-top: 4px;
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
    
    .badge-modern {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<div class="nurse-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-vial"></i>
            Create Lab Request
        </h1>
    </div>
    
    <!-- Create Request Form -->
    <div class="modern-card">
        <div class="card-body-modern">
            <h3 style="margin-bottom: 24px; color: #1e293b;">New Lab Request</h3>
            <form action="<?= site_url('nurse/laboratory/store-request') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="patient_id" class="form-label">Patient *</label>
                            <select class="form-select" id="patient_id" name="patient_id" required>
                                <option value="">Select Patient</option>
                                <?php foreach ($patients as $patient): ?>
                                    <option value="<?= $patient['id'] ?>" <?= old('patient_id') == $patient['id'] ? 'selected' : '' ?>>
                                        <?= esc(ucfirst($patient['firstname']) . ' ' . ucfirst($patient['lastname'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($validation) && $validation->getError('patient_id')): ?>
                                <div class="text-danger"><?= $validation->getError('patient_id') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="requested_date" class="form-label">Requested Date *</label>
                            <input type="date" class="form-control" id="requested_date" name="requested_date" 
                                   value="<?= old('requested_date', date('Y-m-d')) ?>" required>
                            <?php if (isset($validation) && $validation->getError('requested_date')): ?>
                                <div class="text-danger"><?= $validation->getError('requested_date') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="test_type" class="form-label">Test Type *</label>
                            <input type="text" class="form-control" id="test_type" name="test_type" 
                                   value="<?= old('test_type') ?>" required placeholder="e.g., Blood Test, Urine Test">
                            <?php if (isset($validation) && $validation->getError('test_type')): ?>
                                <div class="text-danger"><?= $validation->getError('test_type') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="test_name" class="form-label">Test Name *</label>
                            <input type="text" class="form-control" id="test_name" name="test_name" 
                                   value="<?= old('test_name') ?>" required placeholder="e.g., Complete Blood Count">
                            <?php if (isset($validation) && $validation->getError('test_name')): ?>
                                <div class="text-danger"><?= $validation->getError('test_name') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="priority" class="form-label">Priority *</label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="routine" <?= old('priority') == 'routine' ? 'selected' : '' ?>>Routine</option>
                                <option value="urgent" <?= old('priority') == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                <option value="stat" <?= old('priority') == 'stat' ? 'selected' : '' ?>>Stat</option>
                            </select>
                            <?php if (isset($validation) && $validation->getError('priority')): ?>
                                <div class="text-danger"><?= $validation->getError('priority') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="instructions" class="form-label">Instructions</label>
                    <textarea class="form-control" id="instructions" name="instructions" rows="4" placeholder="Special instructions for the lab..."><?= old('instructions') ?></textarea>
                </div>
                
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px;">
                    <button type="reset" class="btn-modern btn-modern-secondary">
                        <i class="fas fa-redo"></i>
                        Reset
                    </button>
                    <button type="submit" class="btn-modern btn-modern-primary">
                        <i class="fas fa-save"></i>
                        Create Request
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Pending Requests -->
    <?php if (!empty($pendingRequests)): ?>
        <div class="modern-card">
            <div class="card-body-modern">
                <h3 style="margin-bottom: 24px; color: #1e293b;">Pending Lab Requests</h3>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Test Type</th>
                                <th>Test Name</th>
                                <th>Priority</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingRequests as $request): ?>
                                <tr>
                                    <td><?= esc(date('M d, Y', strtotime($request['created_at']))) ?></td>
                                    <td><strong><?= esc(ucfirst($request['firstname']) . ' ' . ucfirst($request['lastname'])) ?></strong></td>
                                    <td><?= esc($request['test_type']) ?></td>
                                    <td><?= esc($request['test_name']) ?></td>
                                    <td>
                                        <span class="badge-modern" style="background: <?= 
                                            $request['priority'] == 'stat' ? '#fee2e2' : 
                                            ($request['priority'] == 'urgent' ? '#fef3c7' : '#d1fae5'); 
                                        ?>; color: <?= 
                                            $request['priority'] == 'stat' ? '#991b1b' : 
                                            ($request['priority'] == 'urgent' ? '#92400e' : '#065f46'); 
                                        ?>;">
                                            <?= esc(ucfirst($request['priority'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-modern" style="background: #fef3c7; color: #92400e;">
                                            Pending
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

