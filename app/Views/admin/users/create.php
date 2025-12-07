<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Add New User<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .admin-page-container {
        padding: 0;
    }
    
    .page-header {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(46, 125, 50, 0.2);
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
        padding: 32px;
        margin-bottom: 24px;
    }
    
    .form-group-modern {
        margin-bottom: 24px;
    }
    
    .form-label-modern {
        display: block;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .form-control-modern {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-control-modern:focus {
        outline: none;
        border-color: #2e7d32;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
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
        background: #f1f5f9;
        color: #475569;
    }
    
    .btn-modern-secondary:hover {
        background: #e2e8f0;
        color: #475569;
    }
    
    .text-danger {
        color: #ef4444;
        font-size: 13px;
        margin-top: 4px;
    }
    
    .alert-modern {
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .alert-modern-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
    
    .form-row-modern {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .form-section-title {
        font-size: 18px;
        font-weight: 700;
        color: #2e7d32;
        margin: 32px 0 20px 0;
        padding-bottom: 12px;
        border-bottom: 2px solid #c8e6c9;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-section-title:first-child {
        margin-top: 0;
    }
    
    .form-section-title i {
        font-size: 20px;
    }
    
    .form-hint {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
        font-style: italic;
    }
</style>

<div class="admin-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-user-plus"></i>
            Add New User
        </h1>
    </div>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert-modern alert-modern-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert-modern alert-modern-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            Please fix the following errors:
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="modern-card">
        <form action="<?= site_url('admin/users/store') ?>" method="post">
            <?= csrf_field() ?>
            
            <!-- Personal Information Section -->
            <h3 class="form-section-title">
                <i class="fas fa-user-circle"></i>
                Personal Information
            </h3>
            
            <div class="form-row-modern">
                <div class="form-group-modern">
                    <label class="form-label-modern" for="first_name">
                        <i class="fas fa-id-card me-2"></i>
                        First Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" class="form-control-modern" value="<?= old('first_name') ?>" required placeholder="Enter first name">
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['first_name'])): ?>
                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['first_name']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group-modern">
                    <label class="form-label-modern" for="middle_name">
                        <i class="fas fa-id-card me-2"></i>
                        Middle Name
                    </label>
                    <input type="text" name="middle_name" id="middle_name" class="form-control-modern" value="<?= old('middle_name') ?>" placeholder="Enter middle name (optional)">
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['middle_name'])): ?>
                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['middle_name']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group-modern">
                    <label class="form-label-modern" for="last_name">
                        <i class="fas fa-id-card me-2"></i>
                        Last Name / Surname <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" class="form-control-modern" value="<?= old('last_name') ?>" required placeholder="Enter last name">
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['last_name'])): ?>
                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['last_name']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-row-modern">
                <div class="form-group-modern">
                    <label class="form-label-modern" for="contact">
                        <i class="fas fa-phone me-2"></i>
                        Contact Number
                    </label>
                    <input type="text" name="contact" id="contact" class="form-control-modern" value="<?= old('contact') ?>" placeholder="09XX-XXX-XXXX">
                    <div class="form-hint">Optional - Enter contact number</div>
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['contact'])): ?>
                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['contact']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group-modern" style="grid-column: span 2;">
                    <label class="form-label-modern" for="address">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Address
                    </label>
                    <input type="text" name="address" id="address" class="form-control-modern" value="<?= old('address') ?>" placeholder="Enter complete address (optional)">
                    <div class="form-hint">Optional - Enter complete address</div>
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['address'])): ?>
                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['address']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Account Information Section -->
            <h3 class="form-section-title">
                <i class="fas fa-key"></i>
                Account Information
            </h3>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="email">
                    <i class="fas fa-envelope me-2"></i>
                    Email <span class="text-danger">*</span>
                </label>
                <input type="email" name="email" id="email" class="form-control-modern" value="<?= old('email') ?>" required placeholder="Enter email address">
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['email'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['email']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="role_id">
                    <i class="fas fa-user-tag me-2"></i>
                    Role <span class="text-danger">*</span>
                </label>
                <select name="role_id" id="role_id" class="form-control-modern" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= esc($role['id']) ?>" data-role-name="<?= esc(strtolower($role['name'])) ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                            <?= esc(ucfirst(str_replace('_', ' ', $role['name']))) ?>
                            <?php if (!empty($role['description'])): ?>
                                - <?= esc($role['description']) ?>
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-hint">Select the user's role in the system</div>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['role_id'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['role_id']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="username">
                    <i class="fas fa-user me-2"></i>
                    Username <span class="text-danger">*</span>
                </label>
                <input type="text" name="username" id="username" class="form-control-modern" value="<?= old('username') ?>" required placeholder="Enter username">
                <div class="form-hint">This will be used for login</div>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['username'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['username']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern" id="specialization_group" style="display: none;">
                <label class="form-label-modern" for="specialization">
                    <i class="fas fa-stethoscope me-2"></i>
                    Specialization <span class="text-danger">*</span>
                </label>
                <select name="specialization" id="specialization" class="form-control-modern">
                    <option value="">Select Specialization</option>
                    <?php if (!empty($specializations)): ?>
                        <?php foreach ($specializations as $spec): ?>
                            <option value="<?= esc($spec) ?>" <?= old('specialization') == $spec ? 'selected' : '' ?>>
                                <?= esc($spec) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <div class="form-hint">Required for Doctor role</div>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['specialization'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['specialization']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern" id="employee_id_group" style="display: none;">
                <label class="form-label-modern" for="employee_id">
                    <i class="fas fa-id-badge me-2"></i>
                    Employee ID <span class="text-danger">*</span>
                </label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" name="employee_id" id="employee_id" class="form-control-modern" value="<?= old('employee_id') ?>" required placeholder="EMP-XXXXXX" style="flex: 1;">
                    <button type="button" id="generate_employee_id_btn" class="btn-modern" style="background: #f1f5f9; color: #475569; padding: 12px 20px; white-space: nowrap;">
                        <i class="fas fa-sync-alt"></i> Auto Generate
                    </button>
                </div>
                <div class="form-hint">Employee ID (auto-generated format: EMP-XXXXXX)</div>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['employee_id'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['employee_id']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern" id="prc_license_group" style="display: none;">
                <label class="form-label-modern" for="prc_license">
                    <i class="fas fa-certificate me-2"></i>
                    <span id="prc_license_label">PRC License Number</span> <span class="text-danger">*</span>
                </label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" name="prc_license" id="prc_license" class="form-control-modern" value="<?= old('prc_license') ?>" required placeholder="PRC-XXXXXX" style="flex: 1;">
                    <button type="button" id="generate_prc_btn" class="btn-modern" style="background: #f1f5f9; color: #475569; padding: 12px 20px; white-space: nowrap;">
                        <i class="fas fa-sync-alt"></i> Auto Generate
                    </button>
                </div>
                <div class="form-hint" id="prc_license_hint">Professional Regulation Commission License Number (auto-generated format: PRC-XXXXXX)</div>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['prc_license'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['prc_license']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern" id="nursing_license_group" style="display: none;">
                <label class="form-label-modern" for="nursing_license">
                    <i class="fas fa-certificate me-2"></i>
                    Nursing License Number <span class="text-danger">*</span>
                </label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" name="nursing_license" id="nursing_license" class="form-control-modern" value="<?= old('nursing_license') ?>" required placeholder="NUR-XXXXXX" style="flex: 1;">
                    <button type="button" id="generate_nursing_btn" class="btn-modern" style="background: #f1f5f9; color: #475569; padding: 12px 20px; white-space: nowrap;">
                        <i class="fas fa-sync-alt"></i> Auto Generate
                    </button>
                </div>
                <div class="form-hint">Nursing License Number (auto-generated format: NUR-XXXXXX)</div>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nursing_license'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['nursing_license']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern" id="schedule_availability_group" style="display: none;">
                <label class="form-label-modern">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Schedule / Availability
                </label>
                <div id="schedule_display" style="padding: 16px; background: #f8fafc; border: 2px solid #e5e7eb; border-radius: 10px; min-height: 100px;">
                    <div style="color: #64748b; font-size: 14px; text-align: center; padding: 20px;">
                        <i class="fas fa-info-circle" style="font-size: 24px; margin-bottom: 8px; display: block; opacity: 0.5;"></i>
                        <p style="margin: 0;">Schedule will be displayed here after doctor is created.<br>
                        <small>You can create a schedule in <strong>Admin > Schedule > Create Schedule</strong></small></p>
                    </div>
                </div>
                <div class="form-hint">Doctor's schedule and availability based on Admin > Schedule</div>
            </div>
            
            <!-- Schedule Creation Section (for Doctor and Nurse) -->
            <div id="schedule_creation_group" style="display: none;">
                <h3 class="form-section-title">
                    <i class="fas fa-calendar-plus"></i>
                    Schedule Creation
                </h3>
                
                <div class="alert-modern" style="background: #e8f5e9; color: #1b5e20; border-left: 4px solid #2e7d32; margin-bottom: 24px;">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Schedule Duration:</strong> Schedules will be automatically generated for <strong>1 year</strong> starting from today (<?= date('M d, Y') ?>) until <?= date('M d, Y', strtotime('+1 year')) ?>.
                </div>
                
                <!-- Working Days -->
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <i class="fas fa-calendar-week me-2"></i>
                        Working Days <span class="text-danger">*</span>
                    </label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; margin-top: 8px;">
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <input type="checkbox" name="working_days[]" value="Monday" style="width: 18px; height: 18px; accent-color: #2e7d32;">
                            <span style="font-weight: 600;">Monday</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <input type="checkbox" name="working_days[]" value="Tuesday" style="width: 18px; height: 18px; accent-color: #2e7d32;">
                            <span style="font-weight: 600;">Tuesday</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <input type="checkbox" name="working_days[]" value="Wednesday" style="width: 18px; height: 18px; accent-color: #2e7d32;">
                            <span style="font-weight: 600;">Wednesday</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <input type="checkbox" name="working_days[]" value="Thursday" style="width: 18px; height: 18px; accent-color: #2e7d32;">
                            <span style="font-weight: 600;">Thursday</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <input type="checkbox" name="working_days[]" value="Friday" style="width: 18px; height: 18px; accent-color: #2e7d32;">
                            <span style="font-weight: 600;">Friday</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <input type="checkbox" name="working_days[]" value="Saturday" style="width: 18px; height: 18px; accent-color: #2e7d32;">
                            <span style="font-weight: 600;">Saturday</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            <input type="checkbox" name="working_days[]" value="Sunday" style="width: 18px; height: 18px; accent-color: #2e7d32;">
                            <span style="font-weight: 600;">Sunday</span>
                        </label>
                    </div>
                    <div class="form-hint">Select the days when this user will be working</div>
                </div>
                
                <!-- Doctor Schedule Fields -->
                <div id="doctor_schedule_fields" style="display: none;">
                    <div class="form-row-modern">
                        <div class="form-group-modern">
                            <label class="form-label-modern" for="shift_type">
                                <i class="fas fa-clock me-2"></i>
                                Shift Type <span class="text-danger">*</span>
                            </label>
                            <select name="shift_type" id="shift_type" class="form-control-modern" onchange="setShiftTimes()">
                                <option value="">-- Select Shift --</option>
                                <option value="morning">Morning</option>
                                <option value="afternoon">Afternoon</option>
                                <option value="evening">Evening</option>
                                <option value="whole_day">Whole Day</option>
                            </select>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern" for="time_in">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Time In <span class="text-danger">*</span>
                            </label>
                            <input type="time" name="time_in" id="time_in" class="form-control-modern" required>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern" for="time_out">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Time Out <span class="text-danger">*</span>
                            </label>
                            <input type="time" name="time_out" id="time_out" class="form-control-modern" required>
                        </div>
                    </div>
                    
                    <div class="form-row-modern">
                        <div class="form-group-modern">
                            <label class="form-label-modern" for="on_call">
                                <i class="fas fa-phone-alt me-2"></i>
                                On-Call <span class="text-danger">*</span>
                            </label>
                            <select name="on_call" id="on_call" class="form-control-modern" required>
                                <option value="no" selected>No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>
                        
                        <div class="form-group-modern" style="grid-column: span 2;">
                            <label class="form-label-modern" for="on_call_notes">
                                <i class="fas fa-sticky-note me-2"></i>
                                On-Call Notes (Optional)
                            </label>
                            <textarea name="on_call_notes" id="on_call_notes" class="form-control-modern" rows="2" placeholder="Enter on-call notes..."></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group-modern">
                        <label class="form-label-modern" for="max_patients">
                            <i class="fas fa-users me-2"></i>
                            Max Patients Per Day (Optional)
                        </label>
                        <input type="number" name="max_patients" id="max_patients" class="form-control-modern" min="1" placeholder="Enter maximum number of patients">
                    </div>
                </div>
                
                <!-- Nurse Schedule Fields -->
                <div id="nurse_schedule_fields" style="display: none;">
                    <div class="form-row-modern">
                        <div class="form-group-modern">
                            <label class="form-label-modern" for="nurse_shift_type">
                                <i class="fas fa-clock me-2"></i>
                                Shift Type <span class="text-danger">*</span>
                            </label>
                            <select name="nurse_shift_type" id="nurse_shift_type" class="form-control-modern" onchange="setNurseShiftTimes()">
                                <option value="">-- Select Shift Type --</option>
                                <option value="morning">AM Shift</option>
                                <option value="pm">PM Shift</option>
                                <option value="night">Night Shift</option>
                                <option value="whole_day">Whole Day</option>
                            </select>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern" for="nurse_time_in">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Time In <span class="text-danger">*</span>
                            </label>
                            <input type="time" name="nurse_time_in" id="nurse_time_in" class="form-control-modern" required>
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern" for="nurse_time_out">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Time Out <span class="text-danger">*</span>
                            </label>
                            <input type="time" name="nurse_time_out" id="nurse_time_out" class="form-control-modern" required>
                        </div>
                    </div>
                    
                    <div class="form-row-modern">
                        <div class="form-group-modern">
                            <label class="form-label-modern" for="duty_type">
                                <i class="fas fa-briefcase me-2"></i>
                                Duty Type <span class="text-danger">*</span>
                            </label>
                            <select name="duty_type" id="duty_type" class="form-control-modern" required onchange="toggleStationAssignment()">
                                <option value="regular" selected>Regular Duty</option>
                                <option value="float">Float Nurse</option>
                            </select>
                        </div>
                        
                        <div class="form-group-modern" id="station_assignment_group">
                            <label class="form-label-modern" for="station_assignment">
                                <i class="fas fa-hospital me-2"></i>
                                Station Assignment <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="station_assignment" id="station_assignment" class="form-control-modern" placeholder="e.g., Emergency Room, ICU">
                        </div>
                        
                        <div class="form-group-modern">
                            <label class="form-label-modern" for="standby">
                                <i class="fas fa-hourglass-half me-2"></i>
                                Standby <span class="text-danger">*</span>
                            </label>
                            <select name="standby" id="standby" class="form-control-modern" required>
                                <option value="no" selected>No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group-modern">
                    <label class="form-label-modern" for="schedule_status">
                        <i class="fas fa-toggle-on me-2"></i>
                        Schedule Status <span class="text-danger">*</span>
                    </label>
                    <select name="schedule_status" id="schedule_status" class="form-control-modern" required>
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <div class="form-hint">Set the initial status for the created schedules</div>
                </div>
            </div>
            
            <div class="form-group-modern" id="shift_preference_group" style="display: none;">
                <label class="form-label-modern" for="shift_preference">
                    <i class="fas fa-clock me-2"></i>
                    Shift Preference <span class="text-danger">*</span>
                </label>
                <select name="shift_preference" id="shift_preference" class="form-control-modern">
                    <option value="">Select Shift Preference</option>
                    <option value="morning" <?= old('shift_preference') == 'morning' ? 'selected' : '' ?>>Morning Shift</option>
                    <option value="night" <?= old('shift_preference') == 'night' ? 'selected' : '' ?>>Night Shift</option>
                    <option value="bulk" <?= old('shift_preference') == 'bulk' ? 'selected' : '' ?>>Bulk (Both Shifts)</option>
                </select>
                <div class="form-hint">Required for Nurse role</div>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['shift_preference'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['shift_preference']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="password">
                    <i class="fas fa-lock me-2"></i>
                    Password <span class="text-danger">*</span>
                </label>
                <input type="password" name="password" id="password" class="form-control-modern" required placeholder="Enter password (minimum 6 characters)">
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['password']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="status">
                    <i class="fas fa-toggle-on me-2"></i>
                    Status <span class="text-danger">*</span>
                </label>
                <select name="status" id="status" class="form-control-modern" required>
                    <option value="">Select Status</option>
                    <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['status'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['status']) ?></div>
                <?php endif; ?>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="submit" class="btn-modern btn-modern-primary">
                    <i class="fas fa-save"></i>
                    Create User
                </button>
                <a href="<?= site_url('admin/users') ?>" class="btn-modern btn-modern-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role_id');
    const shiftPreferenceGroup = document.getElementById('shift_preference_group');
    const shiftPreference = document.getElementById('shift_preference');
    const specializationGroup = document.getElementById('specialization_group');
    const specialization = document.getElementById('specialization');
    const employeeIdGroup = document.getElementById('employee_id_group');
    const employeeId = document.getElementById('employee_id');
    const generateEmployeeIdBtn = document.getElementById('generate_employee_id_btn');
    const prcLicenseGroup = document.getElementById('prc_license_group');
    const prcLicense = document.getElementById('prc_license');
    const prcLicenseLabel = document.getElementById('prc_license_label');
    const prcLicenseHint = document.getElementById('prc_license_hint');
    const generatePrcBtn = document.getElementById('generate_prc_btn');
    const nursingLicenseGroup = document.getElementById('nursing_license_group');
    const nursingLicense = document.getElementById('nursing_license');
    const generateNursingBtn = document.getElementById('generate_nursing_btn');
    const scheduleCreationGroup = document.getElementById('schedule_creation_group');
    const doctorScheduleFields = document.getElementById('doctor_schedule_fields');
    const nurseScheduleFields = document.getElementById('nurse_schedule_fields');
    const doctorRoleId = <?= $doctorRoleId ?? 'null' ?>;
    const nurseRoleId = <?= $nurseRoleId ?? 'null' ?>;
    
    // Function to generate Employee ID
    function generateEmployeeID() {
        // Format: EMP-XXXXXX (6 digits)
        const randomNum = Math.floor(100000 + Math.random() * 900000);
        const empId = 'EMP-' + randomNum.toString();
        if (employeeId) {
            employeeId.value = empId;
        }
    }
    
    // Function to generate PRC License Number
    function generatePRCLicense() {
        // Format: PRC-XXXXXX (6 digits)
        const randomNum = Math.floor(100000 + Math.random() * 900000);
        const prcNumber = 'PRC-' + randomNum.toString();
        if (prcLicense) {
            prcLicense.value = prcNumber;
        }
    }
    
    // Function to generate Nursing License Number
    function generateNursingLicense() {
        // Format: NUR-XXXXXX (6 digits)
        const randomNum = Math.floor(100000 + Math.random() * 900000);
        const nursingNumber = 'NUR-' + randomNum.toString();
        if (nursingLicense) {
            nursingLicense.value = nursingNumber;
        }
    }
    
    // Auto-generate Employee ID when applicable roles are selected
    function autoGenerateEmployeeID() {
        if (employeeId && !employeeId.value) {
            generateEmployeeID();
        }
    }
    
    // Auto-generate PRC License when applicable roles are selected
    function autoGeneratePRC() {
        if (prcLicense && !prcLicense.value) {
            generatePRCLicense();
        }
    }
    
    // Auto-generate Nursing License when Nurse role is selected
    function autoGenerateNursing() {
        if (nursingLicense && !nursingLicense.value) {
            generateNursingLicense();
        }
    }
    
    // Generate Employee ID button click handler
    if (generateEmployeeIdBtn) {
        generateEmployeeIdBtn.addEventListener('click', function() {
            generateEmployeeID();
        });
    }
    
    // Generate PRC button click handler
    if (generatePrcBtn) {
        generatePrcBtn.addEventListener('click', function() {
            generatePRCLicense();
        });
    }
    
    // Generate Nursing License button click handler
    if (generateNursingBtn) {
        generateNursingBtn.addEventListener('click', function() {
            generateNursingLicense();
        });
    }
    
    // Function to set shift times for doctor
    function setShiftTimes() {
        const shiftType = document.getElementById('shift_type')?.value;
        const timeIn = document.getElementById('time_in');
        const timeOut = document.getElementById('time_out');
        
        if (!shiftType || !timeIn || !timeOut) return;
        
        switch(shiftType) {
            case 'morning':
                timeIn.value = '08:00';
                timeOut.value = '12:00';
                break;
            case 'afternoon':
                timeIn.value = '13:00';
                timeOut.value = '17:00';
                break;
            case 'evening':
                timeIn.value = '18:00';
                timeOut.value = '22:00';
                break;
            case 'whole_day':
                timeIn.value = '08:00';
                timeOut.value = '17:00';
                break;
            default:
                timeIn.value = '';
                timeOut.value = '';
        }
    }
    
    // Function to set shift times for nurse
    function setNurseShiftTimes() {
        const shiftType = document.getElementById('nurse_shift_type')?.value;
        const timeIn = document.getElementById('nurse_time_in');
        const timeOut = document.getElementById('nurse_time_out');
        
        if (!shiftType || !timeIn || !timeOut) return;
        
        switch(shiftType) {
            case 'morning':
                timeIn.value = '06:00';
                timeOut.value = '14:00';
                break;
            case 'pm':
                timeIn.value = '14:00';
                timeOut.value = '22:00';
                break;
            case 'night':
                timeIn.value = '22:00';
                timeOut.value = '06:00';
                break;
            case 'whole_day':
                timeIn.value = '08:00';
                timeOut.value = '17:00';
                break;
            default:
                timeIn.value = '';
                timeOut.value = '';
        }
    }
    
    // Function to toggle station assignment for nurse
    function toggleStationAssignment() {
        const dutyType = document.getElementById('duty_type')?.value;
        const stationGroup = document.getElementById('station_assignment_group');
        const stationInput = document.getElementById('station_assignment');
        
        if (!stationGroup || !stationInput) return;
        
        if (dutyType === 'float') {
            stationGroup.style.display = 'none';
            stationInput.removeAttribute('required');
            stationInput.value = '';
        } else {
            stationGroup.style.display = 'block';
            stationInput.setAttribute('required', 'required');
        }
    }
    
    // Make toggleStationAssignment available globally
    window.toggleStationAssignment = toggleStationAssignment;
    window.setShiftTimes = setShiftTimes;
    window.setNurseShiftTimes = setNurseShiftTimes;
    
    function toggleRoleFields() {
        const selectedRoleId = roleSelect.value;
        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const roleName = selectedOption ? selectedOption.getAttribute('data-role-name') : '';
        
        // Handle nurse shift preference (old method - keep for backward compatibility)
        if (roleName === 'nurse' || selectedRoleId == nurseRoleId) {
            shiftPreferenceGroup.style.display = 'none'; // Hide old shift preference, use schedule creation instead
            shiftPreference.removeAttribute('required');
            
            // Show nursing license for nurse
            if (nursingLicenseGroup) {
                nursingLicenseGroup.style.display = 'block';
            }
            if (nursingLicense) {
                nursingLicense.setAttribute('required', 'required');
                autoGenerateNursing(); // Auto-generate nursing license
            }
            
            // Show schedule creation for nurse
            scheduleCreationGroup.style.display = 'block';
            nurseScheduleFields.style.display = 'block';
            doctorScheduleFields.style.display = 'none';
            
            // Set required for nurse schedule fields
            const nurseShiftType = document.getElementById('nurse_shift_type');
            const nurseTimeIn = document.getElementById('nurse_time_in');
            const nurseTimeOut = document.getElementById('nurse_time_out');
            const dutyType = document.getElementById('duty_type');
            const standby = document.getElementById('standby');
            
            if (nurseShiftType) nurseShiftType.setAttribute('required', 'required');
            if (nurseTimeIn) nurseTimeIn.setAttribute('required', 'required');
            if (nurseTimeOut) nurseTimeOut.setAttribute('required', 'required');
            if (dutyType) dutyType.setAttribute('required', 'required');
            if (standby) standby.setAttribute('required', 'required');
            
            // Initialize station assignment visibility
            toggleStationAssignment();
        } else {
            shiftPreferenceGroup.style.display = 'none';
            shiftPreference.removeAttribute('required');
            shiftPreference.value = '';
            nurseScheduleFields.style.display = 'none';
            
            // Hide nursing license for non-nurse roles
            if (nursingLicenseGroup) {
                nursingLicenseGroup.style.display = 'none';
            }
            if (nursingLicense) {
                nursingLicense.removeAttribute('required');
                nursingLicense.value = '';
            }
            
            // Remove required from nurse schedule fields
            const nurseShiftType = document.getElementById('nurse_shift_type');
            const nurseTimeIn = document.getElementById('nurse_time_in');
            const nurseTimeOut = document.getElementById('nurse_time_out');
            const dutyType = document.getElementById('duty_type');
            const standby = document.getElementById('standby');
            const stationAssignment = document.getElementById('station_assignment');
            
            if (nurseShiftType) nurseShiftType.removeAttribute('required');
            if (nurseTimeIn) nurseTimeIn.removeAttribute('required');
            if (nurseTimeOut) nurseTimeOut.removeAttribute('required');
            if (dutyType) dutyType.removeAttribute('required');
            if (standby) standby.removeAttribute('required');
            if (stationAssignment) stationAssignment.removeAttribute('required');
        }
        
        // Handle Employee ID for: admin, finance (accountant), itstaff, receptionist
        const employeeIdRoles = ['admin', 'finance', 'itstaff', 'receptionist'];
        if (employeeIdRoles.includes(roleName)) {
            if (employeeIdGroup) {
                employeeIdGroup.style.display = 'block';
            }
            if (employeeId) {
                employeeId.setAttribute('required', 'required');
                autoGenerateEmployeeID(); // Auto-generate Employee ID
            }
        } else {
            if (employeeIdGroup) {
                employeeIdGroup.style.display = 'none';
            }
            if (employeeId) {
                employeeId.removeAttribute('required');
                employeeId.value = '';
            }
        }
        
        // Handle PRC License for: doctor, lab_staff (Medical Technologist), pharmacy (Pharmacist)
        const prcLicenseRoles = ['doctor', 'lab_staff', 'pharmacy'];
        if (prcLicenseRoles.includes(roleName) || selectedRoleId == doctorRoleId) {
            if (prcLicenseGroup) {
                prcLicenseGroup.style.display = 'block';
            }
            if (prcLicense) {
                prcLicense.setAttribute('required', 'required');
                autoGeneratePRC(); // Auto-generate PRC license
            }
            
            // Update label and hint based on role
            if (prcLicenseLabel && prcLicenseHint) {
                if (roleName === 'lab_staff') {
                    prcLicenseLabel.textContent = 'PRC License Number (Medical Technologist)';
                    prcLicenseHint.textContent = 'PRC License Number for Medical Technologist (auto-generated format: PRC-XXXXXX)';
                } else if (roleName === 'pharmacy') {
                    prcLicenseLabel.textContent = 'PRC License Number (Pharmacist)';
                    prcLicenseHint.textContent = 'PRC License Number for Pharmacist (auto-generated format: PRC-XXXXXX)';
                } else {
                    prcLicenseLabel.textContent = 'PRC License Number';
                    prcLicenseHint.textContent = 'Professional Regulation Commission License Number (auto-generated format: PRC-XXXXXX)';
                }
            }
        } else {
            if (prcLicenseGroup) {
                prcLicenseGroup.style.display = 'none';
            }
            if (prcLicense) {
                prcLicense.removeAttribute('required');
                prcLicense.value = '';
            }
        }
        
        // Handle doctor fields (specialization, schedule)
        if (roleName === 'doctor' || selectedRoleId == doctorRoleId) {
            specializationGroup.style.display = 'block';
            specialization.setAttribute('required', 'required');
            
            // Show schedule creation for doctor
            scheduleCreationGroup.style.display = 'block';
            doctorScheduleFields.style.display = 'block';
            nurseScheduleFields.style.display = 'none';
            
            // Set required for doctor schedule fields
            const shiftType = document.getElementById('shift_type');
            const timeIn = document.getElementById('time_in');
            const timeOut = document.getElementById('time_out');
            const onCall = document.getElementById('on_call');
            
            if (shiftType) shiftType.setAttribute('required', 'required');
            if (timeIn) timeIn.setAttribute('required', 'required');
            if (timeOut) timeOut.setAttribute('required', 'required');
            if (onCall) onCall.setAttribute('required', 'required');
        } else {
            specializationGroup.style.display = 'none';
            specialization.removeAttribute('required');
            specialization.value = '';
            
            doctorScheduleFields.style.display = 'none';
            
            // Remove required from doctor schedule fields
            const shiftType = document.getElementById('shift_type');
            const timeIn = document.getElementById('time_in');
            const timeOut = document.getElementById('time_out');
            const onCall = document.getElementById('on_call');
            
            if (shiftType) shiftType.removeAttribute('required');
            if (timeIn) timeIn.removeAttribute('required');
            if (timeOut) timeOut.removeAttribute('required');
            if (onCall) onCall.removeAttribute('required');
        }
        
        // Hide schedule creation if neither doctor nor nurse
        if (roleName !== 'doctor' && roleName !== 'nurse' && selectedRoleId != doctorRoleId && selectedRoleId != nurseRoleId) {
            scheduleCreationGroup.style.display = 'none';
        }
    }
    
    // Check on page load if role is already selected
    toggleRoleFields();
    
    // Listen for changes
    roleSelect.addEventListener('change', toggleRoleFields);
});
</script>
<?= $this->endSection() ?>

