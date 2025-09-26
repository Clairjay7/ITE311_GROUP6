# Hospital Management System - Database Structure

## 🏥 Overview
Complete database structure for the Hospital Management System with 8 user roles and supporting tables.

## 📋 Migration Files Created

### Core User Tables
1. **`2024-01-01-000001_CreateUsersTable.php`** - Main user accounts
2. **`2024-01-01-000002_CreateDoctorsTable.php`** - Doctor profiles
3. **`2024-01-01-000003_CreateNursesTable.php`** - Nurse profiles  
4. **`2024-01-01-000004_CreateReceptionistsTable.php`** - Receptionist profiles
5. **`2024-01-01-000005_CreateLaboratoriesTable.php`** - Laboratory staff profiles
6. **`2024-01-01-000006_CreatePharmacistsTable.php`** - Pharmacist profiles
7. **`2024-01-01-000007_CreateAccountantsTable.php`** - Accountant profiles
8. **`2024-01-01-000008_CreateItStaffTable.php`** - IT staff profiles

### Supporting Tables
9. **`2024-01-01-000009_CreatePatientsTable.php`** - Patient records
10. **`2024-01-01-000010_CreateAppointmentsTable.php`** - Appointment scheduling
11. **`2024-01-01-000011_CreatePrescriptionsTable.php`** - Prescription management
12. **`2024-01-01-000012_CreateLabRequestsTable.php`** - Laboratory requests
13. **`2024-01-01-000013_CreateBillingTable.php`** - Billing and payments
14. **`2024-01-01-000014_CreateSystemLogsTable.php`** - System activity logs

## 🚀 How to Run Migrations

### Method 1: Using CodeIgniter CLI
```bash
php spark migrate
```

### Method 2: Using the Runner Script
```bash
php run_migrations.php
```

### Method 3: Using Browser (Development Only)
Navigate to: `http://localhost/Group6/public/migrate`

## 📊 Database Schema

### 👤 Users Table (Main)
- **Purpose**: Central user authentication for all roles
- **Key Fields**: id, username, email, password_hash, role, status
- **Roles**: superadmin, doctor, nurse, receptionist, laboratory, pharmacist, accountant, it_staff

### 👨‍⚕️ Doctors Table
- **Purpose**: Doctor-specific information
- **Key Fields**: license_number, specialization, consultation_fee, schedule
- **Foreign Key**: user_id → users.id

### 🧑‍⚕️ Nurses Table  
- **Purpose**: Nurse-specific information
- **Key Fields**: license_number, assigned_ward, shift_type, supervisor_id
- **Foreign Key**: user_id → users.id

### 🧑‍💼 Receptionists Table
- **Purpose**: Receptionist-specific information
- **Key Fields**: employee_id, shift_type, desk_location, access_level
- **Foreign Key**: user_id → users.id

### 🧪 Laboratories Table
- **Purpose**: Laboratory staff information
- **Key Fields**: license_number, lab_section, specialization, equipment_access
- **Foreign Key**: user_id → users.id

### 💊 Pharmacists Table
- **Purpose**: Pharmacist-specific information
- **Key Fields**: license_number, pharmacy_section, inventory_access
- **Foreign Key**: user_id → users.id

### 💼 Accountants Table
- **Purpose**: Accountant-specific information
- **Key Fields**: employee_id, department, access_level, billing_access
- **Foreign Key**: user_id → users.id

### 💻 IT Staff Table
- **Purpose**: IT staff information
- **Key Fields**: employee_id, department, security_clearance, system_admin_access
- **Foreign Key**: user_id → users.id

### 🏥 Supporting Tables

#### Patients
- Patient records with medical history, emergency contacts, allergies

#### Appointments
- Appointment scheduling between patients and doctors
- Status tracking: scheduled, confirmed, completed, cancelled

#### Prescriptions
- Medication prescriptions from doctors
- Dispensing tracking by pharmacists

#### Lab Requests
- Laboratory test requests from doctors
- Processing by laboratory staff

#### Billing
- Financial transactions and payment tracking
- Multiple payment methods supported

#### System Logs
- Activity logging for security and auditing
- User actions and system events

## 🔗 Relationships

```
users (1) → (1) doctors/nurses/receptionists/etc
patients (1) → (∞) appointments
doctors (1) → (∞) appointments  
patients (1) → (∞) prescriptions
doctors (1) → (∞) prescriptions
patients (1) → (∞) lab_requests
doctors (1) → (∞) lab_requests
patients (1) → (∞) billing
users (1) → (∞) system_logs
```

## ⚙️ Configuration

### Database Configuration
Update `app/Config/Database.php`:
```php
public array $default = [
    'DSN'      => '',
    'hostname' => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password',
    'database' => 'hms_database',
    'DBDriver' => 'MySQLi',
    // ... other settings
];
```

### Environment Setup
Update `.env` file:
```
database.default.hostname = localhost
database.default.database = hms_database
database.default.username = your_username
database.default.password = your_password
database.default.DBDriver = MySQLi
```

## 🛡️ Security Features

- **Password Hashing**: All passwords use PHP's `password_hash()`
- **Foreign Key Constraints**: Data integrity maintained
- **User Status**: Active/inactive/suspended user management
- **Activity Logging**: All user actions tracked
- **Role-based Access**: Specific permissions per role

## 📝 Notes

- All tables include `created_at` and `updated_at` timestamps
- Foreign keys use CASCADE for deletions where appropriate
- ENUM fields provide data validation at database level
- Unique constraints prevent duplicate critical data
- Indexes optimize query performance

## 🎯 Ready for Production

This database structure is:
- ✅ Normalized and efficient
- ✅ Scalable for large hospitals
- ✅ Secure with proper constraints
- ✅ Compatible with your existing dashboards
- ✅ Ready for immediate use

Run the migrations and your HMS system will be fully functional!
