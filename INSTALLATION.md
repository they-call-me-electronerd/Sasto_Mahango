# MulyaSuchi - Installation Guide

## Prerequisites

Before installing MulyaSuchi, ensure you have:

- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher (or MariaDB 10.2+)
- **Git**: For version control (optional)
- **Composer**: For PHP dependency management (optional)

### PHP Extensions Required

- `pdo_mysql` - MySQL database driver
- `mbstring` - Multibyte string support
- `json` - JSON processing
- `session` - Session management
- `gd` or `imagick` - Image processing
- `fileinfo` - File type detection
- `openssl` - Encryption support

## Installation Methods

### Method 1: Manual Installation

#### Step 1: Download/Clone Repository

**Option A: Download ZIP**
1. Download the latest release from GitHub
2. Extract to your web server directory

**Option B: Git Clone**
```bash
cd /var/www/html
git clone https://github.com/yourusername/MulyaSuchi.git
cd MulyaSuchi
```

#### Step 2: Configure Environment

1. Copy the environment template:
```bash
cp .env.example .env
```

2. Edit `.env` file with your configuration:
```env
# Application
APP_NAME="MulyaSuchi"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=mulyasuchi
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password
DB_CHARSET=utf8mb4

# Session
SESSION_LIFETIME=7200
SESSION_SECURE=true
SESSION_HTTPONLY=true

# Security
SITE_URL=https://yourdomain.com
CSRF_TOKEN_NAME=_csrf_token
```

#### Step 3: Set Up Database

1. Create database:
```sql
CREATE DATABASE mulyasuchi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Create database user:
```sql
CREATE USER 'mulyasuchi_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON mulyasuchi.* TO 'mulyasuchi_user'@'localhost';
FLUSH PRIVILEGES;
```

3. Import database schema:
```bash
mysql -u mulyasuchi_user -p mulyasuchi < sql/schema.sql
```

4. Import seed data:
```bash
mysql -u mulyasuchi_user -p mulyasuchi < sql/seed_data.sql
```

5. Import product data:
```bash
mysql -u mulyasuchi_user -p mulyasuchi < sql/fresh_500_products.sql
```

#### Step 4: Set Permissions

**For Linux/Unix:**
```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/html/MulyaSuchi

# Set directory permissions
find /var/www/html/MulyaSuchi -type d -exec chmod 755 {} \;

# Set file permissions
find /var/www/html/MulyaSuchi -type f -exec chmod 644 {} \;

# Set writable directories
chmod 775 assets/uploads/
chmod 775 logs/
```

**For Windows (XAMPP):**
1. Ensure web server has write access to:
   - `assets/uploads/`
   - `logs/`

#### Step 5: Configure Web Server

**Apache Configuration:**

1. Enable required modules:
```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl restart apache2
```

2. Create virtual host (optional):
```apache
<VirtualHost *:80>
    ServerName mulyasuchi.local
    DocumentRoot /var/www/html/MulyaSuchi
    
    <Directory /var/www/html/MulyaSuchi>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/mulyasuchi_error.log
    CustomLog ${APACHE_LOG_DIR}/mulyasuchi_access.log combined
</VirtualHost>
```

3. Ensure `.htaccess` is working:
```apache
# Already included in project root
RewriteEngine On
RewriteBase /
```

**Nginx Configuration:**

```nginx
server {
    listen 80;
    server_name mulyasuchi.local;
    root /var/www/html/MulyaSuchi;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Step 6: Create Admin Account

1. Access the database:
```bash
mysql -u mulyasuchi_user -p mulyasuchi
```

2. Insert admin user:
```sql
INSERT INTO users (username, password, email, role, status, created_at) 
VALUES (
    'admin',
    '$2y$10$example_hash_here',  -- Use password_hash() in PHP
    'admin@example.com',
    'admin',
    'active',
    NOW()
);
```

Or use the registration page and manually update role to 'admin' in database.

#### Step 7: Test Installation

1. Visit your site: `http://yourdomain.com/public/`
2. Test user registration
3. Test login functionality
4. Test browsing products
5. Test admin panel: `http://yourdomain.com/admin/`

### Method 2: Docker Installation (Coming Soon)

```bash
docker-compose up -d
```

## Post-Installation

### 1. Security Hardening

- [ ] Change default admin password
- [ ] Remove test files (use `scripts/prepare_production.ps1`)
- [ ] Set up SSL certificate (Let's Encrypt recommended)
- [ ] Configure firewall rules
- [ ] Enable security headers in `.htaccess`

### 2. Optimization

**PHP Configuration (php.ini):**
```ini
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
date.timezone = Asia/Kathmandu
```

**MySQL Optimization:**
```sql
-- Enable query cache
SET GLOBAL query_cache_size = 67108864;
SET GLOBAL query_cache_type = 1;

-- Optimize tables
OPTIMIZE TABLE items, users, categories;
```

### 3. Backup Setup

Create automated backup script:
```bash
#!/bin/bash
# Daily backup script
DATE=$(date +%Y%m%d)
mysqldump -u mulyasuchi_user -p mulyasuchi > backup_$DATE.sql
tar -czf files_$DATE.tar.gz assets/uploads/
```

Add to crontab:
```bash
0 2 * * * /path/to/backup_script.sh
```

### 4. Monitoring

- Set up error logging
- Configure uptime monitoring
- Set up email alerts for errors
- Monitor disk space
- Monitor database size

## Troubleshooting

### Common Issues

**Issue: "500 Internal Server Error"**
- Check Apache error logs: `/var/log/apache2/error.log`
- Verify `.htaccess` syntax
- Check PHP error logs
- Ensure mod_rewrite is enabled

**Issue: "Database connection failed"**
- Verify database credentials in `.env`
- Check MySQL service is running
- Test database connection manually
- Check user permissions

**Issue: "Permission denied" errors**
- Check file permissions
- Verify web server user ownership
- Check SELinux settings (if applicable)

**Issue: Images not uploading**
- Check `upload_max_filesize` in php.ini
- Verify `assets/uploads/` is writable
- Check disk space
- Review PHP error logs

**Issue: Sessions not working**
- Check `session.save_path` in php.ini
- Verify session directory is writable
- Check `session.cookie_secure` settings
- Clear browser cookies

### Getting Help

- Check documentation: `/docs/`
- Review existing issues on GitHub
- Create new issue with details:
  - PHP version
  - MySQL version
  - Error messages
  - Steps to reproduce

## Updating

### Update Process

1. Backup current installation:
```bash
cp -r /var/www/html/MulyaSuchi /var/www/html/MulyaSuchi.backup
mysqldump -u mulyasuchi_user -p mulyasuchi > backup.sql
```

2. Pull latest changes:
```bash
git pull origin main
```

3. Run database migrations (if any):
```bash
mysql -u mulyasuchi_user -p mulyasuchi < sql/migrations/update_xxxx.sql
```

4. Clear cache/sessions:
```bash
rm -rf cache/* sessions/*
```

5. Test thoroughly

## Uninstallation

If you need to remove MulyaSuchi:

1. Backup data if needed
2. Drop database:
```sql
DROP DATABASE mulyasuchi;
DROP USER 'mulyasuchi_user'@'localhost';
```

3. Remove files:
```bash
sudo rm -rf /var/www/html/MulyaSuchi
```

4. Remove web server configuration
5. Remove cron jobs

## Additional Resources

- **Documentation**: See `/docs/DOCUMENTATION_INDEX.md`
- **Deployment Guide**: See `DEPLOYMENT_GUIDE.md`
- **Contributing**: See `CONTRIBUTING.md`
- **License**: See `LICENSE.md`

## Support

For support and questions:
- GitHub Issues: https://github.com/yourusername/MulyaSuchi/issues
- Email: support@example.com
- Documentation: https://yourdomain.com/docs

---

**Note**: Always test in a staging environment before deploying to production.
