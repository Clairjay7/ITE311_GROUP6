<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>📝 Task Management</h2>
        <div class="actions">
            <button class="btn btn-primary" onclick="showAddTaskModal()">Add New Task</button>
            <a href="<?= base_url('nurse/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="tasks-overview">
        <div class="grid grid-4">
            <div class="stat-card">
                <h5>Pending Tasks</h5>
                <h3 style="color: #f59e0b;">8</h3>
            </div>
            <div class="stat-card">
                <h5>In Progress</h5>
                <h3 style="color: #3b82f6;">3</h3>
            </div>
            <div class="stat-card">
                <h5>Completed Today</h5>
                <h3 style="color: #16a34a;">12</h3>
            </div>
            <div class="stat-card">
                <h5>Overdue</h5>
                <h3 style="color: #dc2626;">2</h3>
            </div>
        </div>
    </div>

    <div class="tasks-container">
        <div class="grid grid-3">
            <!-- Pending Tasks -->
            <div class="task-column">
                <h4 class="column-header pending">📋 Pending (8)</h4>
                <div class="task-list">
                    <div class="task-card priority-high">
                        <div class="task-header">
                            <span class="task-title">Vital Signs Check</span>
                            <span class="priority-badge high">High</span>
                        </div>
                        <div class="task-details">
                            <p><strong>Patient:</strong> John Doe (102-B)</p>
                            <p><strong>Due:</strong> 09:00 AM</p>
                            <p><strong>Notes:</strong> Monitor BP closely</p>
                        </div>
                        <div class="task-actions">
                            <button class="btn btn-sm btn-primary" onclick="startTask(1)">Start</button>
                            <button class="btn btn-sm btn-info" onclick="viewTask(1)">View</button>
                        </div>
                    </div>

                    <div class="task-card priority-medium">
                        <div class="task-header">
                            <span class="task-title">Medication Administration</span>
                            <span class="priority-badge medium">Medium</span>
                        </div>
                        <div class="task-details">
                            <p><strong>Patient:</strong> Jane Smith (101-A)</p>
                            <p><strong>Due:</strong> 09:30 AM</p>
                            <p><strong>Notes:</strong> Amoxicillin 500mg</p>
                        </div>
                        <div class="task-actions">
                            <button class="btn btn-sm btn-primary" onclick="startTask(2)">Start</button>
                            <button class="btn btn-sm btn-info" onclick="viewTask(2)">View</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- In Progress Tasks -->
            <div class="task-column">
                <h4 class="column-header in-progress">⏳ In Progress (3)</h4>
                <div class="task-list">
                    <div class="task-card priority-high in-progress">
                        <div class="task-header">
                            <span class="task-title">IV Monitoring</span>
                            <span class="priority-badge high">High</span>
                        </div>
                        <div class="task-details">
                            <p><strong>Patient:</strong> Robert Wilson (104-A)</p>
                            <p><strong>Started:</strong> 08:30 AM</p>
                            <p><strong>Notes:</strong> Check IV site every 30 min</p>
                        </div>
                        <div class="task-actions">
                            <button class="btn btn-sm btn-success" onclick="completeTask(4)">Complete</button>
                            <button class="btn btn-sm btn-warning" onclick="pauseTask(4)">Pause</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Tasks -->
            <div class="task-column">
                <h4 class="column-header completed">✅ Completed (12)</h4>
                <div class="task-list">
                    <div class="task-card completed">
                        <div class="task-header">
                            <span class="task-title">Morning Medication Round</span>
                            <span class="status-badge completed">Done</span>
                        </div>
                        <div class="task-details">
                            <p><strong>Completed:</strong> 07:30 AM</p>
                            <p><strong>Duration:</strong> 45 minutes</p>
                        </div>
                        <div class="task-actions">
                            <button class="btn btn-sm btn-info" onclick="viewTask(6)">View</button>
                        </div>
                    </div>
                </div>
            </div>
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

.tasks-overview {
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

.tasks-container {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.task-column {
    background: #f9fafb;
    border-radius: 8px;
    padding: 1rem;
    min-height: 500px;
}

.column-header {
    margin: 0 0 1rem 0;
    padding: 0.5rem;
    border-radius: 4px;
    text-align: center;
    font-size: 1rem;
}

.column-header.pending { background: #fefce8; color: #ca8a04; }
.column-header.in-progress { background: #eff6ff; color: #2563eb; }
.column-header.completed { background: #f0fdf4; color: #16a34a; }

.task-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.task-card {
    background: white;
    border-radius: 6px;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border-left: 4px solid #e5e7eb;
}

.task-card.priority-high { border-left-color: #dc2626; }
.task-card.priority-medium { border-left-color: #f59e0b; }
.task-card.priority-low { border-left-color: #16a34a; }
.task-card.completed { opacity: 0.8; }

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.task-title {
    font-weight: 600;
    color: #374151;
}

.priority-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.priority-badge.high { background: #fef2f2; color: #dc2626; }
.priority-badge.medium { background: #fefce8; color: #ca8a04; }
.priority-badge.low { background: #f0fdf4; color: #16a34a; }

.status-badge.completed { background: #f0fdf4; color: #16a34a; }

.task-details {
    margin-bottom: 1rem;
}

.task-details p {
    margin: 0.25rem 0;
    font-size: 0.875rem;
    color: #6b7280;
}

.task-actions {
    display: flex;
    gap: 0.5rem;
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
.btn-success { background: #16a34a; color: white; }
</style>

<script>
function startTask(taskId) {
    alert('Task started! Moving to In Progress.');
}

function completeTask(taskId) {
    alert('Task completed! Moving to Completed.');
}

function pauseTask(taskId) {
    alert('Task paused! Moving back to Pending.');
}

function viewTask(taskId) {
    alert('Viewing task details for task ID: ' + taskId);
}

function showAddTaskModal() {
    alert('Add Task modal would open here.');
}
</script>
<?= $this->endSection() ?>
