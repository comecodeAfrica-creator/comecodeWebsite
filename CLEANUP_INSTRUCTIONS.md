# Cleanup Old Files

The following files in the root directory are now in the backend/ or css/ folders and should be deleted:

## Old Frontend Root Files (Delete These)

- `admin.php` - moved to `backend/admin.php`
- `community.php` - moved to `backend/community.php`
- `config.php` - moved to `backend/config.php`
- `community_data.json` - moved to `backend/data/community_data.json`
- `admin.css` - moved to `css/admin.css`
- `community.css` - moved to `css/community.css`

## How to Clean Up via Git

```bash
cd D:\comecode1\comecodeWebsite

# Remove old files from git and filesystem
git rm admin.php community.php config.php community_data.json admin.css community.css

# Commit changes
git add .
git commit -m "Cleanup: Remove old root backend files, now organized in backend/ and css/"

# Optionally, sync to cPanel
```

## Alternative: Manual Deletion

Delete the following files from the root directory:

1. admin.php
2. community.php
3. config.php
4. community_data.json
5. admin.css
6. community.css

After cleanup, your structure will be:

```
comecodeWebsite/
├── backend/          (all PHP files and data)
├── css/              (all stylesheets)
├── img/              (images and assets)
├── uploads/          (user-uploaded files)
├── index.html        (main site)
└── [other HTML pages]
```
