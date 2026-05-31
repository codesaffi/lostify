# LOSTIFY Project - Team Module Assignment

## Project Overview
**Lostify** is a lost & found platform that helps users report lost items, find matches with found items, communicate via chat, track items using GPS, and manage the overall return process.

---

## Team Structure & Module Assignment (By Technical Difficulty)

### 👤 **SAFFI** (Project Lead & Technical Expert)
**Modules: Real-Time Chat System + Location Tracking + Item Matching Algorithm** ⭐⭐⭐ (HARDEST)

#### Responsibilities:
- Lead the entire project
- Handle complex real-time systems
- Implement advanced algorithms
- Answer difficult technical questions in defense
- Coordinate between all team members

#### Files Responsible For:
| File | Purpose | Complexity |
|------|---------|-----------|
| `chat/chat.php` | Real-time messaging interface | ⭐⭐⭐ High |
| `chat/send-message.php` | Message submission API with AJAX | ⭐⭐⭐ High |
| `chat/fetch-messages.php` | Auto-refresh message fetching | ⭐⭐⭐ High |
| `tracking/live-location.php` | GPS tracking with Leaflet.js map | ⭐⭐⭐ High |
| `tracking/fetch-location.php` | Real-time location AJAX API | ⭐⭐⭐ High |
| `tracking/update-location.php` | GPS coordinate updates | ⭐⭐⭐ High |
| `ajax/match-items.php` | Smart item matching algorithm | ⭐⭐⭐ High |
| `includes/functions.php` | Core utility functions | ⭐⭐ Medium |

#### Key Technical Features:
- **Real-time Messaging**: AJAX polling, message synchronization, chat history
- **Live GPS Tracking**: Geolocation API integration, real-time coordinate updates, map rendering
- **Smart Matching**: Category matching, location proximity calculation, match score algorithm
- **Database Optimization**: Complex queries, transaction handling
- **Performance**: AJAX efficiency, real-time updates without page reload

#### Database Tables Used:
- `chats` table - message storage with timestamps
- `matches` table - match coordination
- `lost_items` & `found_items` - matching source data

#### Technical Stack (You'll Defend):
- JavaScript AJAX/Fetch API
- PHP backend APIs
- Leaflet.js mapping library
- Geolocation API
- Complex SQL queries
- Real-time synchronization logic

---

### 💬 **NAFEES** (Item Management Specialist)
**Module: Item Reports & Uploads** ⭐⭐ (MEDIUM)

#### Responsibilities:
- Handle user item submissions
- Manage file uploads
- Implement item forms

#### Files Responsible For:
| File | Purpose |
|------|---------|
| `user/report-lost.php` | Lost item reporting form |
| `user/report-found.php` | Found item reporting form |

#### Key Features:
- Form validation (title, category, description, location, date)
- Image upload with file type checking
- Data storage in database
- Success/error messages
- User session verification

#### Database Tables Used:
- `lost_items` table
- `found_items` table

---

### 📍 **RAFAY** (Dashboard & Statistics)
**Module: Admin Panel & Dashboards** ⭐⭐ (MEDIUM)

#### Responsibilities:
- Build admin interface
- Display statistics
- Create match management page

#### Files Responsible For:
| File | Purpose |
|------|---------|
| `admin/dashboard.php` | Admin stats overview |
| `admin/manage-matches.php` | Approve/reject matches interface |
| `index.php` | Homepage with resolved cases |

#### Key Features:
- Statistics display (users, items, matches, returned)
- Match approval/rejection buttons
- Match percentage display
- Case history display
- Admin-only access control

#### Database Tables Used:
- `matches` table
- `users` table
- `lost_items` & `found_items`

---

### 📦 **MUNEEB** (User Profile & Notifications)
**Module: User Profiles & Notifications** ⭐ (EASIER)

#### Responsibilities:
- Manage user profiles
- Display user statistics
- Handle notifications

#### Files Responsible For:
| File | Purpose |
|------|---------|
| `profile.php` | User profile with stats and history |
| `notifications.php` | User notifications page |
| `leaderboard.php` | Leaderboard rankings |
| `dashboard.php` | User welcome dashboard |

#### Key Features:
- User profile display
- Points, reputation, badges
- Item history (lost & found)
- Notification listing
- Leaderboard ranking
- Profile picture upload
- User stats cards

#### Database Tables Used:
- `users` table
- `notifications` table
- `lost_items` & `found_items`

---

### 🎛️ **MUMTAZ** (Authentication & Infrastructure)
**Module: Authentication & Core System** ⭐ (FOUNDATIONAL)

#### Responsibilities:
- User authentication system
- Session management
- Core configurations
- Database setup
- Shared utilities

#### Files Responsible For:
| File | Purpose |
|------|---------|
| `login.php` | User/Admin login with role selection |
| `register.php` | User registration form |
| `logout.php` | Logout functionality |
| `includes/auth.php` | Authentication middleware |
| `includes/config.php` | BASE_URL configuration |
| `includes/db.php` | Database connection |
| `includes/header.php` | Navigation layout |
| `includes/footer.php` | Footer with scripts |

#### Key Features:
- Role-based login (User vs Admin)
- Secure password hashing
- Session management
- Database connection pooling
- Navigation menu
- Mobile responsive header

---

## Shared Resources (All Members Use)

### Frontend
- `assets/css/style.css` - Main stylesheet (Mobile responsive, dark theme)
- `assets/images/` - Default images
- `assets/uploads/` - User uploaded images for items

### Configuration
- `.htaccess` files in `/chat/` and `/tracking/` - PHP execution permissions

### Database
- All modules connect via `includes/db.php`
- MySQLi connection with prepared statements for security

---

## Technology Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | PHP 7.4+ |
| **Database** | MySQL |
| **Frontend** | HTML5, CSS3, JavaScript (ES6+) |
| **Styling** | Custom CSS (Dark theme, responsive) |
| **Maps** | Leaflet.js for location tracking |
| **Communication** | AJAX/Fetch API for real-time updates |
| **Authentication** | PHP Sessions + Password hashing |

---

## Development Workflow

### Setup
1. **Saffi** provides credentials and environment setup
2. Each developer creates branch: `feature/[module-name]`
3. All use `BASE_URL` constant for routing (auto-detects localhost vs live)

### Key Integration Points
- All pages include `header.php` and `footer.php` (maintained by Saffi)
- All AJAX calls use `BASE_URL_JS` variable
- Database queries use centralized `db.php`
- Authentication checks via `auth.php`

### Testing Locations
- **Localhost**: `http://localhost/lostify/`
- **Live Server**: `https://yourdomain.com/`

---

## File Structure Overview

```
lostify/
├── index.php                    (MUMTAZ - Home)
├── login.php                    (SAFFI - Auth)
├── register.php                 (SAFFI - Auth)
├── logout.php                   (SAFFI - Auth)
├── dashboard.php                (MUMTAZ - User Dashboard)
├── profile.php                  (MUMTAZ - User Profile)
├── notifications.php            (MUMTAZ - Notifications)
├── leaderboard.php              (MUMTAZ - Leaderboard)
│
├── admin/
│   ├── dashboard.php            (MUMTAZ - Admin Stats)
│   └── manage-matches.php       (MUMTAZ - Match Management)
│
├── user/
│   ├── report-lost.php          (MUNEEB - Report Lost)
│   └── report-found.php         (MUNEEB - Report Found)
│
├── chat/
│   ├── chat.php                 (NAFEES - Chat Interface)
│   ├── send-message.php         (NAFEES - Send Message API)
│   └── fetch-messages.php       (NAFEES - Fetch Messages API)
│
├── tracking/
│   ├── live-location.php        (RAFAY - Location Tracking)
│   ├── fetch-location.php       (RAFAY - Fetch Location API)
│   └── update-location.php      (RAFAY - Update Location API)
│
├── ajax/
│   └── match-items.php          (MUNEEB - Match Items API)
│
├── includes/
│   ├── config.php               (SAFFI - Configuration)
│   ├── db.php                   (SAFFI - Database)
│   ├── auth.php                 (SAFFI - Authentication)
│   ├── functions.php            (SAFFI - Utilities)
│   ├── header.php               (SAFFI - Navigation)
│   └── footer.php               (SAFFI - Footer)
│
└── assets/
    ├── css/
    │   └── style.css            (ALL - Styling)
    ├── uploads/                 (ALL - User Images)
    └── images/                  (ALL - Default Images)
```

---

## Deployment Checklist

- [ ] **SAFFI** - Verify all configs and BASE_URL work on live server
- [ ] **NAFEES** - Test chat functionality in both environments
- [ ] **RAFAY** - Test GPS tracking and map display
- [ ] **MUNEEB** - Verify item upload and matching algorithm
- [ ] **MUMTAZ** - Test all dashboards and admin interfaces
- [ ] **SAFFI** - Final integration test and security review

---

## Communication Protocol

1. **Daily Standup**: Check module status
2. **Integration Issues**: Report to **SAFFI** (Project Lead)
3. **Database Changes**: Notify all members via **SAFFI**
4. **Deployment**: **SAFFI** coordinates and leads

---

**Project Completed: May 31, 2026**
**Last Updated: May 31, 2026**
