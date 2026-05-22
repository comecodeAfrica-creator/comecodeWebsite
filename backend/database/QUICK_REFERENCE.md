# Database Quick Reference

## Setup Checklist

```
☐ Edit backend/database/db_config.php with your credentials
☐ Import backend/database/schema.sql into MySQL
☐ Verify connection works
☐ Run migration script (if needed)
☐ Test admin.php dashboard
```

---

## Common Operations

### View All Gallery Images

```php
$gallery = getAll("SELECT * FROM gallery ORDER BY created_at DESC");
foreach ($gallery as $item) {
    echo $item['image'] . " - " . $item['caption'];
}
```

### Get All New Contact Submissions

```php
$new = getAll(
    "SELECT * FROM contact_submissions WHERE status = ? ORDER BY created_at DESC",
    ['new'],
    's'
);
echo "New submissions: " . count($new);
```

### Add Gallery Image

```php
$id = insertRecord('gallery', [
    'image' => 'uploads/photo.jpg',
    'caption' => 'Beautiful moment'
]);
echo "Added with ID: " . $id;
```

### Update Contact Status

```php
updateRecord(
    'contact_submissions',
    ['status' => 'replied'],
    'id = ?',
    [5]
);
```

### Get About Content

```php
$about = getOne("SELECT * FROM about_content LIMIT 1");
echo $about['hero_title'];
echo $about['mission'];
```

### Get Site Settings

```php
$email = getOne(
    "SELECT setting_value FROM site_settings WHERE setting_key = ?",
    ['site_email'],
    's'
);
echo $email['setting_value'];
```

### Count Total Events

```php
$count = countRecords('events');
echo "Total events: " . $count;
```

### Get Recent Events

```php
$upcoming = getAll(
    "SELECT * FROM events WHERE date >= CURDATE() ORDER BY date ASC LIMIT 5"
);
```

### Delete Old Contact Submission

```php
deleteRecord('contact_submissions', 'id = ?', [123]);
```

---

## File Locations

```
backend/
├── admin.php                          ← Main dashboard
├── config.php                         ← PHP utilities
├── database/
│   ├── db_config.php                 ← DATABASE CREDENTIALS (Edit this!)
│   ├── schema.sql                    ← Database structure (Run this!)
│   ├── migrate.php                   ← Migration tool
│   ├── README.md                     ← Full docs
│   └── STRUCTURE.md                  ← Schema details
├── DATABASE_SETUP.md                 ← Setup guide
├── DATABASE_SUMMARY.md               ← This summary
└── ADMIN_DASHBOARD.md                ← Dashboard features
```

---

## Database Credentials

**File**: `backend/database/db_config.php`

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'comecode_db');
```

---

## Connection Test

```php
if (testDBConnection()) {
    echo "✓ Database connected!";
} else {
    echo "✗ Connection failed";
}
```

---

## Import Schema

**phpMyAdmin Method:**

1. Open phpMyAdmin
2. Click "SQL" tab
3. Copy `backend/database/schema.sql`
4. Paste and click Execute

**Command Line:**

```bash
mysql -u root -p comecode_db < backend/database/schema.sql
```

---

## Data Types

| Type         | Example                        |
| ------------ | ------------------------------ |
| VARCHAR(255) | "John Doe", "john@example.com" |
| TEXT         | Long descriptions, messages    |
| DATE         | "2024-05-22"                   |
| TIMESTAMP    | Auto date/time                 |
| ENUM         | 'new', 'replied', 'archived'   |
| INT          | 1, 100, 5000                   |

---

## MySQL Queries

### Show All Databases

```sql
SHOW DATABASES;
```

### Use Database

```sql
USE comecode_db;
```

### Show All Tables

```sql
SHOW TABLES;
```

### Show Table Structure

```sql
DESCRIBE gallery;
```

### Count Records

```sql
SELECT COUNT(*) FROM gallery;
```

### View All Records

```sql
SELECT * FROM gallery;
```

### Delete All Records (Be careful!)

```sql
TRUNCATE TABLE gallery;
```

---

## PHP Helper Functions

### All Available Functions

```php
// Query execution
executeQuery($query, $params, $types)
getOne($query, $params, $types)
getAll($query, $params, $types)

// CRUD operations
insertRecord($table, $data)
updateRecord($table, $data, $where, $params)
deleteRecord($table, $where, $params)

// Utilities
countRecords($table, $where, $params)
testDBConnection()
```

---

## Table Relationships

```
gallery ────┐
            ├──→ No FK (independent)
events ─────┤
            │
contact_submissions  ← Independent
            │
about_content ───────┤
            │
site_settings ───────┘

No foreign keys = Better flexibility
```

---

## Indexes (For Performance)

Created on:

- `created_at` columns (for sorting)
- `status` in contact_submissions (for filtering)
- `email` in contact_submissions (for searching)
- `setting_key` in site_settings (for lookups)

---

## Common Patterns

### Get Latest 10 Records

```php
getAll("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 10");
```

### Search

```php
getAll(
    "SELECT * FROM contact_submissions WHERE name LIKE ?",
    ['%John%'],
    's'
);
```

### Pagination

```php
$page = 2;
$limit = 20;
$offset = ($page - 1) * $limit;
getAll("SELECT * FROM gallery LIMIT ?, ?", [$offset, $limit], 'ii');
```

### Filter by Date

```php
getAll(
    "SELECT * FROM events WHERE date BETWEEN ? AND ?",
    ['2024-01-01', '2024-12-31'],
    'ss'
);
```

---

## Troubleshooting

### Check Table Exists

```sql
SHOW TABLES;
```

### Check User Permissions

```sql
SHOW GRANTS FOR 'root'@'localhost';
```

### View Error Log

Check MySQL error log or run:

```sql
SHOW ENGINE INNODB STATUS;
```

---

## Backup Commands

### Backup Single Table

```bash
mysqldump -u root -p comecode_db gallery > gallery_backup.sql
```

### Backup All Databases

```bash
mysqldump -u root -p --all-databases > full_backup.sql
```

### Restore

```bash
mysql -u root -p < backup.sql
```

---

## Performance Tips

✓ Use indexes on frequently searched columns  
✓ Limit queries with LIMIT clause  
✓ Use TIMESTAMP for auto date tracking  
✓ Regular backups  
✓ Archive old records  
✓ Monitor query performance

---

**For more details, see:**

- `DATABASE_SETUP.md` - Installation
- `STRUCTURE.md` - Schema details
- `README.md` - Full documentation

---

Last Updated: May 2026
