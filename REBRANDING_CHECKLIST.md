# 🔄 Complete Rebranding Checklist: Mulyasuchi → SastoMahango

## ✅ Code Changes Completed

### Frontend Files
- [x] Updated all public PHP files (index.php, about.php, products.php, categories.php, etc.)
- [x] Updated header_professional.php with new branding
- [x] Updated footer files
- [x] Updated navigation components
- [x] Updated all page titles and meta descriptions
- [x] Updated policy pages (privacy, terms, cookies)
- [x] Changed `MULYASUCHI_APP` constant to `SASTOMAHANGO_APP` everywhere

### Backend Files
- [x] Updated config/config.php with new site name, URL, email defaults
- [x] Updated config/database.php with new database name default
- [x] Updated all admin panel files
- [x] Updated all contributor panel files
- [x] Updated AJAX endpoints
- [x] Updated utility scripts

### Database
- [x] Updated schema.sql with new database name (sastomahango_db)
- [x] Updated all seed files with new database references
- [x] Updated site_settings table data (site_name, contact_email)
- [x] Updated user email addresses
- [x] Updated database optimization scripts
- [x] Created rebrand_database.sql for migrating existing databases

### Assets
- [x] Updated JavaScript theme storage key
- [x] Updated CSS comments
- [x] Updated localStorage references

### Documentation
- [x] Updated README.md
- [x] Updated QUICK_REFERENCE.md
- [x] Updated LICENSE
- [x] Updated DEPLOYMENT_GUIDE.md
- [x] Updated DEPLOYMENT_SUMMARY.md
- [x] Updated SQL README
- [x] Updated all docs/*.md files

### Configuration Files
- [x] Updated robots.txt sitemap URL
- [x] Updated backup scripts

---

## 📋 Manual Steps Required

### 1. Database Migration (Choose One Option)

**Option A: Fresh Installation (Recommended for Development)**
```bash
# Drop old database if exists
mysql -u root -p -e "DROP DATABASE IF EXISTS mulyasuchi_db;"

# Create new database
mysql -u root -p < sql/schema.sql

# Import data
mysql -u root -p sastomahango_db < sql/seed_data.sql
mysql -u root -p sastomahango_db < sql/database_optimizations.sql
```

**Option B: Migrate Existing Database (For Production)**
```bash
# Backup existing database
mysqldump -u root -p mulyasuchi_db > backup_mulyasuchi.sql

# Create new database
mysql -u root -p -e "CREATE DATABASE sastomahango_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import backup to new database
mysql -u root -p sastomahango_db < backup_mulyasuchi.sql

# Update references
mysql -u root -p < sql/rebrand_database.sql

# Verify
mysql -u root -p sastomahango_db -e "SELECT * FROM site_settings WHERE setting_key='site_name';"
```

### 2. Environment Configuration

Update `.env` file:
```env
# Old values
DB_NAME=mulyasuchi_db
SITE_NAME=Mulyasuchi
SITE_URL=http://localhost/MulyaSuchi
SITE_EMAIL=contact@mulyasuchi.com

# New values
DB_NAME=sastomahango_db
SITE_NAME=SastoMahango
SITE_URL=http://localhost/SastoMahango
SITE_EMAIL=contact@sastomahango.com
```

### 3. Folder Rename (Optional but Recommended)

```bash
# Windows
cd c:\xampp\htdocs
rename MulyaSuchi SastoMahango

# Linux/Mac
cd /var/www/html
mv MulyaSuchi SastoMahango
```

### 4. Web Server Configuration

**Apache Virtual Host:**
Update `/etc/apache2/sites-available/` or equivalent:
```apache
# Old
ServerName mulyasuchi.local
DocumentRoot /var/www/html/MulyaSuchi/public
ErrorLog ${APACHE_LOG_DIR}/mulyasuchi_error.log
CustomLog ${APACHE_LOG_DIR}/mulyasuchi_access.log combined

# New
ServerName sastomahango.local
DocumentRoot /var/www/html/SastoMahango/public
ErrorLog ${APACHE_LOG_DIR}/sastomahango_error.log
CustomLog ${APACHE_LOG_DIR}/sastomahango_access.log combined
```

**Nginx Configuration:**
Update your nginx site configuration similarly.

### 5. Database User (Optional - For Security)

```sql
-- Create new user
CREATE USER 'sastomahango_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON sastomahango_db.* TO 'sastomahango_user'@'localhost';
FLUSH PRIVILEGES;

-- Update .env
DB_USER=sastomahango_user
DB_PASS=your_secure_password
```

### 6. File Permissions

```bash
# Linux/Mac
sudo chown -R www-data:www-data /var/www/html/SastoMahango
chmod 600 /var/www/html/SastoMahango/.env
chmod 755 /var/www/html/SastoMahango/assets/uploads

# Windows (XAMPP)
# Ensure proper permissions on assets/uploads directory
```

### 7. Clear Caches

```bash
# Clear browser cache and localStorage
# Browser Developer Tools → Application → Clear Storage

# Clear PHP opcache if enabled
# Restart Apache or PHP-FPM

# Clear any CDN or reverse proxy caches
```

---

## 🧪 Testing Checklist

### Functionality Tests
- [ ] Homepage loads correctly with new branding
- [ ] Logo displays "SastoMahango"
- [ ] All navigation links work
- [ ] Search functionality works
- [ ] Product listing/filtering works
- [ ] Individual product pages load
- [ ] Category pages work

### User Authentication
- [ ] Admin login works (admin@sastomahango.com)
- [ ] Contributor login works (contributor@sastomahango.com)
- [ ] Registration works
- [ ] Password reset works (check email addresses)
- [ ] Session management works
- [ ] Logout functions correctly

### Database Operations
- [ ] Create new product works
- [ ] Update product works
- [ ] Delete product works
- [ ] Price updates save correctly
- [ ] Search queries return results
- [ ] Filters work properly

### Admin Panel
- [ ] Dashboard displays correctly
- [ ] User management works
- [ ] Settings page shows "SastoMahango"
- [ ] Validation queue functions
- [ ] System logs accessible

### Contributor Panel
- [ ] Dashboard loads
- [ ] Add item functionality
- [ ] Edit item functionality
- [ ] Price update feature works

### Emails (If Configured)
- [ ] Email templates updated
- [ ] Sender address: contact@sastomahango.com
- [ ] Email footer branding updated

### SEO & Meta
- [ ] Page titles show "SastoMahango"
- [ ] Meta descriptions updated
- [ ] Open Graph tags updated (if present)
- [ ] Sitemap.xml generates correctly
- [ ] Robots.txt points to correct sitemap

---

## 🔍 Verification Commands

```bash
# Check for remaining old references
grep -r "mulyasuchi" /path/to/SastoMahango/ --exclude-dir=".git" --exclude="*.sql"
grep -r "Mulyasuchi" /path/to/SastoMahango/ --exclude-dir=".git" --exclude="*.sql"
grep -r "MulyaSuchi" /path/to/SastoMahango/ --exclude-dir=".git"

# Check database
mysql -u root -p sastomahango_db -e "SELECT setting_key, setting_value FROM site_settings;"
mysql -u root -p sastomahango_db -e "SELECT username, email FROM users;"

# Check environment
cat .env | grep -i mulyasuchi
```

---

## 📧 Post-Deployment Tasks

### Update External Services
- [ ] Update DNS records (if applicable)
- [ ] Update SSL certificates
- [ ] Update Google Analytics property
- [ ] Update Google Search Console
- [ ] Update social media links
- [ ] Update payment gateway settings
- [ ] Update email service provider
- [ ] Update monitoring services

### Communication
- [ ] Notify users of rebrand
- [ ] Update documentation/wikis
- [ ] Update API documentation
- [ ] Update mobile apps (if any)
- [ ] Update marketing materials

### Cleanup (After Verification)
- [ ] Remove old database: `DROP DATABASE mulyasuchi_db;`
- [ ] Remove old backups (after archiving)
- [ ] Update version control tags
- [ ] Archive old branding assets

---

## 🆘 Troubleshooting

### Issue: 404 Errors
**Solution:** Check folder name and web server DocumentRoot configuration

### Issue: Database Connection Error
**Solution:** Verify .env DB_NAME matches actual database name

### Issue: Login Fails
**Solution:** Check user email addresses in database match new domain

### Issue: Theme Not Persisting
**Solution:** Clear browser localStorage and cookies

### Issue: Images Not Loading
**Solution:** Check file paths and permissions on assets/uploads

---

## ✨ Success Indicators

Your rebrand is complete when:
1. ✅ Zero mentions of "Mulyasuchi" in the UI
2. ✅ All URLs contain "SastoMahango" or "sastomahango"
3. ✅ Database named "sastomahango_db"
4. ✅ Email addresses use @sastomahango.com
5. ✅ All CRUD operations function normally
6. ✅ No JavaScript console errors
7. ✅ No PHP errors in logs
8. ✅ All user workflows complete successfully

---

## 📞 Support

If you encounter issues:
1. Check this checklist again
2. Review error logs
3. Verify database connection
4. Check file permissions
5. Clear all caches

---

**Last Updated:** December 2024  
**Rebrand Version:** 1.0.0  
**Status:** Complete ✅
