<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>New Appointment<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/appointments.css?v=20251114') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="appointments-page container py-4">
  <div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h3 class="page-title mb-0">New Appointment</h3>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="post" action="<?= site_url('appointments/create') ?>">
        <?= csrf_field() ?>
        
        <div class="row g-3">
          <!-- Patient Information Section -->
          <div class="col-12">
            <h5 class="mb-3" style="color: #0288d1; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px;">
              <i class="fas fa-user"></i> Patient Information
            </h5>
          </div>
          
          <div class="col-md-12">
            <label class="form-label">First Name <span class="text-danger">*</span></label>
            <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter first name" required>
          </div>
          
          <div class="col-md-12">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middle_name" id="middle_name" class="form-control" placeholder="Enter middle name">
          </div>
          
          <div class="col-md-12">
            <label class="form-label">Surname <span class="text-danger">*</span></label>
            <input type="text" name="surname" id="surname" class="form-control" placeholder="Enter surname" required>
          </div>
          
          <div class="col-md-12">
            <label class="form-label">Contact Number <span class="text-danger">*</span></label>
            <input type="text" name="contact" class="form-control" placeholder="e.g. 09123456789" required>
          </div>
          
          <div class="col-md-4">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" max="<?= date('Y-m-d') ?>">
          </div>
          
          <div class="col-md-4">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
              <option value="">-- Select Gender --</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
          </div>
          
          <div class="col-md-4">
            <label class="form-label">Age</label>
            <input type="number" name="age" id="age" class="form-control" placeholder="Age" min="0" max="150" readonly>
            <small class="form-text text-muted">Automatically calculated from date of birth</small>
          </div>
          
          <div class="col-md-4">
            <label class="form-label">Blood Type</label>
            <select name="blood_type" class="form-select">
              <option value="">-- Select Blood Type --</option>
              <option value="A+">A+</option>
              <option value="A-">A-</option>
              <option value="B+">B+</option>
              <option value="B-">B-</option>
              <option value="AB+">AB+</option>
              <option value="AB-">AB-</option>
              <option value="O+">O+</option>
              <option value="O-">O-</option>
            </select>
          </div>
          
          <div class="col-md-4">
            <label class="form-label">Province <span class="text-danger">*</span></label>
            <select name="address_province" id="address_province" class="form-select" required>
              <option value="">-- Select Province --</option>
            </select>
          </div>
          
          <div class="col-md-4">
            <label class="form-label">City/Municipality <span class="text-danger">*</span></label>
            <input type="text" name="address_city" id="address_city" class="form-control" required placeholder="Enter city or municipality">
          </div>
          
          <div class="col-md-4">
            <label class="form-label">Barangay <span class="text-danger">*</span></label>
            <input type="text" name="address_barangay" id="address_barangay" class="form-control" required placeholder="Enter barangay">
          </div>
          
          <div class="col-md-12">
            <label class="form-label">Complete Address (Street/House No.)</label>
            <textarea name="address" class="form-control" rows="2" placeholder="House No., Street, Building, etc."></textarea>
          </div>
          
          <!-- Appointment Details Section -->
          <div class="col-12 mt-4">
            <h5 class="mb-3" style="color: #0288d1; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px;">
              <i class="fas fa-calendar-check"></i> Appointment Details
            </h5>
          </div>
          
          <div class="col-md-12">
            <label class="form-label">Doctor <span class="text-danger">*</span></label>
            <select name="doctor_id" id="doctor_id" class="form-select" required>
              <?php if (!empty($doctors ?? [])): ?>
                <option value="" disabled selected>Select doctor</option>
                <?php foreach (($doctors ?? []) as $doc): ?>
                  <option value="<?= (int)$doc['id'] ?>" <?= (!($doc['is_available'] ?? true)) ? 'disabled style="color: #ef4444;"' : '' ?>>
                    <?= esc($doc['doctor_name'] ?? $doc['id']) ?>
                    <?php if (!empty($doc['specialization'])): ?>
                      - <?= esc($doc['specialization']) ?>
                    <?php endif; ?>
                    <?php if (!($doc['is_available'] ?? true)): ?>
                      (Unavailable)
                    <?php endif; ?>
                  </option>
                <?php endforeach; ?>
              <?php else: ?>
                <option value="" disabled selected>No doctors available</option>
              <?php endif; ?>
            </select>
            <!-- Doctor Schedule Display -->
            <div id="doctor_schedule_display" style="display: none; margin-top: 12px; margin-left: -15px; margin-right: -15px; padding: 12px; background: #e8f5e9; border-radius: 0; border-left: 4px solid #2e7d32;">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div style="font-weight: 600; color: #2e7d32;">
                  <i class="fas fa-calendar-check"></i> Doctor's Available Schedule:
                </div>
                <div style="display: flex; gap: 8px; align-items: center;">
                  <button type="button" id="prev_month_btn" class="btn btn-sm btn-outline-secondary" style="padding: 4px 12px; font-size: 12px;">
                    <i class="fas fa-chevron-left"></i> Prev
                  </button>
                  <span id="current_month_display" style="font-weight: 600; color: #1e293b; min-width: 150px; text-align: center; font-size: 13px;"></span>
                  <button type="button" id="next_month_btn" class="btn btn-sm btn-outline-secondary" style="padding: 4px 12px; font-size: 12px;">
                    Next <i class="fas fa-chevron-right"></i>
                  </button>
                </div>
              </div>
              <div id="schedule_info" style="color: #475569; font-size: 13px; overflow-x: auto; width: 100%; padding: 0;"></div>
            </div>
            
            <!-- Appointment Date - Below Schedule Display -->
            <div style="margin-top: 16px;">
              <label class="form-label">Appointment Date <span class="text-danger">*</span></label>
              <select name="appointment_date" id="appointment_date" class="form-select" required disabled>
                <option value="">-- Select Doctor First --</option>
              </select>
              <small class="form-text text-muted" id="date_hint">Please select a doctor first to see available dates</small>
            </div>
            
            <!-- Appointment Time - Below Appointment Date -->
            <div style="margin-top: 16px;">
              <label class="form-label">Appointment Time <span class="text-danger">*</span></label>
              <select name="appointment_time" id="appointment_time" class="form-select" required disabled>
                <option value="">-- Select Date First --</option>
              </select>
              <small class="form-text text-muted" id="time_hint">Please select a doctor and date first to see available times</small>
            </div>
          </div>
          
          <!-- Appointment Type is fixed as Consultation -->
          <input type="hidden" name="appointment_type" value="consultation">
          
          <div class="col-12">
            <label class="form-label">Reason for Visit / Notes <span class="text-danger">*</span></label>
            <textarea name="reason" rows="3" class="form-control" placeholder="Please describe the reason for this appointment or any specific concerns..." required></textarea>
          </div>
        </div>
        <div class="mt-3 d-flex justify-content-end gap-2">
          <button type="submit" class="btn btn-primary">Save</button>
          <a href="<?= site_url('receptionist/appointments/list') ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const doctorSelect = document.getElementById('doctor_id');
    const dateSelect = document.getElementById('appointment_date');
    const timeSelect = document.getElementById('appointment_time');
    const scheduleDisplay = document.getElementById('doctor_schedule_display');
    const scheduleInfo = document.getElementById('schedule_info');
    const dateHint = document.getElementById('date_hint');
    const timeHint = document.getElementById('time_hint');
    const dateOfBirthInput = document.getElementById('date_of_birth');
    const ageInput = document.getElementById('age');
    
    // Calculate age from date of birth
    if (dateOfBirthInput && ageInput) {
        dateOfBirthInput.addEventListener('change', function() {
            const dob = this.value;
            if (dob) {
                const today = new Date();
                const birthDate = new Date(dob);
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                
                // Adjust age if birthday hasn't occurred this year
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                
                ageInput.value = age >= 0 ? age : '';
            } else {
                ageInput.value = '';
            }
        });
    }
    
    // Load doctor schedule when doctor is selected
    doctorSelect.addEventListener('change', function() {
        const doctorId = this.value;
        
        if (!doctorId) {
            scheduleDisplay.style.display = 'none';
            dateSelect.innerHTML = '<option value="">-- Select Doctor First --</option>';
            dateSelect.disabled = true;
            timeSelect.innerHTML = '<option value="">-- Select Date First --</option>';
            timeSelect.disabled = true;
            return;
        }
        
        // Load available dates for this doctor
        loadAvailableDates(doctorId);
        // Load and display doctor's schedule
        loadDoctorSchedule(doctorId);
    });
    
    // Load available times when date is selected
    dateSelect.addEventListener('change', function() {
        const doctorId = doctorSelect.value;
        const date = this.value;
        
        if (!doctorId || !date) {
            timeSelect.innerHTML = '<option value="">-- Select Date First --</option>';
            timeSelect.disabled = true;
            return;
        }
        
        loadAvailableTimes(doctorId, date);
    });
    
    function loadAvailableDates(doctorId) {
        fetch('<?= site_url('appointments/get-available-dates') ?>?doctor_id=' + doctorId, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.dates && data.dates.length > 0) {
                dateSelect.innerHTML = '<option value="">-- Select Date --</option>';
                data.dates.forEach(date => {
                    const option = document.createElement('option');
                    option.value = date;
                    const dateObj = new Date(date + 'T00:00:00');
                    const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
                    const formattedDate = dateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                    option.textContent = `${dayName}, ${formattedDate}`;
                    dateSelect.appendChild(option);
                });
                dateSelect.disabled = false;
                dateHint.textContent = 'Select an available date from the doctor\'s schedule';
            } else {
                dateSelect.innerHTML = '<option value="">No available dates</option>';
                dateSelect.disabled = true;
                dateHint.textContent = 'No available dates for this doctor';
            }
        })
        .catch(error => {
            console.error('Error loading dates:', error);
            dateSelect.innerHTML = '<option value="">Error loading dates</option>';
            dateSelect.disabled = true;
        });
    }
    
    function loadAvailableTimes(doctorId, date) {
        fetch('<?= site_url('appointments/get-times-by-doctor-and-date') ?>?doctor_id=' + doctorId + '&date=' + date, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.times && data.times.length > 0) {
                timeSelect.innerHTML = '<option value="">-- Select Time --</option>';
                data.times.forEach(time => {
                    const option = document.createElement('option');
                    option.value = time.value;
                    option.textContent = time.label;
                    timeSelect.appendChild(option);
                });
                timeSelect.disabled = false;
                timeHint.textContent = 'Select an available time slot';
            } else {
                timeSelect.innerHTML = '<option value="">No available times</option>';
                timeSelect.disabled = true;
                timeHint.textContent = 'No available times for this date';
            }
        })
        .catch(error => {
            console.error('Error loading times:', error);
            timeSelect.innerHTML = '<option value="">Error loading times</option>';
            timeSelect.disabled = true;
        });
    }
    
    // Store schedule data globally for month navigation
    let allScheduleData = [];
    let currentMonthIndex = 0;
    let scheduleMonths = [];
    
    function loadDoctorSchedule(doctorId) {
        fetch('<?= site_url('appointments/get-doctor-schedule') ?>?doctor_id=' + doctorId, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.schedule && data.schedule.length > 0) {
                allScheduleData = data.schedule;
                
                // Group by month
                const groupedByMonth = {};
                data.schedule.forEach(item => {
                    const dateObj = new Date(item.shift_date + 'T00:00:00');
                    const monthKey = dateObj.getFullYear() + '-' + String(dateObj.getMonth() + 1).padStart(2, '0');
                    
                    if (!groupedByMonth[monthKey]) {
                        groupedByMonth[monthKey] = [];
                    }
                    groupedByMonth[monthKey].push(item);
                });
                
                // Get sorted month keys
                scheduleMonths = Object.keys(groupedByMonth).sort();
                currentMonthIndex = 0; // Start with first month
                
                // Display first month
                displayMonthSchedule(groupedByMonth, scheduleMonths[currentMonthIndex]);
                
                // Update navigation buttons
                updateMonthNavigation();
                
                scheduleDisplay.style.display = 'block';
            } else {
                scheduleInfo.innerHTML = '<div style="color: #ef4444;">No schedule available for this doctor</div>';
                scheduleDisplay.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading schedule:', error);
            scheduleInfo.innerHTML = '<div style="color: #ef4444;">Error loading schedule</div>';
            scheduleDisplay.style.display = 'block';
        });
    }
    
    function displayMonthSchedule(groupedByMonth, monthKey) {
        if (!groupedByMonth[monthKey] || groupedByMonth[monthKey].length === 0) {
            scheduleInfo.innerHTML = '<div style="color: #64748b; text-align: center; padding: 20px;">No schedule for this month</div>';
            return;
        }
        
        // Group by date within the month
        const groupedByDate = {};
        groupedByMonth[monthKey].forEach(item => {
            if (!groupedByDate[item.shift_date]) {
                groupedByDate[item.shift_date] = [];
            }
            groupedByDate[item.shift_date].push(item);
        });
        
        // Display month name
        const [year, month] = monthKey.split('-');
        const monthName = new Date(year, parseInt(month) - 1, 1).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        document.getElementById('current_month_display').textContent = monthName;
        
        // Landscape layout - full width to edges
        let scheduleHtml = '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 6px; margin-top: 8px; width: 100%; padding: 0;">';
        
        Object.keys(groupedByDate).sort().forEach(date => {
            const dateObj = new Date(date + 'T00:00:00');
            const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'short' });
            const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            
            scheduleHtml += `<div style="background: white; padding: 6px; border-radius: 4px; border: 1px solid #cbd5e1; min-width: 100px;">`;
            scheduleHtml += `<div style="font-weight: 600; color: #1e293b; margin-bottom: 3px; font-size: 11px; line-height: 1.2;">${dayName}<br>${formattedDate}</div>`;
            
            groupedByDate[date].forEach(item => {
                const startTime = new Date('2000-01-01 ' + item.start_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                const endTime = new Date('2000-01-01 ' + item.end_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                scheduleHtml += `<div style="font-size: 10px; color: #64748b; line-height: 1.2;">${startTime}<br>${endTime}</div>`;
            });
            
            scheduleHtml += `</div>`;
        });
        
        scheduleHtml += '</div>';
        scheduleInfo.innerHTML = scheduleHtml;
    }
    
    function updateMonthNavigation() {
        const prevBtn = document.getElementById('prev_month_btn');
        const nextBtn = document.getElementById('next_month_btn');
        
        // Disable prev button if on first month
        prevBtn.disabled = (currentMonthIndex === 0);
        prevBtn.style.opacity = (currentMonthIndex === 0) ? '0.5' : '1';
        prevBtn.style.cursor = (currentMonthIndex === 0) ? 'not-allowed' : 'pointer';
        
        // Disable next button if on last month
        nextBtn.disabled = (currentMonthIndex === scheduleMonths.length - 1);
        nextBtn.style.opacity = (currentMonthIndex === scheduleMonths.length - 1) ? '0.5' : '1';
        nextBtn.style.cursor = (currentMonthIndex === scheduleMonths.length - 1) ? 'not-allowed' : 'pointer';
    }
    
    // Helper function to rebuild groupedByMonth from allScheduleData
    function rebuildGroupedByMonth() {
        const groupedByMonth = {};
        allScheduleData.forEach(item => {
            const dateObj = new Date(item.shift_date + 'T00:00:00');
            const monthKey = dateObj.getFullYear() + '-' + String(dateObj.getMonth() + 1).padStart(2, '0');
            if (!groupedByMonth[monthKey]) {
                groupedByMonth[monthKey] = [];
            }
            groupedByMonth[monthKey].push(item);
        });
        return groupedByMonth;
    }
    
    // Month navigation event listeners - use event delegation
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'prev_month_btn') {
            e.preventDefault();
            if (currentMonthIndex > 0 && scheduleMonths.length > 0) {
                currentMonthIndex--;
                const groupedByMonth = rebuildGroupedByMonth();
                displayMonthSchedule(groupedByMonth, scheduleMonths[currentMonthIndex]);
                updateMonthNavigation();
            }
        }
        
        if (e.target && e.target.id === 'next_month_btn') {
            e.preventDefault();
            if (currentMonthIndex < scheduleMonths.length - 1 && scheduleMonths.length > 0) {
                currentMonthIndex++;
                const groupedByMonth = rebuildGroupedByMonth();
                displayMonthSchedule(groupedByMonth, scheduleMonths[currentMonthIndex]);
                updateMonthNavigation();
            }
        }
        
        // Also handle clicks on button icons
        if (e.target && e.target.closest('#prev_month_btn')) {
            e.preventDefault();
            if (currentMonthIndex > 0 && scheduleMonths.length > 0) {
                currentMonthIndex--;
                const groupedByMonth = rebuildGroupedByMonth();
                displayMonthSchedule(groupedByMonth, scheduleMonths[currentMonthIndex]);
                updateMonthNavigation();
            }
        }
        
        if (e.target && e.target.closest('#next_month_btn')) {
            e.preventDefault();
            if (currentMonthIndex < scheduleMonths.length - 1 && scheduleMonths.length > 0) {
                currentMonthIndex++;
                const groupedByMonth = rebuildGroupedByMonth();
                displayMonthSchedule(groupedByMonth, scheduleMonths[currentMonthIndex]);
                updateMonthNavigation();
            }
        }
    });
});
</script>
<script src="<?= base_url('js/philippine-address.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize provinces
    if (typeof populateProvinces === 'function') {
        populateProvinces('address_province');
    }
});
</script>
<?= $this->endSection() ?>