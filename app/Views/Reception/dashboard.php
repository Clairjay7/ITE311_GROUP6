<?= $this->extend('template/header') ?>

<?= $this->section('content') ?>
<style>
    .dashboard-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
    .mini-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; box-shadow: 0 2px 6px rgba(15,23,42,.08); position: relative; overflow: hidden; transition: all .25s ease; }
    .mini-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #2e7d32, #43a047); }
    .mini-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,24,40,.12); }
    .mini-title { margin: 0; font-size: 14px; font-weight: 700; color: #2e7d32; }
    .mini-value { margin-top: 8px; font-size: 28px; font-weight: 800; color: #1f2937; }
    .mini-subtext { margin-top: 4px; font-size: 12px; color: #64748b; }
    @media (max-width: 600px) { .mini-value { font-size: 24px; } }
</style>
<div class="dashboard-summary">
    <div class="mini-card">
        <div class="mini-title">Today's Appointments</div>
        <div id="appointments_today" class="mini-value">--</div>
        <div class="mini-subtext">+3 from yesterday</div>
    </div>
    
    <div class="mini-card">
        <div class="mini-title">Waiting Patients</div>
        <div id="waiting_patients" class="mini-value">--</div>
        <div class="mini-subtext">In queue</div>
    </div>

    <div class="mini-card">
        <div class="mini-title">New Registrations</div>
        <div id="new_registrations" class="mini-value">--</div>
        <div class="mini-subtext">Today</div>
    </div>

    <div class="mini-card">
        <div class="mini-title">Total In-Patients</div>
        <div id="total_inpatients" class="mini-value">--</div>
        <div class="mini-subtext">Overall registered</div>
    </div>

    <div class="mini-card">
        <div class="mini-title">Total Out-Patients</div>
        <div id="total_outpatients" class="mini-value">--</div>
        <div class="mini-subtext">Overall registered</div>
    </div>
</div>

<!-- Pending Admissions Section -->
<div style="margin-top: 32px; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; box-shadow: 0 2px 6px rgba(15,23,42,.08); border-left: 4px solid #dc2626;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #dc2626; font-size: 24px;">
            <i class="fas fa-hospital"></i> Pending Admissions
        </h2>
        <a href="<?= site_url('receptionist/admission/pending') ?>" style="background: #dc2626; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            View All <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div id="pending-admissions-container">
        <div style="text-align: center; padding: 40px; color: #94a3b8;">
            <i class="fas fa-spinner fa-spin" style="font-size: 24px;"></i>
            <p>Loading pending admissions...</p>
        </div>
    </div>
</div>

<!-- Doctor Schedules Quick Access -->
<div style="margin-top: 32px; background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%); border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(2, 136, 209, 0.2);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="margin: 0 0 8px; color: white; font-size: 24px;">
                <i class="fas fa-calendar-alt"></i> Doctor Schedules
            </h2>
            <p style="margin: 0; color: rgba(255, 255, 255, 0.9); font-size: 14px;">
                View all doctor working schedules for the year
            </p>
        </div>
        <a href="<?= site_url('receptionist/schedule') ?>" style="background: white; color: #0288d1; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease;">
            <i class="fas fa-eye"></i> View Schedules
        </a>
    </div>
</div>

<!-- Waiting List Section -->
<div style="margin-top: 32px; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; box-shadow: 0 2px 6px rgba(15,23,42,.08);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #2e7d32; font-size: 24px;">
            <i class="fas fa-clock"></i> Patient Waiting List
        </h2>
        <button onclick="refreshWaitingList()" style="background: #2e7d32; color: white; border: none; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-weight: 600;">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>
    
    <div id="waiting-list-container">
        <div style="text-align: center; padding: 40px; color: #94a3b8;">
            <i class="fas fa-spinner fa-spin" style="font-size: 24px;"></i>
            <p>Loading waiting list...</p>
        </div>
    </div>
</div>

<!-- Assign Doctor Modal -->
<div id="assignDoctorModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; padding: 32px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 8px 32px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 style="margin: 0; color: #2e7d32; font-size: 20px;">
                <i class="fas fa-user-md"></i> Assign Doctor
            </h3>
            <button onclick="closeAssignModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        
        <form id="assignDoctorForm">
            <input type="hidden" id="modal_patient_id" name="patient_id">
            <input type="hidden" id="modal_patient_source" name="patient_source">
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">Patient</label>
                <div id="modal_patient_info" style="padding: 12px; background: #f8fafc; border-radius: 8px; color: #64748b;"></div>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">Visit Type</label>
                <input type="text" id="modal_visit_type" name="visit_type" readonly style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px; background: #f8fafc;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">Reason for Visit</label>
                <input type="text" id="modal_reason" name="reason" placeholder="Enter reason for visit" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">Appointment Date *</label>
                <input type="date" id="modal_appointment_date" name="appointment_date" required style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">Select Doctor *</label>
                <select id="modal_doctor_id" name="doctor_id" required style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;" onchange="loadDoctorSchedule()">
                    <option value="">-- Select Doctor --</option>
                </select>
            </div>
            
            <div id="doctor_schedule_info" style="margin-bottom: 16px; padding: 12px; background: #f0fdf4; border-radius: 8px; border-left: 4px solid #10b981; display: none;">
                <div style="font-weight: 600; color: #065f46; margin-bottom: 8px;">Doctor Schedule</div>
                <div id="schedule_details" style="color: #047857; font-size: 14px;"></div>
                <div style="margin-top: 8px; color: #065f46; font-weight: 600;">
                    Queue Number: <span id="queue_number_display">--</span>
                </div>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">Appointment Time *</label>
                <input type="time" id="modal_appointment_time" name="appointment_time" required style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #1e293b;">Room/Department</label>
                <input type="text" id="modal_room" name="room" placeholder="Optional" style="width: 100%; padding: 10px; border: 1px solid #e5e7eb; border-radius: 8px;">
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" style="flex: 1; background: #2e7d32; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-check"></i> Assign Doctor
                </button>
                <button type="button" onclick="closeAssignModal()" style="flex: 1; background: #64748b; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let waitingListData = [];

// Load waiting list on page load
document.addEventListener('DOMContentLoaded', function() {
    refreshWaitingList();
    // Set default appointment date to today
    document.getElementById('modal_appointment_date').value = new Date().toISOString().split('T')[0];
    // Load available doctors
    loadAvailableDoctors();
});

async function refreshWaitingList() {
    try {
        const response = await fetch('<?= site_url('receptionist/assign-doctor/waiting-list') ?>', {
            headers: { 'Accept': 'application/json' }
        });
        
        if (!response.ok) throw new Error('Failed to load waiting list');
        
        const data = await response.json();
        waitingListData = data.patients || [];
        renderWaitingList();
    } catch (error) {
        console.error('Error loading waiting list:', error);
        document.getElementById('waiting-list-container').innerHTML = `
            <div style="text-align: center; padding: 40px; color: #ef4444;">
                <i class="fas fa-exclamation-circle"></i>
                <p>Failed to load waiting list. Please try again.</p>
            </div>
        `;
    }
}

function renderWaitingList() {
    const container = document.getElementById('waiting-list-container');
    
    if (waitingListData.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 40px; color: #94a3b8;">
                <i class="fas fa-check-circle" style="font-size: 48px; margin-bottom: 16px; display: block; color: #cbd5e1;"></i>
                <p>No patients waiting for doctor assignment.</p>
            </div>
        `;
        return;
    }
    
    let html = `
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #e8f5e9; border-bottom: 2px solid #4caf50;">
                    <th style="padding: 12px; text-align: left; color: #2e7d32; font-weight: 600;">Patient Name</th>
                    <th style="padding: 12px; text-align: left; color: #2e7d32; font-weight: 600;">Age</th>
                    <th style="padding: 12px; text-align: left; color: #2e7d32; font-weight: 600;">Gender</th>
                    <th style="padding: 12px; text-align: left; color: #2e7d32; font-weight: 600;">Visit Type</th>
                    <th style="padding: 12px; text-align: left; color: #2e7d32; font-weight: 600;">Reason</th>
                    <th style="padding: 12px; text-align: left; color: #2e7d32; font-weight: 600;">Registered</th>
                    <th style="padding: 12px; text-align: center; color: #2e7d32; font-weight: 600;">Action</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    waitingListData.forEach(patient => {
        const isEmergency = patient.visit_type === 'Emergency';
        const canAssignDoctor = !isEmergency && (patient.visit_type === 'Consultation' || patient.visit_type === 'Check-up' || patient.visit_type === 'Follow-up');
        const visitTypeBadge = isEmergency 
            ? '<span style="background: #ef4444; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">EMERGENCY</span>'
            : (patient.visit_type === 'Consultation' || patient.visit_type === 'Check-up' || patient.visit_type === 'Follow-up')
            ? `<span style="background: #3b82f6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">${escapeHtml(patient.visit_type)}</span>`
            : `<span style="background: #64748b; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">${escapeHtml(patient.visit_type || 'N/A')}</span>`;
        
        html += `
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px;"><strong>${escapeHtml(patient.name)}</strong></td>
                <td style="padding: 12px;">${patient.age ?? 'N/A'}</td>
                <td style="padding: 12px;">${escapeHtml(patient.gender)}</td>
                <td style="padding: 12px;">${visitTypeBadge}</td>
                <td style="padding: 12px;">${escapeHtml(patient.reason)}</td>
                <td style="padding: 12px;">${formatDate(patient.registration_date)}</td>
                <td style="padding: 12px; text-align: center;">
                    ${canAssignDoctor 
                        ? `<button onclick="openAssignModal(${patient.id}, '${patient.source}', '${escapeHtml(patient.name)}', '${escapeHtml(patient.visit_type)}', '${escapeHtml(patient.reason)}')" 
                                style="background: #2e7d32; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-user-md"></i> Assign Doctor
                          </button>`
                        : isEmergency
                        ? `<span style="color: #ef4444; font-weight: 600; font-size: 12px;">
                            <i class="fas fa-exclamation-triangle"></i> Requires Triage First
                           </span>`
                        : `<span style="color: #64748b; font-size: 12px;">N/A</span>`
                    }
                </td>
            </tr>
        `;
    });
    
    html += `
            </tbody>
        </table>
    `;
    
    container.innerHTML = html;
}

function openAssignModal(patientId, patientSource, patientName, visitType, reason) {
    document.getElementById('modal_patient_id').value = patientId;
    document.getElementById('modal_patient_source').value = patientSource;
    document.getElementById('modal_patient_info').textContent = patientName;
    document.getElementById('modal_visit_type').value = visitType;
    document.getElementById('modal_reason').value = reason;
    document.getElementById('assignDoctorModal').style.display = 'flex';
    loadAvailableDoctors();
}

function closeAssignModal() {
    document.getElementById('assignDoctorModal').style.display = 'none';
    document.getElementById('assignDoctorForm').reset();
    document.getElementById('doctor_schedule_info').style.display = 'none';
}

async function loadAvailableDoctors() {
    const date = document.getElementById('modal_appointment_date').value || new Date().toISOString().split('T')[0];
    
    try {
        const response = await fetch(`<?= site_url('receptionist/assign-doctor/available-doctors') ?>?date=${date}`, {
            headers: { 'Accept': 'application/json' }
        });
        
        if (!response.ok) throw new Error('Failed to load doctors');
        
        const data = await response.json();
        const select = document.getElementById('modal_doctor_id');
        select.innerHTML = '<option value="">-- Select Doctor --</option>';
        
        data.doctors.forEach(doctor => {
            const option = document.createElement('option');
            option.value = doctor.id;
            option.textContent = `${doctor.name} - ${doctor.specialization} (${doctor.current_appointments}/${doctor.max_capacity})`;
            option.dataset.queue = doctor.queue_number;
            option.dataset.schedules = JSON.stringify(doctor.schedules);
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error loading doctors:', error);
    }
}

function loadDoctorSchedule() {
    const select = document.getElementById('modal_doctor_id');
    const selectedOption = select.options[select.selectedIndex];
    
    if (!selectedOption || !selectedOption.value) {
        document.getElementById('doctor_schedule_info').style.display = 'none';
        return;
    }
    
    const schedules = JSON.parse(selectedOption.dataset.schedules || '[]');
    const queueNumber = selectedOption.dataset.queue || '1';
    
    document.getElementById('queue_number_display').textContent = queueNumber;
    
    let scheduleHtml = '';
    if (schedules.length > 0) {
        schedules.forEach(schedule => {
            scheduleHtml += `<div>${schedule.start_time} - ${schedule.end_time}</div>`;
        });
    } else {
        scheduleHtml = '<div style="color: #f59e0b;">No specific schedule for this date</div>';
    }
    
    document.getElementById('schedule_details').innerHTML = scheduleHtml;
    document.getElementById('doctor_schedule_info').style.display = 'block';
}

// Handle form submission
document.getElementById('assignDoctorForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const select = document.getElementById('modal_doctor_id');
    const selectedOption = select.options[select.selectedIndex];
    formData.append('queue_number', selectedOption ? selectedOption.dataset.queue : '1');
    
    try {
        const response = await fetch('<?= site_url('receptionist/assign-doctor/assign') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            closeAssignModal();
            refreshWaitingList();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error assigning doctor:', error);
        alert('Failed to assign doctor. Please try again.');
    }
});

// Update appointment date change
document.getElementById('modal_appointment_date').addEventListener('change', function() {
    loadAvailableDoctors();
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}
</script>

<script>
const endpoint = '<?= site_url('receptionist/dashboard/stats') ?>';
async function refreshDashboard(){
  try{
    const res = await fetch(endpoint, { headers: { 'Accept': 'application/json' } });
    if(!res.ok) throw new Error('Network');
    const data = await res.json();
    const setText = (id, val) => { const el = document.getElementById(id); if(el) el.textContent = val; };
    setText('appointments_today', data.appointments_today ?? '--');
    setText('waiting_patients', data.waiting_patients ?? '--');
    setText('new_registrations', data.new_registrations ?? '--');
    setText('total_inpatients', data.total_inpatients ?? '--');
    setText('total_outpatients', data.total_outpatients ?? '--');

    const amt = typeof data.pending_payments_amount === 'number' ? new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(data.pending_payments_amount) : '₱--';
    setText('pending_payments_amount', amt);
    setText('pending_invoices', (data.pending_invoices ?? '--') + ' invoices');
    
    // Update pending admissions
    updatePendingAdmissions(data.pending_admissions || []);
  }catch(e){ /* silent fail */ }
}

function updatePendingAdmissions(admissions) {
  const container = document.getElementById('pending-admissions-container');
  if (!container) return;
  
  if (admissions && admissions.length > 0) {
    let html = '';
    admissions.forEach(admission => {
      const consultationDate = new Date(admission.consultation_date).toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
      });
      html += `
        <div style="background: #fee2e2; padding: 16px; border-radius: 8px; margin-bottom: 12px; border-left: 4px solid #dc2626;">
          <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 12px;">
            <div style="flex: 1;">
              <div style="font-weight: 600; color: #991b1b; margin-bottom: 8px;">
                <i class="fas fa-user-injured"></i> ${admission.firstname || ''} ${admission.lastname || ''}
              </div>
              <div style="font-size: 13px; color: #7f1d1d; margin-bottom: 4px;">
                <i class="fas fa-user-md"></i> Doctor: ${admission.doctor_name || 'N/A'}
              </div>
              <div style="font-size: 12px; color: #991b1b; margin-top: 4px;">
                <i class="fas fa-calendar"></i> Consultation: ${consultationDate}
              </div>
            </div>
            <a href="<?= site_url('admission/create/') ?>${admission.id}" 
               style="background: #dc2626; color: white; text-decoration: none; padding: 8px 16px; border-radius: 8px; white-space: nowrap; font-weight: 600;">
              <i class="fas fa-hospital"></i> Admit Patient
            </a>
          </div>
        </div>
      `;
    });
    container.innerHTML = html;
  } else {
    container.innerHTML = '<div style="text-align: center; padding: 20px; color: #94a3b8;"><i class="fas fa-check-circle" style="font-size: 32px; margin-bottom: 8px; opacity: 0.5;"></i><p style="margin: 0; font-size: 14px;">No pending admissions</p></div>';
  }
}
window.addEventListener('DOMContentLoaded', () => {
  refreshDashboard();
  setInterval(refreshDashboard, 15000);
});
</script>
<?= $this->endSection() ?>