# Mumtaz ke liye - Authentication System ka Simple Guide

---

## Mumtaz Ka Kaam Kya Hai?

Mumtaz Lostify application ka **authentication system** develop kiya hai. Iska matlab user registration, login, logout, aur user access control.

Mumtaz ne **8 important files** banaye hain. Har ek file ka apna kaam hai.

---

## File 1: config.php

**Location:** `includes/config.php`

**Kya Karta Hai?**

Ye file application ka **base URL (website ka address)** decide karta hai.

**Simple Explanation:**

```
Jab application localhost par chal raha hai:
├─ Ye file dekhta hai
├─ Localhost detect hota hai
└─ BASE_URL = '/lostify/' set hota hai

Jab application live server par chal raha hai:
├─ Ye file dekhta hai
├─ Live server detect hota hai
└─ BASE_URL = '/' set hota hai
```

**Iska Fayda?**

Links automatically fix ho jate hain. Agar `index.php` link banate hain to:
- Localhost par: `/lostify/index.php` ban jata hai
- Live par: `/index.php` ban jata hai

**Mumtaz Ko Samjhana:**
"Ye file website ke address ko environment ke hisaab se adjust karta hai."

---

## File 2: db.php

**Location:** `includes/db.php`

**Kya Karta Hai?**

Ye file **database se connection** banata hai.

**Simple Explanation:**

```
Jab localhost par chal raha hai:
├─ Local database credentials use karte hain
├─ Username: root
├─ Password: (khali)
└─ Database: lostify

Jab live server par chal raha hai:
├─ Live database credentials use karte hain
├─ Username: if0_42014561
├─ Password: VvqhA0WR6dEN
└─ Database: if0_42014561_lostify
```

**Kya Hota Hai?**

- Database se connection banati hai (`mysqli_connect()`)
- Agar connection fail ho to error message show karta hai
- Successfully connect ho gaya to `$conn` variable mein connection object save hota hai

**Mumtaz Ko Samjhana:**
"Ye file database ke sath application ko connect karta hai. Agar connect nahi hua to program kaam nahi karega."

---

## File 3: register.php

**Location:** `register.php` (root folder)

**Kya Karta Hai?**

Ye file **naye users ko account banane** ke liye form provide karta hai.

**Flow:**

```
1. User register.php kholta hai
   ↓
2. HTML form dikhta hai (Username, Email, Password)
   ↓
3. User form bharta hai aur "Create account" click karta hai
   ↓
4. Data register.php ko POST method se bheja jata hai
   ↓
5. PHP Code yeh karte hain:
   a) Input ko clean karta hai (extra spaces hatata hai)
   b) Special characters ko escape karta hai (SQL injection se bachane ke liye)
   c) 3 validations check karta hai:
      - Sab fields filled hain ya nahi?
      - Email format sahi hai ya nahi?
      - Password 6 characters ya zyada hai ya nahi?
   d) Database mein email pehle se hai ya nahi check karta hai
   e) Password ko hash (encrypt) karta hai
   f) Database mein naya user record insert karta hai
   g) Success message show karta hai
```

**3 Validation Checks:**

```
Check 1: khali fields
  ├─ Username khali? → Error: "All fields required"
  ├─ Email khali? → Error: "All fields required"
  └─ Password khali? → Error: "All fields required"

Check 2: Email format
  ├─ Email sahi format? (abc@gmail.com) → OK
  └─ Email galat format? (abc@gmail) → Error: "Invalid email"

Check 3: Password length
  ├─ 6 ya zyada characters? → OK
  └─ 6 se kam? → Error: "Password too short"
```

**Database Insert:**

```
Username: john_doe
Email: john@gmail.com
Password: secure123 (plain text)
↓ (hashing)
Password: $2y$10$aB3xY9pL... (encrypted)
↓
INSERT into database
```

**Mumtaz Ko Samjhana:**
"Ye file naye users ko account create karne deta hai. Input ko validate karta hai, password ko secure karta hai, aur database mein save karta hai."

---

## File 4: login.php

**Location:** `login.php` (root folder)

**Kya Karta Hai?**

Ye file **existing users ko login** karata hai aur **session create** karta hai.

**Flow:**

```
1. User login.php kholta hai
   ↓
2. Login form dikhta hai (Email, Password, Role selection)
   ↓
3. User email aur password enter karta hai
   ├─ User role select karta hai (User ya Admin)
   └─ "Sign in" button click karta hai
   ↓
4. Data login.php ko POST method se bheja jata hai
   ↓
5. PHP Code yeh karte hain:
   a) Email ko database mein search karta hai
   b) Agar email mila to user record fetch karta hai
   c) Password ko verify karta hai (plain password ko hash se compare)
   d) Role check karta hai (admin select kiya? admin account hai?)
   e) $_SESSION array mein user data store karta hai
   f) User ko appropriate page par redirect karta hai
```

**Password Verification:**

```
User enters: "secure123"
Database mein stored: "$2y$10$aB3xY9pL..."

password_verify("secure123", "$2y$10$aB3xY9pL...")
↓
Ye check karta hai
↓
Match? → YES → Login successful ✓
Match? → NO → Error: "Incorrect password"
```

**Session Creation:**

```
Login successful ho gaya to:

$_SESSION['user_id'] = 5
$_SESSION['username'] = 'john_doe'
$_SESSION['role'] = 'user'

Ab ye data browser ke session cookie mein save hota hai
User ab logged in hai ✓
```

**Redirect Logic:**

```
Agar user admin hai:
└─ Redirect to admin/dashboard.php

Agar user normal user hai:
└─ Redirect to index.php
```

**Mumtaz Ko Samjhana:**
"Ye file users ko login karata hai. Email verify karta hai, password match karta hai, aur session banata hai. Session se user ka logged-in status maintain hota hai."

---

## File 5: logout.php

**Location:** `logout.php` (root folder)

**Kya Karta Hai?**

Ye file **user ko logout** karta hai aur **session destroy** karta hai.

**Flow:**

```
1. User "Logout" button click karta hai
   ↓
2. Browser logout.php par jata hai
   ↓
3. PHP Code:
   a) Session ko start karta hai
   b) session_destroy() se $_SESSION khali kar deta hai
   c) Browser ko login.php par redirect karta hai
   ↓
4. User ab logged out hai
   └─ $_SESSION ke sab data delete ho gaya
```

**Kya Hota Hai Logout Ke Baad:**

```
BEFORE logout:
  $_SESSION['user_id'] = 5
  $_SESSION['username'] = 'john_doe'
  $_SESSION['role'] = 'user'

AFTER session_destroy():
  $_SESSION = [] (khali)
  
User protected pages access nahi kar sakta
```

**Mumtaz Ko Samjhana:**
"Ye file user ka session destroy karta hai. Session destroy hone se user logged out ho jata hai aur koi bhi protected page access nahi kar sakta."

---

## File 6: auth.php

**Location:** `includes/auth.php`

**Kya Karta Hai?**

Ye file **admin pages ko protect** karta hai. Sirf admin users ko access deta hai.

**Flow:**

```
Jab admin page load hota hai (e.g., admin/dashboard.php):

1. Admin page ke top mein: include("includes/auth.php")
   ↓
2. Auth.php yeh checks karta hai:
   
   a) Check 1: User logged in hai ya nahi?
      ├─ $_SESSION['user_id'] exist karta hai?
      ├─ Nahi → Redirect to login.php
      └─ Haan → Next check karo
      
   b) Check 2: User admin role hai ya nahi?
      ├─ $_SESSION['role'] == 'admin'?
      ├─ Nahi → Error: "Access Denied!"
      └─ Haan → Page load ho (admin approved)
```

**2-Level Security:**

```
Level 1: Authentication Check
  "Kya user logged in hai?"
  
Level 2: Authorization Check
  "Kya user admin hai?"

Dono checks pass karna zaroori hai
```

**Example:**

```
Guest tries to access admin page:
└─ No session exist
└─ Redirected to login

Normal user tries to access admin page:
└─ Session exist (user_id exist)
└─ But role = 'user' (not admin)
└─ Error: "Access Denied!"

Admin tries to access admin page:
└─ Session exist (user_id exist)
└─ Role = 'admin'
└─ Access granted! ✓
```

**Mumtaz Ko Samjhana:**
"Ye file admin pages ko protect karta hai. Pehle check karta hai ke user logged in hai, phir check karta hai ke user admin hai. Dono conditions pass karne se hi access milta hai."

---

## File 7: header.php

**Location:** `includes/header.php`

**Kya Karta Hai?**

Ye file **website ke top par header** show karta hai aur **navigation menu** display karta hai. Navigation user ke role ke hisaab se different hota hai.

**Flow:**

```
1. Header.php har page mein include hota hai
   ↓
2. Session check karta hai
   ├─ Session start hai? Haan/Nahi
   └─ Required steps lo
   ↓
3. User ka status check karta hai:
   a) Is admin check: kya user admin hai?
   b) Is logged in check: kya user logged in hai?
   c) Username fetch: username kya hai ya "Guest"
   ↓
4. Unread notifications fetch karta hai (sirf normal users ke liye)
   ├─ Database se query karta hai
   ├─ Unread notifications count nikalta hai
   └─ Badge mein show karta hai
   ↓
5. Navigation menu banata hai (role ke hisaab se):
   
   ADMIN ko:
   ├─ Home
   ├─ Dashboard (Admin)
   ├─ Manage Matches
   └─ Logout
   
   NORMAL USER ko:
   ├─ Home
   ├─ Profile
   ├─ Dashboard
   ├─ Report Lost
   ├─ Report Found
   ├─ Chat
   ├─ Notifications (unread count ke sath)
   └─ Logout
   
   GUEST (not logged in) ko:
   ├─ Home
   ├─ Login
   └─ Register
   ↓
6. HTML header display karta hai:
   ├─ Logo (Lostify)
   ├─ Mobile menu button
   ├─ User greeting ("Hello, username")
   └─ Navigation menu
```

**Key Features:**

```
1. Role-based Navigation
   ├─ Admin different menu dekhe
   ├─ User different menu dekhe
   └─ Guest different menu dekhe

2. Notification Badge
   ├─ Unread notifications count
   ├─ Red badge with number
   └─ Sirf normal users ke liye

3. User Greeting
   ├─ "Hello, john_doe"
   └─ "Hello, Guest" (agar logged in nahi)
```

**Mumtaz Ko Samjhana:**
"Ye file website ke header ko display karta hai. User ke role ke hisaab se different navigation menu show karta hai. Admin ko admin menu dikhega, user ko user menu dikhega."

---

## File 8: footer.php

**Location:** `includes/footer.php`

**Kya Karta Hai?**

Ye file **HTML ko close** karta hai aur **mobile menu** ke liye JavaScript provide karta hai.

**Flow:**

```
1. HTML ko close karta hai
   ├─ </main> tag
   └─ </div> tags
   ↓
2. JavaScript code provide karta hai:
   
   Mobile Menu Functionality:
   
   a) Hamburger button click:
      ├─ Menu ko show/hide toggle karta hai
      └─ 'active' class add/remove hota hai
      
   b) Menu link click:
      ├─ Menu automatically close ho jata hai
      └─ User kisi page par ja jaata hai
      
   c) Outside click:
      ├─ Menu ke bahar click kare
      └─ Menu close ho jata hai
```

**Mobile Menu Code:**

```javascript
// Hamburger button click
menuToggle.addEventListener('click', function(){
    // Menu toggle hota hai (show/hide)
});

// Menu link click
sidebarLinks.forEach(link => {
    // Menu close ho jata hai
});

// Document click (outside)
document.addEventListener('click', function(event){
    // Agar menu open hai aur outside click hua
    // To menu close ho jata hai
});
```

**Mumtaz Ko Samjhana:**
"Ye file HTML ko properly close karta hai aur mobile menu ko interactive banata hai. Mobile par hamburger icon se menu open/close ho sakta hai."

---

## Quick Summary

| File | Location | Kya Karta Hai |
|------|----------|--------------|
| **config.php** | includes/ | Environment ke hisaab se BASE_URL set karta hai |
| **db.php** | includes/ | Database se connection banata hai |
| **register.php** | root | Naye users ko signup karne deta hai |
| **login.php** | root | Users ko login karata hai aur session banata hai |
| **logout.php** | root | User ko logout karta hai aur session destroy karta hai |
| **auth.php** | includes/ | Admin pages ko protect karta hai |
| **header.php** | includes/ | Header aur navigation menu display karta hai |
| **footer.php** | includes/ | Footer aur mobile menu JavaScript provide karta hai |

---

## Mumtaz Ka Presentation Flow

**Step 1: Registration Explain Karo**
"Ye register.php file hai. New users apna account banate hain. Username, email, password enter karte hain. 3 checks hote hain - sab fields filled hain? Email format sahi hai? Password 6 characters hai? Password ko encrypt karte hain aur database mein save karte hain."

**Step 2: Login Explain Karo**
"Ye login.php file hai. Users apna email aur password enter karte hain. Database mein search hota hai. Password verify hota hai. Agar match ho gaya to session banota hai. Session ka matlab user logged in ho gaya. Browser mein session cookie save hota hai."

**Step 3: Protection Explain Karo**
"Ye auth.php file hai. Admin pages ko protect karta hai. 2 checks hote hain - pehle check karte hain ke user logged in hai, phir check karte hain ke user admin hai. Dono pass karne se hi admin page open hota hai."

**Step 4: Navigation Explain Karo**
"Ye header.php file hai. Different users ke liye different menu dikhata hai. Admin ko admin menu, user ko user menu, guest ko login menu dikhata hai."

**Step 5: Logout Explain Karo**
"Ye logout.php file hai. User logout button click kare to session destroy ho jata hai. User logged out ho jata hai."

---

## Important Security Points

**1. Password Hashing**
"Password ko plain text mein save nahi karte. Encrypt (hash) karte hain. Agar database leak ho to password safe rehta hai."

**2. SQL Injection Prevention**
"User input ke dangerous characters ko escape karte hain. Taak hacker database ke sath khilwari nahi kar sake."

**3. Session Security**
"Session cookie mein password nahi bhejte. Sirf session ID rehta hai. Browser ke sath har request mein session ID bhejta hai. Server check karta hai aur user ko recognize karta hai."

---

## Common Questions During Presentation

**Q: Password ko hash kyu karte hain?**
A: "Agar database leak ho to hackers ko sirf hash dikhega, actual password nahi. Password recover nahi kar sakte."

**Q: Session ka kya fayda hai?**
A: "Login ke baad password bar-bar nahi bhejte. Session ID se user ko recognize karte hain. Safer hai."

**Q: Admin access ko kaise protect karte hain?**
A: "auth.php file se. 2 checks hote hain - logged in? Admin? Dono zaroori hain."

**Q: Different users ko different menu kaise dikhate hain?**
A: "header.php mein $_SESSION['role'] check karte hain. Admin hai to admin menu, user hai to user menu."

---

## Database Table Structure

```
users table mein yeh data save hota hai:

id (1, 2, 3...)
├─ User ka unique number

username (john_doe, jane_smith...)
├─ User ka name (unique)

email (john@gmail.com...)
├─ User ka email (unique)

password (hashed value)
├─ Encrypted password

role (admin / user)
├─ User ka role
```

---

## Final Note for Mumtaz

**Ye 8 files ek saath kaam karte hain:**

1. **config.php** → URLs fix karta hai
2. **db.php** → Database connect karta hai
3. **register.php** → New account banata hai
4. **login.php** → Users ko login karata hai
5. **logout.php** → Users ko logout karta hai
6. **auth.php** → Admin pages protect karta hai
7. **header.php** → Navigation show karta hai
8. **footer.php** → Mobile menu handle karta hai

**Sab files secure aur well-structured hain. Presentation ke liye ye simple points use kar. Judges ko samajh aayega.**

---

**Good Luck Mumtaz! 🎯**

