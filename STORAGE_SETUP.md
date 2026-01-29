# Storage Setup Instructions

## Problem
Images are not showing because the storage symlink is missing.

## Solution

Run this command in your terminal (PowerShell):

```powershell
cd c:\fullstack\htdocs\VetSys
php artisan storage:link
```

This will create a symbolic link from `public/storage` → `storage/app/public`, allowing uploaded images to be accessible via the web.

## Verify it worked

After running the command, you should see:
- A `storage` folder created in `public/storage`
- Images uploaded to `storage/app/public/pets/` will be accessible at `http://your-site/storage/pets/filename.jpg`

## Manual Alternative (if symlink doesn't work)

If the symlink command doesn't work on Windows, you can manually create the directory structure:

1. Create folder: `public/storage/pets`
2. Copy uploaded images from `storage/app/public/pets/` to `public/storage/pets/`

Or configure your web server to serve files from `storage/app/public` directly.
