# ComeCode Database Setup - Quick Start

## What's New?

Your admin dashboard now has **full MySQL database support**! The system includes:

✅ Complete MySQL schema with 5 tables  
✅ Database configuration file  
✅ Helper functions for queries  
✅ Migration script for existing data  
✅ Comprehensive documentation

---

## 📋 Files Created

```
backend/database/
├── schema.sql           ← Database structure (import this!)
├── db_config.php        ← Database credentials (edit this!)
├── migrate.php          ← Data migration script (run after setup)
└── README.md            ← Full documentation
```

---

## 🚀 Installation Steps

### 1️⃣ Update Database Credentials

Edit `backend/database/db_config.php`:

```php
define('DB_HOST', 'localhost');    // Your host
define('DB_USER', 'root');         // Your username
define('DB_PASS', 'your_password'); // Your password
define('DB_NAME', 'comecode_db');  // Database name
```

### 2️⃣ Create Database

**Choose ONE method:**

**Method A - phpMyAdmin (Easiest)**

1. Open `http://localhost/phpmyadmin`
2. Go to SQL tab
3. Copy-paste entire `backend/database/schema.sql`
4. Click Execute

**Method B - Command Line**

```bash
mysql -u root -p < backend/database/schema.sql
```

**Method C - MySQL CLI**

```bash
mysql -u root -p
CREATE DATABASE comecode_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE comecode_db;
# Then paste schema.sql content
```

### 3️⃣ Test Connection

Visit: `http://yoursite/backend/admin.php`

The dashboard should load without errors.

### 4️⃣ Migrate Existing Data (Optional)

If you have data in JSON files, run:

```bash
php backend/database/migrate.php
```

This will automatically import all your data from JSON to MySQL.

---

## 🗄️ Database Tables

| Table                 | Purpose                   | Records  |
| --------------------- | ------------------------- | -------- |
| `gallery`             | Gallery images & captions | Multiple |
| `events`              | Community events          | Multiple |
| `contact_submissions` | Contact form submissions  | Multiple |
| `about_content`       | About page text           | 1        |
| `site_settings`       | Site configuration        | Multiple |

---

## ⚙️ Configuration

### In config.php

The main config file (`backend/config.php`) now uses database functions. Make sure it includes:

```php
require_once __DIR__ . '/database/db_config.php';
```

### In admin.php

The admin panel is ready to use with the database. No changes needed!

---

## 🔧 Troubleshooting

### "Cannot connect to database"

- Check MySQL is running
- Verify credentials in `db_config.php`
- Ensure database was created

### "Table doesn't exist"

- Re-run `schema.sql`
- Check MySQL user permissions

### Data not showing

- Run migration script: `php backend/database/migrate.php`
- Check database tables exist: `SHOW TABLES;`

---

## 📚 What's Stored Where?

### Gallery Images & Events

```sql
SELECT * FROM gallery;
SELECT * FROM events;
```

### Contact Submissions

```sql
SELECT * FROM contact_submissions WHERE status = 'new';
```

### About Page Content

```sql
SELECT * FROM about_content;
```

### Site Settings

```sql
SELECT * FROM site_settings WHERE setting_key = 'site_email';
```

---

## 🛡️ Security

✅ All queries use prepared statements  
✅ Data is escaped and sanitized  
✅ Keep `db_config.php` out of version control  
✅ Use strong MySQL passwords

---

## 📖 Full Documentation

See `backend/database/README.md` for:

- Detailed schema documentation
- Helper function references
- Advanced configuration
- Performance tips

---

## ✨ Next Steps

1. **Import schema.sql** into your MySQL database
2. **Update credentials** in db_config.php
3. **Test** by accessing admin.php
4. **Migrate data** if you have existing JSON files
5. **Start using** the database!

---

**All set! Your database is now ready to use.** 🎉
