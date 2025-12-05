<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Patient List<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .doctor-page-container {
        padding: 0;
    }
    
    .page-header {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(46, 125, 50, 0.2);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .page-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .page-header h1 i {
        font-size: 32px;
    }
    
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    
    .card-header-modern {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 20px 24px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .card-header-modern h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
    }
    
    .card-body-modern {
        padding: 24px;
    }
    
    .btn-modern {
        padding: 10px 20px;
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
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
        color: white;
    }
    
    .btn-modern-secondary {
        background: #64748b;
        color: white;
    }
    
    .btn-modern-secondary:hover {
        background: #475569;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-modern-info {
        background: #0288d1;
        color: white;
    }
    
    .btn-modern-info:hover {
        background: #0277bd;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-modern-warning {
        background: #f59e0b;
        color: white;
    }
    
    .btn-modern-warning:hover {
        background: #d97706;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-modern-danger {
        background: #ef4444;
        color: white;
    }
    
    .btn-modern-danger:hover {
        background: #dc2626;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-sm-modern {
        padding: 6px 12px;
        font-size: 13px;
    }
    
    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f4 100%);
    }
    
    .table-modern th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #2e7d32;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #c8e6c9;
    }
    
    .table-modern td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
    }
    
    .table-modern tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-modern tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.01);
    }
    
    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }
    
    .alert-modern {
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .alert-modern-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        border-left: 4px solid #10b981;
    }
    
    .alert-modern-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }
    
    .empty-state i {
        font-size: 72px;
        margin-bottom: 20px;
        opacity: 0.4;
        color: #cbd5e1;
    }
    
    .empty-state h5 {
        margin: 0 0 12px;
        color: #64748b;
        font-size: 20px;
        font-weight: 600;
    }
    
    .empty-state p {
        margin: 0 0 24px;
        color: #94a3b8;
        font-size: 15px;
    }
    
    .badge-modern {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 16px;
            align-items: flex-start;
        }
        
        .page-header h1 {
            font-size: 24px;
        }
        
        .table-modern {
            font-size: 14px;
        }
        
        .table-modern th,
        .table-modern td {
            padding: 12px 8px;
        }
    }
</style>

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-hospital-user"></i>
            My Patients
        </h1>
        <a href="<?= site_url('doctor/patients/create') ?>" class="btn-modern btn-modern-primary">
            <i class="fas fa-plus"></i>
            Add New Patient
        </a>
    </div>
    
    <div class="modern-card">
        <div class="card-body-modern">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert-modern alert-modern-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert-modern alert-modern-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($patients)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Birthdate</th>
                                <th>Gender</th>
                                <th>Visit Type</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($patients as $patient): ?>
                                <tr style="<?= isset($patient['source']) && $patient['source'] === 'receptionist' ? 'background: #f0fdf4;' : '' ?>">
                                    <td><strong>#<?= esc($patient['id'] ?? $patient['patient_id'] ?? 'N/A') ?></strong></td>
                                    <td>
                                        <strong style="color: #1e293b;">
                                            <?= esc(ucfirst($patient['firstname'] ?? '') . ' ' . ucfirst($patient['lastname'] ?? '')) ?>
                                            <?php if (isset($patient['source']) && $patient['source'] === 'receptionist'): ?>
                                                <span style="background: #d1fae5; color: #065f46; padding: 2px 8px; border-radius: 4px; font-size: 10px; margin-left: 8px;">Receptionist</span>
                                            <?php endif; ?>
                                        </strong>
                                    </td>
                                    <td><?= !empty($patient['birthdate']) ? esc(date('M d, Y', strtotime($patient['birthdate']))) : 'N/A' ?></td>
                                    <td>
                                        <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                                            <?= esc(ucfirst($patient['gender'] ?? 'N/A')) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($patient['visit_type'])): ?>
                                            <span class="badge-modern" style="background: <?= 
                                                $patient['visit_type'] === 'Emergency' ? '#fee2e2' : 
                                                ($patient['visit_type'] === 'Consultation' ? '#dbeafe' : 
                                                ($patient['visit_type'] === 'Check-up' ? '#fef3c7' : '#d1fae5')); 
                                            ?>; color: <?= 
                                                $patient['visit_type'] === 'Emergency' ? '#991b1b' : 
                                                ($patient['visit_type'] === 'Consultation' ? '#1e40af' : 
                                                ($patient['visit_type'] === 'Check-up' ? '#92400e' : '#065f46')); 
                                            ?>;">
                                                <?= esc($patient['visit_type']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge-modern" style="background: #f1f5f9; color: #64748b;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($patient['contact'] ?? 'N/A') ?></td>
                                    <td><?= esc(substr($patient['address'] ?? 'N/A', 0, 40)) ?><?= strlen($patient['address'] ?? '') > 40 ? '...' : '' ?></td>
                                    <td>
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <?php 
                                            // For consultation start, use the original patient ID (patient_id for receptionist, id for admin)
                                            // The controller will handle the conversion
                                            $patientSource = $patient['source'] ?? 'admin'; // Default to 'admin' if not set
                                            $consultationPatientId = ($patientSource === 'receptionist') 
                                                ? ($patient['patient_id'] ?? $patient['id']) 
                                                : ($patient['id'] ?? $patient['patient_id']);
                                            $consultationSource = ($patientSource === 'receptionist') ? 'patients' : 'admin_patients';
                                            
                                            // For view/edit/delete, use the appropriate ID
                                            $viewId = ($patient['patient_id'] ?? $patient['id'] ?? $patient['id']);
                                            $editId = ($patient['patient_id'] ?? $patient['id'] ?? $patient['id']);
                                            $deleteId = ($patient['patient_id'] ?? $patient['id'] ?? $patient['id']);
                                            
                                            // Check if there's already a completed consultation for this patient today
                                            $db = \Config\Database::connect();
                                            $doctorId = session()->get('user_id');
                                            $today = date('Y-m-d');
                                            
                                            // Consultations table uses admin_patients.id, so we need to find the correct ID
                                            $checkPatientId = null;
                                            
                                            if ($patientSource === 'admin' || $patientSource === 'admin_patients') {
                                                // Direct ID from admin_patients
                                                $checkPatientId = $consultationPatientId;
                                            } else {
                                                // For patients table, find the corresponding admin_patients record
                                                if ($db->tableExists('patients')) {
                                                    $hmsPatient = $db->table('patients')
                                                        ->where('patient_id', $consultationPatientId)
                                                        ->get()
                                                        ->getRowArray();
                                                    
                                                    if ($hmsPatient) {
                                                        $nameParts = [];
                                                        if (!empty($hmsPatient['first_name'])) $nameParts[] = $hmsPatient['first_name'];
                                                        if (!empty($hmsPatient['last_name'])) $nameParts[] = $hmsPatient['last_name'];
                                                        if (empty($nameParts) && !empty($hmsPatient['full_name'])) {
                                                            $parts = explode(' ', $hmsPatient['full_name'], 2);
                                                            $nameParts = [$parts[0] ?? '', $parts[1] ?? ''];
                                                        }
                                                        
                                                        $adminPatient = $db->table('admin_patients')
                                                            ->where('firstname', $nameParts[0] ?? '')
                                                            ->where('lastname', $nameParts[1] ?? '')
                                                            ->where('doctor_id', $doctorId)
                                                            ->get()
                                                            ->getRowArray();
                                                        
                                                        if ($adminPatient) {
                                                            $checkPatientId = $adminPatient['id'];
                                                        }
                                                    }
                                                }
                                            }
                                            
                                            $hasCompletedConsultation = false;
                                            if ($checkPatientId) {
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
                                            
                                            // Check if appointment time has arrived
                                            $canStartConsultation = true;
                                            $appointmentInfo = '';
                                            $appointmentDate = $patient['appointment_date'] ?? null;
                                            $appointmentTime = $patient['appointment_time'] ?? null;
                                            
                                            if ($appointmentDate && $appointmentTime) {
                                                // Combine date and time
                                                $appointmentDateTime = $appointmentDate . ' ' . $appointmentTime;
                                                $appointmentTimestamp = strtotime($appointmentDateTime);
                                                $currentTimestamp = time();
                                                
                                                // Check if current time is before appointment time
                                                if ($currentTimestamp < $appointmentTimestamp) {
                                                    $canStartConsultation = false;
                                                    $formattedDate = date('M d, Y', strtotime($appointmentDate));
                                                    $formattedTime = date('g:i A', strtotime($appointmentTime));
                                                    $appointmentInfo = "Appointment scheduled for {$formattedDate} at {$formattedTime}";
                                                }
                                            }
                                            ?>
                                            <?php if (!$hasCompletedConsultation): ?>
                                                <?php if ($canStartConsultation): ?>
                                                    <a href="<?= site_url('doctor/consultations/start/' . $consultationPatientId . '/' . $consultationSource) ?>" 
                                                       class="btn-modern btn-modern-primary btn-sm-modern" title="Start Consultation">
                                                        <i class="fas fa-stethoscope"></i> Start Consultation
                                                    </a>
                                                <?php else: ?>
                                                    <span class="btn-modern btn-sm-modern" 
                                                          style="background: #fef3c7; color: #92400e; cursor: not-allowed; opacity: 0.7;" 
                                                          title="<?= esc($appointmentInfo) ?>">
                                                        <i class="fas fa-clock"></i> Not Yet
                                                    </span>
                                                    <span style="font-size: 11px; color: #92400e; display: block; margin-top: 4px;" title="<?= esc($appointmentInfo) ?>">
                                                        <?= esc($appointmentInfo) ?>
                                                    </span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="btn-modern btn-sm-modern" 
                                                      style="background: #d1fae5; color: #065f46; cursor: default;" 
                                                      title="Consultation already completed today">
                                                    <i class="fas fa-check-circle"></i> Consultation Done
                                                </span>
                                            <?php endif; ?>
                                            <a href="<?= site_url('doctor/patients/view/' . $viewId) ?>" 
                                               class="btn-modern btn-modern-info btn-sm-modern" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= site_url('doctor/patients/edit/' . $editId) ?>" 
                                               class="btn-modern btn-modern-warning btn-sm-modern" title="Edit Patient">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= site_url('doctor/patients/delete/' . $deleteId) ?>" 
                                               class="btn-modern btn-modern-danger btn-sm-modern" 
                                               onclick="return confirm('Are you sure you want to delete this patient?')" 
                                               title="Delete Patient">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h5>No Patients Found</h5>
                    <p>You haven't been assigned any patients yet. Patients assigned from the admin panel will appear here.</p>
                    <a href="<?= site_url('doctor/patients/create') ?>" class="btn-modern btn-modern-primary">
                        <i class="fas fa-plus"></i>
                        Add New Patient
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Patients Marked for Admission by Nurse (Pending Doctor Approval) -->
    <?php if (!empty($patientsForAdmission ?? [])): ?>
    <div class="modern-card" style="margin-top: 30px; border-left: 4px solid #f59e0b;">
        <div class="card-header-modern" style="background: #fef3c7; border-bottom: 2px solid #f59e0b;">
            <h5 style="color: #92400e;">
                <i class="fas fa-user-injured"></i>
                Patients Marked for Admission (Pending Your Approval)
            </h5>
            <span class="badge-modern badge-warning">
                <?= count($patientsForAdmission) ?> Patient(s)
            </span>
        </div>
        <div class="card-body-modern">
            <div class="table-container">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Triage Level</th>
                            <th>Chief Complaint</th>
                            <th>Nurse Recommendation</th>
                            <th>Requested At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patientsForAdmission as $patient): ?>
                            <tr>
                                <td><strong><?= esc($patient['patient_name']) ?></strong></td>
                                <td>
                                    <span class="badge-modern <?= 
                                        strtolower($patient['triage_level']) === 'critical' ? 'badge-danger' : 
                                        (strtolower($patient['triage_level']) === 'moderate' ? 'badge-warning' : 'badge-info') 
                                    ?>">
                                        <?= esc($patient['triage_level']) ?>
                                    </span>
                                </td>
                                <td><?= esc($patient['chief_complaint']) ?></td>
                                <td>
                                    <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                         title="<?= esc($patient['admission_reason']) ?>">
                                        <?= esc(substr($patient['admission_reason'], 0, 100)) ?><?= strlen($patient['admission_reason']) > 100 ? '...' : '' ?>
                                    </div>
                                </td>
                                <td><?= date('M d, Y H:i', strtotime($patient['created_at'])) ?></td>
                                <td>
                                    <a href="<?= site_url('doctor/patients/view/' . $patient['patient_id']) ?>" 
                                       class="btn-modern btn-modern-info btn-sm-modern">
                                        <i class="fas fa-eye"></i> View Patient
                                    </a>
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
