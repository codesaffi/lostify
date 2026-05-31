# LOSTIFY PROJECT - CODE GUIDE (ROMAN URDU MEIN SAMJHAO)

---

## PEHLI BAAT - YEH SAMJHO

Yeh 4 files hain jo LOSTIFY application ke core pages hain. Har file ka apna kaam hai:
- **profile.php** - User ka profile page
- **notifications.php** - User ke notifications 
- **leaderboard.php** - Top users ki list
- **dashboard.php** - Welcome page jab user login ho

Har file mein 2 parts hote hain:
1. **PHP Backend** - Database se data laana aur process karna
2. **HTML Frontend** - Data ko page par display karna

---

## FILE 1: PROFILE.PHP (MERI PROFILE WALA PAGE)

### KAHAANI (OVERVIEW):
Profile.php woh page hai jahan user apni profile dekh sakta hai. Apni picture upload kar sakta hai, apne points dekh sakta hai, aur apne past lost/found items dekh sakta hai.

---

### STEP BY STEP EXPLANATION:

#### **STEP 1: SESSION SHURU KARO**
```
session_start();
include("includes/config.php");
include("includes/db.php");
```
**Matlab kya hai:**
- `session_start()` - User ke browser ko remember karo (cookies use hoti hain)
- `include("includes/config.php")` - App ki settings load karo (database ka naam, server ka naam)
- `include("includes/db.php")` - Database se connection setup karo

**Asan lafzo mein:** Jab user website par aata hai to uska data memory mein store hota hai. Isse pata chalta hai ke ye konsa user hai.

---

#### **STEP 2: LOGIN CHECK (SECURITY)**
```
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
```
**Matlab kya hai:**
- `if(!isset($_SESSION['user_id']))` - Check karo ke user login hai ya nahi
- `header("Location: login.php")` - Agar nahi login hai to login page par bhej do
- `exit()` - Baaki code ko mat chalao, yahi par ruko

**Asan lafzo mein:** Agar koi directly URL mein "profile.php" likhta hai to pehle login karna padega. Bina login ke profile nahi dekh sakta.

---

#### **STEP 3: USER KI ID NIKAL LO**
```
$user_id = $_SESSION['user_id'];
```
**Matlab kya hai:**
- Session se user ki ID nikal lo aur `$user_id` variable mein store karo
- Ab is ID se database mein us user ka data search kar sakte ho

**Asan lafzo mein:** Jaise har person ka ID number hota hai, waise hi har user ko ek unique ID mila hota hai.

---

#### **STEP 4: DATABASE SE USER KA DATA LAAO**
```
$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($userQuery);
```
**Matlab kya hai:**
- `mysqli_query()` - Database ko query bhejo (poocho)
- `SELECT * FROM users` - Users table se sab columns select karo
- `WHERE id='$user_id'` - Sirf usi user ka data jo match kare
- `mysqli_fetch_assoc()` - Query ke jawab ko array mein convert karo

**Asan lafzo mein:** Database se jao, us user ka poora data nikalo jo login hai, aur us data ko array mein rakho taako use kar sako.

**Array matlab kya hai:**
```
$user = [
    'username' => 'ali',
    'email' => 'ali@gmail.com',
    'points' => 100,
    'reputation' => 50,
    'badge' => 'Gold',
    'profile_pic' => 'image.jpg'
]
```

---

#### **STEP 5: MESSAGE VARIABLE BANAO**
```
$message = "";
```
**Matlab kya hai:**
- Empty message variable banao
- Jab user picture upload kare to success ya error message dikhega is mein

---

#### **STEP 6: PROFILE PICTURE UPLOAD LOGIC**

**A) UPLOAD BUTTON DABA HAI KYA?**
```
if(isset($_POST['uploadPic'])){
```
**Matlab:** Jab user upload button click kare, yeh code chalega.

**B) FILE PROPERLY UPLOAD HUI?**
```
if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK){
```
**Matlab:** 
- `$_FILES['profile_pic']` - Upload ki hui file
- `['error'] === UPLOAD_ERR_OK` - Koi error to nahi aya?

**C) ALLOWED FILE TYPES DEFINE KARO**
```
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
$allowedMimeTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif'
];
```
**Matlab:** Sirf ye format ki files allow karo - JPG, PNG, GIF

**Kyon dono arrays?**
- `$allowedExtensions` - File ka extension check karte ho (.jpg, .png)
- `$allowedMimeTypes` - File ka actual type check karte ho (security ke liye)

**Kyon security important hai?** Agar koi file ko .jpg naam de de par actually wo virus file hai, to security risk hai. Isliye actual file type check karte ho.

**D) FILE KA SIZE CHECK KARO**
```
$fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$maxSize = 3 * 1024 * 1024; // 3 MB
```
**Matlab:**
- `pathinfo()` - File name se extension nikalo
- `strtolower()` - Extension ko chota karo (JPG -> jpg)
- `$maxSize = 3 * 1024 * 1024` - 3 MB se zyada file nahi

**E) FILE KI ACTUAL TYPE CHECK KARO**
```
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
```
**Matlab:**
- `finfo_open()` - File info tool kholo
- `finfo_file()` - File ki actual type dekho
- `finfo_close()` - Tool ko band karo

**Analogy:** Jaise passport mein likha hota hai ke ye document kaunsa hai, waise file bhi apni "MIME type" batayi hoti hai.

**F) VALIDATION CHECKS**
```
if(!array_key_exists($mimeType, $allowedMimeTypes)){
    $message = "Only JPG, JPEG, PNG and GIF images are allowed.";
}
elseif($file['size'] > $maxSize){
    $message = "Image size must be 3MB or less.";
}
```
**Matlab:**
- `array_key_exists()` - Check karo ke file type humari list mein hai ya nahi
- Agar nahi hai to error message dikhao
- Agar size zyada hai to error message dikhao

**G) UNIQUE FILENAME BANAO**
```
$newFileName = $user_id . '_' . time() . '.' . $fileExtension;
$destination = __DIR__ . '/assets/uploads/' . $newFileName;
```
**Matlab:**
- `$user_id . '_' . time()` - User ID + current time (unique name banane ke liye)
- Example: `5_1700000000.jpg`
- `__DIR__` - Current folder ka path
- `destination` - Jahan file save hona hai

**Kyon unique name?** Agar 2 users ali@gmail.com upload kren to same name se overwrite ho jayga. Isliye unique names banate ho.

**H) FILE UPLOAD KARO**
```
if(move_uploaded_file($file['tmp_name'], $destination)){
```
**Matlab:**
- Temporary folder se permanent folder mein file move karo
- `move_uploaded_file()` - Special function hai file move karne ke liye

**I) PURAANI FILE DELETE KARO**
```
if(!empty($user['profile_pic']) && $user['profile_pic'] !== 'default.png'){
    $oldPath = __DIR__ . '/assets/uploads/' . $user['profile_pic'];
    if(file_exists($oldPath)){
        @unlink($oldPath);
    }
}
```
**Matlab:**
- Agar pehle se koi profile pic the to delete karo
- `file_exists()` - Check karo ke file exist karta hai ya nahi
- `@unlink()` - File ko delete karo (@ matlab error dikhao mat)

**Kyon delete karte ho?** Taako server ka space waste na ho. Har user sirf apni latest photo rakh sakta hai.

**J) DATABASE UPDATE KARO**
```
mysqli_query($conn, "UPDATE users SET profile_pic='$newFileName' WHERE id='$user_id'");
```
**Matlab:**
- Database mein jao
- Users table mein us user ka profile_pic update karo
- New filename store karo

**K) SUCCESS MESSAGE DIKHAO**
```
$message = "Profile picture uploaded successfully.";
```

**L) USER DATA REFRESH KARO**
```
$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($userQuery);
```
**Matlab:** Database se phir se user ki information le lo taako new picture page par dikhe.

---

#### **STEP 7: LOST ITEMS QUERY**
```
$lostQuery = mysqli_query($conn, 
    "SELECT * FROM lost_items WHERE user_id='$user_id' ORDER BY created_at DESC"
);
```
**Matlab:**
- Is user ne jo lost items report kiye hain unko database se nikalo
- `ORDER BY created_at DESC` - Sabse nayi items pehle aayein

---

#### **STEP 8: FOUND ITEMS QUERY**
```
$foundQuery = mysqli_query($conn, 
    "SELECT * FROM found_items WHERE user_id='$user_id' ORDER BY created_at DESC"
);
```
**Matlab:** Is user ne jo found items report kiye hain unko database se nikalo.

---

#### **STEP 9: RESOLVED CASES QUERY**
```
$resolvedCaseQuery = mysqli_query($conn, "
    SELECT ... FROM matches
    JOIN lost_items ...
    JOIN found_items ...
    WHERE matches.status='returned'
    AND (lost_items.user_id='$user_id' OR found_items.user_id='$user_id')
");
```
**Matlab:**
- Un cases ko nikalo jo RESOLVED (completed) ho chuke hain
- `JOIN` - Multiple tables ko connect karo
- Jahan user ne lost item report kiya ya found item report kiya
- Sabse nayi resolved cases pehle aayein

**JOINS KA MATLAB:**
```
Matches table se start karte ho
↓
Lost items ka data add karte ho
↓
Found items ka data add karte ho
↓
Lost user ka name add karte ho
↓
Found user ka name add karte ho

Ab ek complete picture ban jayti hai!
```

---

#### **STEP 10: HTML DISPLAY**

**A) PROFILE PICTURE DISPLAY**
```
$profilePic = 'assets/images/default.png';
if(!empty($user['profile_pic']) && $user['profile_pic'] !== 'default.png'){
    $profilePic = 'assets/uploads/' . $user['profile_pic'];
}
<img src="<?php echo $profilePic; ?>" class="profile-pic" alt="Profile Picture">
```
**Matlab:**
- Agar user ne picture upload ki hai to wo display karo
- Agar nahi ki to default picture dikhao

**B) USER KA NAAM AURA EMAIL**
```
<h1><?php echo $user['username']; ?></h1>
<p><?php echo $user['email']; ?></p>
```
**Matlab:** Database se jo data laya tha us array se username aur email nikalo aur page par likho.

**C) UPLOAD FORM**
```
<form action="" method="POST" enctype="multipart/form-data">
    <input type="file" name="profile_pic" accept="image/*">
    <button type="submit" name="uploadPic">Upload</button>
</form>
```
**Matlab:**
- Form banao file upload karne ke liye
- `enctype="multipart/form-data"` - File upload karne ke liye zaroori
- `accept="image/*"` - Sirf images select kar sakte ho

**D) USER STATS (POINTS, REPUTATION, BADGE)**
```
<h2>⭐ <?php echo $user['points']; ?></h2>
<h2>🔥 <?php echo $user['reputation']; ?></h2>
<h2>🏆 <?php echo $user['badge']; ?></h2>
```
**Matlab:** Database se user ki stats nikalo aur emojis ke sath display karo.

**E) RESOLVED CASES LOOP**
```
<?php while($case = mysqli_fetch_assoc($resolvedCaseQuery)){ ?>
    <!-- Display lost item -->
    <!-- Display found item -->
    <!-- Display users involved -->
<?php } ?>
```
**Matlab:** har resolved case ke liye ek card banao jisme lost item, found item, aur involved users dikhao.

**F) LOST ITEMS HISTORY LOOP**
```
<?php while($lost = mysqli_fetch_assoc($lostQuery)){ ?>
    <h3><?php echo $lost['title']; ?></h3>
    <p><?php echo $lost['description']; ?></p>
    <small><?php echo $lost['created_at']; ?></small>
<?php } ?>
```
**Matlab:** Is user ke sab lost items dikhao - title, description, date.

**G) FOUND ITEMS HISTORY LOOP**
```
<?php while($found = mysqli_fetch_assoc($foundQuery)){ ?>
    <h3><?php echo $found['title']; ?></h3>
    <p><?php echo $found['description']; ?></p>
    <small><?php echo $found['created_at']; ?></small>
<?php } ?>
```
**Matlab:** Is user ke sab found items dikhao.

---

## FILE 2: NOTIFICATIONS.PHP (SOOCHIYON WALA PAGE)

### KAHAANI (OVERVIEW):
Jab koi notification user ko aye, wo sab isi page mein dikha da. Jaise agar uska lost item ko match mile to notification aaye.

---

### STEP BY STEP:

#### **STEP 1: SESSION AUR DATABASE SETUP**
```
session_start();
include("includes/config.php");
include("includes/db.php");
```
**Matlab:** Profile.php ki tarah hi - user ko identify karo aur database se connect karo.

---

#### **STEP 2: LOGIN CHECK**
```
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
```
**Matlab:** Bina login ke notifications nahi dekh sakta.

---

#### **STEP 3: USER ID NIKAL LO**
```
$user_id = $_SESSION['user_id'];
```

---

#### **STEP 4: NOTIFICATIONS DATABASE SE LAAO**
```
$query = "SELECT * FROM notifications WHERE user_id='$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
```
**Matlab:**
- Is user ke liye notifications table mein se sab notification nikalo
- `ORDER BY created_at DESC` - Sabse nayi notifications pehle
- Result ko `$result` variable mein store karo

---

#### **STEP 5: SABA NOTIFICATIONS KO "READ" MARK KARO**
```
mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE user_id='$user_id'");
```
**Matlab:**
- Jab user notifications page kholta hai to sab notifications ko read mark kar do
- `is_read=1` matlab is notification ko pad liya gaya
- Isse notification badge disappear ho jayti hai

---

#### **STEP 6: HTML DISPLAY**

**A) NOTIFICATIONS KO LOOP MEIN CHALAO**
```
<?php while($row = mysqli_fetch_assoc($result)){ ?>
```
**Matlab:** Database se jo notifications aaye hain unhe ek ek karke display karo.

**B) NOTIFICATION TYPE DISPLAY**
```
<h3><?php echo ucfirst($row['type']); ?></h3>
```
**Matlab:**
- Notification ki type dikha do (jaise "match", "new_message")
- `ucfirst()` - Pehla letter capital karo

**C) NOTIFICATION MESSAGE**
```
<p><?php echo $row['message']; ?></p>
```
**Matlab:** Notification ka message dikha do (jo message database mein store hai).

**D) TIMESTAMP DIKHAO**
```
<small><?php echo $row['created_at']; ?></small>
```
**Matlab:** Notification kab ka hai ye dikhao.

---

## FILE 3: LEADERBOARD.PHP (RANKING WALA PAGE)

### KAHAANI (OVERVIEW):
Yeh page top 10 users ko show karta hai jo sabse zyada points rakhte hain. Ek gamification feature hai jo users ko motivated karta hai.

---

### STEP BY STEP:

#### **STEP 1: SESSION AUR DATABASE SETUP**
```
session_start();
include("includes/config.php");
include("includes/db.php");
```

---

#### **STEP 2: TOP 10 USERS DATABASE SE LAAO**
```
$query = mysqli_query($conn, 
    "SELECT username, points, reputation, badge FROM users 
     ORDER BY points DESC LIMIT 10"
);
```
**Matlab:**
- `SELECT username, points, reputation, badge` - Sirf ye 4 columns chahiye
- `ORDER BY points DESC` - Points ke hisaab se sort karo (highest first)
- `LIMIT 10` - Sirf top 10 users

**ORDER BY DESC matlab:**
```
User A - 500 points
User B - 400 points
User C - 300 points
User D - 200 points
...
User J - 50 points
```

**LIMIT 10 matlab:** Sirf 10 rows dikhao, baki 90 ignore karo.

---

#### **STEP 3: HTML DISPLAY**

**A) RANK VARIABLE INITIALIZE KARO**
```
$rank = 1;
```
**Matlab:** Rank 1 se start karenge, phir har user ke liye 1 badhate jayenge.

**B) USERS KO LOOP MEIN CHALAO**
```
<?php while($row = mysqli_fetch_assoc($query)){ ?>
```
**Matlab:** Database se jo 10 users aaye hain unhe ek ek karke display karo.

**C) RANK DISPLAY**
```
<div class="leaderboard-rank">
    #<?php echo $rank; ?>
</div>
```
**Matlab:** #1, #2, #3 ... #10 display karo.

**D) USER INFO DISPLAY**
```
<h2><?php echo $row['username']; ?></h2>
<p>🏆 <?php echo $row['badge']; ?></p>
```
**Matlab:** User ka naam aur badge dikhao.

**E) STATS DISPLAY**
```
<h3>⭐ <?php echo $row['points']; ?></h3>
<small>🔥 Reputation: <?php echo $row['reputation']; ?></small>
```
**Matlab:** User ke points aur reputation dikhao.

**F) RANK INCREMENT**
```
<?php $rank++; } ?>
```
**Matlab:** Loop ke end mein rank ko 1 se badhao (1 -> 2 -> 3 ...)

---

## FILE 4: DASHBOARD.PHP (WELCOME PAGE)

### KAHAANI (OVERVIEW):
Jab user successfully login kare to ye page aata hai. Bas ek welcome message aur logout button hota hai.

---

### STEP BY STEP:

#### **STEP 1: SESSION SHURU KARO**
```
session_start();
include("includes/config.php");
```
**Matlab:** User ko identify karo (database setup nahi chahiye kyon ke sirf welcome message dikhana hai).

---

#### **STEP 2: LOGIN CHECK**
```
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
```
**Matlab:** Bina login ke dashboard nahi dekh sakta.

---

#### **STEP 3: WELCOME MESSAGE**
```
<h1>Welcome, <?php echo $_SESSION['username']; ?> 👋</h1>
```
**Matlab:**
- Session se user ka username nikalo
- "Welcome, Ali 👋" display karo

---

#### **STEP 4: LOGOUT BUTTON**
```
<a href="logout.php">
    <button>Logout</button>
</a>
```
**Matlab:** Button click kare to logout.php par jao, jahan logout process hota hai.

---

## IMPORTANT CONCEPTS SAMJHAO:

### 1. SESSION KYA HOTA HAI?
```
Session = User ke browser mein temporary memory
Jab login karte ho to:
$_SESSION['user_id'] = 5
$_SESSION['username'] = 'ali'

Ab jo bhi page kholo, ye data available rahe ga
Jab logout karte ho to clear ho jayga
```

### 2. DATABASE QUERY KA FLOW:
```
PHP Code → mysqli_query() → Database
                              ↓
                        Process query
                              ↓
                        Return result
                              ↓
mysqli_fetch_assoc() → PHP array mein convert
```

### 3. ARRAYS KA MATLAB:
```
$user = [
    'id' => 5,
    'username' => 'ali',
    'email' => 'ali@gmail.com'
]

// Access karna:
echo $user['username'];  // Output: ali
echo $user['email'];     // Output: ali@gmail.com
```

### 4. LOOPS KA MATLAB:
```
Database se 10 results aaye hain

<?php while($row = mysqli_fetch_assoc($query)){ ?>
    // Ye code 10 baar chalega
    // Har baar $row ko alag data miilega
<?php } ?>
```

### 5. CONDITIONAL STATEMENTS:
```
if (condition) {
    // Agar condition true hai to ye chalega
} elseif (aur condition) {
    // Ya ye
} else {
    // Ya ye
}
```

---

## DATABASE STRUCTURE SAMJHAO:

### USERS TABLE:
```
id | username | email | password | points | reputation | badge | profile_pic
5  | ali      | ... | ...      | 100    | 50        | Gold  | 5_1700.jpg
```

### LOST_ITEMS TABLE:
```
id | user_id | title | description | category | last_seen_location | image | created_at
1  | 5       | Watch| Gold watch  | Jewelry  | Market              | ...   | 2024-01-15
```

### FOUND_ITEMS TABLE:
```
id | user_id | title | description | category | found_location | image | created_at
1  | 3       | Watch| Gold watch  | Jewelry  | Park           | ...   | 2024-01-15
```

### MATCHES TABLE:
```
id | lost_item_id | found_item_id | match_score | status | resolved_at
1  | 1            | 1             | 95          | returned | 2024-01-20
```

### NOTIFICATIONS TABLE:
```
id | user_id | type | message | is_read | created_at
1  | 5       | match| Your lost item... | 1 | 2024-01-20
```

---

## IMPORTANT FUNCTIONS KA MATLAB:

| Function | Matlab |
|----------|--------|
| `session_start()` | Session ko enable karo |
| `mysqli_query()` | Database ko query bhejo |
| `mysqli_fetch_assoc()` | Query result ko array mein convert karo |
| `mysqli_num_rows()` | Kitne rows return hue? |
| `htmlspecialchars()` | Special characters ko safe form mein convert karo (security) |
| `move_uploaded_file()` | Upload ki hui file ko destination par move karo |
| `file_exists()` | Check karo ke file exist karta hai |
| `unlink()` | File ko delete karo |
| `header()` | Browser ko alag page par redirect karo |
| `exit()` | Script ko yahi par stop kar do |
| `ucfirst()` | String ka pehla letter capital karo |
| `strtolower()` | String ko lowercase mein convert karo |
| `pathinfo()` | File path se information nikalo |
| `finfo_open()` / `finfo_file()` | File ki actual MIME type check karo |

---

## SECURITY CONCEPTS:

### 1. SQL INJECTION SE BACHAO:
**GALAT:**
```php
$query = "SELECT * FROM users WHERE username='" . $_GET['username'] . "'";
```

**THEEK:**
```php
$query = "SELECT * FROM users WHERE username='$username'";
// Ya use prepared statements
```

### 2. FILE UPLOAD SECURITY:
```php
// 1. File type check karo
// 2. File size check karo
// 3. MIME type check karo (na ke sirf extension)
// 4. Unique name de do
// 5. Virus scan karo (advanced)
```

### 3. XSS (CROSS SITE SCRIPTING) SE BACHAO:
```php
// GALAT:
echo $user['message'];

// THEEK:
echo htmlspecialchars($user['message']);
```

---

## ERROR HANDLING:

```php
// Check karo ke query execute hui ya error aya
if($result) {
    // Success - data display karo
} else {
    // Error - error message dikhao
    echo "Error: " . mysqli_error($conn);
}

// Check karo ke rows exist karti hain
if(mysqli_num_rows($result) > 0) {
    // Data show karo
} else {
    // "No results found" dikhao
}
```

---

## SUMMARY - SARKA FINAL SAMJHAO:

### Profile.php:
✅ User ki profile dikhata hai
✅ Profile picture upload feature
✅ User ki stats (points, reputation, badge)
✅ Lost items history
✅ Found items history
✅ Resolved cases (successful matches)

### Notifications.php:
✅ User ke sab notifications dikhata hai
✅ Sabse nayi notification pehle
✅ Jab page kholo to sab read mark ho jayein

### Leaderboard.php:
✅ Top 10 users dikhata hai
✅ Points ke hisaab se ranking
✅ Har user ke badge aur reputation
✅ Gamification feature

### Dashboard.php:
✅ Login ke baad welcome page
✅ User ka naam dikhata hai
✅ Logout button

---

## PRESENTATION TIPS:

1. **Session Samjhao:**
   - "Session matlab temporary memory in browser"
   - "Login karte ho to user id store hoti hai"
   - "Isse browser ko pata chalta hai ke ye user who hai"

2. **Database Samjhao:**
   - "Database ek big spreadsheet like hota hai"
   - "Tables mein rows aur columns hote hain"
   - "Query matlab database se soocha dialna"

3. **Security Samjhao:**
   - "File upload mein sirf extension check nahi karte"
   - "Actual file type check karte hain"
   - "Taako koi virus file upload na kar de"

4. **Loops Samjhao:**
   - "While loop matlab jab tak condition true hai tab tak chalao"
   - "10 notifications hain to 10 baar loop chalega"
   - "Har baar alag notification dikha jayga"

---

**YEH GUIDE COMPLETE HAI. AB WORD FILE MEIN COPY KARO AUR MUNEEB KO SEND KARO!**
