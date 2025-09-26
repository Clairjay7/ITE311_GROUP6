<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>🧪 Laboratory Staff Dashboard</h1>

<div class="spacer"></div>

<!-- Summary -->
<div class="grid grid-4">
    <div class="card"><h5>Pending Requests</h5><h3>6</h3></div>
    <div class="card"><h5>Samples In Lab</h5><h3>12</h3></div>
    <div class="card"><h5>Ongoing Tests</h5><h3>5</h3></div>
    <div class="card"><h5>Low-Stock Items</h5><h3>2</h3></div>
    
</div>

<div class="spacer"></div>

<!-- Test Requests Panel -->
<div class="card">
    <h5>⏳ Test Requests</h5>
    <div style="overflow:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Patient</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Test</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Priority</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Requested By</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">John Doe</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">CBC</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#ef4444;">Urgent</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Dr. Smith</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn" href="#">Process</a></td>
                </tr>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Maria Garcia</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Urinalysis</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#f59e0b;">Normal</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">Reception</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn btn-secondary" href="#">Queue</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="spacer"></div>

<!-- Sample Collection & Tracking -->
<div class="card">
    <h5>🔍 Sample Collection & Tracking</h5>
    <div style="overflow:auto;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Sample ID</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Patient</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Test</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Status</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">LAB-00123</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">John Doe</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">CBC</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#f59e0b;">Processing</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;"><a class="btn btn-secondary" href="#">Update</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="spacer"></div>

<!-- Ongoing Tests -->
<div class="card">
    <h5>🧪 Ongoing Tests</h5>
    <ul style="margin:8px 0 0 16px;">
        <li>John Doe — CBC • Analyzer 2 • ETA 15m</li>
        <li>Jane Smith — Chemistry Panel • Analyzer 1 • ETA 30m</li>
    </ul>
</div>

<div class="spacer"></div>

<!-- Test Results Management -->
<div class="card">
    <h5>📄 Test Results Management</h5>
    <div class="actions-row">
        <a class="btn" href="#">Upload Result</a>
        <a class="btn btn-secondary" href="#">Validate</a>
        <a class="btn btn-secondary" href="#">Release to Doctor</a>
    </div>
</div>

<div class="spacer"></div>

<!-- Reports & History -->
<div class="card">
    <h5>📚 Reports & History</h5>
    <div class="actions-row">
        <input type="text" placeholder="Filter by patient/test/date..." style="flex:1; padding:10px; border:1px solid #e2e8f0; border-radius:8px;">
        <a class="btn" href="#">Search</a>
    </div>
    <div style="overflow:auto; margin-top:8px;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Date</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Patient</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Test</th>
                    <th style="text-align:left; padding:8px; border-bottom:1px solid #e2e8f0;">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">2025-09-25</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">John Doe</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9;">CBC</td>
                    <td style="padding:8px; border-bottom:1px solid #f1f5f9; color:#10b981;">Released</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="spacer"></div>

<!-- Notifications & Alerts -->
<div class="card">
    <h5>🔔 Notifications & Alerts</h5>
    <div class="actions-row"><span>Abnormal Result: John Doe CBC</span></div>
    <div class="actions-row"><span>Delayed Test: Urinalysis queue over 30m</span></div>
    <div class="actions-row"><span>System: Analyzer 1 maintenance at 18:00</span></div>
</div>

<div class="spacer"></div>

<!-- Inventory & Supplies Monitoring -->
<div class="card">
    <h5>📦 Inventory & Supplies</h5>
    <div class="grid grid-3">
        <div>CBC Reagent — <span style="color:#ef4444;">Low</span></div>
        <div>Urine Strips — <span style="color:#10b981;">OK</span></div>
        <div>Glucose Kits — <span style="color:#f59e0b;">Warning</span></div>
    </div>
    <div class="actions-row">
        <a class="btn" href="#">Create Restock Request</a>
    </div>
</div>

<div class="spacer"></div>

<!-- Shift & Task Overview -->
<div class="card">
    <h5>🕒 Shift & Task Overview</h5>
    <div class="grid grid-2">
        <div>
            <strong>Current Shift:</strong>
            <div>07:00 - 15:00</div>
            <div>Section: Chemistry</div>
        </div>
        <div>
            <strong>Assigned Tasks:</strong>
            <ul style="margin:8px 0 0 16px; list-style:none; padding:0;">
                <li><input type="checkbox"> Verify CBC batch #1052</li>
                <li><input type="checkbox"> Calibrate Analyzer 2</li>
                <li><input type="checkbox"> Review Urinalysis outliers</li>
            </ul>
        </div>
    </div>
    <div class="actions-row">
        <a class="btn btn-secondary" href="#">Open Task Board</a>
    </div>
</div>

<?= $this->endSection() ?>