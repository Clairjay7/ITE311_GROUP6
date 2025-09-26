<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>🔔 Notifications & Alerts</h2>
        <div class="actions">
            <button class="btn btn-primary" onclick="markAllAsRead()">Mark All as Read</button>
            <a href="<?= base_url('nurse/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="notifications-overview">
        <div class="grid grid-4">
            <div class="stat-card">
                <h5>Unread</h5>
                <h3 style="color: #dc2626;">7</h3>
            </div>
            <div class="stat-card">
                <h5>Critical Alerts</h5>
                <h3 style="color: #dc2626;">3</h3>
            </div>
            <div class="stat-card">
                <h5>System Alerts</h5>
                <h3 style="color: #f59e0b;">2</h3>
            </div>
            <div class="stat-card">
                <h5>Total Today</h5>
                <h3>15</h3>
            </div>
        </div>
    </div>

    <div class="notifications-list">
        <h4>Recent Notifications</h4>
        
        <!-- Critical Alert -->
        <div class="notification-item critical unread" data-id="1">
            <div class="notification-icon critical">
                <i class="icon">⚠️</i>
            </div>
            <div class="notification-content">
                <div class="notification-header">
                    <h5>Critical Vital Signs Alert</h5>
                    <span class="notification-time">2 minutes ago</span>
                </div>
                <p class="notification-message">
                    <strong>John Doe (102-B)</strong> - Blood pressure critically high: 180/110 mmHg
                </p>
                <div class="notification-actions">
                    <button class="btn btn-sm btn-danger">View Patient</button>
                    <button class="btn btn-sm btn-secondary" onclick="markAsRead(1)">Mark as Read</button>
                </div>
            </div>
            <div class="unread-indicator"></div>
        </div>

        <!-- Medication Alert -->
        <div class="notification-item medication unread" data-id="2">
            <div class="notification-icon medication">
                <i class="icon">💊</i>
            </div>
            <div class="notification-content">
                <div class="notification-header">
                    <h5>Medication Due</h5>
                    <span class="notification-time">5 minutes ago</span>
                </div>
                <p class="notification-message">
                    <strong>Jane Smith (101-A)</strong> - Amoxicillin 500mg due at 09:00 AM
                </p>
                <div class="notification-actions">
                    <button class="btn btn-sm btn-primary">Give Medication</button>
                    <button class="btn btn-sm btn-secondary" onclick="markAsRead(2)">Mark as Read</button>
                </div>
            </div>
            <div class="unread-indicator"></div>
        </div>

        <!-- Doctor Order -->
        <div class="notification-item order unread" data-id="3">
            <div class="notification-icon order">
                <i class="icon">📋</i>
            </div>
            <div class="notification-content">
                <div class="notification-header">
                    <h5>New Doctor Order</h5>
                    <span class="notification-time">10 minutes ago</span>
                </div>
                <p class="notification-message">
                    <strong>Dr. Smith</strong> ordered: Increase Lisinopril to 20mg daily for John Doe (102-B)
                </p>
                <div class="notification-actions">
                    <button class="btn btn-sm btn-primary">View Order</button>
                    <button class="btn btn-sm btn-secondary" onclick="markAsRead(3)">Mark as Read</button>
                </div>
            </div>
            <div class="unread-indicator"></div>
        </div>
    </div>
</div>

<style>
.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.notifications-overview {
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
    font-size: 2rem;
}

.notifications-list {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.notifications-list h4 {
    margin: 0;
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.notification-item {
    display: flex;
    padding: 1.5rem;
    border-bottom: 1px solid #f3f4f6;
    position: relative;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background: #f9fafb;
}

.notification-item.unread {
    background: #fefce8;
    border-left: 4px solid #f59e0b;
}

.notification-item.critical.unread {
    background: #fef2f2;
    border-left: 4px solid #dc2626;
}

.notification-item.read {
    opacity: 0.7;
}

.notification-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

.notification-icon.critical { background: #fef2f2; }
.notification-icon.medication { background: #fef3c7; }
.notification-icon.order { background: #dbeafe; }

.notification-icon .icon {
    font-size: 1.5rem;
}

.notification-content {
    flex: 1;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.notification-header h5 {
    margin: 0;
    color: #374151;
    font-size: 1rem;
}

.notification-time {
    color: #6b7280;
    font-size: 0.875rem;
}

.notification-message {
    margin: 0 0 1rem 0;
    color: #6b7280;
    line-height: 1.5;
}

.notification-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.unread-indicator {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #3b82f6;
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
.btn-danger { background: #ef4444; color: white; }
</style>

<script>
function markAsRead(notificationId) {
    const notification = document.querySelector(`[data-id="${notificationId}"]`);
    if (notification) {
        notification.classList.remove('unread');
        notification.classList.add('read');
        
        const indicator = notification.querySelector('.unread-indicator');
        if (indicator) {
            indicator.remove();
        }
    }
}

function markAllAsRead() {
    if (confirm('Mark all notifications as read?')) {
        document.querySelectorAll('.notification-item.unread').forEach(notification => {
            notification.classList.remove('unread');
            notification.classList.add('read');
            
            const indicator = notification.querySelector('.unread-indicator');
            if (indicator) {
                indicator.remove();
            }
        });
        
        alert('All notifications marked as read!');
    }
}
</script>
<?= $this->endSection() ?>
