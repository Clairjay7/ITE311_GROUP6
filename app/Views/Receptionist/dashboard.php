<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>🧑‍💼 Receptionist Dashboard</h1>

<!-- Search & Quick Access Bar -->
<div class="card">
    <div class="actions-row">
        <input type="text" placeholder="Search patients, appointments, or doctors..." style="flex:1; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
        <a class="btn" href="#">Search</a>
        <a class="btn btn-secondary" href="#">New Appointment</a>
        <a class="btn btn-secondary" href="#">Add Patient</a>
    </div>
    
</div>

<div class="spacer"></div>

<!-- Patient Registration -->
<div class="card">
    <h5>📝 Patient Registration</h5>
    <div class="grid grid-2">
        <div>
            <div class="actions-row">
                <input type="text" placeholder="Full Name" style="flex:1; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
                <input type="number" placeholder="Age" style="width:120px; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
                <select style="width:140px; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
                    <option>Gender</option>
                    <option>Male</option>
                    <option>Female</option>
                    <option>Other</option>
                </select>
            </div>
            <div class="actions-row">
                <input type="text" placeholder="Contact" style="flex:1; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
                <input type="text" placeholder="Address" style="flex:2; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
                <input type="text" placeholder="Government ID" style="flex:1; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
            </div>
        </div>
        <div class="actions-row" style="align-items:flex-start; justify-content:flex-end;">
            <a class="btn" href="#">Register</a>
        </div>
    </div>
</div>

<div class="spacer"></div>

<!-- Appointment Management -->
<div class="grid grid-2">
    <div class="card">
        <h5>📅 Calendar</h5>
        <div style="height:220px; border:1px dashed #e2e8f0; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#64748b;">Calendar View</div>
    </div>
    <div class="card">
        <h5>📋 Appointments</h5>
        <div style="overflow:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Date</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Time</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Doctor</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Patient</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Status</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding:8px; border-bottom:1px solid #f1f5f9;">2025-09-26</td>
                        <td style="padding:8px; border-bottom:1px solid #f1f5f9;">09:00</td>
                        <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Dr. Smith</td>
                        <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Jane Smith</td>
                        <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#10b981;">Confirmed</td>
                        <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn btn-secondary" href="#">View</a></td>
                    </tr>
                    <tr>
                        <td style="padding:8px; border-bottom:1px solid #f1f5f9;">2025-09-26</td>
                        <td style="padding:8px; border-bottom:1px solid #f1f5f9;">10:00</td>
                        <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Dr. Lee</td>
                        <td style="padding:8px; border-bottom:1px solid #f1f5f9;">John Doe</td>
                        <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#f59e0b;">Pending</td>
                        <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn" href="#">Confirm</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="spacer"></div>

<!-- Check-in & Check-out Panel -->
<div class="card">
    <h5>🚪 Check-in & Check-out</h5>
    <div class="grid grid-2">
        <div>
            <strong>Arrivals</strong>
            <ul style="margin:8px 0 0 16px;">
                <li>Jane Smith — Checked-in <a class="btn btn-secondary" href="#">Check-out</a></li>
                <li>John Doe — Waiting <a class="btn" href="#">Check-in</a></li>
            </ul>
        </div>
        <div>
            <strong>Departures</strong>
            <ul style="margin:8px 0 0 16px;">
                <li>Maria Garcia — Checked-out</li>
            </ul>
        </div>
    </div>
</div>

<div class="spacer"></div>

<!-- Billing & Payment Status -->
<div class="card">
    <h5>💵 Billing & Payment Status</h5>
    <div style="overflow:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Patient</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Amount</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Status</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">John Doe</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">₱3,500.00</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#ef4444;">Pending</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn" href="#">Collect</a></td>
                </tr>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Jane Smith</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">₱1,200.00</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#10b981;">Paid</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn btn-secondary" href="#">Invoice</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="spacer"></div>

<!-- Visitor Management -->
<div class="card">
    <h5>👥 Visitor Management</h5>
    <div style="overflow:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Patient</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Visitor</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Relation</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Date</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Jane Smith</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Michael Smith</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Husband</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">2025-09-26</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">14:30</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="spacer"></div>

<!-- Doctor Availability -->
<div class="card">
    <h5>🩺 Doctor Availability</h5>
    <div class="grid grid-3">
        <div><strong>Dr. Smith</strong><br><span style="color:#10b981;">Available</span></div>
        <div><strong>Dr. Lee</strong><br><span style="color:#f59e0b;">In Consultation</span></div>
        <div><strong>Dr. Davis</strong><br><span style="color:#ef4444;">Off Duty</span></div>
    </div>
</div>

<div class="spacer"></div>

<!-- Notifications & Alerts -->
<div class="card">
    <h5>🔔 Notifications & Alerts</h5>
    <div class="actions-row"><span>Upcoming: John Doe at 10:00 with Dr. Lee</span></div>
    <div class="actions-row"><span>Pending: Registration for Maria Garcia</span></div>
    <div class="actions-row"><span>System: Update available at 22:00</span></div>
</div>

<?= $this->endSection() ?>