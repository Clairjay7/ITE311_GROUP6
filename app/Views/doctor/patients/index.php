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
                                <th>Blood Type</th>
                                <th>Visit Type</th>
                                <th>Purpose</th>
                                <th>Status</th>
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
                                        <?php if (!empty($patient['blood_type'])): ?>
                                            <span class="badge-modern" style="background: #fee2e2; color: #991b1b; font-weight: 600;">
                                                <?= esc($patient['blood_type']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
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
                                    <td><?= esc($patient['purpose'] ?? '-') ?></td>
                                    <td>
                                        <?php if (!empty($patient['waiting_for_lab_results']) && $patient['waiting_for_lab_results']): ?>
                                            <span class="badge-modern" style="background: #fef3c7; color: #92400e; font-weight: 600;">
                                                <i class="fas fa-vial"></i> Waiting for Lab Results
                                            </span>
                                        <?php elseif (!empty($patient['is_monitoring']) && $patient['is_monitoring']): ?>
                                            <span class="badge-modern" style="background: #dbeafe; color: #1e40af; font-weight: 600;">
                                                <i class="fas fa-heartbeat"></i> Starting Monitoring
                                            </span>
                                        <?php else: ?>
                                            <span class="badge-modern" style="background: #f1f5f9; color: #64748b;">-</span>
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
                                            <?php 
                                            // Check if patient is in OR room (surgery room)
                                            $isInORRoom = $patient['is_in_or_room'] ?? false;
                                            
                                            // Get surgery end datetime
                                            $surgeryEndDateTime = $patient['surgery_end_datetime'] ?? null;
                                            
                                            // DIRECT DATABASE CHECK: Query surgeries table directly to check countdown
                                            // This is the most reliable way to check if countdown has finished
                                            $db = \Config\Database::connect();
                                            $patientIdForCheck = $patient['id'] ?? $patient['patient_id'] ?? null;
                                            
                                            if ($patientIdForCheck && $db->tableExists('surgeries')) {
                                                // Check for scheduled surgeries
                                                $activeSurgery = $db->table('surgeries')
                                                    ->where('patient_id', $patientIdForCheck)
                                                    ->where('status', 'scheduled')
                                                    ->where('deleted_at', null)
                                                    ->orderBy('surgery_date', 'DESC')
                                                    ->orderBy('surgery_time', 'DESC')
                                                    ->get()
                                                    ->getRowArray();
                                                
                                                if ($activeSurgery && !empty($activeSurgery['surgery_date']) && !empty($activeSurgery['surgery_time'])) {
                                                    $surgeryDateTime = $activeSurgery['surgery_date'] . ' ' . $activeSurgery['surgery_time'];
                                                    $surgeryStart = strtotime($surgeryDateTime);
                                                    $surgeryEnd = $surgeryStart + (2 * 60 * 60); // 2 hours
                                                    $now = time();
                                                    
                                                    // If countdown finished (now >= surgeryEnd), NEVER show "In Surgery Room"
                                                    if ($now >= $surgeryEnd) {
                                                        // Countdown is 00:00:00 - FORCE isInORRoom to false
                                                        $isInORRoom = false;
                                                        $surgeryEndDateTime = null;
                                                    } else {
                                                        // Countdown still active - calculate end datetime
                                                        $surgeryEndDateTime = date('Y-m-d H:i:s', $surgeryEnd);
                                                    }
                                                } else {
                                                    // No scheduled surgery found - check if there's a completed one
                                                    // If surgery is completed, definitely don't show as in OR
                                                    $completedSurgery = $db->table('surgeries')
                                                        ->where('patient_id', $patientIdForCheck)
                                                        ->where('status', 'completed')
                                                        ->where('deleted_at', null)
                                                        ->orderBy('surgery_date', 'DESC')
                                                        ->orderBy('surgery_time', 'DESC')
                                                        ->get()
                                                        ->getRowArray();
                                                    
                                                    if ($completedSurgery) {
                                                        // Surgery is completed - don't show as in OR
                                                        $isInORRoom = false;
                                                        $surgeryEndDateTime = null;
                                                    }
                                                }
                                            }
                                            
                                            // ABSOLUTE CHECK: If countdown finished (00:00:00), NEVER show "In Surgery Room"
                                            // Check from patient data
                                            if (!empty($patient['surgery_end_datetime'])) {
                                                $endTimeCheck = strtotime($patient['surgery_end_datetime']);
                                                $nowCheck = time();
                                                if ($nowCheck >= $endTimeCheck) {
                                                    // Countdown is 00:00:00 - FORCE isInORRoom to false
                                                    $isInORRoom = false;
                                                    $surgeryEndDateTime = null;
                                                }
                                            }
                                            
                                            // Also check from $surgeryEndDateTime variable
                                            if (!empty($surgeryEndDateTime)) {
                                                $endTime = strtotime($surgeryEndDateTime);
                                                $now = time();
                                                if ($now >= $endTime) {
                                                    // Countdown is 00:00:00 - FORCE isInORRoom to false
                                                    $isInORRoom = false;
                                                    $surgeryEndDateTime = null;
                                                }
                                            }
                                            
                                            // Hide "Start Consultation" button for patients from inpatient registration
                                            // Check if patient is from inpatient registration
                                            $patientType = trim($patient['type'] ?? '');
                                            $visitTypeRaw = trim($patient['visit_type'] ?? '');
                                            $visitType = strtoupper($visitTypeRaw);
                                            
                                            // Debug: Log the values (remove in production)
                                            // var_dump("Patient: {$patient['firstname']} {$patient['lastname']}, Type: {$patientType}, VisitType: {$visitTypeRaw}, Source: {$patientSource}");
                                            
                                            // Check if it's an inpatient admission:
                                            // 1. Patient source is 'receptionist' AND type is 'In-Patient' (from patients table - direct admission)
                                            // 2. OR visit_type is 'Admission' (case-insensitive check)
                                            // 3. OR patient type is 'In-Patient' with visit_type 'Admission' (regardless of source)
                                            $isInpatientRegistration = (($patientSource === 'receptionist' && $patientType === 'In-Patient')) || 
                                                                      ($visitType === 'ADMISSION') ||
                                                                      ($patientType === 'In-Patient' && $visitType === 'ADMISSION');
                                            
                                            // FINAL ABSOLUTE CHECK: Right before rendering, check one more time
                                            // If countdown is 00:00:00, NEVER render the indicator
                                            if ($isInORRoom && !empty($surgeryEndDateTime)) {
                                                $finalEndTime = strtotime($surgeryEndDateTime);
                                                $finalNow = time();
                                                if ($finalNow >= $finalEndTime) {
                                                    // Countdown is 00:00:00 - DO NOT RENDER INDICATOR
                                                    $isInORRoom = false;
                                                    $surgeryEndDateTime = null;
                                                }
                                            }
                                            
                                            // Also check from patient data one more time
                                            if ($isInORRoom && !empty($patient['surgery_end_datetime'])) {
                                                $finalEndTime2 = strtotime($patient['surgery_end_datetime']);
                                                $finalNow2 = time();
                                                if ($finalNow2 >= $finalEndTime2) {
                                                    // Countdown is 00:00:00 - DO NOT RENDER INDICATOR
                                                    $isInORRoom = false;
                                                    $surgeryEndDateTime = null;
                                                }
                                            }
                                            
                                            // If patient is in OR room, disable all buttons
                                            // Only show if isInORRoom is still true after all checks AND surgeryEndDateTime exists
                                            // CRITICAL: This condition MUST be false if countdown is 00:00:00
                                            // ABSOLUTE FINAL CHECK: Verify countdown hasn't finished before rendering
                                            // This is the CRITICAL check - if countdown is 00:00:00, NEVER show indicator
                                            $shouldShowIndicator = false;
                                            
                                            // Only proceed if both conditions are met
                                            if ($isInORRoom && !empty($surgeryEndDateTime)) {
                                                // One final check - if countdown is 00:00:00, don't show
                                                $finalCheckTime = strtotime($surgeryEndDateTime);
                                                $finalCheckNow = time();
                                                
                                                // CRITICAL: If now >= endTime, countdown is 00:00:00 - DO NOT SHOW
                                                if ($finalCheckNow < $finalCheckTime) {
                                                    // Countdown still active (now < endTime) - can show indicator
                                                    $shouldShowIndicator = true;
                                                } else {
                                                    // Countdown is 00:00:00 (now >= endTime) - DO NOT SHOW
                                                    $shouldShowIndicator = false;
                                                }
                                            }
                                            
                                            // Also check from patient data one more time as absolute safety
                                            if ($shouldShowIndicator && !empty($patient['surgery_end_datetime'])) {
                                                $patientEndTime = strtotime($patient['surgery_end_datetime']);
                                                $patientNow = time();
                                                if ($patientNow >= $patientEndTime) {
                                                    // Countdown is 00:00:00 - DO NOT SHOW
                                                    $shouldShowIndicator = false;
                                                }
                                            }
                                            
                                            // ULTIMATE FINAL CHECK: Direct database query right before rendering
                                            // This is the absolute last check - query database directly
                                            if ($shouldShowIndicator && $patientIdForCheck && $db->tableExists('surgeries')) {
                                                $finalSurgeryCheck = $db->table('surgeries')
                                                    ->where('patient_id', $patientIdForCheck)
                                                    ->where('status', 'scheduled')
                                                    ->where('deleted_at', null)
                                                    ->orderBy('surgery_date', 'DESC')
                                                    ->orderBy('surgery_time', 'DESC')
                                                    ->get()
                                                    ->getRowArray();
                                                
                                                if ($finalSurgeryCheck && !empty($finalSurgeryCheck['surgery_date']) && !empty($finalSurgeryCheck['surgery_time'])) {
                                                    $finalSurgeryDateTime = $finalSurgeryCheck['surgery_date'] . ' ' . $finalSurgeryCheck['surgery_time'];
                                                    $finalSurgeryStart = strtotime($finalSurgeryDateTime);
                                                    $finalSurgeryEnd = $finalSurgeryStart + (2 * 60 * 60); // 2 hours
                                                    $finalNow = time();
                                                    
                                                    // If countdown finished (now >= endTime), DO NOT SHOW
                                                    if ($finalNow >= $finalSurgeryEnd) {
                                                        $shouldShowIndicator = false;
                                                    }
                                                } else {
                                                    // No scheduled surgery found - don't show indicator
                                                    $shouldShowIndicator = false;
                                                }
                                            }
                                            
                                            // ABSOLUTE FINAL CHECK: One more time before rendering
                                            // If ANY surgery_end_datetime exists and is past, NEVER show indicator
                                            if ($shouldShowIndicator) {
                                                // Check from all possible sources one final time
                                                $finalCheckSources = [
                                                    $surgeryEndDateTime,
                                                    $patient['surgery_end_datetime'] ?? null
                                                ];
                                                
                                                foreach ($finalCheckSources as $checkSource) {
                                                    if (!empty($checkSource)) {
                                                        $checkTime = strtotime($checkSource);
                                                        $checkNow = time();
                                                        if ($checkNow >= $checkTime) {
                                                            // Countdown is 00:00:00 - DO NOT SHOW
                                                            $shouldShowIndicator = false;
                                                            break;
                                                        }
                                                    }
                                                }
                                                
                                                // ONE MORE DATABASE CHECK: Query directly one last time
                                                if ($shouldShowIndicator && $patientIdForCheck && $db->tableExists('surgeries')) {
                                                    $lastSurgeryCheck = $db->table('surgeries')
                                                        ->where('patient_id', $patientIdForCheck)
                                                        ->where('status', 'scheduled')
                                                        ->where('deleted_at', null)
                                                        ->orderBy('surgery_date', 'DESC')
                                                        ->orderBy('surgery_time', 'DESC')
                                                        ->limit(1)
                                                        ->get()
                                                        ->getRowArray();
                                                    
                                                    if ($lastSurgeryCheck && !empty($lastSurgeryCheck['surgery_date']) && !empty($lastSurgeryCheck['surgery_time'])) {
                                                        $lastSurgeryDateTime = $lastSurgeryCheck['surgery_date'] . ' ' . $lastSurgeryCheck['surgery_time'];
                                                        $lastSurgeryStart = strtotime($lastSurgeryDateTime);
                                                        $lastSurgeryEnd = $lastSurgeryStart + (2 * 60 * 60);
                                                        $lastNow = time();
                                                        
                                                        // If countdown finished, DO NOT SHOW
                                                        if ($lastNow >= $lastSurgeryEnd) {
                                                            $shouldShowIndicator = false;
                                                        }
                                                    } else {
                                                        // No scheduled surgery - don't show
                                                        $shouldShowIndicator = false;
                                                    }
                                                }
                                            }
                                            
                                            // FINAL RENDER CHECK: Only render if shouldShowIndicator is true
                                            // If countdown is 00:00:00, shouldShowIndicator MUST be false
                                            // THIS IS THE ABSOLUTE FINAL CHECK - NO INDICATOR IF COUNTDOWN IS 00:00:00
                                            // IF YOU SEE THIS COMMENT, KNOW THAT WE'VE CHECKED MULTIPLE TIMES - COUNTDOWN 00:00:00 = NO INDICATOR
                                            
                                            // ONE MORE ABSOLUTE CHECK: Check countdown RIGHT IN THE CONDITION
                                            // This is the final safety net - if countdown is 00:00:00, NEVER render
                                            $countdownFinished = false;
                                            if (!empty($surgeryEndDateTime)) {
                                                $countdownFinished = (time() >= strtotime($surgeryEndDateTime));
                                            }
                                            if (!empty($patient['surgery_end_datetime'])) {
                                                $countdownFinished = $countdownFinished || (time() >= strtotime($patient['surgery_end_datetime']));
                                            }
                                            
                                            // ONLY RENDER IF shouldShowIndicator is true AND countdown is NOT finished
                                            // When countdown finishes (00:00:00), shouldShowIndicator = false, so we skip this block
                                            // and go to the else block below which shows ALL action buttons
                                            if ($shouldShowIndicator && !$countdownFinished) {
                                                $countdownId = 'surgery-countdown-' . ($patient['id'] ?? $patient['patient_id'] ?? uniqid());
                                                ?>
                                                <span class="btn-modern btn-sm-modern surgery-indicator" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: white; cursor: default; opacity: 0.9; display: inline-flex; align-items: center; gap: 8px;" title="Patient is in Surgery Room (OR) - All actions disabled">
                                                    <i class="fas fa-procedures"></i> 
                                                    <span>In Surgery Room</span>
                                                    <span id="<?= $countdownId ?>" style="background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 13px; min-width: 80px; text-align: center;">
                                                        --:--:--
                                                    </span>
                                                </span>
                                                <script>
                                                    (function() {
                                                        const countdownEl = document.getElementById('<?= $countdownId ?>');
                                                        if (!countdownEl) {
                                                            console.warn('Countdown element not found');
                                                            return;
                                                        }
                                                        
                                                        const endTime = new Date('<?= $surgeryEndDateTime ?>').getTime();
                                                        const now = new Date().getTime();
                                                        const initialDistance = endTime - now;
                                                        
                                                        // If countdown already finished when page loads, check if indicator exists
                                                        // If indicator doesn't exist, buttons are already visible - don't reload
                                                        if (initialDistance < 0) {
                                                            countdownEl.textContent = '00:00:00';
                                                            countdownEl.style.background = 'rgba(255,255,255,0.3)';
                                                            // Countdown finished - hide the "In Surgery Room" indicator if it exists
                                                            const surgeryIndicator = countdownEl.closest('.surgery-indicator');
                                                            // Check if we've already reloaded (prevent infinite loop)
                                                            const patientId = <?= json_encode($patient['id'] ?? $patient['patient_id'] ?? '') ?>;
                                                            let hasReloaded = sessionStorage.getItem('surgery_reload_' + patientId);
                                                            
                                                            if (surgeryIndicator && surgeryIndicator.offsetParent !== null && !hasReloaded) {
                                                                // Indicator exists and is visible - hide it and reload to show buttons (only once)
                                                                surgeryIndicator.style.display = 'none';
                                                                sessionStorage.setItem('surgery_reload_' + patientId, 'true');
                                                                setTimeout(() => {
                                                                    window.location.reload();
                                                                }, 500);
                                                            }
                                                            // If indicator doesn't exist, is already hidden, or already reloaded - no need to reload
                                                            return;
                                                        }
                                                        
                                                        let hasCalledAPI = false; // Prevent multiple API calls
                                                        let lastDistance = initialDistance;
                                                        
                                                        function updateCountdown() {
                                                            if (!countdownEl) return;
                                                            
                                                            const now = new Date().getTime();
                                                            const distance = endTime - now;
                                                            
                                                            // Only trigger if countdown JUST finished (was positive, now negative)
                                                            if (distance < 0 && lastDistance >= 0) {
                                                                countdownEl.textContent = '00:00:00';
                                                                countdownEl.style.background = 'rgba(255,255,255,0.3)';
                                                                
                                                                // Hide the "In Surgery Room" indicator immediately
                                                                const surgeryIndicator = countdownEl.closest('.surgery-indicator');
                                                                if (surgeryIndicator) {
                                                                    surgeryIndicator.style.display = 'none';
                                                                }
                                                                
                                                                // Only call API once when countdown JUST finishes
                                                                if (!hasCalledAPI) {
                                                                    hasCalledAPI = true;
                                                                    // Call API to move patient back when countdown ends
                                                                    const patientId = <?= json_encode($patient['id'] ?? $patient['patient_id'] ?? null) ?>;
                                                                    console.log('Countdown finished, moving patient back:', patientId);
                                                                    
                                                                    fetch('<?= site_url('doctor/surgery/check-move-back') ?>', {
                                                                        method: 'POST',
                                                                        headers: {
                                                                            'Content-Type': 'application/json',
                                                                            'X-Requested-With': 'XMLHttpRequest'
                                                                        },
                                                                        body: JSON.stringify({
                                                                            patient_id: patientId
                                                                        })
                                                                    }).then(response => {
                                                                        if (!response.ok) {
                                                                            throw new Error('Network response was not ok');
                                                                        }
                                                                        return response.json();
                                                                    }).then(data => {
                                                                        console.log('Patient moved back response:', data);
                                                                        // Check if we've already reloaded for this patient
                                                                        const patientId = <?= json_encode($patient['id'] ?? $patient['patient_id'] ?? null) ?>;
                                                                        let hasReloaded = sessionStorage.getItem('surgery_reload_' + patientId);
                                                                        if (!hasReloaded) {
                                                                            sessionStorage.setItem('surgery_reload_' + patientId, 'true');
                                                                            // Reload page after a short delay to reflect changes
                                                                            setTimeout(() => {
                                                                                window.location.reload();
                                                                            }, 1000);
                                                                        }
                                                                    }).catch(err => {
                                                                        console.error('Error moving patient back:', err);
                                                                        // Check if we've already reloaded for this patient
                                                                        const patientId = <?= json_encode($patient['id'] ?? $patient['patient_id'] ?? null) ?>;
                                                                        let hasReloaded = sessionStorage.getItem('surgery_reload_' + patientId);
                                                                        if (!hasReloaded) {
                                                                            sessionStorage.setItem('surgery_reload_' + patientId, 'true');
                                                                            // Still reload to check status - maybe it was moved by another process
                                                                            setTimeout(() => {
                                                                                window.location.reload();
                                                                            }, 1500);
                                                                        }
                                                                    });
                                                                }
                                                                return;
                                                            }
                                                            
                                                            // Also check if countdown is already at 00:00:00 (distance < 0)
                                                            if (distance < 0) {
                                                                countdownEl.textContent = '00:00:00';
                                                                countdownEl.style.background = 'rgba(255,255,255,0.3)';
                                                                // Hide the "In Surgery Room" indicator only if it exists and is visible
                                                                const surgeryIndicator = countdownEl.closest('.surgery-indicator');
                                                                if (surgeryIndicator && surgeryIndicator.offsetParent !== null && surgeryIndicator.style.display !== 'none') {
                                                                    surgeryIndicator.style.display = 'none';
                                                                    // Only reload if indicator was visible and we haven't reloaded yet
                                                                    if (!hasCalledAPI) {
                                                                        hasCalledAPI = true;
                                                                        let hasReloaded = sessionStorage.getItem('surgery_reload_' + <?= json_encode($patient['id'] ?? $patient['patient_id'] ?? '') ?>);
                                                                        if (!hasReloaded) {
                                                                            sessionStorage.setItem('surgery_reload_' + <?= json_encode($patient['id'] ?? $patient['patient_id'] ?? '') ?>, 'true');
                                                                            setTimeout(() => {
                                                                                window.location.reload();
                                                                            }, 500);
                                                                        }
                                                                    }
                                                                }
                                                                return;
                                                            }
                                                            
                                                            lastDistance = distance;
                                                            
                                                            // Update countdown display
                                                            if (distance >= 0) {
                                                                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                                                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                                                
                                                                countdownEl.textContent = 
                                                                    String(hours).padStart(2, '0') + ':' + 
                                                                    String(minutes).padStart(2, '0') + ':' + 
                                                                    String(seconds).padStart(2, '0');
                                                            } else {
                                                                countdownEl.textContent = '00:00:00';
                                                                countdownEl.style.background = 'rgba(255,255,255,0.3)';
                                                            }
                                                        }
                                                        
                                                        updateCountdown();
                                                        setInterval(updateCountdown, 1000);
                                                    })();
                                                    </script>
                                                <?php } else {
                                                    // COUNTDOWN FINISHED (00:00:00) - Show ALL action buttons
                                                    // This else block executes when:
                                                    // 1. shouldShowIndicator = false (countdown finished or no surgery)
                                                    // 2. countdownFinished = true (countdown reached 00:00:00)
                                                    // All buttons below will be visible and enabled
                                            ?>
                                            <?php if ($isInpatientRegistration): ?>
                                                <!-- For admitted patients, show View Patient button and Admission badge -->
                                                <a href="<?= site_url('doctor/patients/view/' . $viewId) ?>" 
                                                   class="btn-modern btn-modern-info btn-sm-modern" 
                                                   title="View Patient Details">
                                                    <i class="fas fa-eye"></i> View Patient
                                                </a>
                                                <span class="btn-modern btn-sm-modern" 
                                                      style="background: #dbeafe; color: #1e40af; cursor: default;" 
                                                      title="Direct Inpatient Admission - No consultation needed">
                                                    <i class="fas fa-hospital"></i> Admission
                                                </span>
                                                
                                                <!-- Assign Nurse for Admission Patients -->
                                                <?php if (!empty($patient['assigned_nurse_name'])): ?>
                                                    <!-- Show assigned nurse name -->
                                                    <span class="badge-modern" style="background: #d1fae5; color: #065f46; padding: 6px 12px; font-size: 12px;">
                                                        <i class="fas fa-user-nurse"></i> <?= esc($patient['assigned_nurse_name']) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <!-- Show dropdown if not assigned -->
                                                    <div style="position: relative; display: inline-block;">
                                                        <select class="form-select assign-nurse-dropdown-main" 
                                                                data-patient-id="<?= esc($viewId) ?>"
                                                                style="min-width: 180px; padding: 8px 12px; border-radius: 8px; border: 1px solid #e5e7eb; font-size: 13px; background: white; cursor: pointer;">
                                                            <option value="">Assign Nurse...</option>
                                                            <!-- Options will be loaded via AJAX -->
                                                        </select>
                                                        <div class="nurse-assignment-status-main" style="margin-top: 4px; font-size: 11px; min-height: 16px;"></div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <!-- Check button - only show for In-Patient/Admission, not for Consultation/Out-Patient -->
                                            <?php 
                                            // Only show Check button for In-Patient/Admission patients, not for Consultation/Out-Patient
                                            $patientTypeForCheck = trim($patient['type'] ?? '');
                                            $visitTypeForCheck = trim($patient['visit_type'] ?? '');
                                            $isConsultationOnly = ($patientTypeForCheck === 'Out-Patient' || 
                                                                  $visitTypeForCheck === 'Consultation' || 
                                                                  $visitTypeForCheck === 'Check-up' || 
                                                                  $visitTypeForCheck === 'Follow-up');
                                            
                                            if (!$isConsultationOnly): ?>
                                                <?php 
                                                $doctorCheckStatus = $patient['doctor_check_status'] ?? 'available';
                                                $isCheckDisabled = ($doctorCheckStatus === 'pending_nurse' || $doctorCheckStatus === 'pending_order');
                                                ?>
                                                <?php if ($doctorCheckStatus === 'pending_nurse'): ?>
                                                    <!-- Button disabled - waiting for nurse to complete vitals -->
                                                    <span class="btn-modern btn-modern-secondary btn-sm-modern" 
                                                          style="opacity: 0.6; cursor: not-allowed; background: #94a3b8;"
                                                          title="Waiting for nurse to complete vital signs check">
                                                        <i class="fas fa-clock"></i> Waiting for Nurse...
                                                    </span>
                                                <?php elseif ($doctorCheckStatus === 'pending_order'): ?>
                                                    <!-- Button disabled - waiting for doctor to create and complete order -->
                                                    <span class="btn-modern btn-modern-secondary btn-sm-modern" 
                                                          style="opacity: 0.6; cursor: not-allowed; background: #f59e0b;"
                                                          title="Please create and complete a medical order from Vital Signs History">
                                                        <i class="fas fa-file-medical"></i> Create Order Required
                                                    </span>
                                                <?php else: ?>
                                                    <!-- Button enabled - doctor can click Check -->
                                                    <a href="<?= site_url('doctor/patients/request-vitals-check/' . $viewId) ?>" 
                                                       class="btn-modern btn-modern-primary btn-sm-modern" 
                                                       title="Check Patient - Enable Nurse to Check Vitals"
                                                       onclick="return confirm('Check this patient? This will enable the nurse to check and record vital signs.')">
                                                        <i class="fas fa-check-circle"></i> Check
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            
                                            <?php 
                                            // Check if patient is waiting for lab results
                                            $waitingForLabResults = !empty($patient['waiting_for_lab_results']) && $patient['waiting_for_lab_results'];
                                            $pendingLabConsultationId = null;
                                            if ($waitingForLabResults && !empty($patient['pending_lab_consultation'])) {
                                                $pendingLabConsultationId = $patient['pending_lab_consultation']['id'] ?? null;
                                            }
                                            
                                            // Show "Start Consultation" button for Out-Patient/Consultation patients
                                            $patientType = trim($patient['type'] ?? '');
                                            $visitType = trim($patient['visit_type'] ?? '');
                                            $isOutPatient = ($patientType === 'Out-Patient' || $visitType === 'Consultation' || $visitType === 'Check-up' || $visitType === 'Follow-up');
                                            
                                            // Determine the source and patient ID for consultation
                                            $patientSource = $patient['source'] ?? 'admin';
                                            $consultationPatientId = ($patientSource === 'receptionist') 
                                                ? ($patient['patient_id'] ?? $patient['id']) 
                                                : ($patient['id'] ?? $patient['patient_id']);
                                            $consultationSource = ($patientSource === 'receptionist') ? 'patients' : 'admin_patients';
                                            
                                            if ($isOutPatient && !$isInpatientRegistration && $canStartConsultation && !$waitingForLabResults): ?>
                                                <a href="<?= site_url('doctor/consultations/start/' . $consultationPatientId . '/' . $consultationSource) ?>" 
                                                   class="btn-modern btn-sm-modern" 
                                                   style="background: #0288d1; color: white; box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);"
                                                   title="Start Consultation">
                                                    <i class="fas fa-stethoscope"></i> Start Consultation
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php 
                                            // Note: "Complete Consultation" button removed - consultation is now auto-completed when all lab results are ready
                                            ?>
                                            
                                            <?php if (!$isInpatientRegistration): ?>
                                            <a href="<?= site_url('doctor/patients/view/' . $viewId) ?>" 
                                               class="btn-modern btn-modern-info btn-sm-modern" title="View Details">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <?php endif; ?>
                                            
                                            <?php 
                                            // Show Discharge button if patient has active admission
                                            $hasActiveAdmission = $patient['has_active_admission'] ?? false;
                                            $admissionId = $patient['admission_id'] ?? null;
                                            $isDirectAdmission = $patient['is_direct_admission'] ?? false;
                                            
                                            // Debug: Check visit_type and type for admission
                                            $visitTypeCheck = strtoupper(trim($patient['visit_type'] ?? ''));
                                            $patientTypeCheck = strtoupper(trim($patient['type'] ?? ''));
                                            
                                            // Show discharge button if:
                                            // 1. Has active admission record with admission_id, OR
                                            // 2. Is direct admission (visit_type = 'Admission' or type = 'In-Patient')
                                            $showDischargeButton = false;
                                            $finalAdmissionId = null;
                                            
                                            if ($hasActiveAdmission && $admissionId) {
                                                // Has admission record
                                                $showDischargeButton = true;
                                                $finalAdmissionId = $admissionId;
                                            } elseif ($isDirectAdmission || $visitTypeCheck === 'ADMISSION' || ($patientTypeCheck === 'IN-PATIENT' && ($visitTypeCheck === 'ADMISSION' || empty($visitTypeCheck)))) {
                                                // Direct admission - use patient ID
                                                $showDischargeButton = true;
                                                $finalAdmissionId = $viewId; // Use patient view ID for direct admission
                                            }
                                            ?>
                                            <?php if ($showDischargeButton && $finalAdmissionId): ?>
                                                <a href="<?= site_url('doctor/discharge/create/' . $finalAdmissionId) ?>" 
                                                   class="btn-modern btn-sm-modern" 
                                                   style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);"
                                                   title="Discharge Patient">
                                                    <i class="fas fa-sign-out-alt"></i> Discharge
                                                </a>
                                            <?php endif; ?>
                                            
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
                                            <?php } // End of else block for isInORRoom ?>
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
                            <th>Assign Nurse</th>
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
                                    <?php if (!empty($patient['assigned_nurse_name'])): ?>
                                        <!-- Show assigned nurse name -->
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span class="badge-modern" style="background: #d1fae5; color: #065f46; padding: 8px 12px; font-size: 13px;">
                                                <i class="fas fa-user-nurse"></i> <?= esc($patient['assigned_nurse_name']) ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <!-- Show dropdown if not assigned -->
                                        <select class="form-select assign-nurse-dropdown" 
                                                data-patient-id="<?= esc($patient['patient_id']) ?>"
                                                data-admission-request-id="<?= esc($patient['admission_request_id'] ?? '') ?>"
                                                style="min-width: 200px; padding: 8px 12px; border-radius: 8px; border: 1px solid #e5e7eb; font-size: 14px;">
                                            <option value="">Select Nurse...</option>
                                            <!-- Options will be loaded via AJAX -->
                                        </select>
                                        <div class="nurse-assignment-status" style="margin-top: 4px; font-size: 12px;"></div>
                                    <?php endif; ?>
                                </td>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load available nurses for all dropdowns
    loadAvailableNurses();
    
    // Handle nurse assignment for admission requests section
    document.querySelectorAll('.assign-nurse-dropdown').forEach(function(dropdown) {
        dropdown.addEventListener('change', function() {
            const patientId = this.getAttribute('data-patient-id');
            const admissionRequestId = this.getAttribute('data-admission-request-id');
            const nurseId = this.value;
            const statusDiv = this.parentElement.querySelector('.nurse-assignment-status');
            
            if (!nurseId) {
                if (statusDiv) statusDiv.innerHTML = '';
                return;
            }
            
            // Show loading
            if (statusDiv) {
                statusDiv.innerHTML = '<span style="color: #f59e0b;"><i class="fas fa-spinner fa-spin"></i> Assigning...</span>';
            }
            
            // Assign nurse
            assignNurseToPatient(patientId, nurseId, admissionRequestId, statusDiv, this);
        });
    });
    
    // Handle nurse assignment for main patient list (ADMISSION type)
    document.querySelectorAll('.assign-nurse-dropdown-main').forEach(function(dropdown) {
        dropdown.addEventListener('change', function() {
            const patientId = this.getAttribute('data-patient-id');
            const nurseId = this.value;
            const statusDiv = this.parentElement.querySelector('.nurse-assignment-status-main');
            
            if (!nurseId) {
                if (statusDiv) statusDiv.innerHTML = '';
                return;
            }
            
            // Show loading
            if (statusDiv) {
                statusDiv.innerHTML = '<span style="color: #f59e0b;"><i class="fas fa-spinner fa-spin"></i> Assigning...</span>';
            }
            
            // Assign nurse (no admission_request_id for main list)
            assignNurseToPatient(patientId, nurseId, '', statusDiv, this);
        });
    });
    
    function assignNurseToPatient(patientId, nurseId, admissionRequestId, statusDiv, dropdown) {
        fetch('<?= site_url('doctor/patients/assign-nurse') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                patient_id: patientId,
                nurse_id: nurseId,
                admission_request_id: admissionRequestId || ''
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (statusDiv) {
                    statusDiv.innerHTML = '<span style="color: #10b981;"><i class="fas fa-check-circle"></i> ' + data.message + '</span>';
                }
                // Optionally reload the page after 2 seconds to show updated assignment
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else {
                if (statusDiv) {
                    statusDiv.innerHTML = '<span style="color: #ef4444;"><i class="fas fa-exclamation-circle"></i> ' + (data.message || 'Error assigning nurse') + '</span>';
                }
                // Reset dropdown
                dropdown.value = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (statusDiv) {
                statusDiv.innerHTML = '<span style="color: #ef4444;"><i class="fas fa-exclamation-circle"></i> Error assigning nurse</span>';
            }
            dropdown.value = '';
        });
    }
    
    function loadAvailableNurses() {
        fetch('<?= site_url('doctor/patients/get-available-nurses') ?>', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.nurses && data.nurses.length > 0) {
                // Populate admission requests dropdowns
                document.querySelectorAll('.assign-nurse-dropdown').forEach(function(dropdown) {
                    // Clear existing options except the first one
                    while (dropdown.options.length > 1) {
                        dropdown.remove(1);
                    }
                    
                    // Add available nurses
                    data.nurses.forEach(function(nurse) {
                        const option = document.createElement('option');
                        option.value = nurse.id;
                        let displayText = nurse.name;
                        if (nurse.schedule) {
                            displayText += ' (' + nurse.schedule.shift_type + ': ' + nurse.schedule.start_time + '-' + nurse.schedule.end_time + ')';
                        }
                        option.textContent = displayText;
                        dropdown.appendChild(option);
                    });
                });
                
                // Populate main patient list dropdowns (ADMISSION type)
                document.querySelectorAll('.assign-nurse-dropdown-main').forEach(function(dropdown) {
                    // Clear existing options except the first one
                    while (dropdown.options.length > 1) {
                        dropdown.remove(1);
                    }
                    
                    // Add available nurses
                    data.nurses.forEach(function(nurse) {
                        const option = document.createElement('option');
                        option.value = nurse.id;
                        let displayText = nurse.name;
                        if (nurse.schedule) {
                            displayText += ' (' + nurse.schedule.shift_type + ': ' + nurse.schedule.start_time + '-' + nurse.schedule.end_time + ')';
                        }
                        option.textContent = displayText;
                        dropdown.appendChild(option);
                    });
                });
            } else {
                // Show message if no nurses available
                document.querySelectorAll('.assign-nurse-dropdown, .assign-nurse-dropdown-main').forEach(function(dropdown) {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No available nurses';
                    option.disabled = true;
                    dropdown.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading nurses:', error);
            document.querySelectorAll('.assign-nurse-dropdown, .assign-nurse-dropdown-main').forEach(function(dropdown) {
                const statusDiv = dropdown.parentElement.querySelector('.nurse-assignment-status, .nurse-assignment-status-main');
                if (statusDiv) {
                    statusDiv.innerHTML = '<span style="color: #ef4444; font-size: 11px;">Error loading nurses</span>';
                }
            });
        });
    }
});
</script>

<?= $this->endSection() ?>
