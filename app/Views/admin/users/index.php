<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>User Management<?= $this->endSection() ?>

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
        background: white;
        color: #2e7d32;
        box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
    }
    
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 255, 255, 0.4);
        color: #2e7d32;
        background: #f0fdf4;
    }
    
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
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
    
    .btn-sm-modern {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        cursor: pointer;
    }
    
    .btn-info {
        background: #0288d1;
        color: white;
    }
    
    .btn-info:hover {
        background: #0277bd;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-warning {
        background: #f59e0b;
        color: white;
    }
    
    .btn-warning:hover {
        background: #d97706;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-danger {
        background: #ef4444;
        color: white;
    }
    
    .btn-danger:hover {
        background: #dc2626;
        color: white;
        transform: translateY(-2px);
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
    
    /* Role Filter Buttons */
    .role-filter-btn {
        padding: 10px 20px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        background: white;
        color: #475569;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .role-filter-btn:hover {
        border-color: #2e7d32;
        color: #2e7d32;
        background: #f0fdf4;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.2);
    }
    
    .role-filter-btn.active {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        color: white;
        border-color: #2e7d32;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .role-filter-btn.active:hover {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        border-color: #2563eb;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }
    
    /* Hide rows by default, show via JavaScript */
    .user-row {
        display: table-row;
    }
    
    .user-row.hidden {
        display: none;
    }
</style>

<div class="admin-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-users-cog"></i>
            User Management
        </h1>
        <a href="<?= site_url('admin/users/create') ?>" class="btn-modern btn-modern-primary" style="background: white; color: #2e7d32; box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3); padding: 12px 24px; font-size: 15px; font-weight: 700;">
            <i class="fas fa-user-plus" style="font-size: 16px;"></i>
            Add New User
        </a>
    </div>
    
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

    <!-- Search Bar -->
    <div class="modern-card" style="margin-bottom: 24px; padding: 20px;">
        <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 16px; z-index: 1;"></i>
                <input type="text" id="userSearchInput" placeholder="Search by ID, Name, Username, Email, Role, License/ID, Contact, Status, or Specialization..." style="width: 100%; padding: 12px 16px 12px 45px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px; transition: all 0.3s ease; background: white; color: #1e293b; outline: none;" onfocus="this.style.borderColor='#2e7d32'; this.style.boxShadow='0 0 0 3px rgba(46, 125, 50, 0.1)';" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
            </div>
            <button type="button" id="clearSearchBtn" class="btn-modern" style="background: #f1f5f9; color: #475569; padding: 12px 20px; display: none; border: 2px solid #e5e7eb; transition: all 0.3s ease;" onmouseover="this.style.background='#e2e8f0';" onmouseout="this.style.background='#f1f5f9';">
                <i class="fas fa-times"></i> Clear
            </button>
        </div>
        <div id="searchResultsCount" style="margin-top: 12px; font-size: 13px; color: #64748b; display: none;">
            <i class="fas fa-info-circle"></i> <span id="resultsText"></span>
        </div>
    </div>

    <!-- Role Filter Buttons -->
    <div class="modern-card" style="margin-bottom: 24px; padding: 20px;">
        <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
            <span style="font-weight: 600; color: #1e293b; margin-right: 8px;">Filter by Role:</span>
            <button type="button" class="role-filter-btn active" data-role="all">
                <i class="fas fa-users"></i> All Users
            </button>
            <button type="button" class="role-filter-btn" data-role="doctor">
                <i class="fas fa-user-md"></i> Doctors
            </button>
            <button type="button" class="role-filter-btn" data-role="nurse">
                <i class="fas fa-user-nurse"></i> Nurses
            </button>
            <button type="button" class="role-filter-btn" data-role="receptionist">
                <i class="fas fa-user-tie"></i> Receptionists
            </button>
            <button type="button" class="role-filter-btn" data-role="admin">
                <i class="fas fa-user-shield"></i> Admin
            </button>
            <button type="button" class="role-filter-btn" data-role="itstaff">
                <i class="fas fa-user-cog"></i> IT Staff
            </button>
            <button type="button" class="role-filter-btn" data-role="finance">
                <i class="fas fa-dollar-sign"></i> Finance
            </button>
            <button type="button" class="role-filter-btn" data-role="lab-staff">
                <i class="fas fa-flask"></i> Lab Staff
            </button>
            <button type="button" class="role-filter-btn" data-role="pharmacy">
                <i class="fas fa-pills"></i> Pharmacy
            </button>
        </div>
    </div>

    <div class="modern-card">
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>License/ID</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): 
                            $isDeleted = !empty($user['deleted_at']);
                        ?>
                            <tr class="user-row <?= $isDeleted ? 'deleted-user' : '' ?>" data-role="<?= esc(strtolower(str_replace('_', '-', $user['role_name'] ?? 'all'))) ?>" style="<?= $isDeleted ? 'opacity: 0.7; background: #fef2f2;' : '' ?>">
                                <td><strong>#<?= esc($user['id']) ?></strong></td>
                                <td>
                                    <?php 
                                    $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['middle_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                                    if (empty($fullName)) {
                                        $fullName = 'N/A';
                                    }
                                    ?>
                                    <strong><?= esc($fullName) ?></strong>
                                    <?php if (!empty($user['doctor_specialization'])): ?>
                                        <br>
                                        <small style="color: #64748b; font-size: 11px; margin-top: 2px; display: block;">
                                            <i class="fas fa-stethoscope"></i> <?= esc($user['doctor_specialization']) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= esc($user['username']) ?></strong></td>
                                <td><?= esc($user['email']) ?></td>
                                <td>
                                    <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                                        <?= esc(ucfirst(str_replace('_', ' ', $user['role_name'] ?? 'N/A'))) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $licenseId = '';
                                    $roleName = strtolower($user['role_name'] ?? '');
                                    if (in_array($roleName, ['doctor', 'lab_staff', 'pharmacy']) && !empty($user['prc_license'])) {
                                        $licenseId = $user['prc_license'];
                                        $label = $roleName === 'lab_staff' ? 'PRC (Med Tech)' : ($roleName === 'pharmacy' ? 'PRC (Pharmacist)' : 'PRC');
                                    } elseif ($roleName === 'nurse' && !empty($user['nursing_license'])) {
                                        $licenseId = $user['nursing_license'];
                                        $label = 'Nursing';
                                    } elseif (in_array($roleName, ['admin', 'finance', 'itstaff', 'receptionist']) && !empty($user['employee_id'])) {
                                        $licenseId = $user['employee_id'];
                                        $label = 'Employee ID';
                                    }
                                    ?>
                                    <?php if (!empty($licenseId)): ?>
                                        <span style="font-size: 12px; font-weight: 600; color: #475569;">
                                            <i class="fas fa-id-badge"></i> <?= esc($licenseId) ?>
                                        </span>
                                        <br>
                                        <small style="color: #94a3b8; font-size: 10px;"><?= esc($label ?? '') ?></small>
                                    <?php else: ?>
                                        <span style="color: #94a3b8; font-size: 12px;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($user['contact'])): ?>
                                        <i class="fas fa-phone"></i> <?= esc($user['contact']) ?>
                                    <?php else: ?>
                                        <span style="color: #94a3b8; font-size: 12px;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($isDeleted): ?>
                                        <span class="badge-modern" style="background: #fee2e2; color: #991b1b;">
                                            <i class="fas fa-trash"></i> Deleted
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-modern" style="background: <?= $user['status'] == 'active' ? '#d1fae5' : '#fee2e2'; ?>; color: <?= $user['status'] == 'active' ? '#065f46' : '#991b1b'; ?>;">
                                            <?= esc(ucfirst($user['status'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($user['created_at'] ? date('M d, Y', strtotime($user['created_at'])) : 'N/A') ?></td>
                                <td>
                                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        <?php if ($user['id'] != session()->get('user_id')): ?>
                                            <?php if (!$isDeleted): ?>
                                                <a href="<?= site_url('admin/users/edit/' . $user['id']) ?>" class="btn-sm-modern btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                    Edit
                                                </a>
                                                <a href="<?= site_url('admin/users/delete/' . $user['id']) ?>" class="btn-sm-modern btn-danger" onclick="return confirm('Are you sure you want to mark this user as deleted? The user will be hidden from the system but data will be preserved.')">
                                                    <i class="fas fa-user-slash"></i>
                                                    Mark as Deleted
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= site_url('admin/users/restore/' . $user['id']) ?>" class="btn-sm-modern" style="background: #10b981; color: white;" onclick="return confirm('Are you sure you want to restore this user? The user will be reactivated and can log in again.')">
                                                    <i class="fas fa-undo"></i>
                                                    Restore
                                                </a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge-modern" style="background: #f1f5f9; color: #64748b;">
                                                <i class="fas fa-info-circle"></i>
                                                Current User
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fas fa-users" style="font-size: 48px; margin-bottom: 16px; opacity: 0.4;"></i>
                                <p style="margin: 0;">No users found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.role-filter-btn');
    const userRows = document.querySelectorAll('.user-row');
    const searchInput = document.getElementById('userSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    let currentRoleFilter = 'all';
    
    // Function to get searchable text from a row
    function getRowSearchText(row) {
        const cells = row.querySelectorAll('td');
        if (cells.length < 8) return '';
        
        // Get all searchable text from each cell
        const id = cells[0]?.textContent?.trim() || '';
        const name = cells[1]?.textContent?.trim() || '';
        const username = cells[2]?.textContent?.trim() || '';
        const email = cells[3]?.textContent?.trim() || '';
        const role = cells[4]?.textContent?.trim() || '';
        const licenseId = cells[5]?.textContent?.trim() || '';
        const contact = cells[6]?.textContent?.trim() || '';
        const status = cells[7]?.textContent?.trim() || '';
        const created = cells[8]?.textContent?.trim() || '';
        
        // Also get any hidden text like specialization
        const specialization = row.querySelector('small')?.textContent?.trim() || '';
        
        return `${id} ${name} ${username} ${email} ${role} ${licenseId} ${contact} ${status} ${created} ${specialization}`.toLowerCase();
    }
    
    // Function to filter rows based on search and role
    function filterRows() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const hasSearch = searchTerm.length > 0;
        
        // Show/hide clear button
        if (hasSearch) {
            clearSearchBtn.style.display = 'inline-flex';
        } else {
            clearSearchBtn.style.display = 'none';
        }
        
        let visibleCount = 0;
        
        userRows.forEach(row => {
            const rowRole = row.getAttribute('data-role');
            const rowText = getRowSearchText(row);
            
            // Check role filter
            let roleMatch = false;
            if (currentRoleFilter === 'all') {
                roleMatch = true;
            } else {
                const normalizedRowRole = rowRole.replace(/_/g, '-');
                const normalizedSelectedRole = currentRoleFilter.replace(/_/g, '-');
                roleMatch = normalizedRowRole === normalizedSelectedRole;
            }
            
            // Check search filter
            let searchMatch = true;
            if (hasSearch) {
                searchMatch = rowText.includes(searchTerm);
            }
            
            // Show/hide row
            if (roleMatch && searchMatch) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });
        
        // Show/hide empty message
        const emptyRow = document.querySelector('tbody tr:not(.user-row)');
        if (emptyRow) {
            if (visibleCount === 0) {
                emptyRow.style.display = 'table-row';
            } else {
                emptyRow.style.display = 'none';
            }
        }
        
        // Update search results count
        const resultsCountEl = document.getElementById('searchResultsCount');
        const resultsTextEl = document.getElementById('resultsText');
        if (resultsCountEl && resultsTextEl) {
            const totalCount = userRows.length;
            if (hasSearch || currentRoleFilter !== 'all') {
                resultsCountEl.style.display = 'block';
                if (hasSearch && currentRoleFilter !== 'all') {
                    resultsTextEl.textContent = `Showing ${visibleCount} of ${totalCount} users matching "${searchTerm}" in ${currentRoleFilter} role`;
                } else if (hasSearch) {
                    resultsTextEl.textContent = `Showing ${visibleCount} of ${totalCount} users matching "${searchTerm}"`;
                } else {
                    resultsTextEl.textContent = `Showing ${visibleCount} of ${totalCount} users in ${currentRoleFilter} role`;
                }
            } else {
                resultsCountEl.style.display = 'none';
            }
        }
    }
    
    // Role filter functionality
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const selectedRole = this.getAttribute('data-role');
            currentRoleFilter = selectedRole;
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter rows
            filterRows();
        });
    });
    
    // Debounce function for better performance
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Search functionality with debounce
    if (searchInput) {
        // Use debounce for better performance (300ms delay)
        const debouncedFilter = debounce(filterRows, 300);
        
        searchInput.addEventListener('input', function() {
            debouncedFilter();
        });
        
        // Also allow Enter key to search immediately
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterRows();
            }
        });
        
        // Clear search
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                filterRows();
                searchInput.focus();
            });
        }
    }
    
    // Initial filter to show all users
    filterRows();
});
</script>
<?= $this->endSection() ?>

