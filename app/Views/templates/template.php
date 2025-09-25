<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Hospital Management System') ?></title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-600: #1d4ed8;
            --secondary: #64748b;
            --accent: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #0f172a;
            --muted: #475569;
            --border: #e2e8f0;
            --radius: 12px;
            --shadow: 0 4px 12px rgba(2, 6, 23, 0.08);
        }

        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial, sans-serif;
            color: var(--text);
            background: var(--bg);
        }

        .app-shell { min-height: 100%; display: flex; flex-direction: column; }
        .app-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-600) 100%);
            color: #fff;
            padding: 16px 20px;
            box-shadow: var(--shadow);
        }
        .app-header .brand { font-weight: 700; letter-spacing: .2px; }
        .app-header .brand small { opacity: .9; font-weight: 500; }
        .app-header .actions { float: right; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 10px 14px;
            border-radius: 10px;
            text-decoration: none;
            cursor: pointer;
            transition: transform .12s ease, box-shadow .12s ease, background .2s ease;
            box-shadow: 0 2px 8px rgba(37, 99, 235, .25);
        }
        .btn:hover { transform: translateY(-1px); background: var(--primary-600); }
        .btn-secondary { background: var(--secondary); box-shadow: 0 2px 8px rgba(100,116,139,.2); }
        .btn-secondary:hover { background: #475569; }
        .btn-ghost { background: transparent; color: #fff; border: 1px solid rgba(255,255,255,.3); box-shadow: none; }

        .app-main { flex: 1 1 auto; padding: 24px; }
        .container { max-width: 1200px; margin: 0 auto; }

        .grid { display: grid; gap: 16px; }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        @media (max-width: 1024px) { .grid-4 { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px) { .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; } .app-header .actions { float: none; margin-top: 10px; display: block; } }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 16px;
        }
        .card h5 { margin: 0 0 6px 0; font-size: 16px; color: var(--muted); }
        .card h3 { margin: 4px 0 0 0; font-size: 28px; color: var(--text); }

        .section-title { margin: 8px 0 12px 0; font-size: 20px; }
        .actions-row { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px; }
        .spacer { height: 12px; }
    </style>
</head>
<body>
    <div class="app-shell">
        <header class="app-header">
            <div class="container">
                <div class="brand">Hospital Management System <small>Unified Dashboard</small></div>
                <div class="actions">
                    <a href="<?= base_url('logout') ?>" class="btn btn-ghost">Logout</a>
                </div>
            </div>
        </header>
        <main class="app-main">
            <div class="container">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>
</body>
</html>