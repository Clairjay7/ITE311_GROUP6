# 📋 TRIAGE PATIENT FLOW - Current State & Recommendations

## 🎯 Overview
Dokumentong ito ay naglalarawan kung **saan napupunta ang pasyente pagkatapos ng TRIAGE** at kung paano dapat i-improve ang workflow base sa mga requirements.

---

## 📊 CURRENT STATE (Kasalukuyang Sistema)

### ✅ Ano ang Gumagana:

1. **TRIAGE PROCESS:**
   - ✅ Nurse performs triage assessment
   - ✅ Records vital signs (heart rate, BP, temperature, O2 sat, respiratory rate)
   - ✅ Assigns triage level: **Critical**, **Moderate**, or **Minor**
   - ✅ Records chief complaint and notes

2. **AFTER TRIAGE - Current Flow:**

   **🔴 CRITICAL Triage Level:**
   - ✅ Auto-assigned to ER doctor or on-duty doctor
   - ✅ Creates consultation record (if patient is from `admin_patients`)
   - ✅ Appears in **Doctor Dashboard** → "Emergency Cases (Critical Triage)" section
   - ✅ Doctor can click "Attend Now" to view patient
   - ❌ **WALANG** clear ER/ED workflow distinction
   - ❌ **WALANG** automatic ER bed assignment
   - ❌ **WALANG** direct admission option from triage

   **🟡 MODERATE / 🟢 MINOR Triage Level:**
   - ✅ Status = "completed"
   - ✅ Appears in Nurse Triage Dashboard → "Triaged Patients (Ready for Doctor Assignment)"
   - ✅ Nurse manually selects doctor and clicks "Send to Doctor"
   - ✅ Creates consultation record
   - ✅ Appears in Doctor Dashboard → "Awaiting Consultation"
   - ❌ **WALANG** clear OPD/Clinic queue distinction
   - ❌ **WALANG** automatic OPD assignment

3. **ADMISSION:**
   - ✅ Admission happens **AFTER consultation** (not directly from triage)
   - ✅ Doctor marks consultation as "For Admission"
   - ✅ Nurse/Receptionist processes admission
   - ✅ Room and bed assignment
   - ❌ **WALANG** direct admission option from triage

---

## 🎯 REQUIRED FLOW (Base sa Requirements)

### 1️⃣ **EMERGENCY / URGENT (High Priority) → ER/ED**

**Workflow:**
```
TRIAGE (Critical) 
  → Emergency Room (ER/ED)
    → ER Bed Assignment (automatic or manual)
    → Doctor Assessment (ER Doctor)
    → Emergency Treatment
      → Lab Test / Imaging / IV / Meds
    → After Treatment:
      → ADMIT as In-Patient OR
      → DISCHARGE if stable
```

**Roles Involved:**
- **Nurse:** Performs triage → Assigns to ER → Assists doctor
- **Doctor (ER Doctor):** Assesses → Gives orders → Decides admit/discharge
- **Lab Staff:** Processes lab requests
- **Pharmacy:** Issues emergency medications
- **Accountant:** Bills ER services

---

### 2️⃣ **NON-EMERGENCY (Low Priority) → OPD/Clinic**

**Workflow:**
```
TRIAGE (Moderate/Minor)
  → Out-Patient Department (OPD/Clinic)
    → OPD Queue (waiting list)
    → Doctor Consultation
      → Orders (lab / x-ray / meds)
    → DISCHARGE after consultation
```

**Roles Involved:**
- **Nurse:** Performs triage → Places in OPD queue
- **Doctor (OPD Doctor):** Consults → Gives orders → Discharges
- **Lab Staff:** Processes lab requests
- **Pharmacy:** Issues medications
- **Accountant:** Bills OPD services

---

### 3️⃣ **FOR ADMISSION (After Triage + Quick Doctor Check)**

**Workflow:**
```
TRIAGE (Any Level)
  → Quick Doctor Assessment
    → Decision: FOR ADMISSION
      → Admission Section / Ward Assignment
        → Assign Room & Bed
        → Fill Admission Form
        → Transfer to Ward
        → Start In-Patient Workflow
```

**Roles Involved:**
- **Nurse:** Performs triage → Processes admission
- **Doctor:** Quick assessment → Decides admission
- **Receptionist:** Can also process admission
- **Accountant:** Bills room fees, services

---

## 🔧 RECOMMENDATIONS FOR IMPLEMENTATION

### **Priority 1: Add Triage Level Routing**

#### **Option A: Add "For Admission" Option in Triage Form**

**Modify:** `app/Views/nurse/triage/form.php`

```php
<!-- Add new option in triage_level select -->
<option value="For_Admission">For Admission - Requires ward assignment</option>
```

**Modify:** `app/Controllers/Nurse/TriageController.php`

```php
// In save() method, add handling for "For_Admission"
if ($triageLevel === 'For_Admission') {
    // Mark patient for admission
    // Redirect to admission form or create admission request
}
```

#### **Option B: Add "Disposition" Field After Triage**

**Add new field in triage form:**
- **Disposition:** ER/OPD/Admission

**Workflow:**
- Critical → Auto-route to ER
- Moderate/Minor → Route to OPD
- Any level + "For Admission" → Route to Admission

---

### **Priority 2: Create ER/ED Workflow**

#### **A. ER Bed Management**

**Create:** `app/Controllers/Nurse/EmergencyController.php`

```php
// Functions needed:
- assignERBed($patientId, $triageId)
- listERBeds()
- releaseERBed($bedId)
```

**Database:**
- Add `er_beds` table or use existing `beds` table with `type = 'ER'`

#### **B. ER Queue in Doctor Dashboard**

**Modify:** `app/Controllers/Doctor/DashboardController.php`

```php
// Separate ER cases from regular consultations
$erCases = $triageModel
    ->where('triage_level', 'Critical')
    ->where('disposition', 'ER') // New field
    ->where('status', 'completed')
    ->findAll();
```

#### **C. ER-Specific Views**

**Create:** `app/Views/doctor/emergency/` folder
- `er_queue.php` - List of ER patients
- `er_patient.php` - ER patient details
- `er_orders.php` - ER-specific orders

---

### **Priority 3: Create OPD/Clinic Workflow**

#### **A. OPD Queue System**

**Modify:** `app/Controllers/Nurse/TriageController.php`

```php
// After Moderate/Minor triage, automatically add to OPD queue
if (in_array($triageLevel, ['Moderate', 'Minor'])) {
    // Add to OPD queue
    $db->table('opd_queue')->insert([
        'patient_id' => $patientId,
        'triage_id' => $triageId,
        'queue_number' => $this->getNextOPDQueueNumber(),
        'status' => 'waiting',
        'created_at' => date('Y-m-d H:i:s')
    ]);
}
```

#### **B. OPD Queue View**

**Create:** `app/Views/nurse/opd/queue.php`
- Shows waiting patients
- Queue numbers
- Estimated wait time

**Create:** `app/Views/doctor/opd/queue.php`
- Shows OPD patients assigned to doctor
- Consultation order

---

### **Priority 4: Direct Admission from Triage**

#### **A. Add "For Admission" Button in Triage Form**

**Modify:** `app/Views/nurse/triage/form.php`

```html
<!-- After triage level selection -->
<div class="form-check mt-3">
    <input class="form-check-input" type="checkbox" name="for_admission" id="for_admission">
    <label class="form-check-label" for="for_admission">
        <strong>Mark for Admission</strong> - Patient requires ward assignment
    </label>
</div>
```

#### **B. Handle Admission Request**

**Modify:** `app/Controllers/Nurse/TriageController.php`

```php
$forAdmission = $this->request->getPost('for_admission');

if ($forAdmission) {
    // Create admission request
    $db->table('admission_requests')->insert([
        'patient_id' => $patientId,
        'triage_id' => $triageId,
        'requested_by' => $nurseId,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Redirect to admission form
    return $this->response->setJSON([
        'success' => true,
        'redirect' => '/admission/create?triage_id=' . $triageId
    ]);
}
```

---

## 📋 IMPLEMENTATION CHECKLIST

### **Phase 1: Basic Routing**
- [ ] Add "Disposition" field to triage (ER/OPD/Admission)
- [ ] Update `TriageModel` to include disposition
- [ ] Update triage form to include disposition selection
- [ ] Modify `TriageController::save()` to handle disposition

### **Phase 2: ER/ED Workflow**
- [ ] Create ER bed management system
- [ ] Add ER queue in doctor dashboard
- [ ] Create ER-specific views for doctors
- [ ] Add ER bed assignment in triage completion

### **Phase 3: OPD/Clinic Workflow**
- [ ] Create OPD queue table
- [ ] Auto-add to OPD queue for Moderate/Minor triage
- [ ] Create OPD queue view for nurses
- [ ] Create OPD queue view for doctors
- [ ] Add queue number generation

### **Phase 4: Direct Admission**
- [ ] Add "For Admission" checkbox in triage form
- [ ] Create admission request system
- [ ] Link triage to admission form
- [ ] Auto-populate admission form from triage data

### **Phase 5: Role-Specific Views**
- [ ] **Nurse:** ER bed management, OPD queue management
- [ ] **Doctor:** ER patient list, OPD patient list, Admission requests
- [ ] **Lab Staff:** ER lab requests (priority), OPD lab requests
- [ ] **Pharmacy:** ER prescriptions (priority), OPD prescriptions
- [ ] **Accountant:** ER billing, OPD billing, Admission billing

---

## 🔍 CURRENT DATABASE STRUCTURE

### **Triage Table:**
```sql
- id
- patient_id
- nurse_id
- triage_level (Critical, Moderate, Minor)
- vital_signs (JSON)
- chief_complaint
- notes
- status (pending, completed, sent_to_doctor)
- sent_to_doctor (0/1)
- doctor_id (assigned doctor)
- created_at
- updated_at
```

### **Missing Fields:**
- ❌ `disposition` (ER/OPD/Admission)
- ❌ `er_bed_id` (if assigned to ER)
- ❌ `opd_queue_number`
- ❌ `for_admission` (boolean)

---

## 📝 NOTES

1. **Current System Strengths:**
   - ✅ Triage assessment is complete
   - ✅ Critical patients auto-assigned to doctor
   - ✅ Consultation system works
   - ✅ Admission system exists (but not linked to triage)

2. **Gaps to Address:**
   - ❌ No ER/OPD distinction
   - ❌ No ER bed management
   - ❌ No OPD queue system
   - ❌ No direct admission from triage
   - ❌ No clear workflow routing based on triage level

3. **Recommended Approach:**
   - Start with **Phase 1** (Basic Routing) - Add disposition field
   - Then implement **Phase 2** (ER Workflow) for critical cases
   - Then implement **Phase 3** (OPD Workflow) for non-emergency
   - Finally implement **Phase 4** (Direct Admission)

---

## 🚀 QUICK START: Add Disposition Field

### **Step 1: Database Migration**

```php
// Create: app/Database/Migrations/YYYY-MM-DD-HHMMSS_AddDispositionToTriage.php

public function up()
{
    $this->forge->addColumn('triage', [
        'disposition' => [
            'type' => 'ENUM',
            'constraint' => ['ER', 'OPD', 'Admission', 'Pending'],
            'default' => 'Pending',
            'null' => false,
            'after' => 'triage_level'
        ]
    ]);
}
```

### **Step 2: Update Model**

```php
// app/Models/TriageModel.php
protected $allowedFields = [
    // ... existing fields
    'disposition',
];
```

### **Step 3: Update Form**

```php
// app/Views/nurse/triage/form.php
<select name="disposition" class="form-select" required>
    <option value="">-- Select Disposition --</option>
    <option value="ER">Emergency Room (ER)</option>
    <option value="OPD">Out-Patient Department (OPD)</option>
    <option value="Admission">For Admission</option>
</select>
```

### **Step 4: Update Controller**

```php
// app/Controllers/Nurse/TriageController.php
$disposition = $this->request->getPost('disposition');

$triageData = [
    // ... existing fields
    'disposition' => $disposition,
];

// Auto-set disposition based on triage level
if ($triageLevel === 'Critical' && empty($disposition)) {
    $triageData['disposition'] = 'ER';
} elseif (in_array($triageLevel, ['Moderate', 'Minor']) && empty($disposition)) {
    $triageData['disposition'] = 'OPD';
}
```

---

**Last Updated:** 2025-12-03
**Status:** Documentation Complete - Ready for Implementation

