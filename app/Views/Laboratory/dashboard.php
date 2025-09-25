<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laboratory Staff Dashboard</title>
    <link rel="stylesheet" href="/GROUP6/public/css/laboratory_dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>🧪 Laboratory Staff Dashboard</h2>
            <a href="<?= base_url('logout') ?>" class="logout-link">Logout</a>
        </div>

        <!-- Doctor Notifications -->
        <div class="widget-card" style="background:#dbeafe;">
            <div class="widget-title" style="color:#2563eb;">
                🔔 Notification
            </div>
            <div>
                <strong>Notification:</strong> Dr. Smith notified for John Doe's CBC result.
            </div>
        </div>

        <div class="widgets-row">
            <!-- Pending Test Requests -->
            <div class="widget-card">
                <div class="widget-title">⏳ Pending Test Requests</div>
                <ul>
                    <li>
                        John Doe - CBC
                        <button class="btn btn-outline-primary btn-sm">Process</button>
                    </li>
                    <li>
                        Maria Garcia - Urinalysis
                        <button class="btn btn-outline-primary btn-sm">Process</button>
                    </li>
                </ul>
            </div>
            <!-- Sample Tracking System -->
            <div class="widget-card">
                <div class="widget-title">🔍 Sample Tracking</div>
                <ul>
                    <li>
                        John Doe - CBC: <span class="badge bg-warning text-dark">In Lab</span>
                    </li>
                    <li>
                        Maria Garcia - Urinalysis: <span class="badge bg-success">Received</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="widgets-row">
            <!-- Enter Test Results -->
            <div class="widget-card">
                <div class="widget-title">✏️ Enter Test Results</div>
                <form>
                    <select style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:10px;">
                        <option selected>Select Patient</option>
                        <option>John Doe</option>
                        <option>Maria Garcia</option>
                    </select>
                    <input type="text" style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:10px;" placeholder="Test Type (e.g. CBC)">
                    <textarea style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid #d1d5db;margin-bottom:10px;" rows="2" placeholder="Enter Results"></textarea>
                    <button type="submit" class="btn btn-outline-success btn-sm">Submit Result</button>
                </form>
            </div>
            <!-- Completed Reports & Uploads -->
            <div class="widget-card">
                <div class="widget-title">✅ Completed Reports & Uploads</div>
                <ul>
                    <li>
                        John Doe - CBC <span class="badge bg-success">Completed</span>
                        <button class="btn btn-outline-info btn-sm">Upload</button>
                    </li>
                    <li>
                        Maria Garcia - Urinalysis <span class="badge bg-success">Completed</span>
                        <button class="btn btn-outline-info btn-sm">Upload</button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="widgets-row">
            <!-- Stock & Reagent Monitoring -->
            <div class="widget-card" style="flex:2;">
                <div class="widget-title">🧴 Stock & Reagent Monitoring</div>
                <ul>
                    <li>
                        CBC Reagent - <span class="badge bg-danger">Low Stock</span>
                    </li>
                    <li>
                        Urine Strips - <span class="badge bg-success">Sufficient</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>