<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>👁️ View User Details</h2>
        <div class="actions">
            <a href="<?= base_url('super-admin/users/edit/' . ($user['id'] ?? '')) ?>" class="btn btn-warning">Edit User</a>
            <a href="<?= base_url('super-admin/users') ?>" class="btn btn-secondary">Back to Users</a>
        </div>
    </div>

    <?php if (!empty($user)): ?>
        <div class="user-details">
            <div class="user-header">
                <div class="user-avatar">
                    <div class="avatar-placeholder">
                        <?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1) . substr($user['last_name'] ?? 'U', 0, 1)) ?>
                    </div>
                </div>
                <div class="user-info">
                    <h3><?= esc($user['first_name'] ?? '') ?> <?= esc($user['last_name'] ?? '') ?></h3>
                    <p class="user-role">
                        <span class="role-badge role-<?= esc($user['role']) ?>">
                            <?= ucwords(str_replace('_', ' ', esc($user['role']))) ?>
                        </span>
                    </p>
                    <p class="user-status">
                        <span class="status-badge status-<?= esc($user['status'] ?? 'inactive') ?>">
                            <?= ucfirst(esc($user['status'] ?? 'inactive')) ?>
                        </span>
                    </p>
                </div>
            </div>

            <div class="details-grid">
                <div class="detail-card">
                    <h4>Personal Information</h4>
                    <div class="detail-row">
                        <span class="label">First Name:</span>
                        <span class="value"><?= esc($user['first_name'] ?? 'N/A') ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Last Name:</span>
                        <span class="value"><?= esc($user['last_name'] ?? 'N/A') ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Username:</span>
                        <span class="value"><?= esc($user['username']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Email:</span>
                        <span class="value"><?= esc($user['email']) ?></span>
                    </div>
                </div>

                <div class="detail-card">
                    <h4>System Information</h4>
                    <div class="detail-row">
                        <span class="label">User ID:</span>
                        <span class="value"><?= esc($user['id']) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Role:</span>
                        <span class="value">
                            <span class="role-badge role-<?= esc($user['role']) ?>">
                                <?= ucwords(str_replace('_', ' ', esc($user['role']))) ?>
                            </span>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Status:</span>
                        <span class="value">
                            <span class="status-badge status-<?= esc($user['status'] ?? 'inactive') ?>">
                                <?= ucfirst(esc($user['status'] ?? 'inactive')) ?>
                            </span>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Created:</span>
                        <span class="value">
                            <?php if (!empty($user['created_at'])): ?>
                                <?= date('M j, Y g:i A', strtotime($user['created_at'])) ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Last Login:</span>
                        <span class="value">
                            <?php if (!empty($user['last_login'])): ?>
                                <?= date('M j, Y g:i A', strtotime($user['last_login'])) ?>
                            <?php else: ?>
                                Never
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="no-data">
            <p>User not found.</p>
            <a href="<?= base_url('super-admin/users') ?>" class="btn btn-primary">Back to Users</a>
        </div>
    <?php endif; ?>
</div>

<style>
.user-details {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.user-header {
    display: flex;
    align-items: center;
    padding: 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.user-avatar {
    margin-right: 2rem;
}

.avatar-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    color: white;
}

.user-info h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
}

.user-info p {
    margin: 0.25rem 0;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 0;
}

.detail-card {
    padding: 2rem;
    border-right: 1px solid #e5e7eb;
}

.detail-card:last-child {
    border-right: none;
}

.detail-card h4 {
    margin: 0 0 1.5rem 0;
    color: #374151;
    font-size: 1.1rem;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0.5rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f3f4f6;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-row .label {
    font-weight: 500;
    color: #6b7280;
}

.detail-row .value {
    color: #374151;
}

.role-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.role-super_admin { background: #fef2f2; color: #dc2626; }
.role-doctor { background: #eff6ff; color: #2563eb; }
.role-nurse { background: #f0fdf4; color: #16a34a; }
.role-receptionist { background: #fefce8; color: #ca8a04; }
.role-laboratory_staff { background: #f3e8ff; color: #9333ea; }
.role-pharmacist { background: #ecfdf5; color: #059669; }
.role-accountant { background: #fff7ed; color: #ea580c; }
.role-it_staff { background: #f0f9ff; color: #0284c7; }

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-active { background: #f0fdf4; color: #16a34a; }
.status-inactive { background: #fef2f2; color: #dc2626; }

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
    font-size: 1rem;
    display: inline-block;
}

.btn-primary { background: #3b82f6; color: white; }
.btn-secondary { background: #6b7280; color: white; }
.btn-warning { background: #f59e0b; color: white; }

.no-data {
    text-align: center;
    padding: 3rem;
    color: #6b7280;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .user-header {
        flex-direction: column;
        text-align: center;
    }
    
    .user-avatar {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .detail-card {
        border-right: none;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .detail-card:last-child {
        border-bottom: none;
    }
}
</style>
<?= $this->endSection() ?>
