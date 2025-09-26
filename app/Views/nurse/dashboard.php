<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>🧑‍⚕️ Nurse Dashboard</h1>

<div class="spacer"></div>

<!-- Summary -->
<div class="grid grid-4">
    <div class="card"><h5>Assigned Patients</h5><h3><?= esc($assignedPatientsCount ?? 0) ?></h3></div>
    <div class="card"><h5>Due Medications (2h)</h5><h3><?= esc($dueMedicationsCount ?? 0) ?></h3></div>
    <div class="card"><h5>Open Tasks</h5><h3><?= esc($openTasksCount ?? 0) ?></h3></div>
    <div class="card"><h5>Shift</h5><h3><?= esc($shiftWindow ?? '-') ?></h3></div>
    
</div>

<div class="spacer"></div>

<!-- Patient Overview -->
<div class="card">
    <h5>👥 Patient Overview</h5>
    <div style="overflow:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Name</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Age</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Room/Bed</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Status</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Vitals (Last)</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Jane Smith</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">42</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">101 / A</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#16a34a;">Stable</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">BP 120/80 • HR 78 • T 36.7</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn btn-secondary" href="#">View</a></td>
                </tr>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">John Doe</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">57</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">102 / B</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#dc2626;">Critical</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">BP 150/100 • HR 98 • T 38.2</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn btn-secondary" href="#">View</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="spacer"></div>

<!-- Medication Schedule -->
<div class="card">
    <h5>💊 Medication Schedule</h5>
    <div style="overflow:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Time</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Patient</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Medicine</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Dosage</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Status</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">09:00</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Jane Smith</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Amoxicillin</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">500 mg</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#f59e0b;">Due</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn" href="#">Mark Given</a></td>
                </tr>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">09:30</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">John Doe</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Paracetamol</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">500 mg</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#dc2626;">Overdue</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn btn-secondary" href="#">Give Now</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="spacer"></div>

<!-- Task Management -->
<div class="card">
    <h5>📝 Task Management</h5>
    <div class="grid grid-2">
        <div>
            <strong>Today</strong>
            <ul style="margin:8px 0 0 16px; list-style: none; padding:0;">
                <li><input type="checkbox"> Vital checks (Rooms 101-103)</li>
                <li><input type="checkbox"> IV monitoring (Room 102)</li>
                <li><input type="checkbox"> Patient transfer (Room 103 → 205)</li>
            </ul>
        </div>
        <div>
            <strong>Completed</strong>
            <ul style="margin:8px 0 0 16px; list-style: none; padding:0;">
                <li><input type="checkbox" checked> Medication round 07:00</li>
                <li><input type="checkbox" checked> Ward briefing 06:45</li>
            </ul>
        </div>
    </div>
</div>

<div class="spacer"></div>

<!-- Shift & Duty Roster -->
<div class="card">
    <h5>🕒 Shift & Duty Roster</h5>
    <div class="grid grid-2">
        <div>
            <strong>Current Shift:</strong>
            <div>07:00 - 15:00</div>
            <div>Ward: ICU</div>
        </div>
        <div>
            <strong>Team on Duty:</strong>
            <ul style="margin:8px 0 0 16px;">
                <li>Nurse A (Lead)</li>
                <li>Nurse B</li>
                <li>Nurse C</li>
            </ul>
        </div>
    </div>
    <div class="actions-row">
        <a class="btn btn-secondary" href="#">View Roster</a>
        <a class="btn" href="#">Request Swap</a>
    </div>
</div>

<div class="spacer"></div>

<!-- Notifications / Alerts -->
<div class="card">
    <h5>🔔 Notifications & Alerts</h5>
    <div class="actions-row"><span>Doctor instruction: Recheck BP for John Doe at 10:00</span></div>
    <div class="actions-row"><span>Critical lab update: CBC result ready for Jane Smith</span></div>
    <div class="actions-row"><span>System: Inventory low for saline IV</span></div>
</div>

<div class="spacer"></div>

<!-- Communication Panel -->
<div class="card">
    <h5>💬 Communication Panel</h5>
    <div class="actions-row">
        <input type="text" placeholder="Type a quick note..." style="flex:1; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
        <a class="btn" href="#">Send</a>
    </div>
    <div class="actions-row"><span><strong>Dr. Smith:</strong> Please prioritize Room 102.</span></div>
    <div class="actions-row"><span><strong>Nurse A:</strong> Noted. Vitals at 09:30.</span></div>
</div>

<div class="spacer"></div>

<!-- Reports & Documentation -->
<div class="card">
    <h5>📄 Reports & Documentation</h5>
    <div class="actions-row">
        <a class="btn" href="#">Patient Progress Notes</a>
        <a class="btn btn-secondary" href="#">Nursing Reports</a>
        <a class="btn btn-secondary" href="#">Discharge Preparation</a>
    </div>
</div>

<?= $this->endSection() ?>
