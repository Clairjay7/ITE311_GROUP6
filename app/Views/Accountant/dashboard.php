<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accountant Dashboard - Hospital Management System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            color: #333;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .dashboard-header {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .dashboard-header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .dashboard-subtitle {
            color: #7f8c8d;
            margin: 5px 0 0;
        }
        .overview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .overview-card {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card-content h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .card-value {
            font-size: 24px;
            font-weight: bold;
            color: #3498db;
            margin: 10px 0;
        }
        .logout-link {
            float: right;
            color: #e74c3c;
            text-decoration: none;
            font-weight: 500;
        }
        .logout-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Hospital Management System</h1>
            <div style="float: right;">
                <a href="<?= base_url('logout') ?>" class="logout-link">Logout</a>
            </div>
            <div style="clear: both;"></div>
        </div>
    <div class="dashboard-header">
        <h2>Accountant Dashboard</h2>
        <p class="dashboard-subtitle">Financial Management & Billing</p>
    </div>

    <!-- Overview Cards -->
    <div class="overview-grid">
        <div class="overview-card">
            <div class="card-content">
                <h3>Today's Revenue</h3>
                <div class="card-value">₱<?= number_format($todayRevenue, 2) ?></div>
            </div>
        </div>
        <div class="overview-card">
            <div class="card-content">
                <h3>Pending Bills</h3>
                <div class="card-value"><?= count($pendingBills) ?></div>
            </div>
        </div>
        <div class="overview-card">
            <div class="card-content">
                <h3>Insurance Claims</h3>
                <div class="card-value"><?= count($insuranceClaims) ?></div>
            </div>
        </div>
        <div class="overview-card">
            <div class="card-content">
                <h3>Outstanding Balance</h3>
                <div class="card-value">₱<?= number_format($outstandingBalance, 2) ?></div>
            </div>
        </div>
    </div>

    <style>
        .dashboard-header {
            margin-bottom: 30px;
        }

        .dashboard-header h1 {
            font-size: 28px;
            color: #333;
            margin: 0 0 5px 0;
            font-weight: 600;
        }

        .dashboard-subtitle {
            color: #666;
            font-size: 16px;
            margin: 0;
        }

        .overview-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .overview-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 30px 25px;
            text-align: left;
            transition: box-shadow 0.3s ease;
        }

        .overview-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-content h3 {
            font-size: 16px;
            color: #666;
            margin: 0 0 15px 0;
            font-weight: 500;
        }

        .card-value {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        @media (max-width: 768px) {
            .overview-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    </div>
</body>
</html>
