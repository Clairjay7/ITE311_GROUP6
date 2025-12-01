# 🔍 Pharmacy Add Medicine - Debug Guide

## ✅ Verification Checklist

### 1. Migration Status
```bash
php spark migrate:status | grep Pharmacy
```
**Result:** ✅ CreatePharmacyTable - Migrated on 2025-12-01 13:58:08

### 2. Table Exists
```bash
php spark db:table pharmacy
```
**Result:** ✅ Table exists with correct columns

### 3. Routes Exist
```
GET  /pharmacy/add-medicine  → PharmacyController::addMedicine()
POST /pharmacy/add-medicine  → PharmacyController::addMedicine()
```
**Result:** ✅ Routes are configured

---

## 🐛 Common Issues & Solutions

### Issue 1: "Cannot add medicine" - No error message
**Possible Causes:**
1. Form not submitting
2. Validation failing silently
3. Database connection issue
4. CSRF token mismatch

**Debug Steps:**
1. Open browser Developer Tools (F12)
2. Go to Network tab
3. Try to add medicine
4. Check if POST request is sent
5. Check response status code

### Issue 2: Form submits but no data saved
**Possible Causes:**
1. Validation rules failing
2. Model insert() returning false
3. Database constraints

**Solution:** Check validation errors
```php
// The controller now shows validation errors
if ($this->pharmacyModel->insert($data)) {
    // Success
} else {
    $errors = $this->pharmacyModel->errors();
    // Errors will be displayed
}
```

### Issue 3: CSRF Token Error
**Symptoms:** "The action you requested is not allowed"

**Solution:**
1. Clear browser cache
2. Refresh the page
3. Try again

### Issue 4: Route not found (404)
**Symptoms:** 404 error when accessing /pharmacy/add-medicine

**Solution:**
```bash
# Clear route cache
php spark cache:clear

# Verify routes
php spark routes | grep pharmacy
```

---

## 🧪 Manual Test Steps

### Step 1: Access the Form
```
URL: http://localhost/pharmacy/add-medicine
```

**Expected:** Form loads with fields:
- Medicine Name (required)
- Description (optional)
- Quantity (required)
- Price (required)

### Step 2: Fill the Form
```
Medicine Name: Test Medicine
Description: This is a test
Quantity: 100
Price: 50.00
```

### Step 3: Submit
Click "Save Medicine" button

**Expected Behavior:**
- Redirects to /pharmacy/stock-monitoring
- Shows success message: "Medicine added successfully"
- Medicine appears in the list

### Step 4: Verify in Database
```sql
SELECT * FROM pharmacy ORDER BY id DESC LIMIT 1;
```

**Expected Result:**
```
id: (new id)
item_name: Test Medicine
description: This is a test
quantity: 100
price: 50.00
created_at: (current timestamp)
updated_at: (current timestamp)
deleted_at: NULL
```

---

## 🔧 Quick Fixes

### Fix 1: Clear All Caches
```bash
cd F:\xammp\htdocs\group6
php spark cache:clear
```

### Fix 2: Check Permissions
Make sure `writable/` folder has write permissions

### Fix 3: Check Database Connection
```bash
php spark db:table users
```
If this works, database connection is OK.

### Fix 4: Test Direct Insert
```sql
-- Try inserting directly via SQL
INSERT INTO pharmacy (item_name, description, quantity, price, created_at, updated_at)
VALUES ('Direct Test', 'Testing direct insert', 50, 25.00, NOW(), NOW());

-- Check if it worked
SELECT * FROM pharmacy WHERE item_name = 'Direct Test';
```

If direct insert works, the problem is in the application code.

---

## 🎯 Debugging the Controller

### Add Debug Logging

Temporarily add this to `PharmacyController::addMedicine()`:

```php
if ($this->request->getMethod() === 'post') {
    // Debug: Log posted data
    log_message('debug', 'Posted data: ' . json_encode($this->request->getPost()));
    
    $data = [
        'item_name' => $this->request->getPost('item_name'),
        'description' => $this->request->getPost('description'),
        'quantity' => $this->request->getPost('quantity'),
        'price' => $this->request->getPost('price'),
    ];
    
    // Debug: Log data to insert
    log_message('debug', 'Data to insert: ' . json_encode($data));
    
    if ($this->pharmacyModel->insert($data)) {
        log_message('debug', 'Insert successful');
        return redirect()->to('/pharmacy/stock-monitoring')
            ->with('success', 'Medicine added successfully');
    } else {
        $errors = $this->pharmacyModel->errors();
        log_message('error', 'Insert failed: ' . json_encode($errors));
        
        // ... rest of error handling
    }
}
```

Then check logs:
```bash
# View logs
type writable\logs\log-2025-12-01.php
```

---

## 📝 Validation Rules Check

The model has these validation rules:

```php
'item_name' => 'required|max_length[255]',
'description' => 'permit_empty|max_length[500]',
'quantity' => 'required|integer|greater_than_equal_to[0]',
'price' => 'required|decimal|greater_than_equal_to[0]',
```

**Common Validation Failures:**
1. **item_name** - Must not be empty, max 255 chars
2. **quantity** - Must be integer >= 0
3. **price** - Must be decimal >= 0

---

## 🚀 Quick Test via Browser Console

Open browser console (F12) and run:

```javascript
// Check if form exists
console.log(document.querySelector('form'));

// Check form action
console.log(document.querySelector('form').action);

// Check CSRF token
console.log(document.querySelector('input[name="csrf_test_name"]'));
```

---

## 💡 Alternative: Test via Command Line

Create a test script `test_pharmacy_insert.php`:

```php
<?php
require 'vendor/autoload.php';

$config = config('Database');
$db = \Config\Database::connect();

$data = [
    'item_name' => 'CLI Test Medicine',
    'description' => 'Testing from CLI',
    'quantity' => 100,
    'price' => 50.00,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
];

$result = $db->table('pharmacy')->insert($data);

if ($result) {
    echo "✅ Insert successful! ID: " . $db->insertID() . "\n";
} else {
    echo "❌ Insert failed!\n";
    print_r($db->error());
}
```

Run:
```bash
php test_pharmacy_insert.php
```

---

## 📊 Expected vs Actual

### What SHOULD happen:
1. ✅ Form loads at /pharmacy/add-medicine
2. ✅ User fills in all required fields
3. ✅ User clicks "Save Medicine"
4. ✅ Form submits via POST
5. ✅ Controller validates data
6. ✅ Model inserts into database
7. ✅ Redirects to stock monitoring
8. ✅ Shows success message
9. ✅ Medicine appears in list

### What MIGHT be happening:
1. ❓ Form not submitting (JavaScript error?)
2. ❓ CSRF token invalid
3. ❓ Validation failing
4. ❓ Database insert failing
5. ❓ Redirect not working

---

## 🎯 Next Steps

### If you see an error message:
1. Copy the exact error message
2. Check if it's a validation error
3. Fix the input and try again

### If no error message:
1. Check browser console (F12)
2. Check Network tab for failed requests
3. Check application logs
4. Try direct SQL insert to test database

### If direct SQL works but form doesn't:
1. Problem is in application code
2. Check validation rules
3. Check CSRF token
4. Check routes

---

## 📞 Quick Checklist

Before asking for help, verify:

- [ ] Can you access /pharmacy/add-medicine?
- [ ] Does the form load?
- [ ] Can you fill in the fields?
- [ ] What happens when you click "Save Medicine"?
- [ ] Do you see any error message?
- [ ] Check browser console for errors
- [ ] Check Network tab for failed requests
- [ ] Try direct SQL insert - does it work?

---

**Created:** December 1, 2025  
**Purpose:** Debug guide for pharmacy add medicine functionality

