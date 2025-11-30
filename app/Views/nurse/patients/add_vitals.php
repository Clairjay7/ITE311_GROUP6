<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Add Vital Signs<?= $this->endSection() ?>

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
    
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
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
</style>

<div class="nurse-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-heartbeat"></i>
            Record Vital Signs - <?= esc(ucfirst($patient['firstname']) . ' ' . ucfirst($patient['lastname'])) ?>
        </h1>
    </div>
    
    <div class="modern-card">
        <div class="card-body-modern">
            <form action="<?= site_url('nurse/patients/store-vitals/' . $patient['id']) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="blood_pressure_systolic" class="form-label">Blood Pressure (Systolic)</label>
                            <input type="number" class="form-control" id="blood_pressure_systolic" name="blood_pressure_systolic" 
                                   value="<?= old('blood_pressure_systolic') ?>" min="0" max="300" placeholder="e.g., 120">
                            <?php if (isset($validation) && $validation->getError('blood_pressure_systolic')): ?>
                                <div class="text-danger"><?= $validation->getError('blood_pressure_systolic') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="blood_pressure_diastolic" class="form-label">Blood Pressure (Diastolic)</label>
                            <input type="number" class="form-control" id="blood_pressure_diastolic" name="blood_pressure_diastolic" 
                                   value="<?= old('blood_pressure_diastolic') ?>" min="0" max="300" placeholder="e.g., 80">
                            <?php if (isset($validation) && $validation->getError('blood_pressure_diastolic')): ?>
                                <div class="text-danger"><?= $validation->getError('blood_pressure_diastolic') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="heart_rate" class="form-label">Heart Rate (bpm)</label>
                            <input type="number" class="form-control" id="heart_rate" name="heart_rate" 
                                   value="<?= old('heart_rate') ?>" min="0" max="300" placeholder="e.g., 72">
                            <?php if (isset($validation) && $validation->getError('heart_rate')): ?>
                                <div class="text-danger"><?= $validation->getError('heart_rate') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="temperature" class="form-label">Temperature (°C)</label>
                            <input type="number" step="0.1" class="form-control" id="temperature" name="temperature" 
                                   value="<?= old('temperature') ?>" min="0" max="120" placeholder="e.g., 36.5">
                            <?php if (isset($validation) && $validation->getError('temperature')): ?>
                                <div class="text-danger"><?= $validation->getError('temperature') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="oxygen_saturation" class="form-label">Oxygen Saturation (%)</label>
                            <input type="number" class="form-control" id="oxygen_saturation" name="oxygen_saturation" 
                                   value="<?= old('oxygen_saturation') ?>" min="0" max="100" placeholder="e.g., 98">
                            <?php if (isset($validation) && $validation->getError('oxygen_saturation')): ?>
                                <div class="text-danger"><?= $validation->getError('oxygen_saturation') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="respiratory_rate" class="form-label">Respiratory Rate (per min)</label>
                            <input type="number" class="form-control" id="respiratory_rate" name="respiratory_rate" 
                                   value="<?= old('respiratory_rate') ?>" min="0" max="100" placeholder="e.g., 16">
                            <?php if (isset($validation) && $validation->getError('respiratory_rate')): ?>
                                <div class="text-danger"><?= $validation->getError('respiratory_rate') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="weight" class="form-label">Weight (kg)</label>
                            <input type="number" step="0.1" class="form-control" id="weight" name="weight" 
                                   value="<?= old('weight') ?>" min="0" placeholder="e.g., 70.5">
                            <?php if (isset($validation) && $validation->getError('weight')): ?>
                                <div class="text-danger"><?= $validation->getError('weight') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="height" class="form-label">Height (cm)</label>
                            <input type="number" step="0.1" class="form-control" id="height" name="height" 
                                   value="<?= old('height') ?>" min="0" placeholder="e.g., 170.0">
                            <?php if (isset($validation) && $validation->getError('height')): ?>
                                <div class="text-danger"><?= $validation->getError('height') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="recorded_at" class="form-label">Recorded Date & Time</label>
                    <input type="datetime-local" class="form-control" id="recorded_at" name="recorded_at" 
                           value="<?= old('recorded_at', date('Y-m-d\TH:i')) ?>">
                </div>
                
                <div class="form-group">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="Additional notes..."><?= old('notes') ?></textarea>
                </div>
                
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px;">
                    <a href="<?= site_url('nurse/patients/details/' . $patient['id']) ?>" class="btn-modern btn-modern-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn-modern btn-modern-primary">
                        <i class="fas fa-save"></i>
                        Save Vital Signs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

