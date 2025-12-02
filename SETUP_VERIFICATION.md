# CodeIgniter 4 Setup Verification Guide

## ✅ Configuration Complete

Ang iyong CodeIgniter 4 project ay naka-configure na para hindi na makita ang 'public' sa URL.

### Changes Made:

1. **Root `.htaccess`** - Na-update para i-redirect lahat ng requests papunta sa `public/` folder
2. **Security** - Protected ang `app/`, `system/`, `writable/`, at `tests/` folders
3. **baseURL** - Naka-set na sa `http://localhost/group6/` (walang public)

---

## 🧪 Paano i-Test ang Setup

### 1. Start XAMPP
- Buksan ang XAMPP Control Panel
- I-start ang **Apache** service

### 2. Test sa Browser

#### ✅ Test 1: Root URL (Dapat gumana)
```
http://localhost/group6/
```
**Expected:** Dapat makita mo ang login page o home page (hindi 404 error)

#### ✅ Test 2: Login Page (Dapat gumana)
```
http://localhost/group6/login
```
**Expected:** Dapat makita mo ang login form

#### ✅ Test 3: Dashboard (Dapat gumana pagkatapos mag-login)
```
http://localhost/group6/doctor/dashboard
http://localhost/group6/nurse/dashboard
http://localhost/group6/labstaff/dashboard
```
**Expected:** Dapat makita mo ang dashboard ng respective role

#### ✅ Test 4: Direct Public Access (Dapat gumana pa rin)
```
http://localhost/group6/public/
```
**Expected:** Dapat gumana pa rin (backward compatibility)

#### ❌ Test 5: Security - App Folder (Dapat BLOCKED)
```
http://localhost/group6/app/
```
**Expected:** 403 Forbidden o 404 error (hindi dapat ma-access)

#### ❌ Test 6: Security - System Folder (Dapat BLOCKED)
```
http://localhost/group6/system/
```
**Expected:** 403 Forbidden o 404 error (hindi dapat ma-access)

#### ❌ Test 7: Security - Writable Folder (Dapat BLOCKED)
```
http://localhost/group6/writable/
```
**Expected:** 403 Forbidden o 404 error (hindi dapat ma-access)

#### ❌ Test 8: Security - .env File (Dapat BLOCKED)
```
http://localhost/group6/.env
```
**Expected:** 403 Forbidden o 404 error (hindi dapat ma-access)

---

## 🔧 Troubleshooting

### Problem: 404 Error sa lahat ng pages
**Solution:**
1. Check kung naka-enable ang `mod_rewrite` sa Apache
   - Buksan ang `httpd.conf` sa XAMPP
   - Hanapin ang `#LoadModule rewrite_module modules/mod_rewrite.so`
   - I-uncomment (tanggalin ang `#`)
   - I-restart ang Apache

2. Check kung naka-enable ang `.htaccess` sa Apache
   - Sa `httpd.conf`, hanapin ang `<Directory>` section
   - Siguraduhin na `AllowOverride All` ang naka-set

### Problem: 403 Forbidden
**Solution:**
1. Check ang file permissions
2. Verify na tama ang `.htaccess` syntax
3. Check ang Apache error logs sa XAMPP

### Problem: CSS/JS/Images hindi naglo-load
**Solution:**
1. Check ang `baseURL` sa `app/Config/App.php`
2. Siguraduhin na tama ang paths sa views
3. Check browser console para sa 404 errors

### Problem: Routes hindi gumagana
**Solution:**
1. Verify ang `app/Config/Routes.php`
2. Check kung tama ang `baseURL`
3. Clear ang cache: `php spark cache:clear`

---

## 📝 Important Notes

1. **baseURL** - Naka-set na sa `http://localhost/group6/` (walang `/public/`)
2. **Security** - Protected na ang sensitive folders at files
3. **Backward Compatibility** - Pwede pa rin i-access ang `/public/` directly
4. **Apache mod_rewrite** - Required para sa setup na ito

---

## ✅ Verification Checklist

- [ ] Root URL (`http://localhost/group6/`) gumagana
- [ ] Login page accessible
- [ ] Dashboard accessible pagkatapos mag-login
- [ ] CSS/JS/Images naglo-load correctly
- [ ] Routes gumagana (search, forms, etc.)
- [ ] App folder blocked (403/404)
- [ ] System folder blocked (403/404)
- [ ] Writable folder blocked (403/404)
- [ ] .env file blocked (403/404)

---

## 🚀 Next Steps

Kung lahat ng tests ay passed, ang setup mo ay complete na! Pwede mo nang gamitin ang application nang hindi na kailangan i-type ang `/public/` sa URL.

