
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Management System</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700;800;900&display=swap');
        
        body {
            background: linear-gradient(135deg, #e8f5e8 0%, #f1f8e9 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
        }
        .login-container {
            max-width: 430px;
            margin: 60px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 32px 32px 24px 32px;
        }
        .login-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: #2e7d32;
            margin-bottom: 24px;
            letter-spacing: -0.01em;
            text-align: center;
        }
        .roles-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 18px;
            margin-bottom: 28px;
        }
        .role-icon-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            transition: all 0.18s;
            border-radius: 10px;
            padding: 8px 10px;
            border: 2px solid transparent;
        }
        .role-icon-box:hover {
            background: #f1f5f9;
            transform: scale(1.08);
        }
        .role-icon-box.selected {
            background: #e8f5e8;
            border-color: #4caf50;
            transform: scale(1.05);
        }
        .role-icon {
            font-size: 2.5rem;
            color: #4caf50;
            margin-bottom: 4px;
        }
        .role-label {
            font-size: 0.9rem;
            color: #374151;
            text-align: center;
            font-weight: 500;
        }
        .login-form {
            display: block;
            margin-top: 18px;
        }
        .login-form.active {
            display: block;
        }
        .form-label {
            display: block;
            margin-bottom: 6px;
            color: #2e7d32;
            font-weight: 500;
        }
        .form-input {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            margin-bottom: 14px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-input:focus {
            outline: none;
            border-color: #4caf50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #4caf50, #66bb6a);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }
        .login-btn:hover {
            background: linear-gradient(135deg, #388e3c, #4caf50);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }
        .back-btn {
            width: 100%;
            background: none;
            border: none;
            color: #4caf50;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: underline;
            padding: 8px;
        }
        .back-btn:hover {
            color: #2e7d32;
        }
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        .alert-success {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        .role-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
            text-align: center;
            display: none;
        }
        .role-info.active {
            display: block;
        }
        .role-info h5 {
            margin: 0 0 8px 0;
            color: #2e7d32;
        }
        .role-info p {
            margin: 0;
            color: #64748b;
            font-size: 0.9rem;
        }
        .help-link {
            text-align: center;
            margin-top: 15px;
        }
        .help-link a {
            color: #6b7280;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .help-link a:hover {
            color: #4caf50;
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .login-container {
                margin: 20px auto;
                padding: 24px 20px;
            }
            .roles-row {
                gap: 12px;
            }
            .role-icon-box {
                padding: 6px 8px;
            }
            .role-label {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center" style="height:100vh;">
            <div class="col-10 text-center">
                <div class="login-container">
                    <h3 class="login-title mb-4">ST. PETER HOSPITAL INC.</h3>
                    
                    <!-- Error/Success Messages -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php endif; ?>

                    <div class="text-center mb-4">
                        <h5 style="color: #2e7d32; font-weight: 600;">Hospital Management System</h5>
                        <p class="text-muted">Please enter your login credentials</p>
                    </div>

                    <div id="loginFormContainer" class="login-form active">
                        <h4 id="loginTitle" style="color: #2e7d32; font-weight: 600;">Login to Hospital Management System</h4>
                        
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" action="<?= base_url('login/process') ?>" id="loginForm">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control form-input" id="username" name="username" required placeholder="Enter your username" autocomplete="username">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control form-input" id="password" name="password" required placeholder="Enter your password" autocomplete="current-password">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 login-btn">Login</button>
                        </form>
                        
                        
                        <div class="help-link">
                            <a href="<?= base_url('login-help') ?>">Need help? View login credentials</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form submission handler
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    const username = document.getElementById('username')?.value.trim();
                    const password = document.getElementById('password')?.value;
                    
                    if (!username || !password) {
                        e.preventDefault();
                        alert('Please enter both username and password');
                        return false;
                    }
                    
                    // Show loading state
                    const submitBtn = loginForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Logging in...';
                    }
                });
            }
        });
    </script>
</body>
</html>