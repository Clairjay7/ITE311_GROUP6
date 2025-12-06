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
        <a href="<?= site_url('admin/users/create') ?>" class="btn-modern btn-modern-primary">
            <i class="fas fa-plus"></i>
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
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
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
                            <tr class="user-row" data-role="<?= esc(strtolower(str_replace('_', '-', $user['role_name'] ?? 'all'))) ?>">
                                <td><strong>#<?= esc($user['id']) ?></strong></td>
                                <td><strong><?= esc($user['username']) ?></strong></td>
                                <td><?= esc($user['email']) ?></td>
                                <td>
                                    <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                                        <?= esc(ucfirst(str_replace('_', ' ', $user['role_name'] ?? 'N/A'))) ?>
                                    </span>
                                    <?php if (!empty($user['doctor_specialization'])): ?>
                                        <br>
                                        <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">
                                            <i class="fas fa-stethoscope"></i> <?= esc($user['doctor_specialization']) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge-modern" style="background: <?= $user['status'] == 'active' ? '#d1fae5' : '#fee2e2'; ?>; color: <?= $user['status'] == 'active' ? '#065f46' : '#991b1b'; ?>;">
                                        <?= esc(ucfirst($user['status'])) ?>
                                    </span>
                                </td>
                                <td><?= esc($user['created_at'] ? date('M d, Y h:i A', strtotime($user['created_at'])) : 'N/A') ?></td>
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
                            <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
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
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const selectedRole = this.getAttribute('data-role');
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter rows
            userRows.forEach(row => {
                const rowRole = row.getAttribute('data-role');
                
                if (selectedRole === 'all') {
                    row.classList.remove('hidden');
                } else {
                    // Normalize both roles for comparison (handle both underscore and hyphen)
                    const normalizedRowRole = rowRole.replace(/_/g, '-');
                    const normalizedSelectedRole = selectedRole.replace(/_/g, '-');
                    
                    if (normalizedRowRole === normalizedSelectedRole) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                }
            });
            
            // Show/hide empty message
            const visibleRows = Array.from(userRows).filter(row => !row.classList.contains('hidden'));
            const emptyRow = document.querySelector('tbody tr:not(.user-row)');
            if (emptyRow) {
                if (visibleRows.length === 0) {
                    emptyRow.style.display = 'table-row';
                } else {
                    emptyRow.style.display = 'none';
                }
            }
        });
    });
});
</script>
<?= $this->endSection() ?>

