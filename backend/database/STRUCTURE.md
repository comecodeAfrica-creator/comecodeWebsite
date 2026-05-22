# ComeCode Database Structure

## Entity Relationship Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                                                             │
│    ┌──────────────┐      ┌──────────────┐                 │
│    │   GALLERY    │      │    EVENTS    │                 │
│    ├──────────────┤      ├──────────────┤                 │
│    │ id (PK)      │      │ id (PK)      │                 │
│    │ image        │      │ title        │                 │
│    │ caption      │      │ date         │                 │
│    │ created_at   │      │ description  │                 │
│    │ updated_at   │      │ image        │                 │
│    └──────────────┘      │ created_at   │                 │
│                          │ updated_at   │                 │
│                          └──────────────┘                 │
│                                                             │
│    ┌────────────────────────┐                             │
│    │ CONTACT_SUBMISSIONS    │                             │
│    ├────────────────────────┤                             │
│    │ id (PK)                │                             │
│    │ name                   │                             │
│    │ email                  │                             │
│    │ company                │                             │
│    │ service                │                             │
│    │ message                │                             │
│    │ status (new|replied|...) │                             │
│    │ created_at             │                             │
│    │ updated_at             │                             │
│    └────────────────────────┘                             │
│                                                             │
│    ┌────────────────┐        ┌──────────────────┐        │
│    │ ABOUT_CONTENT  │        │ SITE_SETTINGS    │        │
│    ├────────────────┤        ├──────────────────┤        │
│    │ id (PK)        │        │ id (PK)          │        │
│    │ hero_title     │        │ setting_key (U)  │        │
│    │ hero_subtitle  │        │ setting_value    │        │
│    │ hero_desc.     │        │ setting_type     │        │
│    │ mission        │        │ created_at       │        │
│    │ vision         │        │ updated_at       │        │
│    │ created_at     │        └──────────────────┘        │
│    │ updated_at     │                                     │
│    └────────────────┘                                     │
│                                                             │
└─────────────────────────────────────────────────────────────┘

Legend:
  PK  = Primary Key (Unique identifier)
  U   = Unique (Cannot have duplicates)
  FG  = Foreign Key (Links to another table)
```

---

## Table Details

### 📷 GALLERY

**Purpose**: Store community gallery images  
**Key Fields**:

- `id` - Unique identifier
- `image` - Image file path/URL
- `caption` - Image description
- `created_at` - When image was added
- `updated_at` - Last modification time

**Indexes**: created_at (for sorting)

---

### 📅 EVENTS

**Purpose**: Store community events  
**Key Fields**:

- `id` - Unique identifier
- `title` - Event name
- `date` - Event date
- `description` - Event details
- `image` - Event promotional image
- `created_at` - When event was created
- `updated_at` - Last modification time

**Indexes**: date, created_at (for filtering and sorting)

---

### 💬 CONTACT_SUBMISSIONS

**Purpose**: Store visitor contact form submissions  
**Key Fields**:

- `id` - Unique identifier
- `name` - Contact person's name
- `email` - Contact email
- `company` - Company name (optional)
- `service` - Service interested in
- `message` - Contact message
- `status` - One of: new | replied | archived
- `created_at` - When submitted
- `updated_at` - Last status change

**Indexes**: status, created_at, email (for filtering and searching)

---

### 📝 ABOUT_CONTENT

**Purpose**: Store about page text content  
**Key Fields**:

- `id` - Unique identifier (typically 1)
- `hero_title` - Main heading
- `hero_subtitle` - Subheading
- `hero_description` - Intro text
- `mission` - Company mission statement
- `vision` - Company vision statement
- `created_at` - When created
- `updated_at` - Last update

**Note**: Typically has only 1 record (the current about content)

---

### ⚙️ SITE_SETTINGS

**Purpose**: Store site configuration and preferences  
**Key Fields**:

- `id` - Unique identifier
- `setting_key` - Setting name (e.g., "site_email")
- `setting_value` - Setting value
- `setting_type` - Value type: string | json | integer | boolean
- `created_at` - When created
- `updated_at` - Last update

**Examples**:

```
site_name         → "ComeCode" (string)
site_email        → "info@comecode.com" (string)
site_phone        → "+234 123 456 7890" (string)
site_address      → "Lagos, Nigeria" (string)
social_links      → {"facebook": "...", ...} (json)
```

**Index**: setting_key (for fast lookups)

---

## Query Examples

### Get all gallery images

```sql
SELECT * FROM gallery ORDER BY created_at DESC;
```

### Get upcoming events

```sql
SELECT * FROM events WHERE date >= CURDATE() ORDER BY date ASC;
```

### Get new contact submissions

```sql
SELECT * FROM contact_submissions WHERE status = 'new' ORDER BY created_at DESC;
```

### Count submissions by status

```sql
SELECT status, COUNT(*) as count FROM contact_submissions GROUP BY status;
```

### Get about content

```sql
SELECT * FROM about_content LIMIT 1;
```

### Get all settings

```sql
SELECT setting_key, setting_value FROM site_settings;
```

### Update contact status

```sql
UPDATE contact_submissions SET status = 'replied' WHERE id = 5;
```

---

## Data Types Reference

| Type         | Size      | Use Case               |
| ------------ | --------- | ---------------------- |
| INT          | 4 bytes   | IDs, counts, numbers   |
| VARCHAR(255) | Variable  | Names, titles, URLs    |
| TEXT         | 65KB      | Descriptions, messages |
| LONGTEXT     | 4GB       | Long content, JSON     |
| DATE         | 3 bytes   | Dates only             |
| TIMESTAMP    | 4 bytes   | Dates with time        |
| ENUM         | 1-2 bytes | Fixed options          |

---

## Character Set & Collation

All tables use:

- **Charset**: `utf8mb4` (supports emoji, special characters)
- **Collation**: `utf8mb4_unicode_ci` (case-insensitive comparison)

---

## Performance Features

✅ **Indexes**:

- All primary keys are indexed
- created_at columns indexed for sorting
- status indexed for filtering contacts
- email indexed for searching

✅ **Timestamps**:

- Automatic creation timestamp
- Automatic update timestamp
- Useful for auditing and sorting

✅ **Constraints**:

- Unique setting_key (no duplicate settings)
- Auto-increment IDs
- Proper data types for efficient storage

---

## Database Statistics

```
Total Tables:        5
Total Columns:       30+
Estimated Size:      < 1MB (with thousands of records)
Backup Frequency:    Weekly (recommended)
```

---

## Backup & Restore

### Backup Database

```bash
mysqldump -u root -p comecode_db > backup.sql
```

### Restore Database

```bash
mysql -u root -p comecode_db < backup.sql
```

---

## Scalability Notes

This database design can easily handle:

- ✅ 10,000+ gallery images
- ✅ 1,000+ events
- ✅ 100,000+ contact submissions
- ✅ Multiple site settings

For larger scales, consider:

- Adding database indexes
- Archiving old contact submissions
- Implementing pagination queries
- Using a database cache layer

---

See `backend/database/README.md` for more details!
