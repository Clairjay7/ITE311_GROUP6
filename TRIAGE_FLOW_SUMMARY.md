# 🏥 TRIAGE PATIENT FLOW - Quick Summary

## 📍 **Saan Napupunta ang Pasyente Pagkatapos ng TRIAGE?**

---

## 🔴 **CURRENT FLOW (Kasalukuyang Sistema)**

```
┌─────────────────┐
│  TRIAGE         │
│  (Nurse)        │
└────────┬────────┘
         │
         ├─ CRITICAL
         │   └─→ Auto-assigned to Doctor
         │       └─→ Doctor Dashboard (Emergency Cases)
         │           └─→ Consultation
         │
         ├─ MODERATE/MINOR
         │   └─→ Nurse manually sends to Doctor
         │       └─→ Doctor Dashboard (Awaiting Consultation)
         │           └─→ Consultation
         │
         └─ (No direct admission from triage)
```

**❌ WALANG:** ER/OPD distinction, ER bed assignment, OPD queue, Direct admission

---

## ✅ **REQUIRED FLOW (Dapat na Flow)**

### **1️⃣ EMERGENCY/URGENT (Critical Triage)**

```
┌─────────────────┐
│  TRIAGE         │
│  Level: CRITICAL│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  EMERGENCY ROOM  │
│  (ER/ED)         │
└────────┬────────┘
         │
         ├─→ ER Bed Assignment
         │
         ├─→ ER Doctor Assessment
         │
         ├─→ Emergency Treatment
         │   ├─ Lab Test
         │   ├─ Imaging
         │   ├─ IV
         │   └─ Medications
         │
         └─→ DECISION:
             ├─ ADMIT (In-Patient)
             └─ DISCHARGE (If stable)
```

**Roles:**
- **Nurse:** Triage → ER bed assignment → Assist doctor
- **Doctor (ER):** Assess → Orders → Decide admit/discharge
- **Lab Staff:** Process ER lab requests (PRIORITY)
- **Pharmacy:** Issue emergency meds (PRIORITY)
- **Accountant:** Bill ER services

---

### **2️⃣ NON-EMERGENCY (Moderate/Minor Triage)**

```
┌─────────────────┐
│  TRIAGE         │
│  Level: MODERATE│
│  or MINOR       │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  OPD/CLINIC      │
│  (Out-Patient)   │
└────────┬────────┘
         │
         ├─→ OPD Queue (Waiting List)
         │
         ├─→ Doctor Consultation
         │   ├─ Lab Orders
         │   ├─ X-ray Orders
         │   └─ Medication Orders
         │
         └─→ DISCHARGE
```

**Roles:**
- **Nurse:** Triage → Add to OPD queue
- **Doctor (OPD):** Consult → Orders → Discharge
- **Lab Staff:** Process OPD lab requests
- **Pharmacy:** Issue medications
- **Accountant:** Bill OPD services

---

### **3️⃣ FOR ADMISSION (Any Triage Level)**

```
┌─────────────────┐
│  TRIAGE         │
│  (Any Level)    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Quick Doctor   │
│  Assessment      │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  ADMISSION       │
│  Section         │
└────────┬────────┘
         │
         ├─→ Room & Bed Assignment
         │
         ├─→ Admission Form
         │
         ├─→ Transfer to Ward
         │
         └─→ In-Patient Workflow
```

**Roles:**
- **Nurse:** Triage → Process admission
- **Doctor:** Quick assessment → Decide admission
- **Receptionist:** Can also process admission
- **Accountant:** Bill room fees, services

---

## 🔄 **COMPARISON TABLE**

| Aspect | CURRENT | REQUIRED |
|--------|---------|----------|
| **Critical Triage** | → Doctor Dashboard | → ER/ED → ER Bed → ER Doctor |
| **Moderate/Minor** | → Doctor Dashboard | → OPD Queue → OPD Doctor |
| **Admission** | After consultation only | Direct from triage (if needed) |
| **ER Beds** | ❌ None | ✅ ER Bed Management |
| **OPD Queue** | ❌ None | ✅ OPD Queue System |
| **Routing** | Manual | Automatic based on triage level |

---

## 🎯 **KEY DIFFERENCES**

### **CURRENT:**
- All triaged patients go to **Doctor Dashboard**
- No distinction between ER and OPD
- No queue system
- Admission only after consultation

### **REQUIRED:**
- **Critical** → **ER/ED** (with ER beds)
- **Moderate/Minor** → **OPD/Clinic** (with queue)
- **For Admission** → **Direct Admission** (from triage)
- Clear workflow separation

---

## 📋 **IMPLEMENTATION PRIORITY**

1. **🔴 HIGH:** Add "Disposition" field (ER/OPD/Admission)
2. **🟡 MEDIUM:** Create ER bed management
3. **🟡 MEDIUM:** Create OPD queue system
4. **🟢 LOW:** Direct admission from triage

---

**See `TRIAGE_PATIENT_FLOW.md` for detailed implementation guide.**

