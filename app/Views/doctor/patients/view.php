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
                            <?php if (empty($patient['blood_type']) && empty($patient['allergies']) && empty($patient['existing_conditions']) && empty($patient['current_medications']) && empty($patient['past_surgeries']) && empty($patient['family_history'])): ?>
                            <tr>
                                <td colspan="2" style="text-align: center; color: #94a3b8; padding: 20px;">No medical information available</td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
<?= $this->endSection() ?>
