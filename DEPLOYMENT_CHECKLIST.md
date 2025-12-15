# Production Deployment Checklist

## Pre-Deployment

### 1. Environment Configuration
- [ ] Copy `.env.example` to `.env`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate secure `APP_KEY`
- [ ] Configure production database credentials
- [ ] Set production `SITE_URL`
- [ ] Configure email settings (if applicable)
- [ ] Set secure session settings

### 2. Database Setup
- [ ] Create production database
- [ ] Import `sql/schema.sql`
- [ ] Import `sql/seed_data.sql`
- [ ] Import product data (sql/fresh_500_products.sql)
- [ ] Run any pending migrations
- [ ] Create database user with appropriate privileges
- [ ] Test database connection

### 3. File Permissions
- [ ] Set `assets/uploads/` to 775 (writable by web server)
- [ ] Set `logs/` to 775 (writable by web server)
- [ ] Set all `.php` files to 644
- [ ] Set directories to 755
- [ ] Ensure web server owns upload directories

### 4. Security Configuration
- [ ] Enable HTTPS/SSL certificate
- [ ] Update `.htaccess` for HTTPS redirect
- [ ] Set secure session cookie settings
- [ ] Enable CSRF protection
- [ ] Configure rate limiting
- [ ] Review `config/security.php` settings
- [ ] Disable directory listing
- [ ] Set proper CORS headers

### 5. Code Cleanup
- [ ] Run `scripts/prepare_production.ps1` or `.sh`
- [ ] Remove development files
- [ ] Remove test files
- [ ] Remove debug statements
- [ ] Minify CSS/JS (if needed)
- [ ] Optimize images

### 6. Performance Optimization
- [ ] Enable PHP OPcache
- [ ] Configure MySQL query cache
- [ ] Set up CDN (if applicable)
- [ ] Enable Gzip compression
- [ ] Optimize database indexes
- [ ] Test page load times

## Deployment

### 7. File Upload
- [ ] Upload files via FTP/SFTP or Git
- [ ] Verify all files uploaded correctly
- [ ] Check file permissions after upload
- [ ] Verify `.htaccess` is present
- [ ] Ensure `.env` is not in public repository

### 8. Web Server Configuration
- [ ] Configure Apache/Nginx virtual host
- [ ] Set document root to project root
- [ ] Enable mod_rewrite (Apache)
- [ ] Configure PHP version (7.4+)
- [ ] Set PHP memory limit (128M minimum)
- [ ] Set PHP max execution time
- [ ] Configure upload limits

### 9. Database Configuration
- [ ] Verify database connection
- [ ] Check database charset (utf8mb4)
- [ ] Verify all tables created
- [ ] Test sample queries
- [ ] Set up database backups

### 10. Testing
- [ ] Test homepage loads
- [ ] Test user registration
- [ ] Test user login
- [ ] Test product browsing
- [ ] Test search functionality
- [ ] Test price updates (contributor)
- [ ] Test admin panel access
- [ ] Test image uploads
- [ ] Test form submissions
- [ ] Test on mobile devices
- [ ] Test in multiple browsers
- [ ] Check for broken links
- [ ] Verify SSL certificate

## Post-Deployment

### 11. Monitoring Setup
- [ ] Set up error logging
- [ ] Configure application monitoring
- [ ] Set up uptime monitoring
- [ ] Configure backup schedules
- [ ] Set up email notifications for errors

### 12. SEO & Analytics
- [ ] Submit sitemap to Google Search Console
- [ ] Set up Google Analytics (if applicable)
- [ ] Configure robots.txt
- [ ] Set up social media tags
- [ ] Test sitemap.xml.php

### 13. Documentation
- [ ] Update deployment documentation
- [ ] Document server configuration
- [ ] Document backup procedures
- [ ] Create admin user guide
- [ ] Create contributor user guide

### 14. Security Hardening
- [ ] Change default admin password
- [ ] Set up SSL/TLS
- [ ] Configure firewall rules
- [ ] Enable fail2ban (if applicable)
- [ ] Review security headers
- [ ] Set up security monitoring

### 15. Backup & Recovery
- [ ] Test database backup script
- [ ] Test file backup process
- [ ] Document recovery procedures
- [ ] Test recovery from backup
- [ ] Set up automated backups

## Launch

### 16. Final Checks
- [ ] Verify all features working
- [ ] Check mobile responsiveness
- [ ] Test payment integration (if applicable)
- [ ] Verify email delivery
- [ ] Check loading performance
- [ ] Review error logs
- [ ] Test under load (if possible)

### 17. Go Live
- [ ] Update DNS records (if applicable)
- [ ] Remove "under construction" page
- [ ] Enable public access
- [ ] Announce launch
- [ ] Monitor for issues

### 18. Post-Launch Monitoring
- [ ] Monitor error logs daily (first week)
- [ ] Check performance metrics
- [ ] Review user feedback
- [ ] Fix any critical issues immediately
- [ ] Document lessons learned

## Maintenance Schedule

### Daily
- [ ] Check error logs
- [ ] Monitor uptime
- [ ] Review security alerts

### Weekly
- [ ] Review user activity
- [ ] Check backup integrity
- [ ] Update content as needed
- [ ] Review performance metrics

### Monthly
- [ ] Update dependencies
- [ ] Review security patches
- [ ] Optimize database
- [ ] Review and update documentation
- [ ] Performance audit

## Emergency Contacts
- Server Administrator: ________________
- Database Administrator: ________________
- Developer: ________________
- Security Contact: ________________

## Rollback Plan
1. Keep previous version backed up
2. Document rollback procedure
3. Test rollback process
4. Have rollback scripts ready

---

**Notes:**
- Always test in staging environment first
- Keep backups before any major changes
- Document all configuration changes
- Monitor closely after deployment
