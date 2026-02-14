# Quick Domain Deployment Checklist

## 🔧 **Step 1: Test Current Configuration**
Visit: `https://yourdomain.com/test-domain`

This will show you what URLs your application is actually using.

## 🚨 **Common Issues & Fixes:**

### **Issue 1: Wrong APP_URL**
If `app_url` shows wrong domain:
1. Edit `.env` file on your server
2. Set: `APP_URL=https://yourdomain.com`
3. Run: `php artisan config:clear`

### **Issue 2: Public Folder Not Set**
If you see 404 errors:
1. Point your domain to: `/public` folder
2. Or configure web server to use `/public` as document root

### **Issue 3: .htaccess Missing**
If URLs don't work:
1. Ensure `.htaccess` exists in `/public` folder
2. Content should be:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

## 📋 **Quick Fix Steps:**

### **1. Update .env**
```env
APP_URL=https://yourdomain.com
APP_ENV=production
```

### **2. Clear Caches**
```bash
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

### **3. Check File Permissions**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### **4. Test API Endpoints**
- `https://yourdomain.com/api/events`
- `https://yourdomain.com/api/developer`
- `https://yourdomain.com/test-domain`

## 🔍 **Debug Information:**

### **If API Still Not Working:**
1. Check what `/test-domain` shows
2. Verify `APP_URL` is correct
3. Ensure web server points to `/public`
4. Check error logs: `storage/logs/laravel.log`

### **Common Server Configurations:**

**cPanel:**
- Document root: `/public_html/yourproject/public`
- Or use `.htaccess` to redirect

**Plesk:**
- Document root: `/httpdocs/yourproject/public`

**Direct Apache/Nginx:**
- Point domain to `/public` folder

## ⚡ **Quick Test:**
1. Visit: `https://yourdomain.com/test-domain`
2. Check if `app_url` matches your domain
3. If not, update `.env` and clear caches
4. Test API: `https://yourdomain.com/api/events`

This should fix your API integration on your domain! 🚀
