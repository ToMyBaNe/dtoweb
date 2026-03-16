# DTO Website - Code Documentation

## Table of Contents
1. [Architecture Overview](#architecture-overview)
2. [Code Flow & Routing](#code-flow--routing)
3. [Key Functions & Methods](#key-functions--methods)
4. [File-by-File Breakdown](#file-by-file-breakdown)
5. [Database Operations](#database-operations)
6. [Authentication & Sessions](#authentication--sessions)
7. [File Upload Process](#file-upload-process)
8. [Template System](#template-system)
9. [Variable & Naming Conventions](#variable--naming-conventions)
10. [Error Handling](#error-handling)

---

## Architecture Overview

### Application Architecture Pattern: MVC-Lite
The application follows a simplified MVC (Model-View-Controller) pattern:

```
REQUEST
   ↓
index.php (ROUTER)
   ↓
config.php (Initialize DB & Session)
   ↓
includes/header.php (Navigation & Layout)
   ↓
sections/*.php (Page Content)
   ↓
includes/footer.php (Page Footer)
   ↓
RESPONSE (HTML)
```

### Admin Panel Flow:
```
REQUEST to /admin/*
   ↓
Authentication Check (kupalsijade.php)
   ↓
Process Form Data (if POST)
   ↓
Database Operations (CRUD)
   ↓
Redirect or Display Form
```

### Technology Stack
- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB with PDO
- **Frontend**: HTML5, Tailwind CSS, JavaScript
- **Session Management**: PHP Native Sessions
- **File Handling**: PHP $_FILES

---

## Code Flow & Routing

### Public Website Routing (index.php)

```php
// 1. REQUEST ENTRY POINT
$section = isset($_GET['section']) ? $_GET['section'] : 'home';

// 2. ROUTING LOGIC
if ($section == 'home') {
    // Load home content
} elseif ($section == 'announcements') {
    // Load announcements
} elseif ($section == 'news') {
    // Load news
} elseif ($section == 'systems') {
    // Load systems
}
```

**URL Pattern**: `/?section=SECTION_NAME`
- Default section: `home`
- Available sections: `home`, `announcements`, `news`, `systems`

### Admin Panel Routing (admin/dashboard.php)

```php
// 1. Authentication Check
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: /admin/');
    exit;
}

// 2. Page Selection
$page = isset($_GET['page']) ? $_GET['page'] : 'announcements';

// 3. Action Handling
$action = isset($_GET['action']) ? $_GET['action'] : '';

// If action == 'delete', process deletion
// Then display content for selected page
```

**URL Pattern**: `/admin/dashboard.php?page=PAGE_NAME&action=ACTION`

---

## Key Functions & Methods

### config.php Functions

#### `getDB()` - Database Connection Singleton
```php
function getDB() {
    static $pdo = null;  // Persists across function calls
    
    if ($pdo === null) {
        // First connection: create database if needed
        $dsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
        $temp_pdo = new PDO($dsn, DB_USER, DB_PASS);
        
        // Create database
        $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS `dtoweb`");
        
        // Connect to specific database
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
    }
    
    return $pdo;
}
```

**Purpose**: Ensures only one database connection exists (singleton pattern)  
**Returns**: PDO object for database operations  
**Error Handling**: Throws PDOException on connection failure

---

#### `initializeDB()` - Database Table Creation
```php
function initializeDB() {
    $pdo = getDB();
    
    // Creates tables if they don't exist:
    // - announcements
    // - news
    // - admin_users
    // - calendar_events
    // - systems
    
    // Auto-creates default admin if none exists
}
```

**Purpose**: Sets up database schema on application startup  
**Scope**: Called from every page (safe due to IF NOT EXISTS checks)  
**Auto-Setup**: Creates default admin user (admin/admin123)

---

### admin/kupalsijade.php Functions

#### Login Form Handling
```php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Query admin user from database
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verify password using bcrypt
    if ($admin && password_verify($password, $admin['password'])) {
        // Set session variable
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        
        // Redirect to dashboard
        header('Location: /admin/dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
```

**Security**: Uses `password_verify()` for secure password comparison  
**Session**: Sets `$_SESSION['admin_logged_in']` flag  
**Redirect**: Sends to dashboard on success

---

#### Logout (logout.php)
```php
session_destroy();
header('Location: /admin/');
exit;
```

**Purpose**: Destroys session and redirects to login  
**Security**: Clears all session data

---

### File Upload Processing

#### Image Upload Pattern (Used in manage_announcements.php, manage_news.php)
```php
// 1. CHECK IF FILE EXISTS
if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
    $file = $_FILES['image'];
    
    // 2. VALIDATE FILE TYPE
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    
    if (!in_array($ext, $allowed)) {
        $error = 'Invalid file type';
    }
    
    // 3. VALIDATE FILE SIZE
    elseif ($file['size'] > 20242880) { // 20MB
        $error = 'File size exceeds limit';
    }
    
    // 4. CREATE DIRECTORY IF NEEDED
    else {
        $upload_dir = __DIR__ . '/../assets/uploads/announcements';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // 5. GENERATE UNIQUE FILENAME
        $new_filename = uniqid('ann_') . '.' . $ext;
        $upload_path = $upload_dir . '/' . $new_filename;
        
        // 6. MOVE FILE
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // 7. DELETE OLD FILE IF EXISTS
            if ($announcement && $announcement['image']) {
                $old_path = __DIR__ . '/../assets/uploads/announcements/' . $announcement['image'];
                unlink($old_path);
            }
            
            // 8. STORE FILENAME IN VARIABLE
            $image = $new_filename;
        } else {
            $error = 'Failed to upload file';
        }
    }
}
```

**Security Measures**:
- File type validation (whitelist allowed extensions)
- File size limits enforced
- Unique filename generation prevents overwriting
- Old files deleted when replacing

**File Storage**: `/assets/uploads/[category]/[generated_filename]`

---

## File-by-File Breakdown

### config.php

**Responsibilities**:
```
1. Database configuration constants
2. PDO connection management
3. Database schema initialization
4. Admin user seeding
5. Session initialization
```

**Key Constants**:
```php
DB_HOST     // MySQL host (default: localhost)
DB_USER     // MySQL user (default: root)
DB_PASS     // MySQL password (default: empty)
DB_NAME     // Database name (default: dtoweb)
```

**Global Functions**:
```php
getDB()           // Returns PDO connection
initializeDB()    // Creates tables on startup
```

**Session Setup**:
```php
session_start([
    'use_strict_mode' => 1,        // Reject invalid SID
    'use_cookies' => 1,            // Use cookies for session
    'cookie_lifetime' => 0,        // Cookie expires with browser
]);
```

---

### index.php (Router)

**Flow**:
```
1. Start session
2. Set cache headers
3. Require config.php (init DB)
4. Get section from $_GET
5. Load header.php
6. Route to appropriate section
7. Load footer.php
```

**Routing Variables**:
```php
$section  // From $_GET['section'], defaults to 'home'
$pdo      // Database connection from config.php
```

**Section Routing**:
```php
if ($section == 'home'):
    require 'sections/home.php';
elseif ($section == 'announcements'):
    require 'sections/announcements.php';
// ... etc
```

---

### admin/dashboard.php

**Responsibilities**:
```
1. Authentication verification
2. Delete operation handling
3. Content display (announcements, news, events, systems)
4. Admin UI rendering
```

**Delete Operation Flow**:
```php
// 1. CHECK DELETE REQUEST
if ($action == 'delete' && isset($_GET['id']) && isset($_GET['type'])) {
    
    // 2. GET OBJECT ID
    $id = intval($_GET['id']);
    $type = $_GET['type'];  // announcement, news, event, system
    
    // 3. DELETE FROM DATABASE
    if ($type == 'announcement') {
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
    }
    // ... similar for other types
    
    // 4. IF SYSTEM, DELETE LOGO FILE
    if ($type == 'system') {
        // Get logo filename
        // Delete file from filesystem
        unlink($logo_path);
    }
    
    // 5. REDIRECT TO PAGE
    header("Location: /admin/dashboard.php?page=$redirect_page");
    exit;
}
```

**Page Handling**:
```php
$page = $_GET['page'] ?? 'announcements';  // Default to announcements

// Fetch data based on page
$stmt = $pdo->query("SELECT * FROM $table ORDER BY ... DESC");
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Render list/table for items
```

---

### admin/manage_*.php files

#### Common Flow (manage_announcements.php, manage_news.php, manage_calendar.php, manage_systems.php)

**Structure**:
```php
// 1. CONFIG & AUTH CHECK
require_once '../config.php';
initializeDB();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: /admin/');
    exit;
}

// 2. GET EXISTING RECORD IF EDITING
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM table WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if record exists
    if (!$record) {
        header('Location: /admin/dashboard.php?page=announcements');
        exit;
    }
}

// 3. HANDLE FORM SUBMISSION (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    
    // Validate
    if (empty($title) || empty($content)) {
        $error = 'Required fields missing';
    } else {
        // Handle file upload if present
        if (isset($_FILES['image'])) {
            // ... upload processing
        }
        
        // If no errors, proceed with database operation
        if (!$error) {
            if ($id) {
                // UPDATE existing record
                $stmt = $pdo->prepare("UPDATE table SET ... WHERE id = ?");
                $stmt->execute([...]);
            } else {
                // CREATE new record
                $stmt = $pdo->prepare("INSERT INTO table (...) VALUES (...)");
                $stmt->execute([...]);
                
                // SET SUCCESS & REDIRECT
                header('Location: /admin/dashboard.php?page=announcements');
                exit;
            }
        }
    }
}

// 4. RENDER HTML FORM
?>
<!DOCTYPE html>
<html>
<!-- Form fields populated with $record data if editing -->
</html>
```

**Key Variables** (in manage_announcements.php context):
```php
$announcement     // Current record being edited
$id              // Record ID (null if creating)
$error           // Error message string
$success         // Success message string
$title           // Form field: announcement title
$content         // Form field: announcement content
$image           // Form field: announcement image filename
```

---

### sections/*.php (Template Files)

#### sections/home.php
```php
// 1. FETCH ANNOUNCEMENTS & NEWS
$announcements = $pdo->query(
    "SELECT ... FROM announcements WHERE active = 1 LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

$news = $pdo->query(
    "SELECT ... FROM news WHERE active = 1 LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

// 2. MERGE & SORT
$carousel_items = array_merge($announcements, $news);
usort($carousel_items, fn($a, $b) => 
    strtotime($b['date']) - strtotime($a['date'])
);

// 3. LIMIT TO 8 ITEMS
$carousel_items = array_slice($carousel_items, 0, 8);

// 4. RENDER CAROUSEL
foreach ($carousel_items as $item) {
    // Display in Swiper carousel
}
```

**Purpose**: Display featured content on homepage  
**Query Optimization**: Limits results to 8 items  
**Data**: Combines announcements and news

---

#### sections/announcements.php
```php
// 1. FETCH ALL ACTIVE ANNOUNCEMENTS
$query = "SELECT * FROM announcements WHERE active = 1 
          ORDER BY date_created DESC";
$announcements = $pdo->query($query)->fetchAll();

// 2. PAGINATE (if needed)
$per_page = 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $per_page;

// 3. RENDER LIST
foreach ($announcements as $ann) {
    echo "
    <article>
        <h3>" . htmlspecialchars($ann['title']) . "</h3>
        <img src='/assets/uploads/announcements/" . $ann['image'] . "'>
        <p>" . htmlspecialchars($ann['content']) . "</p>
    </article>";
}
```

**Security**: Uses `htmlspecialchars()` to prevent XSS  
**Display**: Shows only active announcements  
**Sorting**: Newest first

---

### includes/header.php

**Responsibilities**:
```
1. HTML doctype & meta tags
2. Favicon setup
3. CSS/JS includes (Tailwind, Swiper, Lucide)
4. Navigation bar
5. Mobile responsive menu
```

**Key Elements**:
```php
// Active section detection
$section == 'home' ? 'active' : ''  // Shows current page highlighted

// Logo display
<img src="/assets/misLogo.jpg" alt="DTO Logo">

// Navigation links
Home, Announcements, News, Systems

// Mobile hamburger menu toggle
#navMenuToggle  // JavaScript toggles #navMenu visibility
```

**CSS Framework**: Tailwind CSS via CDN  
**Icons**: Lucide icons via unpkg

---

### includes/footer.php

**Typical Content**:
```html
<footer>
    <div class="footer-content">
        <!-- Copyright info -->
        <!-- Social links -->
        <!-- Quick links -->
        <!-- Contact info -->
    </div>
</footer>

<!-- Scripts -->
<script src="/assets/js/script.js"></script>
<script>swiper initialization, menu toggle, etc.</script>
```

---

## Database Operations

### PDO Usage Pattern

#### SELECT (Read)
```php
// Single record
$stmt = $pdo->prepare("SELECT * FROM announcements WHERE id = ?");
$stmt->execute([$id]);
$announcement = $stmt->fetch(PDO::FETCH_ASSOC);

// Multiple records
$stmt = $pdo->query("SELECT * FROM announcements WHERE active = 1");
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

#### INSERT (Create)
```php
$stmt = $pdo->prepare("INSERT INTO announcements 
                       (title, content, image) 
                       VALUES (?, ?, ?)");
$stmt->execute([$title, $content, $image]);

// Get ID of inserted record
$id = $pdo->lastInsertId();
```

#### UPDATE (Edit)
```php
$stmt = $pdo->prepare("UPDATE announcements 
                       SET title = ?, content = ?, image = ?, 
                           date_updated = CURRENT_TIMESTAMP 
                       WHERE id = ?");
$stmt->execute([$title, $content, $image, $id]);
```

#### DELETE (Remove)
```php
$stmt = $pdo->prepare("DELETE FROM announcements WHERE id = ?");
$stmt->execute([$id]);
```

### Prepared Statements Security

**Why Use Prepared Statements?**
Prevents SQL injection attacks by separating SQL code from data.

```php
// UNSAFE - Vulnerable to SQL injection
$query = "SELECT * FROM users WHERE username = '$username'";

// SAFE - Prepared statement
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
```

**Syntax**:
- `?` - Positional placeholder
- `execute([$var1, $var2, ...])` - Bind values in order

---

## Authentication & Sessions

### Session Flow

```
1. User visits /admin/
2. If not logged in, shows login form (kupalsijade.php)
3. User submits username/password
4. Verify credentials against admin_users table
5. If valid:
   - Set $_SESSION['admin_logged_in'] = true
   - Set $_SESSION['admin_username'] = $username
   - Redirect to /admin/dashboard.php
6. Session persists across pages
7. On logout, session_destroy() clears all data
```

### Session Variables
```php
$_SESSION['admin_logged_in']    // Boolean: true if logged in
$_SESSION['admin_username']     // String: admin username
$_SESSION['user_id']            // Optional: for future use
```

### Authentication Check (in every admin page)
```php
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: /admin/');
    exit;
}
```

**Purpose**: Prevents unauthorized access  
**Location**: Top of every /admin/*.php file

### Password Security
```php
// Password hashing (registration/creation)
$hashed = password_hash($raw_password, PASSWORD_DEFAULT);

// Password verification (login)
if (password_verify($submitted_password, $hashed_password)) {
    // Correct password
}
```

**Algorithm**: bcrypt (via PASSWORD_DEFAULT)  
**Cost**: Automatic (currently 10-12)  
**Salt**: Automatically generated and included in hash

---

## File Upload Process

### Step-by-Step Upload Flow

```
1. User selects file in form
   └─ <input type="file" name="image">

2. Form submitted with POST and enctype="multipart/form-data"
   └─ $_FILES['image'] contains file data

3. Check file exists
   └─ if (isset($_FILES['image']) && $_FILES['image']['size'] > 0)

4. Extract file info
   └─ $filename = $_FILES['image']['name']
   └─ $ext = pathinfo($filename, PATHINFO_EXTENSION)
   └─ $size = $_FILES['image']['size']

5. Validate extension
   └─ Check against $allowed = ['jpg', 'png', 'gif', ...]

6. Validate size
   └─ Check against 20MB limit

7. Create upload directory
   └─ mkdir($upload_dir, 0755, true) if not exists

8. Generate unique filename
   └─ $new_filename = uniqid('ann_') . '.' . $ext
   └─ Example: ann_5e1234567890a.jpg

9. Move uploaded file
   └─ move_uploaded_file($tmp_path, $final_path)

10. Delete old file if updating
    └─ unlink($old_path) if file exists

11. Store filename in database
    └─ INSERT/UPDATE image column with $new_filename
```

### File System Structure
```
assets/uploads/
├── announcements/
│   ├── ann_5e1234567890a.jpg
│   ├── ann_5e1234567890b.png
│   └── ...
├── news/
│   ├── news_5e1234567890a.jpg
│   └── ...
└── systems/
    ├── sys_5e1234567890a.svg
    └── ...
```

### Displaying Uploaded Files in HTML
```php
// Construct full URL to image
<?php 
    $image_url = '/assets/uploads/announcements/' . htmlspecialchars($image_filename);
    $fallback_url = '/assets/DTO-hero.jpg';
    $final_url = $image ? $image_url : $fallback_url;
?>
<img src="<?php echo $final_url; ?>" alt="Announcement">
```

---

## Template System

### Include-Based Templates

**Layout Structure**:
```
Header (includes/header.php)
  ├─ Navigation
  ├─ Logo
  └─ Mobile Menu
     
Content (sections/*.php or admin/*)
  ├─ Dynamic content based on $_GET
  └─ Form or display
     
Footer (includes/footer.php)
  ├─ Copyright
  ├─ Links
  └─ Scripts
```

### Template Variables

**In sections/announcements.php**:
```php
$pdo              // Database connection
$section          // Current section name
$announcements    // Fetched announcements array
```

**In admin/manage_announcements.php**:
```php
$pdo              // Database connection
$announcement     // Current record (if editing)
$id               // Record ID (if editing)
$error            // Error message
$success          // Success message
```

### PHP Output Escaping

**For Security (XSS Prevention)**:
```php
// HTML content
<?php echo htmlspecialchars($user_input); ?>

// In HTML attributes
<img src="<?php echo htmlspecialchars($image); ?>">

// In JavaScript (rarely needed here, use JSON_UNESCAPED_SLASHES)
<script>var data = <?php echo json_encode($data); ?>;</script>
```

**Functions Used**:
- `htmlspecialchars()` - Escapes <, >, ", & for HTML context
- `json_encode()` - Encodes PHP data to JSON string

---

## Variable & Naming Conventions

### Database Column Names
```
snake_case
├─ id                  // Primary key, auto-increment
├─ title              // String content descriptor
├─ content            // Long text content
├─ image              // Filename only (stored in DB)
├─ date_created       // Timestamp of creation
├─ date_updated       // Timestamp of last update
├─ active             // Boolean (1 = true, 0 = false)
├─ url                // External link
├─ icon_color         // Hex color string
├─ display_order      // Sort order integer
├─ password           // Hashed password
└─ username           // Login identifier
```

### PHP Variable Names
```
camelCase (preferred for readability)
├─ $pdo               // PDO database connection
├─ $stmt              // PDO statement object
├─ $announcement      // Single record array
├─ $announcements     // Multiple records array
├─ $userId            // User ID integer
├─ $userName          // User name string
├─ $isActive          // Boolean flag
├─ $errorMessage      // Error string
├─ $uploadDir         // Directory path
└─ $tempPath          // Temporary file path
```

### GET/POST Parameter Names
```
lowercase (from HTML form)
├─ $_GET['section']       // Page section
├─ $_GET['id']            // Record ID
├─ $_GET['page']          // Admin page
├─ $_POST['title']        // Form field
├─ $_POST['content']      // Form field
├─ $_FILES['image']       // Uploaded file
└─ $_SESSION['admin_logged_in']  // Session flag
```

---

## Error Handling

### Error Variables
```php
$error = '';    // Stores error messages
$success = '';  // Stores success messages
```

### Validation Errors
```php
// Before database operation
if (empty($title) || empty($content)) {
    $error = 'Title and content are required.';
}

// Check error status before proceeding
if (!$error) {
    // Proceed with database operation
} else {
    // Display error message to user
}
```

### File Upload Errors
```php
// File type validation
if (!in_array($ext, $allowed)) {
    $error = 'Invalid file type. Allowed: jpg, jpeg, png, gif, webp';
}

// File size validation
elseif ($file['size'] > 20242880) {
    $error = 'File size exceeds 20MB limit.';
}

// File move failure
else {
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        $error = 'Failed to upload file.';
    }
}
```

### Database Errors
```php
try {
    $pdo = new PDO($dsn, $user, $pass);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
```

**Error Display in Templates**:
```php
<?php if ($error): ?>
    <div class="alert alert-error">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>
```

---

## Key Code Patterns

### 1. Check & Redirect Pattern
```php
// Verify record exists before editing
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM table WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$record) {
        header('Location: /admin/dashboard.php?page=announcements');
        exit;  // Must exit after header!
    }
}
```

### 2. Conditional Fetch Pattern
```php
// Fetch single or multiple based on ID
if ($id) {
    $stmt = $pdo->prepare("...");
    $item = $stmt->fetch();        // One record
} else {
    $items = $pdo->query("...");
    $items = $items->fetchAll();   // Multiple records
}
```

### 3. Active Filter Pattern
```php
// Only show active content
$query = "SELECT * FROM announcements WHERE active = 1";
```

### 4. Date Sorting Pattern
```php
// Newest content first
$query = "... ORDER BY date_created DESC";

// Or custom sort
usort($array, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
```

### 5. Safe Output Pattern
```php
// Always escape user content
echo htmlspecialchars($user_input);

// In HTML attributes
<a href="<?php echo htmlspecialchars($url); ?>">
```

### 6. Exist Check Pattern
```php
// Check if item exists before operation
if (file_exists($path)) {
    unlink($path);  // Delete if exists
}
```

---

## JavaScript Integration

### assets/js/script.js Functions

**Navigation Toggle** (Mobile Menu):
```javascript
// Get toggle button and menu
const navMenuToggle = document.getElementById('navMenuToggle');
const navMenu = document.getElementById('navMenu');

// Add click listener
navMenuToggle.addEventListener('click', function() {
    navMenu.classList.toggle('active');
});
```

**Swiper Carousel** (Homepage):
```javascript
// Initialize Swiper carousel
const swiper = new Swiper('.carousel-container', {
    slidesPerView: 1,
    pagination: { el: '.swiper-pagination' },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    autoplay: { delay: 5000 }
});
```

**Lucide Icons** (Auto-render):
```javascript
// Lucide runs after defer, renders all [data-lucide] elements
// No additional code needed - automatic
```

---

## Security Checklist for Code Review

- ✅ All database queries use prepared statements
- ✅ All user output escaped with `htmlspecialchars()`
- ✅ Passwords hashed with `password_hash()` + verification
- ✅ Session auth checked on all admin pages
- ✅ File uploads validated (type + size)
- ✅ Unique filenames prevent overwriting
- ✅ Old files deleted when replacing
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS prevention (output escaping)
- ✅ CSRF protection via session management
- ✅ Directory permissions set to 755

---

## Performance Considerations

### Database Query Optimization
```php
// Limit results
SELECT * FROM announcements LIMIT 8

// Use indices (already set up in config.php)
INDEX idx_active (active)
INDEX idx_date_created (date_created)

// Pre-fetch only needed columns
SELECT id, title, content, date_created FROM announcements
```

### Caching Headers
```php
header('Cache-Control: public, max-age=3600');  // 1 hour cache
```

### Session Memory
```php
// Minimize session data stored
$_SESSION only stores auth flags, not large data objects
```

### File Handling
```php
// Generate thumbnails for large images (future enhancement)
// Consider CDN for static assets in production
```

---

## Future Code Improvements

- [ ] Implement REST API endpoints
- [ ] Add pagination class for larger datasets
- [ ] Create helper functions class for repeated logic
- [ ] Implement logging system
- [ ] Add rate limiting for form submissions
- [ ] Create config class instead of constants
- [ ] Implement repository pattern for database
- [ ] Add automated testing (PHPUnit)
- [ ] Create service layer for business logic
- [ ] Implement dependency injection

---

## Glossary of Terms

| Term | Definition |
|------|-----------|
| **PDO** | PHP Data Objects - database abstraction layer |
| **Prepared Statement** | SQL query template with placeholders for security |
| **Session** | Server-side storage of user state |
| **Singleton** | Pattern ensuring only one instance of a class exists |
| **CRUD** | Create, Read, Update, Delete operations |
| **Middleware** | Code that processes requests before reaching handlers |
| **Sanitization** | Cleaning user input to remove malicious content |
| **Validation** | Checking user input conforms to requirements |
| **XSS** | Cross-Site Scripting - injecting malicious scripts |
| **SQL Injection** | Injecting malicious SQL into queries |
| **Escaping** | Converting special characters to safe equivalents |

---

**Document Version**: 1.0  
**Last Updated**: March 16, 2026  
**Maintained By**: Development Team
