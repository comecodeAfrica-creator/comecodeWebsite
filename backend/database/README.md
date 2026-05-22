# ComeCode Database Setup Guide

## Overview

The ComeCode admin dashboard uses **MySQL** as its primary database. This guide will walk you through setting up the database from scratch.

## Prerequisites

- **MySQL 5.7+** or **MariaDB 10.2+**
- **phpMyAdmin** (optional, for GUI management)
- **Command line access** to MySQL

## Quick Setup (3 Steps)

### Step 1: Configure Database Credentials

Edit `backend/database/db_config.php` and update your MySQL credentials:

```php
define('DB_HOST', 'localhost');    // Your MySQL host
define('DB_USER', 'root');         // Your MySQL username
define('DB_PASS', '');             // Your MySQL password
define('DB_NAME', 'comecode_db');  // Database name
```

### Step 2: Create Database and Tables

**Option A: Using phpMyAdmin**

1. Open phpMyAdmin
2. Go to "SQL" tab
3. Copy and paste the entire contents of `backend/database/schema.sql`
4. Click "Execute"

**Option B: Using Command Line**

```bash
mysql -u root -p < backend/database/schema.sql
```

**Option C: Manual Creation**

1. Open MySQL command line: `mysql -u root -p`
2. Run: `CREATE DATABASE comecode_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
3. Copy the schema.sql content and execute

### Step 3: Verify Installation

Access `backend/admin.php` in your browser. The dashboard should now use the MySQL database.

---

## Database Schema

### Tables Overview

#### 1. **gallery**

```
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- image (VARCHAR 255) - Image URL/path
- caption (TEXT) - Image description
- created_at (TIMESTAMP) - Creation date
- updated_at (TIMESTAMP) - Last update
```

#### 2. **events**

```
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- title (VARCHAR 255) - Event name
- date (DATE) - Event date
- description (TEXT) - Event details
- image (VARCHAR 255) - Event image URL
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### 3. **contact_submissions**

```
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- name (VARCHAR 255) - Contact name
- email (VARCHAR 255) - Email address
- company (VARCHAR 255) - Company name
- service (VARCHAR 255) - Interested service
- message (TEXT) - Contact message
- status (ENUM) - 'new' | 'replied' | 'archived'
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### 4. **about_content**

```
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- hero_title (VARCHAR 255)
- hero_subtitle (VARCHAR 255)
- hero_description (TEXT)
- mission (TEXT)
- vision (TEXT)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### 5. **site_settings**

```
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- setting_key (VARCHAR 255) - Unique setting name
- setting_value (LONGTEXT) - Setting value
- setting_type (ENUM) - 'string' | 'json' | 'integer' | 'boolean'
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

---

## Configuration

### Update config.php

The main `config.php` file has been updated to support both JSON and MySQL. To use MySQL, make sure:

1. Database credentials are set in `database/db_config.php`
2. Include the database config in your admin.php

### Include Database in admin.php

```php
<?php
require_once __DIR__ . '/database/db_config.php';
require_once __DIR__ . '/config.php';
```

---

## Using the Database

### Helper Functions Available

**Query Execution:**

```php
// Get single row
$record = getOne("SELECT * FROM gallery WHERE id = ?", [1], "i");

// Get multiple rows
$records = getAll("SELECT * FROM events ORDER BY date DESC");

// Insert record
$id = insertRecord('gallery', [
    'image' => 'path/to/image.jpg',
    'caption' => 'Gallery caption'
]);

// Update record
updateRecord('events',
    ['title' => 'New Title'],
    'id = ?',
    [1]
);

// Delete record
deleteRecord('contact_submissions', 'id = ?', [1]);

// Count records
$count = countRecords('gallery', 'created_at > ?', ['2024-01-01']);

// Test connection
if (testDBConnection()) {
    echo "Database connected!";
}
```

---

## Migration from JSON to MySQL

If you have existing JSON data, follow these steps:

1. **Backup JSON files** (in `backend/data/`)
2. **Import data** using SQL INSERT statements
3. **Update config.php** to use database functions

Sample migration script location: `backend/database/migrate.php` (optional)

---

## Troubleshooting

### Error: "Connection refused"

- Check if MySQL is running
- Verify host, user, and password in `db_config.php`
- Make sure database name matches

### Error: "Table doesn't exist"

- Run the schema.sql file again
- Check that database was created: `SHOW DATABASES;`
- Verify tables: `USE comecode_db; SHOW TABLES;`

### Error: "Access denied"

- Check credentials in `db_config.php`
- Verify MySQL user permissions: `GRANT ALL ON comecode_db.* TO 'user'@'localhost';`

### No data appearing

- Check if you migrated JSON data
- Verify INSERT statements executed without errors
- Test with: `SELECT COUNT(*) FROM gallery;`

---

## Additional Settings

### Create Admin User (Optional)

Uncomment the admin_users table in `schema.sql` if you want user authentication:

```sql
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
```

Then add users:

```php
$passwordHash = password_hash('your_password', PASSWORD_BCRYPT);
insertRecord('admin_users', [
    'username' => 'admin',
    'password' => $passwordHash,
    'email' => 'admin@comecode.com'
]);
```

---

## Performance Tips

1. **Indexes**: All important columns are indexed for fast queries
2. **Charset**: Using `utf8mb4` for emoji and special character support
3. **Timestamps**: Automatic creation and update timestamps
4. **Prepared Statements**: All queries use parameterized statements for security

---

## Support

For issues or questions, check:

- MySQL documentation: https://dev.mysql.com/doc/
- PHP MySQLi: https://www.php.net/manual/en/book.mysqli.php
- phpMyAdmin: https://www.phpmyadmin.net/

---

**Note**: Keep your database credentials secure and never commit `db_config.php` with real credentials to version control.
