# ComeCode Backend Structure

## Directory Organization

```
backend/
├── admin.php              # Admin dashboard for community management
├── community.php          # Community page with gallery and events
├── config.php            # Configuration and utility functions
├── data/                 # Data storage directory
│   ├── community_data.json # Community gallery and events data
│   └── index.php         # Directory access protection
├── index.php            # Backend directory protection
└── .htaccess            # Web server security rules
```

## File Descriptions

- **admin.php**: Professional admin panel for uploading community images and events
- **community.php**: Dynamic community page that displays gallery and upcoming events
- **config.php**: Backend configuration with constants, initialization functions, and utility helpers
- **community_data.json**: JSON file storing gallery images and events data

## Security

- All backend files are protected from direct directory access
- JSON data files are protected from direct access via `.htaccess`
- Upload directory has PHP protection headers

## Styling & Assets

CSS and other frontend assets are located in the root with separate folders:

- `css/` - Stylesheets (admin.css, community.css)
- `img/` - Images and assets
- `uploads/` - User-uploaded files (community images)

## Setup Instructions for cPanel

1. Upload all backend files to `backend/` folder
2. Ensure `backend/data/` directory is writable (chmod 755)
3. Ensure `uploads/community/` directory is writable (chmod 755)
4. Verify `.htaccess` files are properly configured on your server

## URLs

- Community Page: `yourdomain.com/backend/community.php`
- Admin Dashboard: `yourdomain.com/backend/admin.php`
- Main Site: `yourdomain.com/index.html`
