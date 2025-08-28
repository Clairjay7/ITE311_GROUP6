
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Management System</title>
    <style>
        body {
            background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
            font-family: 'Segoe UI', Arial, sans-serif;
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
            font-size: 2rem;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 24px;
            letter-spacing: 1px;
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
            transition: transform 0.18s;
            border-radius: 10px;
            padding: 8px 10px;
        }
        .role-icon-box:hover {
            background: #f1f5f9;
            transform: scale(1.08);
        }
        .role-icon {
            font-size: 2.5rem;
            color: #2563eb;
            margin-bottom: 4px;
        }
        .role-label {
            font-size: 1rem;
            color: #374151;
        }
        .login-form {
            display: none;
            margin-top: 18px;
        }
        .login-form.active {
            display: block;
        }
        .form-label {
            display: block;
            margin-bottom: 6px;
            color: #2563eb;
            font-weight: 500;
        }
        .form-input {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            margin-bottom: 14px;
            font-size: 1rem;
        }
        .login-btn {
            width: 100%;
            padding: 10px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 8px;
            transition: background 0.18s;
        }
        .login-btn:hover {
            background: #1e40af;
        }
        .back-btn {
            width: 100%;
            background: none;
            border: none;
            color: #2563eb;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            .login-container {
                padding: 18px 6px;
            }
            .roles-row {
                gap: 10px;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center" style="height:100vh;">
            <div class="col-10 text-center">
                <div class="login-container">
                    <h3 class="login-title mb-4">LOG IN</h3>
                    <div class="roles-row mb-5">
                        <div class="role-icon-box" id="superAdminIcon" title="Super Admin">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="#2563eb"><circle cx="12" cy="8" r="4"/><path d="M4 20v-1c0-2.8 4-4.3 8-4.3s8 1.5 8 4.3v1z"/></svg>
                            <div class="role-label">Super Admin</div>
                        </div>
                        <div class="role-icon-box" id="doctorIcon" title="Doctor">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="#2563eb"><circle cx="12" cy="8" r="4"/><path d="M4 20v-1c0-2.8 4-4.3 8-4.3s8 1.5 8 4.3v1z"/><rect x="10" y="16" width="4" height="6" rx="2" fill="#fff" stroke="#2563eb" stroke-width="1"/></svg>
                            <div class="role-label">Doctor</div>
                        </div>
                        <div class="role-icon-box" id="nurseIcon" title="Nurse">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="#2563eb"><circle cx="12" cy="8" r="4"/><path d="M4 20v-1c0-2.8 4-4.3 8-4.3s8 1.5 8 4.3v1z"/><rect x="9" y="13" width="6" height="2" rx="1" fill="#fff" stroke="#2563eb" stroke-width="1"/></svg>
                            <div class="role-label">Nurse</div>
                        </div>
                        <div class="role-icon-box" id="receptionistIcon" title="Receptionist">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="#2563eb"><circle cx="12" cy="8" r="4"/><rect x="6" y="16" width="12" height="4" rx="2" fill="#fff" stroke="#2563eb" stroke-width="1"/></svg>
                            <div class="role-label">Receptionist</div>
                        </div>
                        <div class="role-icon-box" id="labIcon" title="Laboratory Staff">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"><rect x="7" y="2" width="10" height="4" rx="2" fill="#2563eb"/><rect x="9" y="6" width="6" height="12" rx="3" fill="#2563eb"/><ellipse cx="12" cy="20" rx="4" ry="2" fill="#2563eb" opacity="0.5"/></svg>
                            <div class="role-label">Laboratory Staff</div>
                        </div>
                        <div class="role-icon-box" id="pharmacistIcon" title="Pharmacist">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"><rect x="6" y="10" width="12" height="8" rx="4" fill="#2563eb"/><rect x="10" y="2" width="4" height="8" rx="2" fill="#2563eb"/><rect x="8" y="18" width="8" height="2" rx="1" fill="#2563eb"/></svg>
                            <div class="role-label">Pharmacist</div>
                        </div>
                        <div class="role-icon-box" id="accountantIcon" title="Accountant">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"><rect x="4" y="6" width="16" height="12" rx="3" fill="#2563eb"/><rect x="8" y="10" width="8" height="2" rx="1" fill="#fff"/><rect x="8" y="14" width="4" height="2" rx="1" fill="#fff"/></svg>
                            <div class="role-label">Accountant</div>
                        </div>
                        <div class="role-icon-box" id="itIcon" title="IT Staff">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none"><rect x="4" y="6" width="16" height="10" rx="2" fill="#2563eb"/><rect x="8" y="18" width="8" height="2" rx="1" fill="#2563eb"/><circle cx="12" cy="11" r="2" fill="#fff"/></svg>
                            <div class="role-label">IT Staff</div>
                        </div>
                    </div>
                    <div id="loginFormContainer" class="login-form">
                        <h4 id="roleTitle"></h4>
                        <form method="post" action="<?= base_url('login') ?>">
                            <input type="hidden" name="role" id="roleInput">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control form-input" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control form-input" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 login-btn">Login</button>
                            <button type="button" class="btn btn-link w-100 mt-2 back-btn" id="backBtn">Back</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const loginForm = document.getElementById('loginFormContainer');
        const roleTitle = document.getElementById('roleTitle');
        const roleInput = document.getElementById('roleInput');
        const backBtn = document.getElementById('backBtn');

        document.getElementById('superAdminIcon').onclick = function() {
            showLogin('Super Admin');
        };
        document.getElementById('doctorIcon').onclick = function() {
            showLogin('Doctor');
        };
        document.getElementById('nurseIcon').onclick = function() {
            showLogin('Nurse');
        };
        document.getElementById('receptionistIcon').onclick = function() {
            showLogin('Receptionist');
        };
        document.getElementById('labIcon').onclick = function() {
            showLogin('Laboratory Staff');
        };
        document.getElementById('pharmacistIcon').onclick = function() {
            showLogin('Pharmacist');
        };
        document.getElementById('accountantIcon').onclick = function() {
            showLogin('Accountant');
        };
        document.getElementById('itIcon').onclick = function() {
            showLogin('IT Staff');
        };
        backBtn.onclick = function() {
            loginForm.style.display = 'none';
        };

        function showLogin(role) {
            roleTitle.textContent = 'Login as ' + role;
            roleInput.value = role.toLowerCase().replace(/ /g, '_');
            loginForm.style.display = 'block';
        }
    </script>
</body>
</html>