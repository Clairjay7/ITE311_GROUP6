<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Patient Details<?= $this->endSection() ?>

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
        flex-wrap: wrap;
        gap: 16px;
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
        margin-bottom: 24px;
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
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .card-body-modern {
        padding: 32px;
    }
    
    .info-section {
        background: #f8fafc;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 24px;
    }
    
    .info-section-title {
        font-size: 16px;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .info-section-title::before {
        content: '';
        width: 4px;
        height: 20px;
        background: #2e7d32;
        border-radius: 2px;
    }
    
    .info-table {
        width: 100%;
    }
    
    .info-table tr {
        border-bottom: 1px solid #e5e7eb;
    }
    
    .info-table tr:last-child {
        border-bottom: none;
    }
    
    .vital-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 6px;
    }
    
    .vital-status-improving {
        background: #d1fae5;
        color: #065f46;
    }
    
    .vital-status-worsening {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .vital-status-stable {
        background: #fef3c7;
        color: #92400e;
    }
    
    .vital-status-new {
        background: #e0f2fe;
        color: #0369a1;
    }
    
    .info-table td {
        padding: 12px 0;
        vertical-align: top;
    }
    
    .info-table td:first-child {
        width: 180px;
        font-weight: 600;
        color: #64748b;
    }
    
    .info-table td:last-child {
        color: #1e293b;
        font-weight: 500;
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
    
    .btn-modern-secondary {
        background: #64748b;
        color: white;
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
    
    .table-modern tbody tr:hover {
        background: #f8fafc;
    }
    
    .badge-modern {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }
    
    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.4;
    }
</style>

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-user-circle"></i>
            Patient Details
        </h1>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <?php if (!isset($patientSource) || $patientSource !== 'patients'): ?>
                <!-- Only show Edit button for admin_patients, not receptionist patients -->
                <a href="<?= site_url('doctor/patients/edit/' . ($patient['id'] ?? $patient['patient_id'])) ?>" class="btn-modern btn-modern-warning">
                    <i class="fas fa-edit"></i>
                    Edit Patient
                </a>
            <?php else: ?>
                <!-- Receptionist patients - read only -->
                <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 8px; font-size: 13px;">
                    <i class="fas fa-info-circle"></i> Read-only (Receptionist Patient)
                </span>
            <?php endif; ?>
            <?php 
            $isInORRoom = $isInORRoom ?? false;
            $surgeryEndDateTime = $surgeryEndDateTime ?? null;
            
            // DIRECT DATABASE CHECK: Query surgeries table directly to check countdown
            $db = \Config\Database::connect();
            $patientIdForCheck = $patient['id'] ?? $patient['patient_id'] ?? null;
            
            if ($patientIdForCheck && $db->tableExists('surgeries')) {
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
                    
                    if ($now >= $surgeryEnd) {
                        // Countdown is 00:00:00 - FORCE isInORRoom to false
                        $isInORRoom = false;
                        $surgeryEndDateTime = null;
                    }
                }
            }
            
            // CRITICAL: Double-check: If countdown finished, don't show as in OR
            if ($surgeryEndDateTime) {
                $endTime = strtotime($surgeryEndDateTime);
                $now = time();
                // If countdown finished (current time >= end time), don't show as in OR
                if ($now >= $endTime) {
                    // Countdown finished - force isInORRoom to false so buttons are visible
                    $isInORRoom = false;
                    // Also clear surgeryEndDateTime so countdown doesn't show
                    $surgeryEndDateTime = null;
                }
            }
            
            // ABSOLUTE FINAL CHECK: Verify countdown hasn't finished before rendering
            $shouldShowIndicator = false;
            if ($isInORRoom && !empty($surgeryEndDateTime)) {
                $finalCheckTime = strtotime($surgeryEndDateTime);
                $finalCheckNow = time();
                if ($finalCheckNow < $finalCheckTime) {
                    // Countdown still active - can show indicator
                    $shouldShowIndicator = true;
                } else {
                    // Countdown is 00:00:00 - DO NOT SHOW
                    $shouldShowIndicator = false;
                }
            }
            
            if ($shouldShowIndicator): 
                $countdownId = 'surgery-countdown-view-' . ($patient['id'] ?? $patient['patient_id'] ?? uniqid());
            ?>
                <span class="btn-modern" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: white; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3); cursor: default; opacity: 0.9; display: inline-flex; align-items: center; gap: 8px;" title="Patient is in Surgery Room (OR) - Surgery button disabled">
                    <i class="fas fa-procedures"></i>
                    <span>In Surgery Room</span>
                    <?php if ($surgeryEndDateTime): ?>
                        <span id="<?= $countdownId ?>" style="background: rgba(255,255,255,0.2); padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 13px; min-width: 80px; text-align: center;">
                            --:--:--
                        </span>
                    <?php endif; ?>
                </span>
                <?php if ($surgeryEndDateTime): ?>
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
                        
                        // If countdown already finished when page loads, don't start timer
                        // The server-side auto-move logic will handle it
                        if (initialDistance < 0) {
                            countdownEl.textContent = '00:00:00';
                            countdownEl.style.background = 'rgba(255,255,255,0.3)';
                            // Don't auto-reload - let server handle it on next page load
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
                                        // Reload page after a short delay to reflect changes
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 1500);
                                    }).catch(err => {
                                        console.error('Error moving patient back:', err);
                                        // Still reload to check status - maybe it was moved by another process
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 2000);
                                    });
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
                <?php endif; ?>
            <?php else: ?>
                <?php 
                // Hide Surgery button for Consultation/Out-Patient visit types
                $patientType = trim($patient['type'] ?? '');
                $visitType = trim($patient['visit_type'] ?? '');
                $isConsultation = ($patientType === 'Out-Patient' || 
                                  $visitType === 'Consultation' || 
                                  $visitType === 'Check-up' || 
                                  $visitType === 'Follow-up');
                
                if (!$isConsultation): ?>
                    <a href="<?= site_url('doctor/surgery/create/' . ($patient['id'] ?? $patient['patient_id'])) ?>" class="btn-modern" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%); color: white; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);">
                        <i class="fas fa-procedures"></i>
                        Surgery
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <a href="<?= site_url('doctor/patients') ?>" class="btn-modern btn-modern-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
        </div>
    </div>
    
    <div class="modern-card">
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-section">
                        <div class="info-section-title">
                            <i class="fas fa-user"></i>
                            Personal Information
                        </div>
                        <table class="info-table">
                            <tr>
                                <td>Patient ID:</td>
                                <td><strong>#<?= esc($patient['id'] ?? $patient['patient_id'] ?? 'N/A') ?></strong></td>
                            </tr>
                            <?php if (!empty($patient['patient_reg_no'])): ?>
                            <tr>
                                <td>Registration No:</td>
                                <td><?= esc($patient['patient_reg_no']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td>Full Name:</td>
                                <td><strong><?= esc(trim(($patient['firstname'] ?? $patient['first_name'] ?? '') . ' ' . ($patient['middle_name'] ?? '') . ' ' . ($patient['lastname'] ?? $patient['last_name'] ?? '') . ' ' . ($patient['extension_name'] ?? ''))) ?></strong></td>
                            </tr>
                            <tr>
                                <td>Birthdate:</td>
                                <td><?= !empty($patient['birthdate'] ?? $patient['date_of_birth'] ?? null) ? esc(date('F d, Y', strtotime($patient['birthdate'] ?? $patient['date_of_birth']))) : 'N/A' ?></td>
                            </tr>
                            <tr>
                                <td>Age:</td>
                                <td><?= !empty($patient['age']) ? esc($patient['age']) : 'N/A' ?></td>
                            </tr>
                            <tr>
                                <td>Gender:</td>
                                <td>
                                    <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                                        <?= esc(ucfirst($patient['gender'] ?? 'N/A')) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php if (!empty($patient['civil_status'])): ?>
                            <tr>
                                <td>Civil Status:</td>
                                <td><?= esc($patient['civil_status']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td>Contact:</td>
                                <td><?= esc($patient['contact'] ?? 'N/A') ?></td>
                            </tr>
                            <?php if (!empty($patient['email'])): ?>
                            <tr>
                                <td>Email:</td>
                                <td><?= esc($patient['email']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td>Address:</td>
                                <td>
                                    <?php 
                                    $addressParts = array_filter([
                                        $patient['address_street'] ?? null,
                                        $patient['address_barangay'] ?? null,
                                        $patient['address_city'] ?? null,
                                        $patient['address_province'] ?? null
                                    ]);
                                    $fullAddress = !empty($addressParts) ? implode(', ', $addressParts) : ($patient['address'] ?? 'N/A');
                                    echo esc($fullAddress);
                                    ?>
                                </td>
                            </tr>
                            <?php if (!empty($patient['nationality'])): ?>
                            <tr>
                                <td>Nationality:</td>
                                <td><?= esc($patient['nationality']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($patient['religion'])): ?>
                            <tr>
                                <td>Religion:</td>
                                <td><?= esc($patient['religion']) ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-section">
                        <div class="info-section-title">
                            <i class="fas fa-info-circle"></i>
                            Registration Information
                        </div>
                        <table class="info-table">
                            <?php if (isset($patientSource) && $patientSource === 'patients'): ?>
                                <tr>
                                    <td>Patient Type:</td>
                                    <td><span class="badge-modern" style="background: #d1fae5; color: #065f46;"><?= esc($patient['type'] ?? 'Out-Patient') ?></span></td>
                                </tr>
                                <?php if (!empty($patient['visit_type'])): ?>
                                <tr>
                                    <td>Visit Type:</td>
                                    <td><span class="badge-modern" style="background: #dbeafe; color: #1e40af;"><?= esc($patient['visit_type']) ?></span></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($patient['purpose'])): ?>
                                <tr>
                                    <td>Purpose/Reason:</td>
                                    <td><?= esc($patient['purpose']) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($patient['admission_date'])): ?>
                                <tr>
                                    <td>Admission Date:</td>
                                    <td><?= date('F d, Y', strtotime($patient['admission_date'])) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($patient['room_number'])): ?>
                                <tr>
                                    <td>Room Number:</td>
                                    <td><?= esc($patient['room_number']) ?></td>
                                </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (!empty($patient['registration_date'])): ?>
                            <tr>
                                <td>Registration Date:</td>
                                <td><?= date('F d, Y', strtotime($patient['registration_date'])) ?></td>
                            </tr>
                            <?php else: ?>
                            <tr>
                                <td>Registered Date:</td>
                                <td><?= !empty($patient['created_at']) ? date('F d, Y h:i A', strtotime($patient['created_at'])) : 'N/A' ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td>Last Updated:</td>
                                <td><?= !empty($patient['updated_at']) ? date('F d, Y h:i A', strtotime($patient['updated_at'])) : 'N/A' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Information -->
    <?php 
    // Show medical information for In-Patients (regardless of source)
    $isInPatient = ($patient['type'] ?? '') === 'In-Patient' || strtoupper(trim($patient['visit_type'] ?? '')) === 'ADMISSION';
    ?>
    <?php if ($isInPatient): ?>
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-heartbeat"></i>
                Medical Information
            </h5>
        </div>
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-section">
                        <table class="info-table">
                            <?php if (!empty($patient['blood_type'])): ?>
                            <tr>
                                <td>Blood Type:</td>
                                <td><span class="badge-modern" style="background: #fee2e2; color: #991b1b;"><?= esc($patient['blood_type']) ?></span></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($patient['allergies'])): ?>
                            <tr>
                                <td>Allergies:</td>
                                <td><?= esc($patient['allergies']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($patient['existing_conditions'])): ?>
                            <tr>
                                <td>Existing Conditions:</td>
                                <td><?= esc($patient['existing_conditions']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($patient['current_medications'])): ?>
                            <tr>
                                <td>Current Medications:</td>
                                <td><?= esc($patient['current_medications']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($patient['past_surgeries'])): ?>
                            <tr>
                                <td>Past Surgeries:</td>
                                <td><?= esc($patient['past_surgeries']) ?></td>
                            </tr>
                            <?php endif; ?>
                            <?php if (!empty($patient['family_history'])): ?>
                            <tr>
                                <td>Family History:</td>
                                <td><?= esc($patient['family_history']) ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-section">
                        <div class="info-section-title" style="margin-bottom: 16px;">
                            <i class="fas fa-heartbeat"></i>
                            Latest Vital Signs
                        </div>
                        <?php if (!empty($latestVitals)): ?>
                            <?php
                            $nurseName = trim(($latestVitals['nurse_first_name'] ?? '') . ' ' . ($latestVitals['nurse_last_name'] ?? ''));
                            if (empty($nurseName)) {
                                $nurseName = $latestVitals['nurse_username'] ?? 'Nurse';
                            }
                            ?>
                            <table class="info-table">
                                <tr>
                                    <td>Date & Time:</td>
                                    <td><strong><?= esc(date('M d, Y h:i A', strtotime($latestVitals['recorded_at'] ?? $latestVitals['created_at']))) ?></strong></td>
                                </tr>
                                <?php if ($latestVitals['blood_pressure_systolic'] && $latestVitals['blood_pressure_diastolic']): 
                                    $systolic = (float)$latestVitals['blood_pressure_systolic'];
                                    $diastolic = (float)$latestVitals['blood_pressure_diastolic'];
                                    // Normal BP: 90-120/60-80 mmHg
                                    $bpStatus = 'normal';
                                    $bpColor = '#2e7d32';
                                    if ($systolic > 120 || $diastolic > 80) {
                                        $bpStatus = 'high';
                                        $bpColor = '#ef4444';
                                    } elseif ($systolic < 90 || $diastolic < 60) {
                                        $bpStatus = 'low';
                                        $bpColor = '#f59e0b';
                                    }
                                ?>
                                <tr>
                                    <td>Blood Pressure:</td>
                                    <td>
                                        <strong style="color: <?= $bpColor ?>; font-size: 16px;"><?= esc($latestVitals['blood_pressure_systolic']) ?>/<?= esc($latestVitals['blood_pressure_diastolic']) ?></strong> mmHg
                                        <br>
                                        <small style="color: #64748b; font-size: 12px;">
                                            <i class="fas fa-info-circle"></i> Normal: 90-120/60-80 mmHg
                                            <?php if ($bpStatus !== 'normal'): ?>
                                                <span style="color: <?= $bpColor ?>; font-weight: 600; margin-left: 8px;">
                                                    (<?= $bpStatus === 'high' ? 'High' : 'Low' ?>)
                                                </span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($latestVitals['heart_rate'])): 
                                    $hr = (float)$latestVitals['heart_rate'];
                                    // Normal HR: 60-100 bpm (adults)
                                    $hrStatus = 'normal';
                                    $hrColor = '#2e7d32';
                                    if ($hr > 100) {
                                        $hrStatus = 'high';
                                        $hrColor = '#ef4444';
                                    } elseif ($hr < 60) {
                                        $hrStatus = 'low';
                                        $hrColor = '#f59e0b';
                                    }
                                ?>
                                <tr>
                                    <td>Heart Rate:</td>
                                    <td>
                                        <strong style="color: <?= $hrColor ?>; font-size: 16px;"><?= esc($latestVitals['heart_rate']) ?></strong> bpm
                                        <br>
                                        <small style="color: #64748b; font-size: 12px;">
                                            <i class="fas fa-info-circle"></i> Normal: 60-100 bpm
                                            <?php if ($hrStatus !== 'normal'): ?>
                                                <span style="color: <?= $hrColor ?>; font-weight: 600; margin-left: 8px;">
                                                    (<?= $hrStatus === 'high' ? 'High' : 'Low' ?>)
                                                </span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($latestVitals['temperature'])): 
                                    $temp = (float)$latestVitals['temperature'];
                                    // Normal Temp: 36.5-37.5°C
                                    $tempStatus = 'normal';
                                    $tempColor = '#2e7d32';
                                    if ($temp > 37.5) {
                                        $tempStatus = 'high';
                                        $tempColor = '#ef4444';
                                    } elseif ($temp < 36.5) {
                                        $tempStatus = 'low';
                                        $tempColor = '#f59e0b';
                                    }
                                ?>
                                <tr>
                                    <td>Temperature:</td>
                                    <td>
                                        <strong style="color: <?= $tempColor ?>; font-size: 16px;"><?= esc($latestVitals['temperature']) ?></strong> °C
                                        <br>
                                        <small style="color: #64748b; font-size: 12px;">
                                            <i class="fas fa-info-circle"></i> Normal: 36.5-37.5°C
                                            <?php if ($tempStatus !== 'normal'): ?>
                                                <span style="color: <?= $tempColor ?>; font-weight: 600; margin-left: 8px;">
                                                    (<?= $tempStatus === 'high' ? 'High' : 'Low' ?>)
                                                </span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($latestVitals['oxygen_saturation'])): 
                                    $o2 = (float)$latestVitals['oxygen_saturation'];
                                    // Normal O2 Sat: 95-100%
                                    $o2Status = 'normal';
                                    $o2Color = '#2e7d32';
                                    if ($o2 < 95) {
                                        $o2Status = 'low';
                                        $o2Color = '#ef4444';
                                    }
                                ?>
                                <tr>
                                    <td>O2 Saturation:</td>
                                    <td>
                                        <strong style="color: <?= $o2Color ?>; font-size: 16px;"><?= esc($latestVitals['oxygen_saturation']) ?></strong> %
                                        <br>
                                        <small style="color: #64748b; font-size: 12px;">
                                            <i class="fas fa-info-circle"></i> Normal: 95-100%
                                            <?php if ($o2Status !== 'normal'): ?>
                                                <span style="color: <?= $o2Color ?>; font-weight: 600; margin-left: 8px;">
                                                    (Low - Critical)
                                                </span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($latestVitals['respiratory_rate'])): 
                                    $rr = (float)$latestVitals['respiratory_rate'];
                                    // Normal RR: 12-20 /min
                                    $rrStatus = 'normal';
                                    $rrColor = '#2e7d32';
                                    if ($rr > 20) {
                                        $rrStatus = 'high';
                                        $rrColor = '#ef4444';
                                    } elseif ($rr < 12) {
                                        $rrStatus = 'low';
                                        $rrColor = '#f59e0b';
                                    }
                                ?>
                                <tr>
                                    <td>Respiratory Rate:</td>
                                    <td>
                                        <strong style="color: <?= $rrColor ?>; font-size: 16px;"><?= esc($latestVitals['respiratory_rate']) ?></strong> /min
                                        <br>
                                        <small style="color: #64748b; font-size: 12px;">
                                            <i class="fas fa-info-circle"></i> Normal: 12-20 /min
                                            <?php if ($rrStatus !== 'normal'): ?>
                                                <span style="color: <?= $rrColor ?>; font-weight: 600; margin-left: 8px;">
                                                    (<?= $rrStatus === 'high' ? 'High' : 'Low' ?>)
                                                </span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($latestVitals['weight'])): ?>
                                <tr>
                                    <td>Weight:</td>
                                    <td><strong><?= esc($latestVitals['weight']) ?></strong> kg</td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($latestVitals['height'])): ?>
                                <tr>
                                    <td>Height:</td>
                                    <td><strong><?= esc($latestVitals['height']) ?></strong> cm</td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td>Recorded By:</td>
                                    <td><span class="badge-modern" style="background: #d1fae5; color: #065f46;"><?= esc($nurseName) ?></span></td>
                                </tr>
                                <?php if (!empty($latestVitals['notes'])): ?>
                                <tr>
                                    <td>Notes:</td>
                                    <td><?= esc($latestVitals['notes']) ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        <?php else: ?>
                            <div style="text-align: center; padding: 20px; color: #94a3b8;">
                                <i class="fas fa-heartbeat" style="font-size: 32px; margin-bottom: 8px; opacity: 0.3;"></i>
                                <p style="margin: 0; font-size: 14px;">No vital signs recorded yet</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Vital Signs History Table -->
            <?php if (!empty($vitalSigns)): ?>
            <div style="margin-top: 32px;">
                <div class="info-section-title" style="margin-bottom: 16px;">
                    <i class="fas fa-history"></i>
                    Vital Signs History
                </div>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>BP</th>
                                <th>HR</th>
                                <th>Temp</th>
                                <th>O2 Sat</th>
                                <th>RR</th>
                                <th>Weight</th>
                                <th>Height</th>
                                <th>Status</th>
                                <th>Recorded By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vitalSigns as $vital): 
                                $nurseName = trim(($vital['nurse_first_name'] ?? '') . ' ' . ($vital['nurse_last_name'] ?? ''));
                                if (empty($nurseName)) {
                                    $nurseName = $vital['nurse_username'] ?? 'Nurse';
                                }
                                
                                // Show Create Order button only for the latest vital (first in array) if it has no order
                                $showCreateOrderButton = ($vital === $vitalSigns[0]) && empty($vital['has_order']);
                                ?>
                                <tr>
                                    <td><?= esc(date('M d, Y h:i A', strtotime($vital['recorded_at'] ?? $vital['created_at']))) ?></td>
                                    <td>
                                        <?php if ($vital['blood_pressure_systolic'] && $vital['blood_pressure_diastolic']): 
                                            $systolic = (float)$vital['blood_pressure_systolic'];
                                            $diastolic = (float)$vital['blood_pressure_diastolic'];
                                            $bpStatus = 'normal';
                                            $bpColor = '#2e7d32';
                                            if ($systolic > 120 || $diastolic > 80) {
                                                $bpStatus = 'high';
                                                $bpColor = '#ef4444';
                                            } elseif ($systolic < 90 || $diastolic < 60) {
                                                $bpStatus = 'low';
                                                $bpColor = '#f59e0b';
                                            }
                                        ?>
                                            <div>
                                                <strong style="color: <?= $bpColor ?>;"><?= esc($vital['blood_pressure_systolic']) ?>/<?= esc($vital['blood_pressure_diastolic']) ?></strong>
                                                <br>
                                                <small style="color: #64748b; font-size: 11px;">Normal: 90-120/60-80</small>
                                                <?php if ($bpStatus !== 'normal'): ?>
                                                    <br>
                                                    <small style="color: <?= $bpColor ?>; font-weight: 600; font-size: 11px;">
                                                        (<?= $bpStatus === 'high' ? 'High' : 'Low' ?>)
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #94a3b8;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($vital['heart_rate'])): 
                                            $hr = (float)$vital['heart_rate'];
                                            $hrStatus = 'normal';
                                            $hrColor = '#2e7d32';
                                            if ($hr > 100) {
                                                $hrStatus = 'high';
                                                $hrColor = '#ef4444';
                                            } elseif ($hr < 60) {
                                                $hrStatus = 'low';
                                                $hrColor = '#f59e0b';
                                            }
                                        ?>
                                            <div>
                                                <strong style="color: <?= $hrColor ?>;"><?= esc($vital['heart_rate']) ?></strong> bpm
                                                <br>
                                                <small style="color: #64748b; font-size: 11px;">Normal: 60-100</small>
                                                <?php if ($hrStatus !== 'normal'): ?>
                                                    <br>
                                                    <small style="color: <?= $hrColor ?>; font-weight: 600; font-size: 11px;">
                                                        (<?= $hrStatus === 'high' ? 'High' : 'Low' ?>)
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #94a3b8;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($vital['temperature'])): 
                                            $temp = (float)$vital['temperature'];
                                            $tempStatus = 'normal';
                                            $tempColor = '#2e7d32';
                                            if ($temp > 37.5) {
                                                $tempStatus = 'high';
                                                $tempColor = '#ef4444';
                                            } elseif ($temp < 36.5) {
                                                $tempStatus = 'low';
                                                $tempColor = '#f59e0b';
                                            }
                                        ?>
                                            <div>
                                                <strong style="color: <?= $tempColor ?>;"><?= esc($vital['temperature']) ?></strong>°C
                                                <br>
                                                <small style="color: #64748b; font-size: 11px;">Normal: 36.5-37.5</small>
                                                <?php if ($tempStatus !== 'normal'): ?>
                                                    <br>
                                                    <small style="color: <?= $tempColor ?>; font-weight: 600; font-size: 11px;">
                                                        (<?= $tempStatus === 'high' ? 'High' : 'Low' ?>)
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #94a3b8;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($vital['oxygen_saturation'])): 
                                            $o2 = (float)$vital['oxygen_saturation'];
                                            $o2Status = 'normal';
                                            $o2Color = '#2e7d32';
                                            if ($o2 < 95) {
                                                $o2Status = 'low';
                                                $o2Color = '#ef4444';
                                            }
                                        ?>
                                            <div>
                                                <strong style="color: <?= $o2Color ?>;"><?= esc($vital['oxygen_saturation']) ?></strong>%
                                                <br>
                                                <small style="color: #64748b; font-size: 11px;">Normal: 95-100</small>
                                                <?php if ($o2Status !== 'normal'): ?>
                                                    <br>
                                                    <small style="color: <?= $o2Color ?>; font-weight: 600; font-size: 11px;">
                                                        (Low - Critical)
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #94a3b8;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($vital['respiratory_rate'])): 
                                            $rr = (float)$vital['respiratory_rate'];
                                            $rrStatus = 'normal';
                                            $rrColor = '#2e7d32';
                                            if ($rr > 20) {
                                                $rrStatus = 'high';
                                                $rrColor = '#ef4444';
                                            } elseif ($rr < 12) {
                                                $rrStatus = 'low';
                                                $rrColor = '#f59e0b';
                                            }
                                        ?>
                                            <div>
                                                <strong style="color: <?= $rrColor ?>;"><?= esc($vital['respiratory_rate']) ?></strong> /min
                                                <br>
                                                <small style="color: #64748b; font-size: 11px;">Normal: 12-20</small>
                                                <?php if ($rrStatus !== 'normal'): ?>
                                                    <br>
                                                    <small style="color: <?= $rrColor ?>; font-weight: 600; font-size: 11px;">
                                                        (<?= $rrStatus === 'high' ? 'High' : 'Low' ?>)
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #94a3b8;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($vital['weight'] ? $vital['weight'] . ' kg' : 'N/A') ?></td>
                                    <td><?= esc($vital['height'] ? $vital['height'] . ' cm' : 'N/A') ?></td>
                                    <td>
                                        <?php if (isset($vital['status']['overall'])): ?>
                                            <?php if ($vital['status']['overall'] === 'new'): ?>
                                                <span class="vital-status-badge vital-status-new">
                                                    <i class="fas fa-plus-circle"></i> New
                                                </span>
                                            <?php elseif ($vital['status']['overall'] === 'improving'): ?>
                                                <span class="vital-status-badge vital-status-improving">
                                                    <i class="fas fa-arrow-up"></i> Improving
                                                </span>
                                            <?php elseif ($vital['status']['overall'] === 'worsening'): ?>
                                                <span class="vital-status-badge vital-status-worsening">
                                                    <i class="fas fa-arrow-down"></i> Worsening
                                                </span>
                                            <?php else: ?>
                                                <span class="vital-status-badge vital-status-stable">
                                                    <i class="fas fa-minus-circle"></i> Stable
                                                </span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: #94a3b8;">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($nurseName) ?></td>
                                    <td>
                                        <?php 
                                        // Show Create Order button for all vital signs in history
                                        $isLatestVital = ($vital === $vitalSigns[0]);
                                        $vitalStatus = $vital['status']['overall'] ?? 'stable';
                                        $orderPatientId = $vitalsPatientId ?? $patient['id'] ?? $patient['patient_id'] ?? null;
                                        
                                        // Show "Create Order" button for all vitals (not just latest or worsening)
                                        if (empty($vital['has_order'])): 
                                            $vitalId = $vital['id'] ?? null;
                                            ?>
                                            <a href="<?= site_url('doctor/orders/create?patient_id=' . ($orderPatientId ?? '') . ($vitalId ? '&vital_id=' . $vitalId : '')) ?>" 
                                               class="btn-modern btn-modern-primary btn-sm-modern" 
                                               style="padding: 6px 12px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;"
                                               title="Create medical order based on this vital signs record">
                                                <i class="fas fa-file-medical"></i>
                                                Create Order
                                            </a>
                                        <?php elseif ($isLatestVital && !empty($vital['admission_id']) && $vitalStatus === 'stable'): 
                                            // Show "Ready for Discharge" button only for latest vital if status is stable and patient has admission
                                            ?>
                                            <a href="<?= site_url('doctor/discharge/create/' . $vital['admission_id']) ?>" 
                                               class="btn-modern btn-modern-success btn-sm-modern" 
                                               style="padding: 6px 12px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px; background: #10b981; color: white;">
                                                <i class="fas fa-sign-out-alt"></i>
                                                Ready for Discharge
                                            </a>
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-size: 12px;">Order created</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lab Test Results -->
    <?php if (!empty($labResults)): ?>
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-vial"></i>
                Lab Test Results
            </h5>
        </div>
        <div class="card-body-modern">
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Test Name</th>
                            <th>Test Type</th>
                            <th>Priority</th>
                            <th>Requested Date</th>
                            <th>Completed Date</th>
                            <th>Completed By</th>
                            <th>Result</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($labResults as $result): ?>
                            <tr>
                                <td>
                                    <strong style="color: #0288d1;"><?= esc($result['test_name']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                                        <?= esc($result['test_type'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $priority = strtoupper($result['priority'] ?? 'routine');
                                    $priorityClass = ($priority === 'STAT' || $priority === 'URGENT') ? 'badge-danger' : 'badge-info';
                                    ?>
                                    <span class="badge-modern <?= $priorityClass ?>">
                                        <?= esc($priority) ?>
                                    </span>
                                </td>
                                <td><?= esc(date('M d, Y', strtotime($result['requested_date'] ?? $result['created_at']))) ?></td>
                                <td>
                                    <?php if (!empty($result['completed_at'])): ?>
                                        <strong style="color: #10b981;">
                                            <?= esc(date('M d, Y h:i A', strtotime($result['completed_at']))) ?>
                                        </strong>
                                    <?php else: ?>
                                        <span style="color: #94a3b8;">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($result['completed_by_name'])): ?>
                                        <span class="badge-modern" style="background: #d1fae5; color: #065f46;">
                                            <?= esc($result['completed_by_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #94a3b8;">Lab Staff</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($result['result'])): ?>
                                        <div style="max-width: 300px;">
                                            <div style="padding: 8px; background: #f8fafc; border-radius: 6px; border-left: 3px solid #0288d1;">
                                                <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">
                                                    Result Preview:
                                                </div>
                                                <div style="font-size: 13px; color: #1e293b; white-space: pre-wrap; max-height: 60px; overflow: hidden; text-overflow: ellipsis;">
                                                    <?= esc(substr($result['result'], 0, 150)) ?>
                                                    <?= strlen($result['result']) > 150 ? '...' : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #94a3b8;">No result available</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($result['result'])): ?>
                                        <button onclick="viewLabResult(<?= $result['id'] ?>, '<?= esc($result['test_name'], 'js') ?>')" 
                                                class="btn-modern btn-modern-primary btn-sm-modern"
                                                style="padding: 6px 12px; font-size: 12px;">
                                            <i class="fas fa-eye"></i> View Result
                                        </button>
                                    <?php endif; ?>
                                    <?php if (!empty($result['result_file'])): ?>
                                        <a href="<?= base_url($result['result_file']) ?>" 
                                           target="_blank"
                                           class="btn-modern btn-modern-info btn-sm-modern"
                                           style="padding: 6px 12px; font-size: 12px; margin-top: 4px; display: inline-block;">
                                            <i class="fas fa-file-pdf"></i> Download
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Insurance Information -->
    <?php if ($isInPatient && (!empty($patient['insurance_provider']) || !empty($patient['insurance_number']) || !empty($patient['philhealth_number']))): ?>
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-shield-alt"></i>
                Insurance Information
            </h5>
        </div>
        <div class="card-body-modern">
            <div class="info-section">
                <table class="info-table">
                    <?php if (!empty($patient['insurance_provider'])): ?>
                    <tr>
                        <td>Insurance Provider:</td>
                        <td><?= esc($patient['insurance_provider']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($patient['insurance_number'])): ?>
                    <tr>
                        <td>Insurance Number / Member ID:</td>
                        <td><?= esc($patient['insurance_number']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($patient['philhealth_number'])): ?>
                    <tr>
                        <td>PhilHealth Number:</td>
                        <td><?= esc($patient['philhealth_number']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($patient['payment_type'])): ?>
                    <tr>
                        <td>Payment Type:</td>
                        <td><span class="badge-modern" style="background: #dbeafe; color: #1e40af;"><?= esc($patient['payment_type']) ?></span></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($patient['billing_address'])): ?>
                    <tr>
                        <td>Billing Address:</td>
                        <td><?= esc($patient['billing_address']) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Emergency Contact Information -->
    <?php if ($isInPatient && (!empty($patient['emergency_name']) || !empty($patient['emergency_contact']))): ?>
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-phone-alt"></i>
                Emergency Contact Information
            </h5>
        </div>
        <div class="card-body-modern">
            <div class="info-section">
                <table class="info-table">
                    <?php if (!empty($patient['emergency_name'])): ?>
                    <tr>
                        <td>Emergency Contact Name:</td>
                        <td><strong><?= esc($patient['emergency_name']) ?></strong></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($patient['emergency_relationship'])): ?>
                    <tr>
                        <td>Relationship:</td>
                        <td><?= esc($patient['emergency_relationship']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($patient['emergency_contact'])): ?>
                    <tr>
                        <td>Emergency Contact Number:</td>
                        <td><?= esc($patient['emergency_contact']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($patient['emergency_address'])): ?>
                    <tr>
                        <td>Emergency Contact Address:</td>
                        <td><?= esc($patient['emergency_address']) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- All Orders History -->
    <?php if (!empty($allPatientOrders)): ?>
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-clipboard-list"></i>
                All Orders History
            </h5>
        </div>
        <div class="card-body-modern">
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Order Type</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Completed</th>
                            <th>Completed By</th>
                            <th>Nurse</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allPatientOrders as $order): ?>
                            <tr>
                                <td><strong>#<?= esc($order['id']) ?></strong></td>
                                <td>
                                    <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                                        <?= esc(ucwords(str_replace('_', ' ', $order['order_type']))) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="max-width: 300px;">
                                        <?= esc($order['order_description'] ?? 'N/A') ?>
                                        <?php if (!empty($order['medicine_name'])): ?>
                                            <br><small style="color: #64748b;">Medicine: <?= esc($order['medicine_name']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($order['status'] === 'completed'): ?>
                                        <span class="badge-modern" style="background: #d1fae5; color: #065f46;">
                                            <i class="fas fa-check-circle"></i> Completed
                                        </span>
                                    <?php elseif ($order['status'] === 'in_progress'): ?>
                                        <span class="badge-modern" style="background: #dbeafe; color: #1e40af;">
                                            <i class="fas fa-spinner"></i> In Progress
                                        </span>
                                    <?php elseif ($order['status'] === 'cancelled'): ?>
                                        <span class="badge-modern" style="background: #fee2e2; color: #991b1b;">
                                            <i class="fas fa-times-circle"></i> Cancelled
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-modern" style="background: #fef3c7; color: #92400e;">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc(date('M d, Y h:i A', strtotime($order['created_at']))) ?></td>
                                <td>
                                    <?php if (!empty($order['completed_at'])): ?>
                                        <?= esc(date('M d, Y h:i A', strtotime($order['completed_at']))) ?>
                                    <?php else: ?>
                                        <span style="color: #94a3b8;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($order['completed_by_name'])): ?>
                                        <span class="badge-modern" style="background: #f0fdf4; color: #065f46;">
                                            <?= esc($order['completed_by_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: #94a3b8;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($order['nurse_name'])): ?>
                                        <?= esc($order['nurse_name']) ?>
                                    <?php else: ?>
                                        <span style="color: #94a3b8;">N/A</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Consultation History -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-calendar-check"></i>
                Consultation History
            </h5>
        </div>
        <div class="card-body-modern">
            <?php if (!empty($consultations)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Details</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($consultations as $consultation): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($consultation['consultation_date'])) ?></td>
                                    <td><?= date('h:i A', strtotime($consultation['consultation_time'])) ?></td>
                                    <td>
                                        <span class="badge-modern" style="background: <?= $consultation['type'] == 'upcoming' ? '#dbeafe' : '#d1fae5'; ?>; color: <?= $consultation['type'] == 'upcoming' ? '#1e40af' : '#065f46'; ?>;">
                                            <?= esc(ucfirst($consultation['type'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-modern" style="background: <?= 
                                            $consultation['status'] == 'approved' ? '#d1fae5' : 
                                            ($consultation['status'] == 'pending' ? '#fef3c7' : '#fee2e2'); 
                                        ?>; color: <?= 
                                            $consultation['status'] == 'approved' ? '#065f46' : 
                                            ($consultation['status'] == 'pending' ? '#92400e' : '#991b1b'); 
                                        ?>;">
                                            <?= esc(ucfirst($consultation['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="max-width: 400px;">
                                            <?php if (!empty($consultation['diagnosis'])): ?>
                                                <div style="margin-bottom: 6px;">
                                                    <strong style="color: #1e293b; font-size: 12px;">Diagnosis:</strong> 
                                                    <div style="color: #475569; margin-top: 2px;">
                                                        <?= esc(substr($consultation['diagnosis'], 0, 100)) ?><?= strlen($consultation['diagnosis']) > 100 ? '...' : '' ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Lab Tests -->
                                            <?php if (!empty($consultation['lab_tests'])): ?>
                                                <div style="margin-bottom: 6px; padding: 8px; background: #eff6ff; border-radius: 6px; border-left: 3px solid #3b82f6;">
                                                    <strong style="color: #1e40af; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                                                        <i class="fas fa-vial"></i> Lab Tests (<?= count($consultation['lab_tests']) ?>):
                                                    </strong>
                                                    <div style="margin-top: 4px;">
                                                        <?php foreach ($consultation['lab_tests'] as $labTest): ?>
                                                            <div style="font-size: 11px; color: #475569; margin-bottom: 2px;">
                                                                • <?= esc($labTest['test_name'] ?? $labTest['test_type'] ?? 'Lab Test') ?>
                                                                <?php if (!empty($labTest['status'])): ?>
                                                                    <span style="background: <?= 
                                                                        $labTest['status'] == 'completed' ? '#d1fae5' : 
                                                                        ($labTest['status'] == 'in_progress' ? '#fef3c7' : '#fee2e2'); 
                                                                    ?>; color: <?= 
                                                                        $labTest['status'] == 'completed' ? '#065f46' : 
                                                                        ($labTest['status'] == 'in_progress' ? '#92400e' : '#991b1b'); 
                                                                    ?>; padding: 2px 6px; border-radius: 4px; font-size: 10px; margin-left: 4px;">
                                                                        <?= esc(ucfirst($labTest['status'])) ?>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Prescriptions -->
                                            <?php if (!empty($consultation['prescriptions'])): ?>
                                                <div style="margin-bottom: 6px; padding: 8px; background: #f0fdf4; border-radius: 6px; border-left: 3px solid #22c55e;">
                                                    <strong style="color: #166534; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                                                        <i class="fas fa-prescription"></i> Prescriptions (<?= count($consultation['prescriptions']) ?>):
                                                    </strong>
                                                    <div style="margin-top: 4px;">
                                                        <?php foreach ($consultation['prescriptions'] as $prescription): ?>
                                                            <div style="font-size: 11px; color: #475569; margin-bottom: 2px;">
                                                                • <strong><?= esc($prescription['medicine_name'] ?? 'Medication') ?></strong>
                                                                <?php if (!empty($prescription['dosage'])): ?>
                                                                    - <?= esc($prescription['dosage']) ?>
                                                                <?php endif; ?>
                                                                <?php if (!empty($prescription['frequency'])): ?>
                                                                    (<?= esc($prescription['frequency']) ?>)
                                                                <?php endif; ?>
                                                                <?php if (!empty($prescription['pharmacy_status'])): ?>
                                                                    <span style="background: <?= 
                                                                        $prescription['pharmacy_status'] == 'dispensed' ? '#d1fae5' : 
                                                                        ($prescription['pharmacy_status'] == 'prepared' ? '#fef3c7' : '#fee2e2'); 
                                                                    ?>; color: <?= 
                                                                        $prescription['pharmacy_status'] == 'dispensed' ? '#065f46' : 
                                                                        ($prescription['pharmacy_status'] == 'prepared' ? '#92400e' : '#991b1b'); 
                                                                    ?>; padding: 2px 6px; border-radius: 4px; font-size: 10px; margin-left: 4px;">
                                                                        <?= esc(ucfirst($prescription['pharmacy_status'])) ?>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($consultation['observations'])): ?>
                                                <div style="margin-bottom: 4px; color: #64748b; font-size: 12px;">
                                                    <strong>Observations:</strong> 
                                                    <?= esc(substr($consultation['observations'], 0, 80)) ?><?= strlen($consultation['observations']) > 80 ? '...' : '' ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($consultation['notes'])): ?>
                                                <div style="color: #64748b; font-size: 12px;">
                                                    <strong>Notes:</strong> 
                                                    <?= esc(substr($consultation['notes'], 0, 80)) ?><?= strlen($consultation['notes']) > 80 ? '...' : '' ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if (empty($consultation['diagnosis']) && empty($consultation['observations']) && empty($consultation['notes']) && empty($consultation['lab_tests']) && empty($consultation['prescriptions'])): ?>
                                                <span style="color: #94a3b8;">No details</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('doctor/consultations/view/' . $consultation['id']) ?>" 
                                           class="btn-sm-modern btn-info" 
                                           title="View Consultation Details">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-check"></i>
                    <p style="margin: 0; color: #64748b;">No consultation history found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Lab Result Modal -->
<div id="labResultModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; overflow-y: auto;">
    <div style="max-width: 800px; margin: 50px auto; background: white; border-radius: 12px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #e5e7eb;">
            <h3 style="margin: 0; color: #0288d1;">
                <i class="fas fa-vial"></i> Lab Test Result
            </h3>
            <button onclick="closeLabResultModal()" style="background: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 14px;">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
        <div id="labResultContent" style="color: #1e293b;">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
// Lab Results Data
const labResultsData = <?= json_encode($labResults ?? []) ?>;

function viewLabResult(labRequestId, testName) {
    const result = labResultsData.find(r => r.id == labRequestId);
    if (!result) {
        alert('Lab result not found');
        return;
    }
    
    const modal = document.getElementById('labResultModal');
    const content = document.getElementById('labResultContent');
    
    let html = `
        <div style="margin-bottom: 20px;">
            <h4 style="color: #0288d1; margin-bottom: 12px;">${testName}</h4>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 16px;">
                <div style="padding: 12px; background: #f8fafc; border-radius: 8px;">
                    <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Test Type</div>
                    <div style="font-weight: 600; color: #1e293b;">${result.test_type || 'N/A'}</div>
                </div>
                <div style="padding: 12px; background: #f8fafc; border-radius: 8px;">
                    <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Priority</div>
                    <div style="font-weight: 600; color: #1e293b;">${(result.priority || 'routine').toUpperCase()}</div>
                </div>
                <div style="padding: 12px; background: #f8fafc; border-radius: 8px;">
                    <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Requested Date</div>
                    <div style="font-weight: 600; color: #1e293b;">${new Date(result.requested_date || result.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</div>
                </div>
                <div style="padding: 12px; background: #f8fafc; border-radius: 8px;">
                    <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Completed Date</div>
                    <div style="font-weight: 600; color: #10b981;">${result.completed_at ? new Date(result.completed_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A'}</div>
                </div>
            </div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <h5 style="color: #475569; margin-bottom: 12px; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px;">Test Result</h5>
            <div style="padding: 16px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #0288d1; white-space: pre-wrap; font-family: 'Courier New', monospace; font-size: 14px; line-height: 1.6; color: #1e293b;">
                ${result.result || 'No result available'}
            </div>
        </div>
    `;
    
    if (result.result_file) {
        html += `
            <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #e5e7eb;">
                <a href="${result.result_file}" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #0288d1; color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    <i class="fas fa-file-pdf"></i> Download Result File
                </a>
            </div>
        `;
    }
    
    if (result.completed_by_name) {
        html += `
            <div style="margin-top: 16px; padding: 12px; background: #d1fae5; border-radius: 8px; color: #065f46; font-size: 13px;">
                <i class="fas fa-user-check"></i> Completed by: <strong>${result.completed_by_name}</strong>
            </div>
        `;
    }
    
    content.innerHTML = html;
    modal.style.display = 'block';
}

function closeLabResultModal() {
    document.getElementById('labResultModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('labResultModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeLabResultModal();
    }
});
</script>

<?= $this->endSection() ?>
