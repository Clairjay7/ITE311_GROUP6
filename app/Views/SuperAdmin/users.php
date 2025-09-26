<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>👥 User Management</h2>
        <div class="actions">
            <a href="<?= base_url('super-admin/users/add') ?>" class="btn btn-primary">Add New User</a>
            <a href="<?= base_url('super-admin/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="users-overview">
        <div class="grid grid-4">
            <div class="stat-card">
                <h5>Total Users</h5>
                <h3><?= count($users ?? []) ?></h3>
            </div>
            <div class="stat-card">
                <h5>Active Users</h5>
                <h3><?= count(array_filter($users ?? [], fn($u) => $u['status'] === 'active')) ?></h3>
            </div>
            <div class="stat-card">
                <h5>Doctors</h5>
                <h3><?= count(array_filter($users ?? [], fn($u) => $u['role'] === 'doctor')) ?></h3>
            </div>
            <div class="stat-card">
                <h5>Staff</h5>
                <h3><?= count(array_filter($users ?? [], fn($u) => in_array($u['role'], ['nurse', 'receptionist', 'laboratory_staff', 'pharmacist', 'accountant', 'it_staff']))) ?></h3>
            </div>
        </div>
    </div>

    <div class="users-table">
        <h4>All Users</h4>
        <?php if (!empty($users)): ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= esc($user['id']) ?></td>
                                <td>
                                    <div class="user-info">
                                        <strong><?= esc($user['first_name'] ?? '') ?> <?= esc($user['last_name'] ?? '') ?></strong>
                                    </div>
                                </td>
                                <td><?= esc($user['username']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td>
                                    <span class="role-badge role-<?= esc($user['role']) ?>">
                                        <?= ucwords(str_replace('_', ' ', esc($user['role']))) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= esc($user['status'] ?? 'inactive') ?>">
                                        <?= ucfirst(esc($user['status'] ?? 'inactive')) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($user['last_login'])): ?>
                                        <?= date('M j, Y g:i A', strtotime($user['last_login'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Never</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= base_url('super-admin/users/view/' . $user['id']) ?>" class="btn btn-sm btn-info" title="View">👁️</a>
                                        <a href="<?= base_url('super-admin/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-warning" title="Edit">✏️</a>
                                        <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $user['id'] ?>)" title="Delete">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <p>No users found.</p>
                <a href="<?= base_url('super-admin/users/add') ?>" class="btn btn-primary">Add First User</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.actions {
    display: flex;
    gap: 1rem;
}

.users-overview {
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-card h5 {
    margin: 0 0 0.5rem 0;
    color: #6b7280;
    font-size: 0.9rem;
}

.stat-card h3 {
    margin: 0;
    color: #1f2937;
    font-size: 2rem;
}

.users-table {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table-responsive {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.table th,
.table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.table th {
    background-color: #f9fafb;
    font-weight: 600;
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

.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
    font-size: 0.875rem;
    display: inline-block;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.btn-primary { background: #3b82f6; color: white; }
.btn-secondary { background: #6b7280; color: white; }
.btn-info { background: #06b6d4; color: white; }
.btn-warning { background: #f59e0b; color: white; }
.btn-danger { background: #ef4444; color: white; }

.no-data {
    text-align: center;
    padding: 3rem;
    color: #6b7280;
}

.text-muted {
    color: #6b7280;
}
</style>

<script>
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        fetch(`<?= base_url('super-admin/users/') ?>${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User deleted successfully');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete user'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the user');
        });
    }
}
</script>
<?= $this->endSection() ?>


