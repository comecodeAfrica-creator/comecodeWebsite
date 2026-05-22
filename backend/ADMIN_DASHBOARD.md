# ComeCode Admin Dashboard

Complete backend admin dashboard for managing all site content.

## Features

### 1. **Dashboard Tab**

- Overview statistics showing:
  - Total gallery images
  - Community events count
  - Contact submissions count
  - New unread messages
- Quick preview of recent gallery images and upcoming events

### 2. **Community Tab**

- **Upload Gallery Images**: Add images with captions to the gallery
- **Create Events**: Publish community events with title, date, description, and image
- **Manage Gallery**: View all gallery images and delete as needed
- **Manage Events**: View all events and delete as needed

### 3. **Contacts Tab**

- **View All Submissions**: See all contact form submissions
- **Add Manual Submissions**: Manually add contact entries
- **Status Management**: Mark submissions as:
  - New (unread)
  - Replied (action taken)
  - Archived (completed)
- **Delete Submissions**: Remove contact entries
- Each submission shows: name, email, company, service, message, and timestamp

### 4. **About Tab**

- **Edit About Page Content**:
  - Hero title and subtitle
  - Hero description
  - Mission statement
  - Vision statement
- Real-time preview of current about content

### 5. **Settings Tab**

- **Site Information**:
  - Site name
  - Contact email
  - Phone number
  - Physical address
  - Site description
- **Social Media Links**:
  - Facebook URL
  - Twitter URL
  - LinkedIn URL
  - Instagram URL
- Settings preview panel

## Data Storage

### Database Option (Recommended)

Your admin dashboard now has **full MySQL database support**! All data can be stored in a MySQL database for better performance and scalability.

**Database Tables:**

- **gallery**: Gallery images and captions
- **events**: Community events with dates and descriptions
- **contact_submissions**: Contact form submissions with status tracking
- **about_content**: About page text content
- **site_settings**: Site configuration and social media links

**Setup**: See `DATABASE_SETUP.md` for quick installation instructions.

### JSON Files (Legacy)

For backward compatibility, data can still be stored in JSON files:

- **community_data.json**: Gallery images and events
- **contact_data.json**: Contact form submissions
- **about_data.json**: About page content
- **settings_data.json**: Site settings and social links

**Location**: `/backend/data/`

### Migration

If you have existing JSON data, use the migration script:

```bash
php backend/database/migrate.php
```

This will automatically import all data from JSON to MySQL.

## How to Use

1. Navigate to `backend/admin.php` in your browser
2. Use the tab navigation to access different sections
3. Fill in forms and submit to save data
4. View real-time previews and statistics
5. Manage all content without touching the frontend

## Backend Files

- **admin.php**: Main admin dashboard interface
- **config.php**: Data management functions and utilities
- **data/**: JSON storage files (optional, for legacy support)

**Database Files** (New):

- **database/db_config.php**: MySQL connection configuration
- **database/schema.sql**: Database structure and tables
- **database/migrate.php**: Script to migrate JSON data to MySQL
- **DATABASE_SETUP.md**: Database installation guide
- **STRUCTURE.md**: Database schema documentation

## Features Added

✅ Community gallery and events management
✅ Contact submission tracking and management
✅ About page content editor
✅ Site settings and configuration
✅ Admin dashboard with tabs and statistics
✅ Status tracking for submissions
✅ **MySQL database integration** with full schema
✅ Database helper functions for queries
✅ Migration script for JSON to MySQL conversion
✅ No frontend modifications required

## Notes

- All text inputs are sanitized for security
- File uploads support JPG, JPEG, PNG, WEBP (max 5MB)
- Contact submissions include timestamp and unique ID
- Settings update in real-time
- Tab navigation preserves state in URL

**Database Notes:**

- Uses MySQL with prepared statements for security
- All queries are parameterized to prevent SQL injection
- Keep `db_config.php` with real credentials out of version control
- Use strong MySQL passwords
- Regular database backups recommended
