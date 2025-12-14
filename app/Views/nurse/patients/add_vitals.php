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
    
    .vital-comparison {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-left: 8px;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
    }
    
    .vital-improving {
        background: #d1fae5;
        color: #065f46;
    }
    
    .vital-worsening {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .vital-stable {
        background: #fef3c7;
        color: #92400e;
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
                            <label for="blood_pressure_systolic" class="form-label">
                                Blood Pressure (Systolic)
                                <?php if (!empty($previousVitals['blood_pressure_systolic'])): ?>
                                    <span style="font-size: 12px; color: #64748b; font-weight: normal;">(Previous: <?= esc($previousVitals['blood_pressure_systolic']) ?>)</span>
                                <?php endif; ?>
                            </label>
                            <input type="number" class="form-control" id="blood_pressure_systolic" name="blood_pressure_systolic" 
                                   value="<?= old('blood_pressure_systolic') ?>" min="0" max="300" placeholder="e.g., 120"
                                   data-previous="<?= esc($previousVitals['blood_pressure_systolic'] ?? '') ?>"
                                   data-type="bp_systolic">
                            <span id="bp_systolic_comparison" class="vital-comparison" style="display: none;"></span>
                            <?php if (isset($validation) && $validation->getError('blood_pressure_systolic')): ?>
                                <div class="text-danger"><?= $validation->getError('blood_pressure_systolic') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="blood_pressure_diastolic" class="form-label">
                                Blood Pressure (Diastolic)
                                <?php if (!empty($previousVitals['blood_pressure_diastolic'])): ?>
                                    <span style="font-size: 12px; color: #64748b; font-weight: normal;">(Previous: <?= esc($previousVitals['blood_pressure_diastolic']) ?>)</span>
                                <?php endif; ?>
                            </label>
                            <input type="number" class="form-control" id="blood_pressure_diastolic" name="blood_pressure_diastolic" 
                                   value="<?= old('blood_pressure_diastolic') ?>" min="0" max="300" placeholder="e.g., 80"
                                   data-previous="<?= esc($previousVitals['blood_pressure_diastolic'] ?? '') ?>"
                                   data-type="bp_diastolic">
                            <span id="bp_diastolic_comparison" class="vital-comparison" style="display: none;"></span>
                            <?php if (isset($validation) && $validation->getError('blood_pressure_diastolic')): ?>
                                <div class="text-danger"><?= $validation->getError('blood_pressure_diastolic') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="heart_rate" class="form-label">
                                Heart Rate (bpm)
                                <?php if (!empty($previousVitals['heart_rate'])): ?>
                                    <span style="font-size: 12px; color: #64748b; font-weight: normal;">(Previous: <?= esc($previousVitals['heart_rate']) ?>)</span>
                                <?php endif; ?>
                            </label>
                            <input type="number" class="form-control" id="heart_rate" name="heart_rate" 
                                   value="<?= old('heart_rate') ?>" min="0" max="300" placeholder="e.g., 72"
                                   data-previous="<?= esc($previousVitals['heart_rate'] ?? '') ?>"
                                   data-type="heart_rate">
                            <span id="heart_rate_comparison" class="vital-comparison" style="display: none;"></span>
                            <?php if (isset($validation) && $validation->getError('heart_rate')): ?>
                                <div class="text-danger"><?= $validation->getError('heart_rate') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="temperature" class="form-label">
                                Temperature (°C)
                                <?php if (!empty($previousVitals['temperature'])): ?>
                                    <span style="font-size: 12px; color: #64748b; font-weight: normal;">(Previous: <?= esc($previousVitals['temperature']) ?>)</span>
                                <?php endif; ?>
                            </label>
                            <input type="number" step="0.1" class="form-control" id="temperature" name="temperature" 
                                   value="<?= old('temperature') ?>" min="0" max="120" placeholder="e.g., 36.5"
                                   data-previous="<?= esc($previousVitals['temperature'] ?? '') ?>"
                                   data-type="temperature">
                            <span id="temperature_comparison" class="vital-comparison" style="display: none;"></span>
                            <?php if (isset($validation) && $validation->getError('temperature')): ?>
                                <div class="text-danger"><?= $validation->getError('temperature') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="oxygen_saturation" class="form-label">
                                Oxygen Saturation (%)
                                <?php if (!empty($previousVitals['oxygen_saturation'])): ?>
                                    <span style="font-size: 12px; color: #64748b; font-weight: normal;">(Previous: <?= esc($previousVitals['oxygen_saturation']) ?>)</span>
                                <?php endif; ?>
                            </label>
                            <input type="number" class="form-control" id="oxygen_saturation" name="oxygen_saturation" 
                                   value="<?= old('oxygen_saturation') ?>" min="0" max="100" placeholder="e.g., 98"
                                   data-previous="<?= esc($previousVitals['oxygen_saturation'] ?? '') ?>"
                                   data-type="oxygen_saturation">
                            <span id="oxygen_saturation_comparison" class="vital-comparison" style="display: none;"></span>
                            <?php if (isset($validation) && $validation->getError('oxygen_saturation')): ?>
                                <div class="text-danger"><?= $validation->getError('oxygen_saturation') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="respiratory_rate" class="form-label">
                                Respiratory Rate (per min)
                                <?php if (!empty($previousVitals['respiratory_rate'])): ?>
                                    <span style="font-size: 12px; color: #64748b; font-weight: normal;">(Previous: <?= esc($previousVitals['respiratory_rate']) ?>)</span>
                                <?php endif; ?>
                            </label>
                            <input type="number" class="form-control" id="respiratory_rate" name="respiratory_rate" 
                                   value="<?= old('respiratory_rate') ?>" min="0" max="100" placeholder="e.g., 16"
                                   data-previous="<?= esc($previousVitals['respiratory_rate'] ?? '') ?>"
                                   data-type="respiratory_rate">
                            <span id="respiratory_rate_comparison" class="vital-comparison" style="display: none;"></span>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Vital signs comparison logic
    const vitalInputs = document.querySelectorAll('input[data-previous]');
    
    vitalInputs.forEach(input => {
        input.addEventListener('input', function() {
            const currentValue = parseFloat(this.value);
            const previousValue = parseFloat(this.getAttribute('data-previous'));
            const type = this.getAttribute('data-type');
            const comparisonSpan = document.getElementById(type + '_comparison');
            
            if (!comparisonSpan || isNaN(currentValue) || isNaN(previousValue)) {
                if (comparisonSpan) comparisonSpan.style.display = 'none';
                return;
            }
            
            let status = '';
            let className = '';
            let icon = '';
            
            // Define normal ranges and determine if change is good or bad
            const diff = currentValue - previousValue;
            const percentChange = Math.abs((diff / previousValue) * 100);
            
            // Only show comparison if change is significant (more than 2%)
            if (percentChange < 2) {
                status = 'Stable';
                className = 'vital-stable';
                icon = '<i class="fas fa-minus-circle"></i>';
            } else {
                switch(type) {
                    case 'bp_systolic':
                    case 'bp_diastolic':
                        // For BP: Lower is generally better (unless too low), but depends on context
                        // For simplicity: significant increase = worsening, decrease = improving (if not too low)
                        if (diff > 10) {
                            status = 'Worsening';
                            className = 'vital-worsening';
                            icon = '<i class="fas fa-arrow-up"></i>';
                        } else if (diff < -10 && currentValue > 90) {
                            status = 'Improving';
                            className = 'vital-improving';
                            icon = '<i class="fas fa-arrow-down"></i>';
                        } else {
                            status = 'Stable';
                            className = 'vital-stable';
                            icon = '<i class="fas fa-minus-circle"></i>';
                        }
                        break;
                    case 'heart_rate':
                        // HR: 60-100 is normal, too high or too low is bad
                        if (currentValue > 100 || (previousValue <= 100 && currentValue > previousValue + 15)) {
                            status = 'Worsening';
                            className = 'vital-worsening';
                            icon = '<i class="fas fa-arrow-up"></i>';
                        } else if (currentValue < 60 || (previousValue >= 60 && currentValue < previousValue - 15)) {
                            status = 'Worsening';
                            className = 'vital-worsening';
                            icon = '<i class="fas fa-arrow-down"></i>';
                        } else if (previousValue > 100 && currentValue <= 100) {
                            status = 'Improving';
                            className = 'vital-improving';
                            icon = '<i class="fas fa-arrow-down"></i>';
                        } else if (previousValue < 60 && currentValue >= 60) {
                            status = 'Improving';
                            className = 'vital-improving';
                            icon = '<i class="fas fa-arrow-up"></i>';
                        } else {
                            status = 'Stable';
                            className = 'vital-stable';
                            icon = '<i class="fas fa-minus-circle"></i>';
                        }
                        break;
                    case 'temperature':
                        // Temp: 36.1-37.2°C is normal, higher = fever (worsening), lower = improving
                        if (currentValue > 37.2 || (previousValue <= 37.2 && currentValue > previousValue + 0.5)) {
                            status = 'Worsening';
                            className = 'vital-worsening';
                            icon = '<i class="fas fa-arrow-up"></i>';
                        } else if (previousValue > 37.2 && currentValue <= 37.2) {
                            status = 'Improving';
                            className = 'vital-improving';
                            icon = '<i class="fas fa-arrow-down"></i>';
                        } else {
                            status = 'Stable';
                            className = 'vital-stable';
                            icon = '<i class="fas fa-minus-circle"></i>';
                        }
                        break;
                    case 'oxygen_saturation':
                        // O2 Sat: Higher is better, >95% is normal
                        if (currentValue < 95 || (previousValue >= 95 && currentValue < previousValue - 3)) {
                            status = 'Worsening';
                            className = 'vital-worsening';
                            icon = '<i class="fas fa-arrow-down"></i>';
                        } else if (previousValue < 95 && currentValue >= 95) {
                            status = 'Improving';
                            className = 'vital-improving';
                            icon = '<i class="fas fa-arrow-up"></i>';
                        } else {
                            status = 'Stable';
                            className = 'vital-stable';
                            icon = '<i class="fas fa-minus-circle"></i>';
                        }
                        break;
                    case 'respiratory_rate':
                        // RR: 12-20 is normal, too high or too low is bad
                        if (currentValue > 20 || (previousValue <= 20 && currentValue > previousValue + 5)) {
                            status = 'Worsening';
                            className = 'vital-worsening';
                            icon = '<i class="fas fa-arrow-up"></i>';
                        } else if (currentValue < 12 || (previousValue >= 12 && currentValue < previousValue - 5)) {
                            status = 'Worsening';
                            className = 'vital-worsening';
                            icon = '<i class="fas fa-arrow-down"></i>';
                        } else if (previousValue > 20 && currentValue <= 20) {
                            status = 'Improving';
                            className = 'vital-improving';
                            icon = '<i class="fas fa-arrow-down"></i>';
                        } else if (previousValue < 12 && currentValue >= 12) {
                            status = 'Improving';
                            className = 'vital-improving';
                            icon = '<i class="fas fa-arrow-up"></i>';
                        } else {
                            status = 'Stable';
                            className = 'vital-stable';
                            icon = '<i class="fas fa-minus-circle"></i>';
                        }
                        break;
                    default:
                        status = 'Stable';
                        className = 'vital-stable';
                        icon = '<i class="fas fa-minus-circle"></i>';
                }
            }
            
            comparisonSpan.innerHTML = icon + ' ' + status;
            comparisonSpan.className = 'vital-comparison ' + className;
            comparisonSpan.style.display = 'inline-flex';
        });
    });
});
</script>

<?= $this->endSection() ?>

