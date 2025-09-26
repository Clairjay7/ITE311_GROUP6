<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>🕒 Duty Roster & Schedule</h2>
        <div class="actions">
            <button class="btn btn-primary" onclick="requestShiftSwap()">Request Shift Swap</button>
            <a href="<?= base_url('nurse/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="roster-overview">
        <div class="grid grid-4">
            <div class="stat-card">
                <h5>Current Shift</h5>
                <h3>Day</h3>
                <small>07:00 - 15:00</small>
            </div>
            <div class="stat-card">
                <h5>Hours This Week</h5>
                <h3>32</h3>
                <small>8 hours remaining</small>
            </div>
            <div class="stat-card">
                <h5>Overtime</h5>
                <h3 style="color: #f59e0b;">4</h3>
                <small>This month</small>
            </div>
            <div class="stat-card">
                <h5>Days Off</h5>
                <h3 style="color: #16a34a;">2</h3>
                <small>This week</small>
            </div>
        </div>
    </div>

    <div class="current-shift">
        <h4>Current Shift Details</h4>
        <div class="shift-card active">
            <div class="shift-header">
                <div class="shift-info">
                    <h5>Day Shift - ICU Ward</h5>
                    <p>Monday, September 26, 2025 • 07:00 - 15:00</p>
                </div>
                <div class="shift-status">
                    <span class="status-badge active">Active</span>
                </div>
            </div>
            <div class="shift-details">
                <div class="grid grid-3">
                    <div>
                        <strong>Assigned Patients:</strong>
                        <ul>
                            <li>John Doe (102-B)</li>
                            <li>Jane Smith (101-A)</li>
                            <li>Mary Johnson (103-A)</li>
                            <li>Robert Wilson (104-A)</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Team Members:</strong>
                        <ul>
                            <li>Nurse Sarah (Lead)</li>
                            <li>Nurse Mike</li>
                            <li>Nurse Lisa</li>
                            <li>Dr. Smith (Attending)</li>
                        </ul>
                    </div>
                    <div>
                        <strong>Special Instructions:</strong>
                        <ul>
                            <li>Monitor John Doe's BP closely</li>
                            <li>Wound care for Mary Johnson</li>
                            <li>Discharge prep for Jane Smith</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="weekly-schedule">
        <h4>Weekly Schedule</h4>
        <div class="schedule-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Date</th>
                        <th>Shift</th>
                        <th>Time</th>
                        <th>Ward</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="today">
                        <td><strong>Monday</strong></td>
                        <td>Sep 26</td>
                        <td>Day</td>
                        <td>07:00 - 15:00</td>
                        <td>ICU</td>
                        <td><span class="status-badge active">Active</span></td>
                        <td>
                            <button class="btn btn-sm btn-info">View Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Tuesday</td>
                        <td>Sep 27</td>
                        <td>Day</td>
                        <td>07:00 - 15:00</td>
                        <td>ICU</td>
                        <td><span class="status-badge scheduled">Scheduled</span></td>
                        <td>
                            <button class="btn btn-sm btn-warning">Request Swap</button>
                            <button class="btn btn-sm btn-info">View Details</button>
                        </td>
                    </tr>
                    <tr>
                        <td>Wednesday</td>
                        <td>Sep 28</td>
                        <td>Night</td>
                        <td>23:00 - 07:00</td>
                        <td>General</td>
                        <td><span class="status-badge scheduled">Scheduled</span></td>
                        <td>
                            <button class="btn btn-sm btn-warning">Request Swap</button>
                            <button class="btn btn-sm btn-info">View Details</button>
                        </td>
                    </tr>
                    <tr class="off-day">
                        <td>Thursday</td>
                        <td>Sep 29</td>
                        <td>-</td>
                        <td>Off Day</td>
                        <td>-</td>
                        <td><span class="status-badge off">Off</span></td>
                        <td>
                            <button class="btn btn-sm btn-secondary">Pick Up Shift</button>
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

.roster-overview {
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
    margin: 0 0 0.25rem 0;
    font-size: 2rem;
}

.stat-card small {
    color: #9ca3af;
    font-size: 0.8rem;
}

.current-shift, .weekly-schedule {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.shift-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
}

.shift-card.active {
    border-color: #3b82f6;
    background: #f0f9ff;
}

.shift-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.shift-info h5 {
    margin: 0 0 0.25rem 0;
    color: #374151;
}

.shift-info p {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.shift-details ul {
    margin: 0.5rem 0 0 0;
    padding-left: 1.5rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.shift-details li {
    margin-bottom: 0.25rem;
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

.table tr.today {
    background-color: #f0f9ff;
    border-left: 4px solid #3b82f6;
}

.table tr.off-day {
    background-color: #f9fafb;
    opacity: 0.7;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.active { background: #dcfce7; color: #16a34a; }
.status-badge.scheduled { background: #dbeafe; color: #2563eb; }
.status-badge.off { background: #f3f4f6; color: #6b7280; }

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
.btn-warning { background: #f59e0b; color: white; }
</style>

<script>
function requestShiftSwap() {
    alert('Shift swap request feature would open a modal to select shifts and colleagues.');
}
</script>
<?= $this->endSection() ?>
