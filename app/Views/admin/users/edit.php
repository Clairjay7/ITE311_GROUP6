<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Edit User<?= $this->endSection() ?>

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
    
    .password-note {
        background: #f8fafc;
        border-left: 4px solid #0288d1;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 13px;
        color: #475569;
        margin-top: 8px;
    }
</style>

<div class="admin-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-user-edit"></i>
            Edit User
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
        <form action="<?= site_url('admin/users/update/' . $user['id']) ?>" method="post">
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
                    <input type="text" name="first_name" id="first_name" class="form-control-modern" value="<?= old('first_name', $user['first_name'] ?? '') ?>" placeholder="Enter first name">
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['first_name'])): ?>
                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['first_name']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group-modern">
                    <label class="form-label-modern" for="middle_name">
                        <i class="fas fa-id-card me-2"></i>
                        Middle Name
                    </label>
                    <input type="text" name="middle_name" id="middle_name" class="form-control-modern" value="<?= old('middle_name', $user['middle_name'] ?? '') ?>" placeholder="Enter middle name (optional)">
                    <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['middle_name'])): ?>
                        <div class="text-danger"><?= esc(session()->getFlashdata('errors')['middle_name']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group-modern">
                    <label class="form-label-modern" for="last_name">
                        <i class="fas fa-id-card me-2"></i>
                        Last Name / Surname <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" class="form-control-modern" value="<?= old('last_name', $user['last_name'] ?? '') ?>" placeholder="Enter last name">
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
                    <input type="text" name="contact" id="contact" class="form-control-modern" value="<?= old('contact', $user['contact'] ?? '') ?>" placeholder="09XX-XXX-XXXX">
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
                    <input type="text" name="address" id="address" class="form-control-modern" value="<?= old('address', $user['address'] ?? '') ?>" placeholder="Enter complete address (optional)">
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
                <input type="email" name="email" id="email" class="form-control-modern" value="<?= old('email', $user['email']) ?>" required placeholder="Enter email address">
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
                        <option value="<?= esc($role['id']) ?>" data-role-name="<?= esc(strtolower($role['name'])) ?>" <?= (old('role_id', $user['role_id']) == $role['id']) ? 'selected' : '' ?>>
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
            
            <div class="form-group-modern" id="specialization_group" style="display: none;">
                <label class="form-label-modern" for="specialization">
                    <i class="fas fa-stethoscope me-2"></i>
                    Specialization <span class="text-danger">*</span>
                </label>
                <select name="specialization" id="specialization" class="form-control-modern">
                    <option value="">Select Specialization</option>
                    <?php 
                    $db = \Config\Database::connect();
                    $specializations = [];
                    if ($db->tableExists('doctors')) {
                        $specializations = $db->table('doctors')
                            ->select('specialization')
                            ->distinct()
                            ->where('specialization IS NOT NULL')
                            ->where('specialization !=', '')
                            ->orderBy('specialization', 'ASC')
                            ->get()
                            ->getResultArray();
                        $specializations = array_column($specializations, 'specialization');
                    }
                    if (empty($specializations)) {
                        $specializations = [
                            'Internal Medicine',
                            'Pediatrics',
                            'Family Medicine',
                            'Obstetrics and Gynecology',
                            'General Surgery'
                        ];
                    }
                    ?>
                    <?php foreach ($specializations as $spec): ?>
                        <option value="<?= esc($spec) ?>" <?= (old('specialization', $user['specialization'] ?? '') == $spec) ? 'selected' : '' ?>>
                            <?= esc($spec) ?>
                        </option>
                    <?php endforeach; ?>
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
                    <input type="text" name="employee_id" id="employee_id" class="form-control-modern" value="<?= old('employee_id', $user['employee_id'] ?? '') ?>" placeholder="EMP-XXXXXX" style="flex: 1;">
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
                    <input type="text" name="prc_license" id="prc_license" class="form-control-modern" value="<?= old('prc_license', $user['prc_license'] ?? '') ?>" placeholder="PRC-XXXXXX" style="flex: 1;">
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
                    <input type="text" name="nursing_license" id="nursing_license" class="form-control-modern" value="<?= old('nursing_license', $user['nursing_license'] ?? '') ?>" placeholder="NUR-XXXXXX" style="flex: 1;">
                    <button type="button" id="generate_nursing_btn" class="btn-modern" style="background: #f1f5f9; color: #475569; padding: 12px 20px; white-space: nowrap;">
                        <i class="fas fa-sync-alt"></i> Auto Generate
                    </button>
                </div>
                <div class="form-hint">Nursing License Number (auto-generated format: NUR-XXXXXX)</div>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nursing_license'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['nursing_license']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="username">
                    <i class="fas fa-user me-2"></i>
                    Username <span class="text-danger">*</span>
                </label>
                <input type="text" name="username" id="username" class="form-control-modern" value="<?= old('username', $user['username']) ?>" required placeholder="Enter username">
                <div class="form-hint">This will be used for login</div>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['username'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['username']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="password">
                    <i class="fas fa-lock me-2"></i>
                    Password (Leave blank to keep current password)
                </label>
                <input type="password" name="password" id="password" class="form-control-modern" placeholder="Enter new password (minimum 6 characters)">
                <div class="password-note">
                    <i class="fas fa-info-circle me-2"></i>
                    Leave this field blank if you don't want to change the password.
                </div>
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
                    <option value="active" <?= (old('status', $user['status']) == 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= (old('status', $user['status']) == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['status'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['status']) ?></div>
                <?php endif; ?>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="submit" class="btn-modern btn-modern-primary">
                    <i class="fas fa-save"></i>
                    Update User
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
    const specializationGroup = document.getElementById('specialization_group');
    const specialization = document.getElementById('specialization');
    const doctorRoleId = <?= $doctorRoleId ?? 'null' ?>;
    const nurseRoleId = <?= $nurseRoleId ?? 'null' ?>;
    
    // Function to generate Employee ID
    function generateEmployeeID() {
        const randomNum = Math.floor(100000 + Math.random() * 900000);
        const empId = 'EMP-' + randomNum.toString();
        if (employeeId) {
            employeeId.value = empId;
        }
    }
    
    // Function to generate PRC License Number
    function generatePRCLicense() {
        const randomNum = Math.floor(100000 + Math.random() * 900000);
        const prcNumber = 'PRC-' + randomNum.toString();
        if (prcLicense) {
            prcLicense.value = prcNumber;
        }
    }
    
    // Function to generate Nursing License Number
    function generateNursingLicense() {
        const randomNum = Math.floor(100000 + Math.random() * 900000);
        const nursingNumber = 'NUR-' + randomNum.toString();
        if (nursingLicense) {
            nursingLicense.value = nursingNumber;
        }
    }
    
    // Generate button click handlers
    if (generateEmployeeIdBtn) {
        generateEmployeeIdBtn.addEventListener('click', function() {
            generateEmployeeID();
        });
    }
    
    if (generatePrcBtn) {
        generatePrcBtn.addEventListener('click', function() {
            generatePRCLicense();
        });
    }
    
    if (generateNursingBtn) {
        generateNursingBtn.addEventListener('click', function() {
            generateNursingLicense();
        });
    }
    
    function toggleRoleFields() {
        const selectedRoleId = roleSelect.value;
        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const roleName = selectedOption ? selectedOption.getAttribute('data-role-name') : '';
        
        // Handle Employee ID for: admin, finance, itstaff, receptionist
        const employeeIdRoles = ['admin', 'finance', 'itstaff', 'receptionist'];
        if (employeeIdRoles.includes(roleName)) {
            if (employeeIdGroup) {
                employeeIdGroup.style.display = 'block';
            }
            if (employeeId) {
                employeeId.setAttribute('required', 'required');
            }
        } else {
            if (employeeIdGroup) {
                employeeIdGroup.style.display = 'none';
            }
            if (employeeId) {
                employeeId.removeAttribute('required');
            }
        }
        
        // Handle PRC License for: doctor, lab_staff, pharmacy
        const prcLicenseRoles = ['doctor', 'lab_staff', 'pharmacy'];
        if (prcLicenseRoles.includes(roleName) || selectedRoleId == doctorRoleId) {
            if (prcLicenseGroup) {
                prcLicenseGroup.style.display = 'block';
            }
            if (prcLicense) {
                prcLicense.setAttribute('required', 'required');
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
            }
        }
        
        // Handle Nursing License for: nurse
        if (roleName === 'nurse' || selectedRoleId == nurseRoleId) {
            if (nursingLicenseGroup) {
                nursingLicenseGroup.style.display = 'block';
            }
            if (nursingLicense) {
                nursingLicense.setAttribute('required', 'required');
            }
        } else {
            if (nursingLicenseGroup) {
                nursingLicenseGroup.style.display = 'none';
            }
            if (nursingLicense) {
                nursingLicense.removeAttribute('required');
            }
        }
        
        // Handle doctor fields (specialization)
        if (roleName === 'doctor' || selectedRoleId == doctorRoleId) {
            if (specializationGroup) {
                specializationGroup.style.display = 'block';
            }
            if (specialization) {
                specialization.setAttribute('required', 'required');
            }
        } else {
            if (specializationGroup) {
                specializationGroup.style.display = 'none';
            }
            if (specialization) {
                specialization.removeAttribute('required');
            }
        }
    }
    
    // Check on page load if role is already selected
    toggleRoleFields();
    
    // Listen for changes
    roleSelect.addEventListener('change', toggleRoleFields);
});
</script>
<?= $this->endSection() ?>

