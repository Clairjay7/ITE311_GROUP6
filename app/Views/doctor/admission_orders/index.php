<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Admitted Patients<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .page-header {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(46, 125, 50, 0.2);
        color: white;
    }
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .card-header {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f4 100%);
        padding: 20px 24px;
        border-bottom: 2px solid #c8e6c9;
    }
    .card-body {
        padding: 24px;
    }
    .patient-card {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
        transition: all 0.3s ease;
    }
    .patient-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #2e7d32;
    }
    .badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-primary {
        background: #2e7d32;
        color: white;
    }
    .badge-warning {
        background: #f59e0b;
        color: white;
    }
    .btn-modern {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    .btn-primary {
        background: #2e7d32;
        color: white;
    }
    .btn-primary:hover {
        background: #1b5e20;
        color: white;
        transform: translateY(-2px);
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-hospital"></i> Admitted Patients</h1>
    <p style="margin: 8px 0 0; opacity: 0.9;">Review and manage your admitted patients</p>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div class="modern-card">
    <div class="card-header">
        <h3 style="margin: 0; color: #2e7d32;"><i class="fas fa-list"></i> My Admitted Patients</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($admittedPatients)): ?>
            <?php foreach ($admittedPatients as $patient): ?>
                <div class="patient-card">
                    <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 16px;">
                        <div style="flex: 1;">
                            <h4 style="margin: 0 0 12px; color: #1e293b;">
                                <i class="fas fa-user-injured"></i> 
                                <?= esc(ucwords($patient['firstname'] . ' ' . $patient['lastname'])) ?>
                            </h4>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 12px;">
                                <div>
                                    <small style="color: #64748b;">Room</small>
                                    <div style="font-weight: 600; color: #1e293b;">
                                        <?= esc($patient['room_number'] ?? 'N/A') ?> - <?= esc($patient['ward'] ?? 'N/A') ?>
                                    </div>
                                </div>
                                <div>
                                    <small style="color: #64748b;">Admission Date</small>
                                    <div style="font-weight: 600; color: #1e293b;">
                                        <?= date('M d, Y', strtotime($patient['admission_date'])) ?>
                                    </div>
                                </div>
                                <div>
                                    <small style="color: #64748b;">Admission Reason</small>
                                    <div style="font-weight: 600; color: #1e293b;">
                                        <?= esc(substr($patient['admission_reason'] ?? 'N/A', 0, 50)) ?>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($patient['diagnosis'])): ?>
                                <div style="background: #fef3c7; padding: 12px; border-radius: 8px; margin-bottom: 12px;">
                                    <small style="color: #92400e; font-weight: 600;">Diagnosis:</small>
                                    <div style="color: #78350f; margin-top: 4px;"><?= esc($patient['diagnosis']) ?></div>
                                </div>
                            <?php endif; ?>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <?php if ($patient['pending_orders_count'] > 0): ?>
                                    <span class="badge badge-warning">
                                        <i class="fas fa-exclamation-circle"></i> <?= $patient['pending_orders_count'] ?> Pending Orders
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <?php
                            // Check if there's already a completed consultation for this patient today
                            $db = \Config\Database::connect();
                            $doctorId = session()->get('user_id');
                            $today = date('Y-m-d');
                            
                            // Determine patient source and ID for consultation
                            $patientSource = $patient['source'] ?? 'admin';
                            $consultationPatientId = null;
                            $consultationSource = 'admin_patients';
                            
                            if ($patientSource === 'receptionist') {
                                // In-Patient from patients table
                                $consultationPatientId = $patient['patient_id'];
                                $consultationSource = 'patients';
                                
                                // Check for completed consultation using patient_id
                                $checkPatientId = $consultationPatientId;
                            } else {
                                // From admin_patients via admissions
                                $consultationPatientId = $patient['patient_id'];
                                $consultationSource = 'admin_patients';
                                $checkPatientId = $consultationPatientId;
                            }
                            
                            // Check if consultation already completed today
                            $hasCompletedConsultation = false;
                            if ($checkPatientId) {
                                if ($patientSource === 'receptionist') {
                                    // For patients table, consultations are saved with admin_patients.id
                                    // So we need to find the admin_patients record first
                                    $hmsPatient = $db->table('patients')
                                        ->where('patient_id', $checkPatientId)
                                        ->get()
                                        ->getRowArray();
                                    
                                    if ($hmsPatient) {
                                        // Extract name parts
                                        $nameParts = [];
                                        if (!empty($hmsPatient['first_name'])) $nameParts[] = $hmsPatient['first_name'];
                                        if (!empty($hmsPatient['last_name'])) $nameParts[] = $hmsPatient['last_name'];
                                        if (empty($nameParts) && !empty($hmsPatient['full_name'])) {
                                            $parts = explode(' ', $hmsPatient['full_name'], 2);
                                            $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                                        }
                                        
                                        // Find admin_patients record
                                        $adminPatient = null;
                                        if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                                            $adminPatient = $db->table('admin_patients')
                                                ->where('firstname', $nameParts[0])
                                                ->where('lastname', $nameParts[1])
                                                ->where('doctor_id', $doctorId)
                                                ->get()
                                                ->getRowArray();
                                        }
                                        
                                        // Check consultations using admin_patients.id if found
                                        if ($adminPatient) {
                                            $existingConsultation = $db->table('consultations')
                                                ->where('patient_id', $adminPatient['id'])
                                                ->where('doctor_id', $doctorId)
                                                ->where('consultation_date', $today)
                                                ->where('type', 'completed')
                                                ->where('status', 'approved')
                                                ->where('deleted_at', null)
                                                ->get()
                                                ->getRowArray();
                                            
                                            $hasCompletedConsultation = !empty($existingConsultation);
                                        }
                                        
                                        // Also check directly with patients.patient_id (in case consultation was saved differently)
                                        if (!$hasCompletedConsultation) {
                                            $existingConsultation = $db->table('consultations')
                                                ->where('patient_id', $checkPatientId)
                                                ->where('doctor_id', $doctorId)
                                                ->where('consultation_date', $today)
                                                ->where('type', 'completed')
                                                ->where('status', 'approved')
                                                ->where('deleted_at', null)
                                                ->get()
                                                ->getRowArray();
                                            
                                            $hasCompletedConsultation = !empty($existingConsultation);
                                        }
                                    }
                                } else {
                                    // For admin_patients
                                    $existingConsultation = $db->table('consultations')
                                        ->where('patient_id', $checkPatientId)
                                        ->where('doctor_id', $doctorId)
                                        ->where('consultation_date', $today)
                                        ->where('type', 'completed')
                                        ->where('status', 'approved')
                                        ->where('deleted_at', null)
                                        ->get()
                                        ->getRowArray();
                                    
                                    $hasCompletedConsultation = !empty($existingConsultation);
                                }
                            }
                            ?>
                            
                            <?php if (!$hasCompletedConsultation && $consultationPatientId): ?>
                                <a href="<?= site_url('doctor/consultations/start/' . $consultationPatientId . '/' . $consultationSource) ?>" 
                                   class="btn-modern btn-primary" style="background: #0288d1;">
                                    <i class="fas fa-stethoscope"></i> Start Consultation
                                </a>
                            <?php elseif ($hasCompletedConsultation): ?>
                                <span class="btn-modern" 
                                      style="background: #d1fae5; color: #065f46; cursor: default;" 
                                      title="Consultation already completed today">
                                    <i class="fas fa-check-circle"></i> Consultation Done
                                </span>
                            <?php endif; ?>
                            
                            <?php if (isset($patient['source']) && $patient['source'] === 'receptionist'): ?>
                                <!-- In-Patient from receptionist - link to patient view instead -->
                                <a href="<?= site_url('doctor/patients/view/' . $patient['patient_id']) ?>" 
                                   class="btn-modern btn-primary">
                                    <i class="fas fa-eye"></i> View Patient
                                </a>
                                <span style="background: #dbeafe; color: #1e40af; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-info-circle"></i> Direct Admission
                                </span>
                            <?php else: ?>
                                <!-- Regular admission with orders -->
                                <a href="<?= site_url('doctor/admission-orders/view/' . $patient['id']) ?>" 
                                   class="btn-modern btn-primary">
                                    <i class="fas fa-eye"></i> View & Manage Orders
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-hospital" style="font-size: 72px; margin-bottom: 20px; opacity: 0.4; color: #cbd5e1;"></i>
                <h5>No Admitted Patients</h5>
                <p>You currently have no admitted patients under your care.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>


