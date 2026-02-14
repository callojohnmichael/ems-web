# API Deployment Guide - Domain Configuration

## 🔍 **Problem Identified:**
Your API works offline but not on your domain because URLs were hardcoded to `http://ems-web.test` instead of using dynamic URLs.

## ✅ **Fixed Issues:**

### **1. Dynamic URL Configuration**
All hardcoded URLs have been replaced with Laravel's `url()` helper:
- **Before**: `http://ems-web.test/api/events`
- **After**: `{{ url('/api/events') }}`

### **2. Updated Files:**
- `resources/views/api/developer-portal.blade.php` - All API URLs now dynamic
- `routes/web.php` - Added API URL controller route

## 🚀 **Deployment Steps for Your Domain:**

### **Step 1: Update Environment Configuration**
Make sure your `.env` file has the correct domain:

```env
APP_URL=https://yourdomain.com
APP_ENV=production
```

### **Step 2: Clear All Caches**
Run these commands on your server:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### **Step 3: Set File Permissions**
Ensure proper permissions:

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### **Step 4: Verify API Routes**
Test these endpoints on your domain:

```bash
# Test API base URL
https://yourdomain.com/api/events

# Test developer portal
https://yourdomain.com/api/developer

# Test API integration
https://yourdomain.com/api/integration
```

## 🔧 **Common Domain Issues & Solutions:**

### **Issue 1: CORS Policy**
If you get CORS errors, add this to your `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->api([
        \Fruitcake\Cors\HandleCors::class,
        // ... other middleware
    ]);
})
```

### **Issue 2: HTTPS Redirect**
If your domain uses HTTPS, ensure:

```env
APP_URL=https://yourdomain.com
```

### **Issue 3: Apache/Nginx Configuration**
Make sure your web server allows API routes:

**Apache (.htaccess):**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

**Nginx:**
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## 📋 **Pre-Deployment Checklist:**

### **✅ Configuration**
- [ ] `APP_URL` set to your domain
- [ ] `APP_ENV` set to `production`
- [ ] Database credentials correct
- [ ] Mail credentials configured

### **✅ File Structure**
- [ ] All files uploaded to server
- [ ] `vendor` directory present
- [ ] `.env` file exists and configured
- [ ] `storage` directory writable

### **✅ Permissions**
- [ ] `storage/` - 755
- [ ] `bootstrap/cache/` - 755
- [ ] `public/` - 755

### **✅ Caches Cleared**
- [ ] Config cache cleared
- [ ] View cache cleared
- [ ] Route cache cleared
- [ ] Application cache cleared

## 🧪 **Testing Your API on Domain:**

### **1. Test Basic API Access**
```bash
curl -X GET "https://yourdomain.com/api/events" \
  -H "Content-Type: application/json"
```

### **2. Test Developer Portal**
Visit: `https://yourdomain.com/api/developer`

### **3. Test API Integration**
Visit: `https://yourdomain.com/account/api-integration`

### **4. Test Registration**
```bash
curl -X POST "https://yourdomain.com/api/integration/register" \
  -H "Content-Type: application/json" \
  -d '{
    "app_name": "Test App",
    "description": "Testing API",
    "contact_email": "test@example.com"
  }'
```

## 🚨 **Troubleshooting:**

### **404 Errors**
- Check if `mod_rewrite` is enabled (Apache)
- Verify Nginx configuration
- Ensure routes are properly cached

### **500 Errors**
- Check Laravel logs: `storage/logs/laravel.log`
- Verify file permissions
- Ensure `.env` file is correct

### **CORS Issues**
- Install CORS package: `composer require fruitcake/laravel-cors`
- Configure CORS in `config/cors.php`

### **SSL/HTTPS Issues**
- Ensure `APP_URL` uses `https://`
- Check SSL certificate validity
- Verify server SSL configuration

## 🔄 **Post-Deployment:**

### **1. Monitor API Usage**
Check your API endpoints are working:
- Developer portal loads correctly
- Code examples show correct URLs
- Registration forms work

### **2. Test Authentication**
- Register a new application
- Generate API tokens
- Test authenticated requests

### **3. Verify Documentation**
- API documentation shows correct URLs
- All links work properly
- Examples use your domain

## 📞 **If Issues Persist:**

### **Debug Information to Collect:**
1. Laravel logs: `storage/logs/laravel.log`
2. Web server error logs
3. Environment variables: `php artisan tinker --execute="echo env('APP_URL')"`
4. Route list: `php artisan route:list`

### **Quick Test Script:**
Create `test-api.php` in your public folder:
```php
<?php
echo "APP_URL: " . env('APP_URL') . "\n";
echo "Base URL: " . url('/') . "\n";
echo "API URL: " . url('/api') . "\n";
echo "Events URL: " . url('/api/events') . "\n";
?>
```

Access: `https://yourdomain.com/test-api.php`

## ✅ **Success Indicators:**
- Developer portal loads with your domain URLs
- API endpoints return proper responses
- Registration forms work correctly
- Code examples show your domain
- No 404 or 500 errors

Your API should now work perfectly on your domain! 🎉
