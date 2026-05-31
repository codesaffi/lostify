# LOSTIFY Project - Team Assignment Summary

## Quick Reference Table

| **Member** | **Difficulty** | **Modules** | **Files** | **Key Complexity** |
|---|---|---|---|---|
| **SAFFI** ⭐⭐⭐ | **HARDEST** | • Real-Time Chat<br>• GPS Tracking<br>• Item Matching | `chat/chat.php`<br>`chat/send-message.php`<br>`chat/fetch-messages.php`<br>`tracking/live-location.php`<br>`tracking/fetch-location.php`<br>`tracking/update-location.php`<br>`ajax/match-items.php` | AJAX Polling, Leaflet.js Maps, GPS API, Match Algorithm, Real-time Sync |
| **NAFEES** ⭐⭐ | **MEDIUM** | • Item Reports | `user/report-lost.php`<br>`user/report-found.php` | File Upload, Form Validation, Image Storage |
| **RAFAY** ⭐⭐ | **MEDIUM** | • Admin Dashboard | `admin/dashboard.php`<br>`admin/manage-matches.php`<br>`index.php` | Statistics Queries, Match Approval, Case Display |
| **MUNEEB** ⭐ | **EASIER** | • User Profiles<br>• Notifications | `profile.php`<br>`notifications.php`<br>`leaderboard.php`<br>`dashboard.php` | Profile Display, Stats Cards, History List |
| **MUMTAZ** ⭐ | **FOUNDATIONAL** | • Authentication<br>• Core System | `login.php`<br>`register.php`<br>`logout.php`<br>`includes/auth.php`<br>`includes/config.php`<br>`includes/db.php`<br>`includes/header.php`<br>`includes/footer.php` | Login System, Sessions, DB Connection |

---

## Defense & Q&A Preparation

### 🔴 **SAFFI** - Expected Hard Questions
1. **"Explain how real-time chat works without WebSockets?"**
   - AJAX polling mechanism
   - Message synchronization strategy
   - Database transaction handling

2. **"How does the GPS tracking work?"**
   - Geolocation API integration
   - Leaflet.js map rendering
   - Real-time coordinate updates
   - User location privacy

3. **"How is the item matching algorithm calculated?"**
   - Category matching logic
   - Location proximity calculation
   - Match score percentage
   - Database query optimization

4. **"What happens when multiple users send messages simultaneously?"**
   - Race condition handling
   - Database locks
   - Message ordering

5. **"How do you handle GPS failures or missing coordinates?"**
   - Error handling
   - Fallback mechanisms
   - User notifications

6. **"Optimize the chat performance - what would you improve?"**
   - AJAX polling interval
   - Database indexing
   - Caching strategies

7. **"Explain the flow from chat to location tracking"**
   - State management
   - Match transitions
   - Return confirmation flow

---

### 🟡 **NAFEES** - Expected Medium Questions
1. **"How do you validate file uploads?"**
   - File type checking
   - Size limitations
   - Security measures

2. **"Where are images stored and how do you prevent malicious uploads?"**
   - File naming strategy (timestamp prefix)
   - Directory structure
   - MIME type validation

3. **"What prevents duplicate item reports?"**
   - Validation on form
   - Database constraints
   - User feedback

---

### 🟡 **RAFAY** - Expected Medium Questions
1. **"Write a query to get all pending matches for admin approval"**
   - JOIN operations
   - WHERE conditions
   - ORDER BY logic

2. **"How do you calculate statistics efficiently?"**
   - COUNT() optimization
   - Database indexing
   - Caching considerations

3. **"What happens when an admin rejects a match?"**
   - Status update logic
   - Notification triggers
   - User feedback

---

### 🟢 **MUNEEB** - Expected Easier Questions
1. **"How do you display a user's item history?"**
   - Query structure
   - Filtering logic
   - Display layout

2. **"How is the leaderboard calculated?"**
   - ORDER BY points DESC
   - LIMIT 10
   - User ranking

3. **"What information is shown on a user profile?"**
   - Points, reputation, badges
   - Item history
   - Resolved cases

---

### 🟢 **MUMTAZ** - Expected Foundational Questions
1. **"Explain the login flow"**
   - Form submission
   - Password verification
   - Session creation

2. **"What's the difference between user and admin roles?"**
   - Session['role'] value
   - Access control
   - Permissions

3. **"How are passwords stored securely?"**
   - password_hash()
   - password_verify()
   - Why not plain text

---

## File Assignment Summary

### SAFFI Gets (⭐⭐⭐ HARDEST):
```
✓ 7 Files - Complex real-time systems
✓ 2 Folders - chat/ and tracking/ directories
✓ 8 Database tables used
✓ Explains 3 major technical systems in defense
```

### NAFEES Gets (⭐⭐ MEDIUM):
```
✓ 2 Files - Form submissions
✓ File upload handling
✓ 2 Database tables
✓ Straightforward validation
```

### RAFAY Gets (⭐⭐ MEDIUM):
```
✓ 3 Files - Admin interfaces
✓ Statistics display
✓ 3 Database tables
✓ Query joins and conditions
```

### MUNEEB Gets (⭐ EASIER):
```
✓ 4 Files - Mostly display pages
✓ Profile and notification views
✓ 3 Database tables
✓ Simple data presentation
```

### MUMTAZ Gets (⭐ FOUNDATIONAL):
```
✓ 8 Files - Base infrastructure
✓ Shared by all modules
✓ Authentication layer
✓ Database connection
```

---

## Why This Assignment Works for Evaluation

✅ **SAFFI**: Can explain complex systems deeply and answer technical deep-dives  
✅ **NAFEES**: Handles medium complexity with file operations  
✅ **RAFAY**: Shows understanding of database queries and admin logic  
✅ **MUNEEB**: Demonstrates solid fundamentals with user interfaces  
✅ **MUMTAZ**: Explains foundational architecture that everything depends on  

---

## Integration Dependencies

```
MUMTAZ (Base)
    ↓
SAFFI (Uses Auth + Chat/Tracking)
    ↓
NAFEES (Uses Base + Reports)
    ↓
RAFAY (Uses Admin Dashboard + Reports)
    ↓
MUNEEB (Uses Profiles + Everything)
```

Each team member needs to understand how their modules depend on the layers below them.

---

**Created: May 31, 2026**
**Team Lead: SAFFI** (Most Complex Modules)
