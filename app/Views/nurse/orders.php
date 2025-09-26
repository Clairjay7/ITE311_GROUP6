<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>📋 Doctor Orders</h2>
        <div class="actions">
            <a href="<?= base_url('nurse/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="orders-overview">
        <div class="grid grid-4">
            <div class="stat-card">
                <h5>New Orders</h5>
                <h3 style="color: #3b82f6;">5</h3>
            </div>
            <div class="stat-card">
                <h5>In Progress</h5>
                <h3 style="color: #f59e0b;">8</h3>
            </div>
            <div class="stat-card">
                <h5>Completed Today</h5>
                <h3 style="color: #16a34a;">15</h3>
            </div>
            <div class="stat-card">
                <h5>Urgent</h5>
                <h3 style="color: #dc2626;">2</h3>
            </div>
        </div>
    </div>

    <div class="orders-table">
        <h4>Doctor Orders</h4>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Doctor</th>
                        <th>Patient</th>
                        <th>Order Type</th>
                        <th>Details</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="urgent-order">
                        <td><strong>08:45</strong></td>
                        <td>Dr. Smith</td>
                        <td>John Doe (102-B)</td>
                        <td><span class="order-type medication">Medication</span></td>
                        <td>Increase Lisinopril to 20mg daily</td>
                        <td><span class="priority-badge urgent">Urgent</span></td>
                        <td><span class="status-badge new">New</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="acknowledgeOrder(1)">Acknowledge</button>
                            <button class="btn btn-sm btn-info" onclick="viewOrder(1)">View</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>08:30</strong></td>
                        <td>Dr. Johnson</td>
                        <td>Jane Smith (101-A)</td>
                        <td><span class="order-type lab">Lab Test</span></td>
                        <td>CBC with differential, morning draw</td>
                        <td><span class="priority-badge routine">Routine</span></td>
                        <td><span class="status-badge new">New</span></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="acknowledgeOrder(2)">Acknowledge</button>
                            <button class="btn btn-sm btn-info" onclick="viewOrder(2)">View</button>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>08:15</strong></td>
                        <td>Dr. Wilson</td>
                        <td>Mary Johnson (103-A)</td>
                        <td><span class="order-type procedure">Procedure</span></td>
                        <td>Wound dressing change BID</td>
                        <td><span class="priority-badge routine">Routine</span></td>
                        <td><span class="status-badge in-progress">In Progress</span></td>
                        <td>
                            <button class="btn btn-sm btn-success" onclick="completeOrder(3)">Complete</button>
                            <button class="btn btn-sm btn-info" onclick="viewOrder(3)">View</button>
                        </td>
                    </tr>
                </tbody>
            </table>
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

.orders-overview {
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

.orders-table {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
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

.table tr.urgent-order {
    background-color: #fef2f2;
    border-left: 4px solid #dc2626;
}

.order-type {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.order-type.medication { background: #fef3c7; color: #92400e; }
.order-type.lab { background: #dbeafe; color: #1e40af; }
.order-type.procedure { background: #d1fae5; color: #065f46; }

.priority-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.priority-badge.urgent { background: #fef2f2; color: #dc2626; }
.priority-badge.routine { background: #f0fdf4; color: #16a34a; }

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.new { background: #dbeafe; color: #1e40af; }
.status-badge.in-progress { background: #fef3c7; color: #92400e; }
.status-badge.completed { background: #d1fae5; color: #065f46; }

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
    font-size: 0.875rem;
    display: inline-block;
    margin-right: 0.25rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.btn-primary { background: #3b82f6; color: white; }
.btn-secondary { background: #6b7280; color: white; }
.btn-info { background: #06b6d4; color: white; }
.btn-success { background: #16a34a; color: white; }
</style>

<script>
function acknowledgeOrder(orderId) {
    if (confirm('Acknowledge this order?')) {
        alert('Order acknowledged successfully!');
    }
}

function completeOrder(orderId) {
    if (confirm('Mark this order as completed?')) {
        alert('Order completed successfully!');
    }
}

function viewOrder(orderId) {
    alert('Viewing order details for order ID: ' + orderId);
}
</script>
<?= $this->endSection() ?>
