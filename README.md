# DTO - Digital Transformation Office Website

## Project Overview

**DTO (Digital Transformation Office)** is a comprehensive web application designed to serve as the central hub for digital transformation initiatives. The platform features "Foresight And Futures Thinking Office" capabilities, providing a modern interface for managing announcements, news, calendar events, and integrated digital systems.

The application consists of:
- **Public Website**: A responsive front-end for visitors to view announcements, news, and access digital systems
- **Admin Dashboard**: A secure backend for administrators to manage all website content

---

## Features

### Public Website Features
- **Home Page**: Featured carousel showcasing latest announcements and news
- **Announcements Section**: Browse and view all active announcements with images
- **News Section**: Read latest news articles with multimedia support
- **Systems Section**: Access and view linked digital transformation systems
- **Responsive Design**: Mobile-friendly interface using Tailwind CSS
- **Dynamic Carousel**: Swiper.js-powered carousel for featured content

### Admin Panel Features
- **Secure Authentication**: Username/password login system with session management
- **Announcement Management**: Create, edit, and delete announcements with image uploads
- **News Management**: Manage news articles with featured content support
- **Calendar Management**: Create and manage events with date, time, location, and custom colors
- **Systems Management**: Add and manage digital systems with logos and URLs
- **Content Management**: Full CRUD (Create, Read, Update, Delete) operations for all content types
- **Image/Logo Upload**: Handle file uploads for announcements, news, and system logos

---

## Project Structure

```
dtoweb/
├── admin/                          # Admin panel directory
│   ├── dashboard.php              # Admin dashboard main page
│   ├── kupalsijade.php            # Admin login/authentication
│   ├── logout.php                 # Logout functionality
│   ├── manage_announcements.php   # Announcement management
│   ├── manage_calendar.php        # Calendar event management
│   ├── manage_news.php            # News management
│   └── manage_systems.php         # Systems management
├── assets/                         # Static assets
│   ├── css/
│   │   └── styles.css             # Custom CSS styles
│   ├── js/
│   │   └── script.js              # Client-side JavaScript
│   ├── uploads/                   # User uploads
│   │   ├── announcements/         # Announcement images
│   │   ├── news/                  # News images
│   │   └── systems/               # System logos
│   └── misLogo.jpg               # Website favicon
├── includes/                       # Shared templates
│   ├── header.php                 # Page header/navigation
│   └── footer.php                 # Page footer
├── sections/                       # Public page sections
│   ├── home.php                   # Home page content
│   ├── announcements.php          # Announcements listing
│   ├── news.php                   # News listing
│   └── systems.php                # Systems listing
├── config.php                      # Database configuration & initialization
├── index.php                       # Main entry point
├── setup_mysql.php                # Database setup script
├── dtoweb_hostinger.sql           # Database backup/structure
└── README.md                       # This file
```

---

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL/MariaDB 5.7 or higher
- Apache web server with mod_rewrite enabled
- Composer (optional, for dependency management)

### Step 1: Clone/Download Project
```bash
# Navigate to your web root
cd /path/to/webroot
# Copy dtoweb files to your directory
```

### Step 2: Database Configuration
Edit `config.php` and update database credentials:
```php
define('DB_HOST', 'localhost');      # MySQL host
define('DB_USER', 'root');           # MySQL username
define('DB_PASS', '');               # MySQL password
define('DB_NAME', 'dtoweb');         # Database name
```

### Step 3: Initial Database Setup
1. Run the database initialization:
   - Visit `http://localhost/setup_mysql.php` in your browser, OR
   - Import `dtoweb_hostinger.sql` into MySQL

2. Default admin credentials are created automatically:
   - **Username**: `admin`
   - **Password**: `admin123`
   - **Email**: `admin@dtoweb.com`

### Step 4: Set Folder Permissions
```bash
# Make upload directories writable
chmod -R 755 assets/uploads/
```

### Step 5: Access the Application
- **Public Site**: `http://localhost/dtoweb/`
- **Admin Panel**: `http://localhost/dtoweb/admin/`

---

## Database Structure

### Tables Overview

#### 1. **announcements**
Stores announcement content with multimedia support
```sql
- id (INT, PRIMARY KEY)
- title (VARCHAR 255)
- content (LONGTEXT)
- image (VARCHAR 255)
- date_created (DATETIME)
- date_updated (DATETIME)
- active (TINYINT)
```

#### 2. **news**
Stores news articles with featured content support
```sql
- id (INT, PRIMARY KEY)
- title (VARCHAR 255)
- content (LONGTEXT)
- image (VARCHAR 255)
- date_published (DATETIME)
- date_updated (DATETIME)
- active (TINYINT)
- featured (TINYINT)
```

#### 3. **calendar_events**
Manages calendar events with time and location
```sql
- id (INT, PRIMARY KEY)
- title (VARCHAR 255)
- description (LONGTEXT)
- event_date (DATE)
- start_time (TIME)
- end_time (TIME)
- location (VARCHAR 255)
- color (VARCHAR 7)
- created_at (DATETIME)
- updated_at (DATETIME)
- active (TINYINT)
```

#### 4. **systems**
Stores digital transformation system information
```sql
- id (INT, PRIMARY KEY)
- name (VARCHAR 255)
- description (LONGTEXT)
- url (VARCHAR 500)
- logo (VARCHAR 255)
- icon_color (VARCHAR 7)
- display_order (INT)
- created_at (DATETIME)
- updated_at (DATETIME)
- active (TINYINT)
```

#### 5. **admin_users**
Manages administrator accounts
```sql
- id (INT, PRIMARY KEY)
- username (VARCHAR 255, UNIQUE)
- password (VARCHAR 255)
- email (VARCHAR 255)
- created_at (DATETIME)
```

---

## Admin Panel Usage

### Login
1. Navigate to `http://localhost/dtoweb/admin/`
2. Enter credentials (default: admin/admin123)
3. Click "Login"

### Managing Announcements
- **Create**: Click "New Announcement" button
  - Enter title and content
  - Upload optional image
  - Click "Save"
  - Redirects to announcements list automatically
- **Edit**: Click edit icon on announcement
  - Update content
  - Change image if needed
  - Save changes
- **Delete**: Click delete icon to remove announcement

### Managing News
- Similar workflow to announcements
- Option to feature specific news articles
- Redirects to news list after creation

### Managing Calendar Events
- **Create**: Add event with:
  - Title and description
  - Date, start time, end time
  - Location
  - Custom color selection
  - Active status
- **Edit**: Modify event details
- **Delete**: Remove from calendar
- Redirects to calendar page after creation

### Managing Systems
- **Create**: Add digital system with:
  - System name and description
  - External URL link
  - Logo upload
  - Icon color
  - Display order
  - Active status
- **Edit**: Update system information
- **Delete**: Remove system
- Redirects to systems page after creation

### Dashboard
- View count of items in each category
- Quick links to management sections
- Announcements: `/admin/dashboard.php?page=announcements`
- News: `/admin/dashboard.php?page=news`
- Calendar: `/admin/dashboard.php?page=calendar`
- Systems: `/admin/dashboard.php?page=systems`

---

## Public Website Usage

### Navigation
- **Home**: Landing page with featured carousel
- **Announcements**: View all active announcements
- **News**: Browse latest news articles
- **Systems**: View and access linked systems

### URL Structure
- Home: `/`
- Announcements: `/?section=announcements`
- News: `/?section=news`
- Systems: `/?section=systems`

### Features
- Responsive carousel showcasing latest content
- Search and browse capabilities
- System links directly to external platforms
- Mobile-optimized design

---

## File Upload Management

### Supported File Types
- **Images**: JPG, JPEG, PNG, GIF, WebP
- **Logos**: JPG, JPEG, PNG, GIF, WebP, SVG

### Upload Limits
- **Images**: 20MB maximum
- **Logos**: 20MB maximum

### Upload Directories
```
assets/uploads/
├── announcements/    # Announcement images
├── news/             # News images
└── systems/          # System logos
```

### File Naming
- Automatically generated unique filenames with prefixes:
  - Announcements: `ann_[uniqueid].ext`
  - News: `news_[uniqueid].ext`
  - Systems: `sys_[uniqueid].ext`

---

## Configuration Details

### Database Auto-Initialization
- `config.php` automatically creates tables on first access
- Default admin user created if none exists
- UTF-8 charset support with unicode collation

### Session Management
- Strict session mode enabled
- Cookie-based sessions
- Session timeout: Browser close
- Automatic initialization check

### Security Features
- Password hashing using PHP's `password_hash()`
- SQL injection prevention via prepared statements
- XSS protection with `htmlspecialchars()` output encoding
- CSRF protection via session management

---

## Code Structure & Key Files

### config.php
- Database connection management (PDO)
- Database initialization with auto-table creation
- Admin user creation
- Session configuration

### index.php (Main Router)
```php
- Routes to different sections based on $_GET['section']
- Includes appropriate section template
- Main entry point for public website
```

### admin/dashboard.php
- Admin authentication check
- Content management interface
- Delete functionality with redirect
- Multi-page display (announcements, news, calendar, systems)

### admin/manage_*.php files
- Form handling (POST requests)
- File upload processing
- Database CRUD operations
- Automatic redirect after creation

### sections/ directory
- Individual page templates
- Content display logic
- Query database for active content

---

## Styling & Frontend

### Technologies Used
- **Tailwind CSS**: Utility-first CSS framework
- **Swiper.js**: Carousel functionality
- **Lucide Icons**: SVG icon library
- **Geist Font**: Primary font family

### Responsive Breakpoints
- Mobile-first design approach
- Tailwind breakpoints: sm, md, lg, xl, 2xl

### Customization
- Main styles: `assets/css/styles.css`
- Modify Tailwind config in HTML style tags
- Logo: `assets/misLogo.jpg`

---

## API/Integration Points

### Public Data Access
Content can be accessed programmatically:
```php
// Get announcements
$stmt = $pdo->query("SELECT * FROM announcements WHERE active = 1");

// Get news
$stmt = $pdo->query("SELECT * FROM news WHERE active = 1");

// Get calendar events
$stmt = $pdo->query("SELECT * FROM calendar_events WHERE active = 1");

// Get systems
$stmt = $pdo->query("SELECT * FROM systems WHERE active = 1");
```

---

## Troubleshooting

### Common Issues

**Database Connection Error**
- Verify MySQL is running
- Check credentials in `config.php`
- Ensure database user has correct permissions

**File Upload Issues**
- Check folder permissions on `assets/uploads/`
- Verify file type is supported
- Check file size doesn't exceed limit

**Admin Login Not Working**
- Clear browser cookies
- Verify session.save_path is writable
- Check browser console for errors

**No Data Showing**
- Verify content is marked as active in database
- Check database has been initialized
- Verify queries return results

---

## Performance Optimization

### Database Indexes
- All tables include indices on:
  - `active` column (for filtering)
  - Date columns (for sorting)
  - Primary keys (auto-indexed)

### Caching Headers
- HTTP caching enabled (3600s max-age)
- Pragma cache headers set
- Optimized for static content serving

### Query Optimization
- Limited carousel to 8 most recent items
- Prepared statements prevent injection
- Database charset optimized for UTF-8

---

## Security Recommendations

1. **Change Admin Password**
   - LOGIN and change default password immediately
   - Use strong passwords (mix of uppercase, lowercase, numbers, symbols)

2. **Environment Variables**
   - Move database credentials to `.env` file in production
   - Keep `config.php` out of version control

3. **File Permissions**
   - Set `uploads/` to 755 at minimum
   - Prevent direct PHP execution in upload directories

4. **Regular Backups**
   - Schedule regular database backups
   - Use `dtoweb_hostinger.sql` for manual exports

5. **HTTPS**
   - Enable SSL/TLS in production
   - Update site URL references accordingly

---

## Future Enhancement Ideas

- [ ] Two-factor authentication (2FA)
- [ ] Role-based access control (RBAC)
- [ ] Email notifications for new content
- [ ] Search functionality
- [ ] Content versioning/audit trail
- [ ] Scheduled publishing (date-based)
- [ ] User comments/feedback
- [ ] Analytics dashboard
- [ ] Multi-language support
- [ ] REST API for third-party integration
- [ ] Content scheduling
- [ ] Bulk import/export

---

## Maintenance

### Regular Tasks
- Monitor upload folder sizes
- Review database backups
- Check for broken system links
- Update content regularly
- Monitor server logs for errors

### Database Maintenance
```sql
-- Optimize tables
OPTIMIZE TABLE announcements, news, calendar_events, systems, admin_users;

-- Check table integrity
CHECK TABLE announcements, news, calendar_events, systems, admin_users;

-- View database size
SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
FROM information_schema.TABLES
WHERE table_schema = 'dtoweb';
```

---

## Support & Documentation

### Key Concepts
- **Session**: User login state management
- **PDO**: PHP Data Objects for database access
- **Prepared Statements**: SQL injection prevention
- **Auto-increment ID**: Unique record identification
- **Timestamp**: Automatic date tracking

### Useful Resources
- [PHP Manual](https://www.php.net/manual/)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [Swiper.js Documentation](https://swiperjs.com/)
- [Lucide Icons](https://lucide.dev/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

---

## License & Credits

**Project**: DTO - Digital Transformation Office Website
**Created**: 2026
**Version**: 1.0.0

---

## Changelog

### Version 1.0.0 (Initial Release)
- ✅ Public website with announcements, news, and systems
- ✅ Admin dashboard with full CRUD operations
- ✅ Calendar event management
- ✅ File upload support
- ✅ Responsive design
- ✅ Database auto-initialization
- ✅ Session-based authentication
- ✅ Featured carousel functionality

---

**Last Updated**: March 16, 2026  
**Maintained By**: Development Team
