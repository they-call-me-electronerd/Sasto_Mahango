# 🚀 PRODUCTION DEPLOYMENT GUIDE
## MulyaSuchi Market Intelligence Platform

---

## ✅ PRE-DEPLOYMENT CHECKLIST

### 1. **Environment Setup**
```bash
# Copy environment file
cp .env.example .env

# Edit .env and set production values
nano .env
```

**Required .env variables:**
- `APP_ENV=production`
- `APP_DEBUG=false`
- `DB_PASS=` (STRONG PASSWORD)
- `SITE_URL=https://your-domain.com`
- Set all email, Redis, and security settings

### 2. **Database Setup**
```bash
# Create database
mysql -u root -p
```

```sql
CREATE DATABASE mulyasuchi_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create dedicated user (NEVER use root in production)
CREATE USER 'mulyasuchi_user'@'localhost' IDENTIFIED BY 'YOUR_STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON mulyasuchi_db.* TO 'mulyasuchi_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# Import schema
mysql -u mulyasuchi_user -p mulyasuchi_db < sql/schema.sql

# Run optimizations
mysql -u mulyasuchi_user -p mulyasuchi_db < sql/database_optimizations.sql

# Import seed data (optional)
mysql -u mulyasuchi_user -p mulyasuchi_db < sql/seed_data.sql
```

### 3. **File Permissions**
```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/mulyasuchi

# Set directory permissions
find /var/www/mulyasuchi -type d -exec chmod 755 {} \;

# Set file permissions
find /var/www/mulyasuchi -type f -exec chmod 644 {} \;

# Make scripts executable
chmod +x scripts/*.sh

# Writable directories
chmod 775 assets/uploads/items
chmod 775 logs
sudo chown -R www-data:www-data assets/uploads
sudo chown -R www-data:www-data logs
```

### 4. **Security Configuration**

**Create logs directory:**
```bash
mkdir -p /var/log/mulyasuchi
sudo chown www-data:www-data /var/log/mulyasuchi
chmod 755 /var/log/mulyasuchi
```

**Protect .env file:**
```bash
chmod 600 .env
```

**Verify .htaccess is active:**
```bash
# Enable mod_rewrite
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod deflate
sudo a2enmod expires

# Restart Apache
sudo systemctl restart apache2
```

### 5. **SSL Certificate (HTTPS)**
```bash
# Install Certbot
sudo apt-get update
sudo apt-get install certbot python3-certbot-apache

# Get SSL certificate
sudo certbot --apache -d mulyasuchi.com -d www.mulyasuchi.com

# Auto-renewal (already set up by certbot)
# Test renewal:
sudo certbot renew --dry-run
```

**After SSL is installed, update .htaccess:**
Uncomment these lines:
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 6. **Database Backup Setup**
```bash
# Edit backup script
nano scripts/backup_database.sh

# Update credentials:
DB_USER="mulyasuchi_user"
DB_PASS="your_password"

# Make executable
chmod +x scripts/backup_database.sh

# Test backup
./scripts/backup_database.sh

# Setup cron job for daily backups at 2 AM
crontab -e

# Add this line:
0 2 * * * /var/www/mulyasuchi/scripts/backup_database.sh >> /var/log/mulyasuchi/backup.log 2>&1
```

### 7. **PHP Configuration**
```bash
# Edit php.ini
sudo nano /etc/php/8.0/apache2/php.ini
```

**Production settings:**
```ini
display_errors = Off
display_startup_errors = Off
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
log_errors = On
error_log = /var/log/mulyasuchi/php_errors.log

max_execution_time = 30
max_input_time = 60
memory_limit = 256M
post_max_size = 10M
upload_max_filesize = 5M

session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = Strict
session.use_strict_mode = 1
```

```bash
# Restart Apache
sudo systemctl restart apache2
```

---

## 🔧 POST-DEPLOYMENT

### 1. **Create Admin User**
```bash
# Access MySQL
mysql -u mulyasuchi_user -p mulyasuchi_db
```

```sql
-- Create admin account
INSERT INTO users (username, email, password_hash, full_name, role, status) 
VALUES (
    'admin',
    'admin@mulyasuchi.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: 'password' (CHANGE THIS!)
    'System Administrator',
    'admin',
    'active'
);
```

**IMMEDIATELY log in and change the password!**

### 2. **Test Security**
- [ ] Try accessing `/config/database.php` (should be 403 Forbidden)
- [ ] Try accessing `/.env` (should be 403 Forbidden)
- [ ] Verify HTTPS redirect works
- [ ] Test login rate limiting (5 failed attempts)
- [ ] Check file upload restrictions

### 3. **Performance Testing**
```bash
# Install Apache Bench
sudo apt-get install apache2-utils

# Test homepage (100 requests, 10 concurrent)
ab -n 100 -c 10 https://mulyasuchi.com/public/index.php

# Target: < 2 second response time
```

### 4. **Monitoring Setup**

**Error Monitoring:**
```bash
# Watch error logs
tail -f /var/log/mulyasuchi/php_errors.log
tail -f /var/log/mulyasuchi/app.log
```

**Uptime Monitoring:** (Choose one)
- UptimeRobot (free)
- Pingdom
- StatusCake
- Better Stack

### 5. **SEO Configuration**

**Submit sitemap:**
```
Google Search Console: https://search.google.com/search-console
Bing Webmaster: https://www.bing.com/webmasters

Sitemap URL: https://mulyasuchi.com/sitemap.xml.php
```

**robots.txt verification:**
```bash
# Check if accessible
curl https://mulyasuchi.com/robots.txt
```

### 6. **Email Configuration**
Update `.env` with your email provider:
```
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

For Gmail, create an app password:
1. Enable 2FA
2. Go to App Passwords
3. Generate password for "Mail"

---

## 📊 MONITORING CHECKLIST

### Daily Checks
- [ ] Error log review
- [ ] Backup verification
- [ ] Site uptime
- [ ] Response time < 2s

### Weekly Checks
- [ ] Database size
- [ ] Disk space
- [ ] Security updates
- [ ] User activity logs

### Monthly Checks
- [ ] Backup restore test
- [ ] Security audit
- [ ] Performance optimization
- [ ] Database optimization (ANALYZE TABLES)

---

## 🔒 SECURITY MAINTENANCE

### Regular Updates
```bash
# Update system packages
sudo apt-get update && sudo apt-get upgrade

# Update PHP
sudo apt-get install php8.0

# Restart services
sudo systemctl restart apache2
sudo systemctl restart mysql
```

### Security Scanning
```bash
# Install OWASP ZAP or run online scan
# Scan URL: https://mulyasuchi.com
```

### Password Policy
- Admin passwords: Change every 90 days
- Database passwords: Change annually
- Use strong passwords (16+ characters, mixed case, numbers, symbols)

---

## 🚨 TROUBLESHOOTING

### Issue: Database connection failed
```bash
# Check MySQL is running
sudo systemctl status mysql

# Check credentials in .env
# Verify user has permissions
mysql -u mulyasuchi_user -p
```

### Issue: 500 Internal Server Error
```bash
# Check error logs
tail -50 /var/log/apache2/error.log
tail -50 /var/log/mulyasuchi/php_errors.log

# Check file permissions
ls -la /var/www/mulyasuchi
```

### Issue: Images not uploading
```bash
# Check upload directory permissions
ls -la assets/uploads/items

# Set correct permissions
sudo chown www-data:www-data assets/uploads/items
chmod 775 assets/uploads/items
```

### Issue: Session not persisting
```bash
# Check session directory
ls -la /var/lib/php/sessions

# Set permissions
sudo chown root:www-data /var/lib/php/sessions
sudo chmod 730 /var/lib/php/sessions
```

---

## 📈 PERFORMANCE OPTIMIZATION

### Enable OPcache
```bash
# Edit php.ini
sudo nano /etc/php/8.0/apache2/php.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### MySQL Optimization
```sql
-- Add to my.cnf
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
max_connections = 150
```

### CDN Setup (Optional)
- CloudFlare (free tier)
- Configure DNS
- Enable caching

---

## 🎯 SUCCESS METRICS

After deployment, verify:
- ✅ Site loads over HTTPS
- ✅ All security headers present
- ✅ Page load time < 2 seconds
- ✅ Mobile responsive
- ✅ Database backups running daily
- ✅ Error logging functional
- ✅ Email notifications working
- ✅ Rate limiting functional
- ✅ File uploads working with size limits
- ✅ Admin panel accessible
- ✅ Contributor registration working
- ✅ Search and filters working

---

## 📞 SUPPORT

For issues:
1. Check error logs first
2. Review this guide
3. Check documentation in `/docs/`
4. Contact: admin@mulyasuchi.com

---

## 🎉 CONGRATULATIONS!

Your MulyaSuchi platform is now production-ready with:
- ✅ Enterprise-grade security
- ✅ Optimized performance
- ✅ Automated backups
- ✅ Error monitoring
- ✅ SEO optimization
- ✅ Mobile responsive design

**Next Steps:**
1. Market and promote your platform
2. Monitor user feedback
3. Iterate and improve
4. Scale as needed

Good luck! 🚀
