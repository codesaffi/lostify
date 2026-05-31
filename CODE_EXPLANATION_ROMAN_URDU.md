# Lostify Project - 4 Files Explanation (Roman Urdu)

---

## 1. INDEX.PHP - Resolved Cases Page

### Ye File Kya Hai?
Ye file Lostify app ka main page hai jis par wo items dikhaye jate hain jo **successfully wapas mil chuke hain** (resolved cases). Jab koi lost item aur found item successfully match ho jaayein aur item return ho jaye, to ye page un dono ka success story show karta hai.

### File Ka Maqsad (Purpose):
- Resolved/returned items ko display karna
- Users ko inspire karna ke "dekho itne items wapas mil chuke hain"
- Lost items aur found items ke comparison dikhana

---

### Code Ka Flow (Kadam be Kadam)

#### **Step 1: Database Connection**
```
include("includes/config.php");
include("includes/db.php");
```
- Pehle database se connection setup karte hain
- Config file se BASE_URL aur settings milti hain

#### **Step 2: Database Query - Data Lena**
```
$resolvedQuery = mysqli_query($conn, "SELECT ...")
```

Ye query kya karta hai:
1. **matches table** se `status='returned'` wali entries leni hain (jo items wapas ho gaye)
2. **lost_items table** se match karne wali details (title, description, image, etc.)
3. **found_items table** se match karne wali details (title, description, image, etc.)
4. **users table** se dono users ke usernames (lost_user aur found_user)

Simple lawaazi mein:
- "Mujhe sab returned items de jo database mein hain"
- "Har returned item ke liye mujhe tell karo ke lost item ka kya naam tha"
- "Har returned item ke liye mujhe tell karo ke found item ka kya naam tha"
- "Aur unhe jo users ne report kiya tha unke usernames bhi include karo"

#### **Step 3: HTML Page Banaina**
```
<!DOCTYPE html>
<html>
```
- Ek normal HTML page banate hain jo browser mein display ho

#### **Step 4: Navbar Dikhana (Header)**
```
<?php include("includes/header.php"); ?>
```
- Top par navigation bar show hota hai (Home, Login, Profile, etc.)

#### **Step 5: Hero Section - Welcome Message**
```
<div class="resolved-hero">
    <h1>Resolved Cases</h1>
    <p>Successfully returned lost items from the Lostify community.</p>
</div>
```
- Ek attractive banner banate hain jo title show kare
- Agar user login nahi hai to "Login" aur "Register" buttons dikhate hain

#### **Step 6: Cases Check - Kya Data Hai?**
```
<?php if(mysqli_num_rows($resolvedQuery) === 0){ ?>
    <div class="empty-chat-state large">
        No resolved cases yet.
    </div>
<?php } ?>
```
- Agar database mein koi resolved cases nahi hain to message dikhate hain
- Agar hain to aage ka code chalega

#### **Step 7: Loop - Har Case ka Card Banana**
```
<?php while($case = mysqli_fetch_assoc($resolvedQuery)){ 
```
- Database se har resolved case ko ek-ek karke nikaalte hain
- Har case ke liye ek "card" banate hain

#### **Step 8: Image Path Check Karna**
```
$lostImage = !empty($case['lost_image']) ? "assets/uploads/" . $case['lost_image'] : "";
$foundImage = !empty($case['found_image']) ? "assets/uploads/" . $case['found_image'] : "";
```
- Check karte hain ke image exist karta hai ya nahi
- Agar image hai to path banate hain "assets/uploads/" + filename
- Agar nahi hai to empty string

#### **Step 9: Card Ka HTML Structure**
```
<article class="resolved-card">
    <div class="resolved-card-header">
        <span class="status-pill">Resolved</span>
        <strong>80% match</strong>
    </div>
    
    <div class="resolved-pair">
        <!-- Lost Item Section -->
        <section>
            <h2>Lost Report</h2>
            <img src="..."> (agar image hai)
            <h3>Item Title</h3>
            <p>Description</p>
        </section>
        
        <!-- Found Item Section -->
        <section>
            <h2>Found Report</h2>
            <img src="..."> (agar image hai)
            <h3>Item Title</h3>
            <p>Description</p>
        </section>
    </div>
    
    <div class="resolved-meta">
        <span>Lost by @username1</span>
        <span>Found by @username2</span>
        <span>Resolved date</span>
    </div>
</article>
```

Card mein ye dikhta hai:
- Lost item ki image
- Lost item ka title aur description
- Lost item ki category aur last seen location
- Found item ki image
- Found item ka title aur description
- Match percentage
- Dono users ke names

#### **Step 10: Loop Khatam**
```
<?php } ?>
```
- Jab sab cases display ho jayein to loop khatam

### Index.php Ka Summary:
- **Kaam**: Resolved/returned items ko ek attractive grid format mein show karna
- **Data Source**: Database se `matches` table (status='returned') 
- **Display**: Lost aur found items ke side-by-side comparison cards
- **Security**: `htmlspecialchars()` use kar ke dangerous characters ko block karte hain

---

## 2. ADMIN/MANAGE-MATCHES.PHP - Match Review Panel

### Ye File Kya Hai?
Ye file **sirf admin ke liye** hai. Admin yahan par pending matches ko review karke approve, reject, ya returned mark karte hain. Jab normal users lost item ya found item report karte hain, to system automatically similar items ko match karta hai aur admin ko review karne ke liye pending matches deta hai.

### File Ka Maqsad:
- Admin ko pending matches dikhana
- Admin ko approve/reject/returned options dena
- Match quality dikhana (80% match, etc.)
- Notifications bhejni (jab approve ho)
- Points reward karna (jab item return ho)

---

### Code Ka Flow

#### **Step 1: Includes & Auth Check**
```
include("../includes/config.php");
include("../includes/auth.php");
include("../includes/db.php");
include("../includes/functions.php");
```
- Config, auth, database, aur helper functions load karte hain
- Auth file check karta hai ke logged-in user admin hai ya nahi

#### **Step 2: APPROVE ACTION - Jab Admin Approve Kare**
```
if(isset($_GET['approve'])){
    $id = (int) $_GET['approve'];
    
    // Database update
    mysqli_query($conn, "UPDATE matches SET status='approved' WHERE id='$id'");
    
    // Get user IDs
    $getUsers = mysqli_query($conn, "SELECT lost_items.user_id, found_items.user_id...");
    $users = mysqli_fetch_assoc($getUsers);
    
    // Send notifications to both users
    createNotification($conn, $users['lost_user'], "Admin approved your match. Chat is now enabled.", "approval");
    createNotification($conn, $users['found_user'], "Admin approved your match. Chat is now enabled.", "approval");
}
```

Ye kya karte hain:
1. **URL se ID lena**: `?approve=5` (match number 5)
2. **Database update**: Us match ki status ko "approved" kar do
3. **Users ko find karna**: Ke ye match kis 2 users ke beech mein hai
4. **Notifications bhejni**: Dono users ko message bhejo ke "Admin ne approve kar diya"
5. **Effect**: Ab dono users ek doosre se chat kar sakte hain

#### **Step 3: RETURNED ACTION - Jab Admin "Mark Returned" Click Kare**
```
if(isset($_GET['returned'])){
    $id = (int) $_GET['returned'];
    
    // Update status
    mysqli_query($conn, "UPDATE matches SET status='returned' WHERE id='$id'");
    
    // Get finder user
    $query = mysqli_query($conn, "SELECT found_items.user_id AS finder...");
    $data = mysqli_fetch_assoc($query);
    
    // Reward finder with 50 points
    rewardUser($conn, $data['finder'], 50);
    
    // Send notification
    createNotification($conn, $data['finder'], "Congratulations! You earned 50 points...", "reward");
}
```

Ye kya karte hain:
1. **Status update**: Match ko "returned" status do
2. **Finder find karna**: Ke kis user ne item find kiya tha
3. **Points reward**: Finder ko 50 points award karo
4. **Badge update**: Agar user 50+ points ka hai to "Trusted Finder" badge de do
5. **Notification**: Finder ko message bhejo ke "Congratulations! You earned points"

#### **Step 4: REJECT ACTION - Jab Admin Reject Kare**
```
if(isset($_GET['reject'])){
    $id = (int) $_GET['reject'];
    
    mysqli_query($conn, "UPDATE matches SET status='rejected' WHERE id='$id'");
}
```

Ye kya karte hain:
- Match status ko "rejected" kar do
- zyada complex kuch nahi, sirf status change

#### **Step 5: Main Query - Sab Matches Nikalna**
```
$query = "SELECT matches.*, lost_items.*, found_items.*, 
         lost_user.username, found_user.username 
         FROM matches 
         JOIN lost_items ON matches.lost_item_id = lost_items.id 
         JOIN found_items ON matches.found_item_id = found_items.id 
         JOIN users AS lost_user...
         ORDER BY matches.match_score DESC";

$result = mysqli_query($conn, $query);
```

Ye query:
- Sab matches nikale
- Har match ke saath uske related lost item details
- Har match ke saath uske related found item details
- Dono users ke names
- Highest match_score wale first (best matches pehle)

#### **Step 6: HTML Loop - Har Match Ka Card**
```
<?php while($row = mysqli_fetch_assoc($result)){ ?>

<div class="match-card">
    <!-- Lost Item Section -->
    <div class="item-section">
        <h2>Lost Item</h2>
        <img src=""> <!-- agar image hai -->
        <h3><?php echo $row['lost_title']; ?></h3>
        <p>Description, Category, Location, Date</p>
    </div>
    
    <!-- Found Item Section -->
    <div class="item-section">
        <h2>Found Item</h2>
        <img src=""> <!-- agar image hai -->
        <h3><?php echo $row['found_title']; ?></h3>
        <p>Description, Category, Location, Date</p>
    </div>
    
    <!-- Admin Actions -->
    <div class="match-info">
        <h2><?php echo $row['match_score']; ?>% Match</h2>
        <p>Status: <?php echo $row['status']; ?></p>
        
        <div class="action-buttons">
            <a href="?approve=<?php echo $row['id']; ?>">
                <button>Approve</button>
            </a>
            <a href="?reject=<?php echo $row['id']; ?>">
                <button>Reject</button>
            </a>
            <a href="?returned=<?php echo $row['id']; ?>">
                <button>Mark Returned</button>
            </a>
        </div>
    </div>
</div>

<?php } ?>
```

Card mein:
- Lost item ka side-by-side comparison
- Found item ke saath
- Match percentage
- Current status
- 3 action buttons: Approve, Reject, Mark Returned

### Manage-Matches.php Ka Summary:
- **Kaam**: Admin ko pending matches review karne de
- **Approve**: Status update + notification + chat enable
- **Returned**: Status update + reward points + badge update
- **Reject**: Status update (bas itna)
- **Display**: Lost aur found items ke saath match details

---

## 3. AJAX/MATCH-ITEMS.PHP - Automatic Matching Engine

### Ye File Kya Hai?
Ye file **background mein** chalti hai aur automatically lost items ko found items se match karta hai. Ye AJAX file hai matlab ye browser se call hota hai background mein without page reload ke. Ye algorithm comparable items ko find karta hai aur database mein matches create karta hai.

### File Ka Maqsad:
- Naye lost items ko naye found items se match karna
- Intelligent comparison (title, category, location, description)
- Automatic notifications send karna
- Strong matches ko database mein save karna

---

### Code Ka Flow

#### **Step 1: Setup & Includes**
```
include("../includes/config.php");
include("../includes/db.php");
include("../includes/functions.php");
```
- Database aur functions load karte hain

#### **Step 2: Lost Items Array Banana**
```
$lostItems = [];
$lostQuery = mysqli_query($conn, "SELECT * FROM lost_items");
while($row = mysqli_fetch_assoc($lostQuery)){
    $lostItems[] = $row;
}
```

Ye kya karte hain:
1. Empty array banate hain: `$lostItems = []`
2. Database se **sab lost items** nikaal ke loop mein fetch karte hain
3. Har lost item ko array mein add karte hain
4. Result: Array jismein sab lost items

#### **Step 3: Found Items Array Banana**
```
$foundItems = [];
$foundQuery = mysqli_query($conn, "SELECT * FROM found_items");
while($row = mysqli_fetch_assoc($foundQuery)){
    $foundItems[] = $row;
}
```

Ye kya karte hain:
1. Empty array banate hain: `$foundItems = []`
2. Database se **sab found items** nikaal ke loop mein fetch karte hain
3. Har found item ko array mein add karte hain
4. Result: Array jismein sab found items

#### **Step 4: Matching Algorithm - Double Loop**
```
foreach($lostItems as $lostItem){
    foreach($foundItems as $foundItem){
        // ... comparison logic ...
    }
}
```

Simple explanation:
- **First Loop**: Har lost item ke liye
  - **Second Loop**: Har found item ke saath
    - Dono ko compare karo
    - Agar match worthy ho to action lo

#### **Step 5: Same User Check**
```
if($lostItem['user_id'] == $foundItem['user_id']){
    continue;
}
```

Ye kya check karte hain:
- Agar same user ne lost item aur found item dono report kiye to skip karo
- Kyunke ek hi user apne item se apne item ko match nahi kar sakta

#### **Step 6: Match Score Calculate**
```
$score = calculateMatchScore($lostItem, $foundItem);
```

Ye function (jو functions.php mein hai) compare karte hain:

**calculateMatchScore Logic:**
```
1. Category Match: Agar category same hai (+40 points)
2. Title Similarity: "Lost: Black Cat" aur "Found: Black Cat" (+25 points based on similarity)
3. Location Similarity: "Last seen: Clifton" aur "Found at: Clifton" (+20 points)
4. Description Similarity: Descriptions mein common words (+15 points)

Total Score: 0 se 100 tak
```

#### **Step 7: Score Threshold Check**
```
if($score < 60){
    continue;
}
```

Ye kya karte hain:
- Agar score 60 se kam hai to ye match kharab hai, skip karo
- Sirf 60+ wale strong matches raakhte hain

#### **Step 8: Duplicate Match Check**
```
$checkMatch = mysqli_query($conn, "SELECT * FROM matches 
              WHERE lost_item_id='{$lostItem['id']}' 
              AND found_item_id='{$foundItem['id']}'");

if(mysqli_num_rows($checkMatch) > 0){
    continue;
}
```

Ye kya karte hain:
- Check karte hain ke ye match pehle se database mein exist karta hai ya nahi
- Agar exist karta hai to duplicate entry avoid karte hain

#### **Step 9: Match Insert Karna**
```
mysqli_query($conn, "INSERT INTO matches
             (lost_item_id, found_item_id, match_score)
             VALUES
             ('{$lostItem['id']}', '{$foundItem['id']}', '$score')");
```

Ye kya karte hain:
- Database mein naya match record create karte hain
- 3 values: lost item ID, found item ID, match score
- Example: Match #123 = Lost Item #45 + Found Item #78 + 85%

#### **Step 10: Notifications Bhejni**
```
createNotification($conn, $lostItem['user_id'], 
                  "A potential match was found for your lost item.", "match");

createNotification($conn, $foundItem['user_id'], 
                  "Your found item may match a lost report.", "match");
```

Ye kya karte hain:
1. **Lost user ko notify**: "A potential match was found for your lost item"
2. **Found user ko notify**: "Your found item may match a lost report"
3. Notification type: "match" (alag-alag type hote hain: approval, reward, match)

### Match-Items.php Ka Summary:
- **Kaam**: Intelligent automatic matching system
- **Algorithm**: Title, Category, Location, Description comparison
- **Score System**: 0-100 percentage
- **Minimum Threshold**: 60% to create match
- **Duplicate Prevention**: Same match na duplicate rahe
- **Notifications**: Dono users ko automatically notify kare

---

## 4. ADMIN/DASHBOARD.PHP - Admin Statistics Page

### Ye File Kya Hai?
Ye file **admin ke liye statistics dashboard** hai. Admin yahan par ek nazar mein dekh sakta hai ke:
- Total kitne users hain
- Total kitni lost items hain
- Total kitni found items hain
- Total kitne matches create huye hain
- Total kitni items successfully return ho gaye hain

### File Ka Maqsad:
- Admin ko quick statistics dikhana
- Application health check karna (kitna active hai)
- Performance overview dena

---

### Code Ka Flow

#### **Step 1: Session aur Includes**
```
session_start();

include("../includes/config.php");
include("../includes/auth.php");
include("../includes/db.php");
```
- Session start karte hain (logged in check ke liye)
- Auth file se admin confirmation
- Database connection

#### **Step 2: Helper Function Banana**
```
function getTotal($conn, $sql){
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result)['total'];
}
```

Ye simple function:
- SQL query execute karte hain
- Result fetch karte hain
- Sirf 'total' value return karte hain
- Repetitive code ko avoid karte hain (DRY principle)

#### **Step 3: Statistics Queries - Har Count**

**Total Users:**
```
$totalUsers = getTotal($conn, "SELECT COUNT(*) AS total FROM users");
```
- Database mein kitne users hain = COUNT(*)

**Total Lost Items:**
```
$totalLost = getTotal($conn, "SELECT COUNT(*) AS total FROM lost_items");
```
- Database mein kitni lost items hain

**Total Found Items:**
```
$totalFound = getTotal($conn, "SELECT COUNT(*) AS total FROM found_items");
```
- Database mein kitni found items hain

**Total Matches:**
```
$totalMatches = getTotal($conn, "SELECT COUNT(*) AS total FROM matches");
```
- Database mein kitne matches create huye (saare status wale)

**Total Returned Items:**
```
$totalReturned = getTotal($conn, "SELECT COUNT(*) AS total FROM matches 
                  WHERE status='returned'");
```
- Sirf wo matches jinhone successfully "returned" status pa gaye

#### **Step 4: HTML Page**
```
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
```
- Simple HTML page start

#### **Step 5: Header Include**
```
<?php include("../includes/header.php"); ?>
```
- Navigation bar aur header show karte hain

#### **Step 6: Dashboard Container**
```
<div class="dashboard">
    <h1>📊 Admin Dashboard</h1>
    
    <div class="card-container">
        <div class="card">👤 Users: <?php echo $totalUsers; ?></div>
        <div class="card">📦 Lost Items: <?php echo $totalLost; ?></div>
        <div class="card">🔍 Found Items: <?php echo $totalFound; ?></div>
        <div class="card">🤝 Matches: <?php echo $totalMatches; ?></div>
        <div class="card">✅ Returned: <?php echo $totalReturned; ?></div>
    </div>
</div>
```

Display format:
- Ek grid of cards
- Har card mein ek statistic
- Emojis aur numbers
- Simple aur clean UI

#### **Step 7: Footer**
```
<?php include("../includes/footer.php"); ?>
```
- Page footer include

### Dashboard.php Ka Summary:
- **Kaam**: Admin statistics display
- **Data**: 5 key metrics (Users, Lost Items, Found Items, Matches, Returned)
- **Method**: Simple COUNT queries
- **UI**: Card-based grid layout
- **Purpose**: Admin ko app health check karne de

---

## 🎯 OVERALL FLOW - Ye Sab Kaise Ek Saath Kaam Karte Hain

### Timeline:

1. **User Report Karte Hain**
   - Lost item report: "Mera phone gaya"
   - Found item report: "Mujhe phone mila"

2. **match-items.php Call Hota Hai** (Background mein)
   - Naye reported items ko analyze karte hain
   - Title, category, location compare karte hain
   - Agar 60%+ match ho to entry create karte hain
   - Notifications bhej dete hain

3. **Admin manage-matches.php Se Check Karte Hain**
   - Pending matches dekh le
   - Approve/Reject karte hain
   - Approved ho gaye to chat enable hoti hai

4. **Users Chat Karte Hain aur Item Return Karte Hain**
   - Dono users ek doosre se chat karte hain
   - Item return ho jata hai

5. **Admin "Mark Returned" Press Karte Hain**
   - Finder ko 50 points reward
   - Badge update ho jata hai
   - Success count badhti hai

6. **index.php Par Success Story Show Hoti Hai**
   - Successfully returned match display hota hai
   - Community ko inspire karte hain

7. **Admin dashboard.php Check Karte Hain**
   - Statistics dekh le
   - "Dekho 150 users hain, 80 items return ho gaye"
   - App performance check karte hain

---

## 📝 KEY CONCEPTS

### Database Tables Jo Use Hote Hain:
1. **users** - sab users ki info
2. **lost_items** - lost reports
3. **found_items** - found reports
4. **matches** - lost + found ka pairing
5. **notifications** - user messages

### Important Functions:
- `calculateMatchScore()` - items ko compare karta hai
- `createNotification()` - users ko notify karta hai
- `rewardUser()` - points aur badges award karta hai

### Security Measures:
- `htmlspecialchars()` - dangerous characters block karte hain
- `(int)` casting - SQL injection prevent karte hain
- `auth.php` - admin check karte hain

---

## 🚀 SIMPLE SUMMARY TABLE

| File | Maqsad | Data Source | Output |
|------|--------|-------------|--------|
| index.php | Resolved cases show karna | matches (returned) | HTML grid of success stories |
| manage-matches.php | Admin ko review karne de | matches, lost_items, found_items | Admin panel with action buttons |
| match-items.php | Auto matching | lost_items, found_items | matches table mein entries |
| dashboard.php | Statistics dikhana | COUNT queries | Admin dashboard with cards |

---

## 💡 Ye Seekhne Ke Baad Aap Samjh Jayenge:

✅ Database se data kaise fetch hote hain  
✅ While loop se multiple records kaise process hote hain  
✅ Conditions se logic kaise implement hote hain  
✅ Functions kaise reusable code banate hain  
✅ Admin vs User ke different features kaise hote hain  
✅ Notifications aur rewards system kaise kaam karte hain  
✅ HTML + PHP integration  
✅ Database joins aur queries  


---

## 5. USER/REPORT-LOST.PHP - Lost Item Report Form

### Ye File Kya Hai?
Ye file users ko **lost item ko report karne** ka form deta hai. Jab user ne koi cheez khone khai to ye page par aakar report kar sakta hai. Item ki details, image, location, date - sab kuch fill karte hain aur submit karte hain.

### File Ka Maqsad:
- Users ko lost item report karne ka interface dena
- Item ka data database mein save karna
- Automatically matching algorithm chalana
- Database mein found items ke saath match karna

---

### Code Ka Flow

#### **Step 1: Session Check aur Auth**
```php
session_start();
include("../includes/config.php");
include("../includes/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}
```
- User logged in hai ya nahi check karte hain
- Agar login nahi hai to login page par bhej dete hain

#### **Step 2: Form Submit Check**
```php
if(isset($_POST['submitLost'])){
    // ... code ...
}
```
- Check karte hain ke form submit hua hai ya nahi
- Button click hone par yeh block execute hota hai

#### **Step 3: Form Data Capture**
```php
$user_id = $_SESSION['user_id'];
$title = mysqli_real_escape_string($conn, $_POST['title']);
$category = mysqli_real_escape_string($conn, $_POST['category']);
$description = mysqli_real_escape_string($conn, $_POST['description']);
$location = mysqli_real_escape_string($conn, $_POST['location']);
$date = $_POST['date'];
```
- Form se data le lo
- `mysqli_real_escape_string()` se SQL injection attack prevent karte hain (dangerous characters ko block karte hain)

#### **Step 4: Image Upload (Optional)**
```php
$imageName = "";
if(!empty($_FILES['image']['name'])){
    $imageName = time() . "_" . $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];
    move_uploaded_file($tmp, "../assets/uploads/" . $imageName);
}
```
- Image upload karna optional hai
- Agar image upload hui to:
  1. Unique name banate hain (timestamp + original name)
  2. File ko uploads folder mein move karte hain
  3. Name ko variable mein save karte hain

#### **Step 5: Database Insert**
```php
$query = "INSERT INTO lost_items 
(user_id, title, category, description, last_seen_location, lost_date, image)
VALUES 
('$user_id', '$title', '$category', '$description', '$location', '$date', '$imageName')";

if(mysqli_query($conn, $query)){
    // success
}
```
- Database mein naya lost item record banate hain
- 7 values: user_id, title, category, description, location, date, image

#### **Step 6: Get Inserted ID aur Matching**
```php
$inserted_id = mysqli_insert_id($conn);

$lostData = [
    'id' => $inserted_id,
    'title' => $title,
    'category' => $category,
    'last_seen_location' => $location,
    'description' => $description
];

$foundQuery = mysqli_query($conn, "SELECT * FROM found_items");

while($foundItem = mysqli_fetch_assoc($foundQuery)){
    $score = calculateMatchScore($lostData, $foundItem);
    
    if($score >= 60){
        // ... insert match ...
    }
}
```
- Newly inserted item ka ID lo
- Lost item ka array banao
- Sab found items ko loop se nikalo
- Har found item ke saath compare karo
- Agar 60%+ match ho to database mein match create karo

#### **Step 7: Form HTML**
```html
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Item Title" required>
    <select name="category" required>
        <option value="Wallet">Wallet</option>
        <option value="Phone">Phone</option>
        <!-- ... more options ... -->
    </select>
    <textarea name="description" placeholder="Description" required></textarea>
    <input type="text" name="location" placeholder="Last Seen Location" required>
    <input type="date" name="date" required>
    <input type="file" name="image">
    <button type="submit" name="submitLost">Submit Report</button>
</form>
```
- Form ke fields
- Title, category, description zaroori hain
- Image optional hai

### Report-Lost.php Ka Summary:
- **Maqsad**: Users ko lost item report karne de
- **Image**: Optional
- **Matching**: Automatically found items ke saath match karte hain
- **Database**: lost_items table mein insert

---

## 6. USER/REPORT-FOUND.PHP - Found Item Report Form

### Ye File Kya Hai?
Ye file users ko **found item ko report karne** ka form deta hai. Jab user ne koi cheez paayi to ye page par aakar report kar sakta hai. Item ki details, image, location, date - sab kuch fill karte hain.

### File Ka Maqsad:
- Users ko found item report karne ka interface dena
- Item ka data database mein save karna
- Automatically matching algorithm chalana
- Database mein lost items ke saath match karna
- Report-lost.php se bilkul same logic, bus reverse hai

---

### Code Ka Flow (Report-Lost.php ke jaisa hi)

#### **Step 1: Session Check aur Auth**
- User logged in hai ya nahi check karte hain

#### **Step 2: Form Submit Check**
- Check karte hain ke form submit hua hai ya nahi

#### **Step 3: Form Data Capture**
```php
$user_id = $_SESSION['user_id'];
$title = mysqli_real_escape_string($conn, $_POST['title']);
$category = mysqli_real_escape_string($conn, $_POST['category']);
$description = mysqli_real_escape_string($conn, $_POST['description']);
$location = mysqli_real_escape_string($conn, $_POST['location']);
$date = $_POST['date'];
$condition = mysqli_real_escape_string($conn, $_POST['condition']);
$notes = mysqli_real_escape_string($conn, $_POST['notes']);
```
- Form se data le lo
- Report-found mein extra fields hain: condition (item ka state) aur notes (additional info)

#### **Step 4: Image Upload (REQUIRED)**
```php
if(empty($_FILES['image']['name'])){
    $message = "Image is required!";
} else {
    $imageName = time() . "_" . $_FILES['image']['name'];
    $tmpName = $_FILES['image']['tmp_name'];
    move_uploaded_file($tmpName, "../assets/uploads/" . $imageName);
}
```
- Image **zaroori** hai found item ke liye
- Agar image nahi hai to error message show karte hain
- Image unique name de kar upload folder mein save karte hain

#### **Step 5: Database Insert**
```php
$query = "INSERT INTO found_items
(user_id, title, category, description, found_location, found_date, image, item_condition, additional_notes)
VALUES
('$user_id', '$title', '$category', '$description', '$location', '$date', '$imageName', '$condition', '$notes')";
```
- Database mein naya found item record banate hain
- 9 values: user_id, title, category, description, location, date, image, condition, notes

#### **Step 6: Matching aur Insert**
```php
$inserted_id = mysqli_insert_id($conn);

$foundData = [
    'id' => $inserted_id,
    'title' => $title,
    'category' => $category,
    'found_location' => $location,
    'description' => $description
];

$lostQuery = mysqli_query($conn, "SELECT * FROM lost_items");

while($lostItem = mysqli_fetch_assoc($lostQuery)){
    $score = calculateMatchScore($lostItem, $foundData);
    
    if($score >= 60){
        // ... insert match ...
    }
}
```
- Newly inserted item ka ID lo
- Found item ka array banao
- Sab lost items ko nikalo
- Har lost item ke saath compare karo
- Agar 60%+ match ho to match create karo

### Report-Found.php Ka Summary:
- **Maqsad**: Users ko found item report karne de
- **Image**: REQUIRED (zaroori hai)
- **Extra Fields**: Condition aur notes
- **Matching**: Automatically lost items ke saath match karte hain
- **Database**: found_items table mein insert

---

## 7. CHAT/SEND-MESSAGE.PHP - Chat Message Sender

### Ye File Kya Hai?
Ye AJAX file hai jo **background mein** message send karta hai without page reload ke. Jab dono users ek doosre ko message bhejte hain, ye file use hota hai message database mein save karne ke liye.

### File Ka Maqsad:
- Users ke beech message exchange karna
- Message database mein save karna
- Security check karna (authorized user hi message bhej sakte hain)
- Instant messaging enable karna

---

### Code Ka Flow

#### **Step 1: Check - Message aur User Logged In Hain?**
```php
if(isset($_POST['message']) && isset($_SESSION['user_id'])){
    // ... code ...
}
```
- Check karte hain ke message data hai aur user logged in hai

#### **Step 2: Form Data Capture**
```php
$match_id = (int)$_POST['match_id'];
$message = mysqli_real_escape_string($conn, $_POST['message']);
$sender_id = (int)$_SESSION['user_id'];
```
- Match ID (kaunse match ke liye)
- Message text (kya message hai)
- Sender ID (kine ne bheja)

#### **Step 3: Security Check - Authorization**
```php
$check = mysqli_query(
    $conn,
    "SELECT matches.id
     FROM matches
     JOIN lost_items ON matches.lost_item_id = lost_items.id
     JOIN found_items ON matches.found_item_id = found_items.id
     WHERE matches.id='$match_id'
     AND matches.status IN ('approved', 'location_ready')
     AND (lost_items.user_id='$sender_id' OR found_items.user_id='$sender_id')"
);

if(mysqli_num_rows($check) === 0){
    exit();
}
```

Ye security check:
- Check karte hain ke match approved ya ready status mein hai
- Check karte hain ke sender user is match ka involved hai
- Check karte hain ke match kisi aur ka nahi hai
- Agar unauthorized ho to exit karte hain (message nahi save hota)

#### **Step 4: Message Insert**
```php
mysqli_query(
    $conn,
    "INSERT INTO chats
    (match_id, sender_id, message)
    VALUES
    ('$match_id', '$sender_id', '$message')"
);
```
- Database mein message record banate hain
- 3 values: match_id, sender_id, message text
- Timestamp automatically save hota hai (database default)

### Send-Message.php Ka Summary:
- **Maqsad**: Message send karna
- **Security**: Authorized users hi message send kar sakte hain
- **Database**: chats table mein insert
- **Status Check**: Sirf approved/ready matches pe message allow

---

## 8. CHAT/FETCH-MESSAGES.PHP - Chat Messages Fetcher

### Ye File Kya Hai?
Ye AJAX file hai jo **match ke sab messages nikaal kar display** karta hai. Jab user chat page load karte hain ya refresh karte hain, ye file messages fetch karke show karta hai.

### File Ka Maqsad:
- Specific match ke sab messages fetch karna
- Messages ko chronological order mein dikhana
- Sent/received styling distinguish karna
- Real-time chat experience dena

---

### Code Ka Flow

#### **Step 1: Get Match ID aur Current User**
```php
$match_id = (int)$_GET['match_id'];
$current_user = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
```
- URL se match ID lo
- Session se current user ID lo

#### **Step 2: Query - Sab Messages Fetch Karo**
```php
$query = "SELECT chats.*, users.username
          FROM chats
          JOIN users ON chats.sender_id = users.id
          WHERE match_id = '$match_id'
          ORDER BY sent_at ASC";

$result = mysqli_query($conn, $query);
```

Ye query kya karte hain:
- Specific match ke sab messages
- Har message ke sender ka username bhi include karte hain
- Oldest se newest order mein (ASC = ascending)
- Chronological order = chat mein sab messages sequence mein milte hain

#### **Step 3: Loop - Har Message Display Karo**
```php
while($row = mysqli_fetch_assoc($result)){
?>
```
- Database result se har message ko ek-ek karke nikaal ke display karte hain

#### **Step 4: Message Styling aur Display**
```html
<div class="chat-message <?php echo ((int)$row['sender_id'] === $current_user) ? 'sent' : 'received'; ?>">
    <strong>
        <?php echo htmlspecialchars($row['username']); ?>
    </strong>
    <p>
        <?php echo htmlspecialchars($row['message']); ?>
    </p>
</div>
```

Display logic:
- Check karte hain ke message kine ne bheja
- Agar current user ne bheja to "sent" class (right side, different color)
- Agar doosre user ne bheja to "received" class (left side)
- Username show karte hain
- Message text show karte hain

### Fetch-Messages.php Ka Summary:
- **Maqsad**: Match ke sab messages display karna
- **Query**: Match ID se specific messages fetch karte hain
- **Styling**: Sent vs Received messages different look
- **Order**: Chronological order (oldest first)

---

**Ye 8 files milke ek complete Lost & Found matching system create karte hain jo automated matching, admin review, user reporting, aur real-time chat ke saath fully functional hai!**

