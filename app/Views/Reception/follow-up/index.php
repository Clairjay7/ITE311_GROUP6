<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Follow Up Appointments<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<style>
    .follow-up-page {
        padding: 24px 0;
    }
    
    .page-header-modern {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        border-radius: 16px;
        padding: 28px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(2, 136, 209, 0.2);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .page-header-modern h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .page-header-modern h2 i {
        font-size: 32px;
    }
    
    .stats-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }
    
    .card-modern {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    
    .card-header-modern {
        background: #f8fafc;
        padding: 20px 24px;
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .card-header-modern h4 {
        margin: 0;
        color: #1e293b;
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .card-body-modern {
        padding: 24px;
    }
    
    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    }
    
    .table-modern thead th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #1e293b;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #cbd5e1;
    }
    
    .table-modern tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .table-modern tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .table-modern tbody td {
        padding: 16px;
        color: #475569;
        font-size: 14px;
        vertical-align: middle;
    }
    
    .patient-name {
        font-weight: 600;
        color: #1e293b;
        font-size: 15px;
    }
    
    .doctor-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .doctor-name {
        font-weight: 600;
        color: #1e293b;
    }
    
    .doctor-spec {
        font-size: 12px;
        color: #64748b;
    }
    
    .badge-modern {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }
    
    .badge-scheduled {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
    }
    
    .badge-confirmed {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
    }
    
    .badge-pending {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
    }
    
    .btn-modern {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-view {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        color: white;
    }
    
    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    
    .empty-state i {
        font-size: 64px;
        color: #cbd5e1;
        margin-bottom: 16px;
        display: block;
    }
    
    .empty-state h3 {
        color: #64748b;
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 8px;
    }
    
    .empty-state p {
        color: #94a3b8;
        font-size: 14px;
        margin: 0;
    }
    
    .search-filter {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    
    .search-filter input,
    .search-filter select {
        flex: 1;
        min-width: 200px;
        padding: 10px 14px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    
    .search-filter input:focus,
    .search-filter select:focus {
        outline: none;
        border-color: #0288d1;
        box-shadow: 0 0 0 3px rgba(2, 136, 209, 0.1);
    }
    
    .btn-filter {
        background: #0288d1;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .btn-filter:hover {
        background: #0277bd;
        transform: translateY(-1px);
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<div class="follow-up-page container py-4">
  <div class="page-header-modern">
    <h2>
      <i class="fas fa-arrow-rotate-right"></i> Follow Up Appointments
    </h2>
    <div class="stats-badge">
      <i class="fas fa-calendar-check"></i> <?= count($appointments ?? []) ?> Scheduled
    </div>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success" style="border-radius: 10px; border-left: 4px solid #10b981;">
      <i class="fas fa-check-circle"></i> <?= esc(session()->getFlashdata('success')) ?>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger" style="border-radius: 10px; border-left: 4px solid #ef4444;">
      <i class="fas fa-exclamation-circle"></i> <?= esc(session()->getFlashdata('error')) ?>
    </div>
  <?php endif; ?>

  <div class="card-modern">
    <div class="card-header-modern">
      <h4>
        <i class="fas fa-list"></i> Appointment List
      </h4>
    </div>
    
    <div class="card-body-modern">
      <!-- Patient Filter Dropdown -->
      <div class="search-filter" style="margin-bottom: 24px;">
        <label for="patientFilter" style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: #1e293b; min-width: 150px;">
          <i class="fas fa-user" style="color: #0288d1;"></i> Filter by Patient:
        </label>
        <select id="patientFilter" name="patient_id" class="form-control" style="flex: 1; min-width: 250px; padding: 10px 14px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: all 0.2s ease;" onchange="filterByPatient()">
          <option value="">All Patients</option>
          <?php if (!empty($patients)): ?>
            <?php foreach ($patients as $patient): ?>
              <option value="<?= esc($patient['id']) ?>" <?= ($selectedPatientId ?? '') == $patient['id'] ? 'selected' : '' ?>>
                <?= esc($patient['name']) ?> <?= !empty($patient['contact']) ? ' - ' . esc($patient['contact']) : '' ?>
              </option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
        <?php if (!empty($selectedPatientId)): ?>
          <a href="<?= site_url('receptionist/follow-up') ?>" class="btn-filter" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-times"></i> Clear Filter
          </a>
        <?php endif; ?>
      </div>
      <?php if (empty($appointments)): ?>
        <div class="empty-state">
          <i class="fas fa-calendar-times"></i>
          <h3>No Follow-Up Appointments</h3>
          <p>There are no scheduled follow-up appointments at this time.</p>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table-modern">
            <thead>
              <tr>
                <th style="width: 60px;">No#</th>
                <th>Patient Name</th>
                <th>Contact</th>
                <th>Doctor</th>
                <th>Date</th>
                <th>Time</th>
                <th>Reason</th>
                <th style="width: 120px;">Status</th>
                <th style="width: 100px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php $i = 1; foreach ($appointments as $appointment): 
                $status = strtolower($appointment['status'] ?? 'scheduled');
                $badgeClass = 'badge-scheduled';
                if ($status === 'confirmed') $badgeClass = 'badge-confirmed';
                if ($status === 'pending') $badgeClass = 'badge-pending';
              ?>
                <tr>
                  <td style="text-align: center; font-weight: 600; color: #64748b;"><?= $i++ ?></td>
                  <td>
                    <div class="patient-name">
                      <?= esc(trim(($appointment['patient_first_name'] ?? '') . ' ' . ($appointment['patient_last_name'] ?? ''))) ?: 'N/A' ?>
                    </div>
                  </td>
                  <td>
                    <i class="fas fa-phone" style="color: #64748b; margin-right: 6px;"></i>
                    <?= esc($appointment['patient_contact'] ?? 'N/A') ?>
                  </td>
                  <td>
                    <div class="doctor-info">
                      <span class="doctor-name">
                        <i class="fas fa-user-md" style="color: #0288d1; margin-right: 6px;"></i>
                        <?= esc($appointment['doctor_name'] ?? 'N/A') ?>
                      </span>
                      <?php if (!empty($appointment['specialization'])): ?>
                        <span class="doctor-spec"><?= esc($appointment['specialization']) ?></span>
                      <?php endif; ?>
                    </div>
                  </td>
                  <td>
                    <i class="fas fa-calendar" style="color: #64748b; margin-right: 6px;"></i>
                    <?= date('M d, Y', strtotime($appointment['appointment_date'])) ?>
                  </td>
                  <td>
                    <i class="fas fa-clock" style="color: #64748b; margin-right: 6px;"></i>
                    <?= date('h:i A', strtotime($appointment['appointment_time'])) ?>
                  </td>
                  <td>
                    <span style="color: #475569;"><?= esc($appointment['reason'] ?? 'N/A') ?></span>
                  </td>
                  <td>
                    <span class="badge-modern <?= $badgeClass ?>">
                      <?= esc(ucfirst($appointment['status'] ?? 'scheduled')) ?>
                    </span>
                  </td>
                  <td>
                    <button type="button" class="btn-modern btn-view" onclick="viewAppointment(<?= $appointment['id'] ?>)">
                      <i class="fas fa-eye"></i> View
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Appointment Details Modal -->
<div id="appointmentModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
  <div style="background: white; border-radius: 16px; padding: 32px; max-width: 700px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 8px 32px rgba(0,0,0,0.2); position: relative;">
    <button onclick="closeAppointmentModal()" style="position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 28px; cursor: pointer; color: #64748b; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s ease;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
      &times;
    </button>
    
    <div style="margin-bottom: 24px;">
      <h3 style="margin: 0; color: #0288d1; font-size: 24px; font-weight: 700; display: flex; align-items: center; gap: 12px;">
        <i class="fas fa-calendar-check"></i> Appointment Details
      </h3>
    </div>
    
    <div id="appointmentDetails" style="display: grid; gap: 20px;">
      <!-- Content will be loaded here -->
      <div style="text-align: center; padding: 40px;">
        <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #0288d1;"></i>
        <p style="margin-top: 16px; color: #64748b;">Loading appointment details...</p>
      </div>
    </div>
  </div>
</div>

<script>
function viewAppointment(appointmentId) {
  const modal = document.getElementById('appointmentModal');
  const detailsContainer = document.getElementById('appointmentDetails');
  
  // Show modal
  modal.style.display = 'flex';
  
  // Load appointment details
  detailsContainer.innerHTML = `
    <div style="text-align: center; padding: 40px;">
      <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: #0288d1;"></i>
      <p style="margin-top: 16px; color: #64748b;">Loading appointment details...</p>
    </div>
  `;
  
  fetch('<?= site_url('appointments/show/') ?>' + appointmentId)
    .then(response => response.json())
    .then(data => {
      if (data.success && data.appointment) {
        const apt = data.appointment;
        const appointmentDate = apt.appointment_date ? new Date(apt.appointment_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A';
        const appointmentTime = apt.appointment_time ? new Date('2000-01-01 ' + apt.appointment_time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }) : 'N/A';
        
        detailsContainer.innerHTML = `
          <div style="display: grid; gap: 20px;">
            <!-- Patient Information -->
            <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border-left: 4px solid #0288d1;">
              <h4 style="margin: 0 0 16px; color: #1e293b; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-user" style="color: #0288d1;"></i> Patient Information
              </h4>
              <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                <div>
                  <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Patient Name</div>
                  <div style="font-weight: 600; color: #1e293b;">${apt.patient_name || 'N/A'}</div>
                </div>
                <div>
                  <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Contact Number</div>
                  <div style="font-weight: 600; color: #1e293b;">${apt.patient_phone || 'N/A'}</div>
                </div>
              </div>
            </div>
            
            <!-- Doctor Information -->
            <div style="background: #f0fdf4; padding: 20px; border-radius: 12px; border-left: 4px solid #10b981;">
              <h4 style="margin: 0 0 16px; color: #1e293b; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-user-md" style="color: #10b981;"></i> Doctor Information
              </h4>
              <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                <div>
                  <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Doctor Name</div>
                  <div style="font-weight: 600; color: #1e293b;">${apt.doctor_name || 'N/A'}</div>
                </div>
                <div>
                  <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Email</div>
                  <div style="font-weight: 600; color: #1e293b;">${apt.doctor_email || 'N/A'}</div>
                </div>
              </div>
            </div>
            
            <!-- Appointment Details -->
            <div style="background: #fef3c7; padding: 20px; border-radius: 12px; border-left: 4px solid #f59e0b;">
              <h4 style="margin: 0 0 16px; color: #1e293b; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-calendar-alt" style="color: #f59e0b;"></i> Appointment Details
              </h4>
              <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                <div>
                  <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Date</div>
                  <div style="font-weight: 600; color: #1e293b;">${appointmentDate}</div>
                </div>
                <div>
                  <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Time</div>
                  <div style="font-weight: 600; color: #1e293b;">${appointmentTime}</div>
                </div>
                <div>
                  <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Type</div>
                  <div style="font-weight: 600; color: #1e293b; text-transform: capitalize;">${apt.appointment_type || 'N/A'}</div>
                </div>
                <div>
                  <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Status</div>
                  <div>
                    <span style="display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: capitalize; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af;">
                      ${apt.status || 'scheduled'}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Reason -->
            ${apt.reason ? `
            <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border-left: 4px solid #64748b;">
              <h4 style="margin: 0 0 12px; color: #1e293b; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-file-alt" style="color: #64748b;"></i> Reason for Appointment
              </h4>
              <div style="color: #475569; line-height: 1.6;">${apt.reason}</div>
            </div>
            ` : ''}
            
            ${apt.notes ? `
            <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border-left: 4px solid #64748b;">
              <h4 style="margin: 0 0 12px; color: #1e293b; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-sticky-note" style="color: #64748b;"></i> Notes
              </h4>
              <div style="color: #475569; line-height: 1.6;">${apt.notes}</div>
            </div>
            ` : ''}
            
            <!-- Prescriptions -->
            ${apt.prescriptions && apt.prescriptions.length > 0 ? `
            <div style="background: #fef3c7; padding: 20px; border-radius: 12px; border-left: 4px solid #f59e0b;">
              <h4 style="margin: 0 0 16px; color: #1e293b; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-pills" style="color: #f59e0b;"></i> Prescriptions
              </h4>
              <div style="display: grid; gap: 16px;">
                ${apt.prescriptions.map((prescription, index) => `
                  <div style="background: white; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                      <span style="background: #f59e0b; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">${index + 1}</span>
                      <div>
                        <div style="font-weight: 700; color: #1e293b; font-size: 15px;">${prescription.medicine_name || 'Unknown Medicine'}</div>
                        ${prescription.generic_name ? `<div style="font-size: 12px; color: #64748b; margin-top: 2px;">${prescription.generic_name}</div>` : ''}
                      </div>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 12px;">
                      <div>
                        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Dosage</div>
                        <div style="font-weight: 600; color: #1e293b; font-size: 14px;">${prescription.dosage || 'N/A'}</div>
                      </div>
                      <div>
                        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Frequency</div>
                        <div style="font-weight: 600; color: #1e293b; font-size: 14px;">${prescription.frequency || 'N/A'}</div>
                      </div>
                      <div>
                        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Duration</div>
                        <div style="font-weight: 600; color: #1e293b; font-size: 14px;">${prescription.duration || 'N/A'}</div>
                      </div>
                      <div>
                        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">When to Take</div>
                        <div style="font-weight: 600; color: #1e293b; font-size: 14px;">
                          ${(() => {
                            const whenToTake = prescription.when_to_take || '';
                            const labels = {
                              'before_meal': 'Before Meal',
                              'after_meal': 'After Meal',
                              'with_meal': 'With Meal',
                              'empty_stomach': 'Empty Stomach',
                              'as_needed': 'As Needed (PRN)'
                            };
                            return labels[whenToTake] || whenToTake || 'N/A';
                          })()}
                        </div>
                      </div>
                      ${prescription.instructions && prescription.instructions !== 'N/A' ? `
                      <div style="grid-column: 1 / -1;">
                        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Instructions</div>
                        <div style="color: #475569; font-size: 14px; line-height: 1.5;">${prescription.instructions}</div>
                      </div>
                      ` : ''}
                    </div>
                  </div>
                `).join('')}
              </div>
            </div>
            ` : ''}
            
            <!-- Lab Results -->
            ${apt.lab_results && apt.lab_results.length > 0 ? `
            <div style="background: #e0f2fe; padding: 20px; border-radius: 12px; border-left: 4px solid #0288d1;">
              <h4 style="margin: 0 0 16px; color: #1e293b; font-size: 16px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-vial" style="color: #0288d1;"></i> Laboratory Test Results
              </h4>
              <div style="display: grid; gap: 16px;">
                ${apt.lab_results.map((labResult, index) => `
                  <div style="background: white; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                      <span style="background: #0288d1; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;">${index + 1}</span>
                      <div style="font-weight: 700; color: #1e293b; font-size: 15px;">${labResult.test_name || 'N/A'}</div>
                      ${labResult.test_type ? `<span style="background: #cbd5e1; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; margin-left: 8px;">${labResult.test_type}</span>` : ''}
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 12px;">
                      <div>
                        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Result</div>
                        <div style="font-weight: 600; color: #1e293b; font-size: 14px;">${labResult.result || 'N/A'}</div>
                      </div>
                      <div>
                        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Normal Range</div>
                        <div style="font-weight: 600; color: #1e293b; font-size: 14px;">${labResult.normal_range || 'N/A'}</div>
                      </div>
                      <div>
                        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Status</div>
                        <div>
                          <span style="display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: capitalize; background: ${labResult.status === 'normal' ? '#dcfce7' : labResult.status === 'abnormal' ? '#fee2e2' : '#fef3c7'}; color: ${labResult.status === 'normal' ? '#166534' : labResult.status === 'abnormal' ? '#991b1b' : '#92400e'};">
                            ${labResult.status || 'N/A'}
                          </span>
                        </div>
                      </div>
                      ${labResult.completed_at ? `
                      <div>
                        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Completed At</div>
                        <div style="font-weight: 600; color: #1e293b; font-size: 14px;">${new Date(labResult.completed_at).toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</div>
                      </div>
                      ` : ''}
                      ${labResult.notes ? `
                      <div style="grid-column: 1 / -1;">
                        <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Notes</div>
                        <div style="color: #475569; font-size: 14px; line-height: 1.5;">${labResult.notes}</div>
                      </div>
                      ` : ''}
                      ${labResult.result_file ? `
                      <div style="grid-column: 1 / -1;">
                        <a href="${labResult.result_file}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; color: #0288d1; text-decoration: none; font-weight: 600; font-size: 14px;">
                          <i class="fas fa-file-pdf"></i> View Result File
                        </a>
                      </div>
                      ` : ''}
                    </div>
                  </div>
                `).join('')}
              </div>
            </div>
            ` : ''}
          </div>
        `;
      } else {
        detailsContainer.innerHTML = `
          <div style="text-align: center; padding: 40px;">
            <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #ef4444; margin-bottom: 16px;"></i>
            <p style="color: #64748b;">${data.message || 'Failed to load appointment details'}</p>
          </div>
        `;
      }
    })
    .catch(error => {
      console.error('Error:', error);
      detailsContainer.innerHTML = `
        <div style="text-align: center; padding: 40px;">
          <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #ef4444; margin-bottom: 16px;"></i>
          <p style="color: #64748b;">An error occurred while loading appointment details.</p>
        </div>
      `;
    });
}

function closeAppointmentModal() {
  document.getElementById('appointmentModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('appointmentModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeAppointmentModal();
  }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeAppointmentModal();
  }
});

// Filter by patient function
function filterByPatient() {
  const patientId = document.getElementById('patientFilter').value;
  const url = new URL(window.location.href);
  
  if (patientId) {
    url.searchParams.set('patient_id', patientId);
  } else {
    url.searchParams.delete('patient_id');
  }
  
  window.location.href = url.toString();
}
</script>

<?= $this->endSection() ?>
