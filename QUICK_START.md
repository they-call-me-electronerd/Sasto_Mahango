# ⚡ QUICK START GUIDE
## Get Your Production-Ready Platform Running in 10 Minutes

---

## 🎯 **FOR LOCAL TESTING (Windows/XAMPP)**

### Step 1: Configure Environment (2 minutes)
```powershell
# In PowerShell, navigate to your project
cd C:\xampp\htdocs\MulyaSuchi

# Copy environment file
copy .env.example .env

# Edit .env in Notepad
notepad .env
```

**Minimum changes needed:**
```ini
DB_CHARSET=utf8mb4
DB_PASS=    # Leave empty for XAMPP default
APP_ENV=development
APP_DEBUG=true
```

### Step 2: Run Database Optimizations (1 minute)
```powershell
# In PowerShell
Get-Content sql\database_optimizations.sql | C:\xampp\mysql\bin\mysql.exe -u root mulyasuchi_db
```

You should see: ✅ "analyze status OK" for all tables

### Step 3: Create Logs Directory (30 seconds)
```powershell
# Create logs directory
mkdir logs -Force
```

### Step 4: Test Security Features (3 minutes)

**Test Rate Limiting:**
1. Go to `http://localhost/MulyaSuchi/admin/login.php`
2. Try logging in with wrong password 6 times
3. Should show: "Too many login attempts. Please try again in X minutes."
4. Check `logs/rate_limits.json` - should see attempts logged

**Test File Upload Security:**
1. Go to contributor section
2. Try to add an item
3. Upload works ✅
4. File gets resized automatically ✅
5. EXIF data stripped ✅

### Step 5: Verify Security (2 minutes)

**Check Protected Files:**
- Try: `http://localhost/MulyaSuchi/.env` → Should show **403 Forbidden**
- Try: `http://localhost/MulyaSuchi/config/database.php` → Should show **403 Forbidden**
- Try: `http://localhost/MulyaSuchi/sql/` → Should show **403 Forbidden**

**All blocked?** ✅ Security is working!

### Step 6: Test Performance (1 minute)
```powershell
# Check page in browser (F12 → Network tab)
# Homepage should load in < 2 seconds
```

---

## 🚀 **FOR PRODUCTION DEPLOYMENT (Linux Server)**

### Prerequisites
- Linux server (Ubuntu 20.04+ recommended)
- Apache 2.4+
- MySQL 8.0+
- PHP 8.0+
- Domain name with DNS pointed to server

### Quick Deployment (15 minutes)

```bash
# 1. Upload files to server
scp -r * user@your-server:/var/www/mulyasuchi/

# 2. SSH into server
ssh user@your-server

# 3. Set permissions
cd /var/www/mulyasuchi
sudo chown -R www-data:www-data .
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod 775 assets/uploads/items
chmod 775 logs

# 4. Configure environment
cp .env.example .env
nano .env

# Edit these:
# APP_ENV=production
# APP_DEBUG=false
# DB_PASS=your_strong_password
# SITE_URL=https://mulyasuchi.com

# 5. Create database
mysql -u root -p

CREATE DATABASE mulyasuchi_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mulyasuchi_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON mulyasuchi_db.* TO 'mulyasuchi_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# 6. Import database
mysql -u mulyasuchi_user -p mulyasuchi_db < sql/schema.sql
mysql -u mulyasuchi_user -p mulyasuchi_db < sql/database_optimizations.sql
mysql -u mulyasuchi_user -p mulyasuchi_db < sql/seed_data.sql

# 7. Enable Apache modules
sudo a2enmod rewrite headers deflate expires
sudo systemctl restart apache2

# 8. Install SSL (Let's Encrypt)
sudo apt-get install certbot python3-certbot-apache
sudo certbot --apache -d mulyasuchi.com -d www.mulyasuchi.com

# 9. Enable HTTPS redirect
nano .htaccess
# Uncomment these lines:
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# 10. Create admin user
mysql -u mulyasuchi_user -p mulyasuchi_db

INSERT INTO users (username, email, password_hash, full_name, role, status) 
VALUES ('admin', 'admin@mulyasuchi.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Admin', 'admin', 'active');
EXIT;

# Default password is 'password' - CHANGE IT IMMEDIATELY after first login!

# 11. Set up automated backups
chmod +x scripts/backup_database.sh
crontab -e
# Add: 0 2 * * * /var/www/mulyasuchi/scripts/backup_database.sh

# 12. Test the site
curl -I https://mulyasuchi.com
# Should return: HTTP/2 200

# 13. Submit sitemap
# Google Search Console: https://search.google.com/search-console
# Sitemap URL: https://mulyasuchi.com/sitemap.xml.php
```

---

## ✅ **VERIFICATION CHECKLIST**

After setup, verify these work:

### Security ✅
- [ ] HTTPS redirect works
- [ ] Login rate limiting (6 failed = locked)
- [ ] `.env` returns 403 Forbidden
- [ ] `/config/` returns 403 Forbidden
- [ ] Security headers present (check: securityheaders.com)

### Performance ✅
- [ ] Page loads in < 2 seconds
- [ ] Images lazy load
- [ ] Gzip compression active
- [ ] Browser caching works (check Network tab)

### Functionality ✅
- [ ] Admin login works
- [ ] Contributor registration works
- [ ] Item submission works
- [ ] Image upload works (and resizes)
- [ ] Search and filters work
- [ ] Price updates work

### SEO ✅
- [ ] Sitemap accessible: /sitemap.xml.php
- [ ] robots.txt accessible: /robots.txt
- [ ] Meta tags present on pages
- [ ] Page titles descriptive

### Monitoring ✅
- [ ] Error logs created in `/var/log/mulyasuchi/`
- [ ] Rate limit logs in `logs/rate_limits.json`
- [ ] Backups running daily (check tomorrow!)

---

## 🎨 **DEFAULT CREDENTIALS**

### Admin Account
```
Username: admin
Password: password
```
**⚠️ CHANGE PASSWORD IMMEDIATELY!**

### Database (Production)
```
User: mulyasuchi_user
Password: (set during installation)
Database: mulyasuchi_db
```

---

## 🔧 **COMMON ISSUES & FIXES**

### "Database connection failed"
```bash
# Check MySQL is running
sudo systemctl status mysql

# Check credentials in .env match database user
cat .env | grep DB_
```

### "Session not working"
```bash
# Check session directory permissions
sudo chmod 730 /var/lib/php/sessions
sudo chown root:www-data /var/lib/php/sessions
```

### "Images not uploading"
```bash
# Fix permissions
sudo chown www-data:www-data assets/uploads/items
chmod 775 assets/uploads/items
```

### "500 Internal Server Error"
```bash
# Check Apache error log
sudo tail -50 /var/log/apache2/error.log

# Check PHP error log
sudo tail -50 /var/log/mulyasuchi/php_errors.log
```

### "Rate limiting not working"
```bash
# Check logs directory is writable
ls -la logs/
sudo chown www-data:www-data logs/
chmod 775 logs/
```

---

## 📚 **NEXT STEPS**

1. **Customize Content**
   - Add your categories
   - Import initial products
   - Configure email templates

2. **Configure Features**
   - Set up email notifications
   - Configure price change thresholds
   - Customize validation workflow

3. **Marketing**
   - Submit to search engines
   - Set up social media
   - Create content marketing plan

4. **Monitoring**
   - Set up uptime monitoring (UptimeRobot)
   - Configure error alerts
   - Track analytics (Google Analytics)

---

## 🎉 **YOU'RE READY!**

Your platform now has:
- ✅ Enterprise security
- ✅ Optimized performance
- ✅ Automated backups
- ✅ Production hardening
- ✅ SEO optimization

**Time to launch! 🚀**

---

## 📖 **DOCUMENTATION**

For detailed information, see:
- `PRODUCTION_READY_SUMMARY.md` - Complete changes list
- `DEPLOYMENT_GUIDE.md` - Detailed deployment steps
- `docs/` - Feature documentation

## 💬 **SUPPORT**

Need help? Check:
1. Error logs first
2. This guide
3. DEPLOYMENT_GUIDE.md
4. Email: admin@mulyasuchi.com
