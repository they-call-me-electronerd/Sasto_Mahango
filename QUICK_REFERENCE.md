# 🚀 SastoMahango - Quick Reference Guide

## Production Deployment - Quick Start

### Prerequisites Checklist
- [ ] PHP 7.4+ installed
- [ ] MySQL 5.7+ installed
- [ ] Apache 2.4+ with mod_rewrite enabled
- [ ] Git installed
- [ ] SSL certificate ready (for production)

---

## 🔥 Quick Deployment (5 Steps)

### 1. Clone Repository
```bash
git clone https://github.com/your-username/SastoMahango.git
cd SastoMahango
```

### 2. Configure Environment
```bash
cp .env.example .env
nano .env  # Edit with your values
```

**Critical .env Variables:**
```env
APP_ENV=production
APP_DEBUG=false
DB_HOST=localhost
DB_NAME=sastomahango_db
DB_USER=your_db_user
DB_PASS=your_secure_password
SITE_URL=https://your-domain.com
```

### 3. Create Database
```bash
mysql -u root -p
```

```sql
CREATE DATABASE sastomahango_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sastomahango_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON sastomahango_db.* TO 'sastomahango_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
mysql -u sastomahango_user -p sastomahango_db < sql/schema.sql
mysql -u sastomahango_user -p sastomahango_db < sql/database_optimizations.sql
mysql -u sastomahango_user -p sastomahango_db < sql/seed_data.sql
```

### 4. Set Permissions
```bash
# Linux/Mac
chmod 755 assets/uploads/ logs/
chown -R www-data:www-data assets/uploads/ logs/
chmod 600 .env

# Windows (XAMPP) - Ensure folders are writable
```

### 5. Access Application
```
http://your-domain.com/public/
```

---

## 🔑 Default Login Credentials

**Admin:**
- Email: `admin@sastomahango.com`
- Password: `admin123`

**Contributor:**
- Email: `contributor@sastomahango.com`
- Password: `contributor123`

**⚠️ CHANGE THESE IMMEDIATELY IN PRODUCTION!**

---

## 📂 Key Files

| File | Purpose |
|------|---------|
| `.env` | Configuration (DO NOT commit!) |
| `README.md` | Full documentation |
| `DEPLOYMENT_GUIDE.md` | Detailed deployment steps |
| `DEPLOYMENT_SUMMARY.md` | Preparation report |
| `LICENSE` | MIT License |
| `.gitignore` | Git exclusions |

---

## 🛠️ Common Commands

### Database
```bash
# Backup database
mysqldump -u sastomahango_user -p sastomahango_db > backup.sql

# Restore database
mysql -u sastomahango_user -p sastomahango_db < backup.sql

# Reset database
mysql -u sastomahango_user -p sastomahango_db < sql/schema.sql
```

### Git
```bash
# Check status
git status

# Pull latest changes
git pull origin main

# View commit history
git log --oneline

# Create new branch
git checkout -b feature/your-feature
```

### Apache
```bash
# Restart Apache (Linux)
sudo systemctl restart apache2

# Check Apache status
sudo systemctl status apache2

# Test configuration
sudo apache2ctl configtest
```

---

## 🔍 Troubleshooting

### Database Connection Error
1. Check `.env` credentials
2. Verify MySQL is running: `sudo systemctl status mysql`
3. Test connection: `mysql -u sastomahango_user -p`

### Permission Errors
```bash
# Fix upload permissions
sudo chown -R www-data:www-data assets/uploads/
sudo chmod -R 755 assets/uploads/
```

### Apache 500 Error
1. Check `.htaccess` exists in root
2. Enable mod_rewrite: `sudo a2enmod rewrite`
3. Check error logs: `tail -f /var/log/apache2/error.log`

### File Upload Not Working
1. Check `php.ini` settings:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```
2. Restart Apache
3. Verify folder permissions

---

## 📊 Project URLs

### Development
- Home: `http://localhost/SastoMahango/public/`
- Admin: `http://localhost/SastoMahango/admin/`
- Contributor: `http://localhost/SastoMahango/contributor/`

### Production
- Home: `https://your-domain.com/`
- Admin: `https://your-domain.com/admin/`
- Contributor: `https://your-domain.com/contributor/`

---

## 🎯 Post-Deployment Checklist

- [ ] Test login (admin and contributor)
- [ ] Add new item
- [ ] Update item price
- [ ] Search functionality works
- [ ] Category filtering works
- [ ] Image upload works
- [ ] Price history chart displays
- [ ] Dark mode toggles
- [ ] Mobile responsive on all pages
- [ ] All forms validate properly
- [ ] HTTPS enabled and working
- [ ] Error logs show no critical issues

---

## 📧 Support

**Issues?** Check these resources:
1. `README.md` - Full documentation
2. `DEPLOYMENT_GUIDE.md` - Detailed deployment steps
3. `DEPLOYMENT_SUMMARY.md` - What was changed
4. `docs/SETUP_NOTES.md` - Setup tips

**Still stuck?**
- Email: contact@sastomahango.com
- GitHub Issues: https://github.com/your-username/SastoMahango/issues

---

## 🎓 Next Steps

1. **Security:** Change default passwords
2. **Backup:** Set up automated database backups
3. **Monitoring:** Configure error logging and alerts
4. **SSL:** Install SSL certificate (Let's Encrypt)
5. **Performance:** Enable opcache and caching
6. **Testing:** Run through all user flows
7. **Documentation:** Update with production-specific notes

---

## 📝 Version Information

- **Version:** 1.0.0
- **Status:** Production-Ready
- **Last Updated:** November 25, 2025
- **PHP Version:** 7.4+
- **MySQL Version:** 5.7+

---

**🚀 Happy Deploying!**

*For detailed information, see README.md*
