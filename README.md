# MulyaSuchi (मूल्यसूची)

**Nepal's Premier Market Intelligence Platform**

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange)](https://www.mysql.com/)

MulyaSuchi is a comprehensive market intelligence platform designed to provide real-time, accurate pricing information for commodities across Nepal's markets. The platform empowers consumers, vendors, and market analysts with transparent pricing data for vegetables, fruits, grains, and other essential commodities.

## 🌟 Features

### For Users
- **Real-Time Price Tracking**: Browse current market prices for 500+ commodities
- **Advanced Search & Filters**: Search by name, category, market, or price range
- **Price History Charts**: Visualize price trends with interactive Chart.js graphs
- **Market Comparison**: Compare prices across 50+ markets in Nepal
- **Responsive Design**: Optimized mobile-first design with dark mode support
- **Multi-Category Browse**: Filter by 20+ categories including vegetables, fruits, grains, dairy, and more

### For Contributors
- **Price Update System**: Verified contributors can submit real-time price updates
- **Item Management**: Add new items with images and detailed specifications
- **Contributor Dashboard**: Track submissions, update history, and activity stats
- **Image Upload**: Support for product images with client-side preview and validation

### For Administrators
- **User Management**: Manage contributors, approve registrations, and set permissions
- **Validation Queue**: Review and approve/reject price updates from contributors
- **System Logs**: Monitor platform activity and security events
- **Item Editing**: Edit item details, prices, markets, and availability status
- **Settings Management**: Configure site-wide settings and feature flags

## 🛠️ Tech Stack

### Backend
- **PHP 7.4+**: Core application logic with OOP principles
- **MySQL 5.7+**: Relational database with optimized indexes
- **PDO**: Prepared statements for SQL injection prevention
- **Session Management**: Secure session handling with CSRF protection

### Frontend
- **HTML5 & CSS3**: Semantic markup with modern CSS features
- **Vanilla JavaScript**: Zero framework dependency, pure ES6+
- **Chart.js**: Interactive price history visualization
- **Bootstrap Icons**: Comprehensive icon library

### Architecture
- **MVC Pattern**: Clean separation of concerns
- **Singleton Pattern**: Database connection management
- **CSRF Protection**: Token-based request validation
- **Rate Limiting**: API endpoint protection
- **File Upload Security**: Validated and sanitized file handling

### Key Classes
- `Database`: Singleton PDO connection manager
- `Auth`: Authentication and session management
- `User`: User CRUD operations and role management
- `Item`: Item management and search functionality
- `Category`: Category hierarchy and item counts
- `Validation`: Price update validation queue
- `Logger`: System activity logging
- `RateLimiter`: Request rate limiting

## 📁 Project Structure

```
MulyaSuchi/
├── admin/                  # Admin panel pages
│   ├── dashboard.php
│   ├── user_management.php
│   ├── validation_queue.php
│   ├── edit_item.php
│   └── system_logs.php
├── assets/                 # Static resources
│   ├── css/               # Stylesheets
│   │   ├── core/         # Core styles (reset, variables, layout)
│   │   ├── components/   # Component styles (navbar, footer, cards)
│   │   ├── pages/        # Page-specific styles
│   │   └── themes/       # Theme files (dark mode)
│   ├── js/                # JavaScript files
│   │   ├── core/         # Core utilities (theme manager, utils)
│   │   ├── components/   # Component scripts (navbar, footer, charts)
│   │   └── animations/   # Animation scripts
│   ├── images/            # Site images and icons
│   └── uploads/           # User-uploaded files
├── classes/               # PHP classes (OOP)
│   ├── Database.php
│   ├── Auth.php
│   ├── User.php
│   ├── Item.php
│   ├── Category.php
│   ├── Validation.php
│   ├── Logger.php
│   └── RateLimiter.php
├── config/                # Configuration files
│   ├── config.php         # Main configuration
│   ├── database.php       # Database connection
│   ├── env.php            # Environment loader
│   ├── constants.php      # Application constants
│   └── security.php       # Security functions
├── contributor/           # Contributor portal
│   ├── dashboard.php
│   ├── add_item.php
│   ├── edit_item.php
│   └── update_price.php
├── docs/                  # Documentation
│   ├── SETUP_NOTES.md
│   ├── COLOR_PALETTE.md
│   └── PRODUCTS_PAGE_GUIDE.md
├── includes/              # Reusable components
│   ├── header_professional.php
│   ├── footer_professional.php
│   ├── nav.php
│   └── functions.php
├── public/                # Public pages
│   ├── index.php          # Landing page
│   ├── products.php       # Product listing with AJAX
│   ├── item.php           # Item detail page
│   ├── categories.php     # Category browser
│   ├── about.php
│   ├── privacy-policy.php
│   └── terms-of-service.php
├── scripts/               # Utility scripts
│   ├── backup_database.sh
│   └── cleanup_orphaned_images.php
├── sql/                   # Database files
│   ├── schema.sql         # Database schema
│   ├── seed_data.sql      # Initial data
│   └── database_optimizations.sql
├── tests/                 # Test suite
│   └── README.md
├── .env.example           # Environment template
├── .gitignore
├── .htaccess              # Apache configuration
└── README.md              # This file
```

## 🚀 Getting Started

### Prerequisites

- **PHP 7.4 or higher**
- **MySQL 5.7 or higher** / MariaDB 10.2+
- **Apache 2.4+** with `mod_rewrite` enabled
- **Composer** (optional, for future dependency management)

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/your-username/MulyaSuchi.git
cd MulyaSuchi
```

2. **Set up the environment file**
```bash
cp .env.example .env
```

Edit `.env` and configure your database credentials:
```env
DB_HOST=localhost
DB_NAME=mulyasuchi_db
DB_USER=your_db_user
DB_PASS=your_secure_password
SITE_URL=http://localhost/MulyaSuchi
APP_ENV=development
APP_DEBUG=true
```

3. **Create the database**
```bash
mysql -u root -p
```

```sql
CREATE DATABASE mulyasuchi_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'mulyasuchi_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON mulyasuchi_db.* TO 'mulyasuchi_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

4. **Import the database schema**
```bash
mysql -u mulyasuchi_user -p mulyasuchi_db < sql/schema.sql
mysql -u mulyasuchi_user -p mulyasuchi_db < sql/database_optimizations.sql
mysql -u mulyasuchi_user -p mulyasuchi_db < sql/seed_data.sql
```

5. **Set proper permissions**
```bash
# For Linux/Mac
chmod 755 assets/uploads/
chmod 755 logs/

# Make sure Apache can write to these directories
chown -R www-data:www-data assets/uploads/ logs/
```

6. **Configure Apache Virtual Host** (Optional)

Create `/etc/apache2/sites-available/mulyasuchi.conf`:
```apache
<VirtualHost *:80>
    ServerName mulyasuchi.local
    DocumentRoot /var/www/html/MulyaSuchi/public
    
    <Directory /var/www/html/MulyaSuchi/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/mulyasuchi_error.log
    CustomLog ${APACHE_LOG_DIR}/mulyasuchi_access.log combined
</VirtualHost>
```

Enable the site:
```bash
sudo a2ensite mulyasuchi
sudo systemctl reload apache2
```

7. **Access the application**

Open your browser and navigate to:
- Local: `http://localhost/MulyaSuchi/public/`
- Virtual Host: `http://mulyasuchi.local/`

### Default Credentials

After importing seed data, use these credentials to log in:

**Admin Account:**
- Email: `admin@mulyasuchi.com`
- Password: `admin123`

**Contributor Account:**
- Email: `contributor@mulyasuchi.com`
- Password: `contributor123`

**⚠️ IMPORTANT**: Change these passwords immediately in production!

## 🔧 Configuration

### Environment Variables

Key configuration options in `.env`:

```env
# Application
APP_ENV=production          # development, staging, or production
APP_DEBUG=false             # Enable debug mode (never in production)

# Database
DB_HOST=localhost
DB_NAME=mulyasuchi_db
DB_USER=mulyasuchi_user
DB_PASS=secure_password

# Site
SITE_NAME=Mulyasuchi
SITE_URL=https://mulyasuchi.com
SITE_EMAIL=contact@mulyasuchi.com

# Security
SESSION_LIFETIME=3600       # Session timeout in seconds
PASSWORD_MIN_LENGTH=8       # Minimum password length

# File Upload
MAX_FILE_SIZE=5242880       # 5MB in bytes
UPLOAD_DIR=/path/to/uploads/

# Rate Limiting
RATE_LIMIT_ENABLED=true
RATE_LIMIT_MAX_ATTEMPTS=5
RATE_LIMIT_DECAY_MINUTES=15
```

## 📦 Build and Run

### Development Mode

```bash
# Start Apache and MySQL (XAMPP)
sudo /opt/lampp/lampp start

# Or for separate services
sudo systemctl start apache2
sudo systemctl start mysql

# Access the application
http://localhost/MulyaSuchi/public/
```

### Production Mode

1. Update `.env`:
```env
APP_ENV=production
APP_DEBUG=false
```

2. Clear any cached data:
```bash
rm -rf logs/*.log
rm -rf cache/*
```

3. Set strict file permissions:
```bash
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod 600 .env
chmod 700 scripts/*.sh
```

4. Enable production optimizations in `php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

## 🧪 Testing

Tests are located in the `tests/` directory. To run tests:

```bash
# Install PHPUnit (if using Composer)
composer require --dev phpunit/phpunit

# Run all tests
./vendor/bin/phpunit tests/

# Run specific test suite
./vendor/bin/phpunit tests/unit/
```

**Note**: Test suite implementation is in progress. See `tests/README.md` for details.

## 🚀 Deployment

### Deployment Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Use strong database passwords
- [ ] Enable HTTPS and set `cookie_secure=true`
- [ ] Configure proper file permissions
- [ ] Set up automated backups
- [ ] Configure error logging
- [ ] Enable rate limiting
- [ ] Review security settings
- [ ] Test all critical user flows

For detailed deployment instructions, see [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md).

### Quick Deployment (Production Server)

```bash
# 1. Clone repository
git clone https://github.com/your-username/MulyaSuchi.git /var/www/html/mulyasuchi

# 2. Configure environment
cp .env.example .env
nano .env  # Edit production values

# 3. Import database
mysql -u root -p < sql/schema.sql

# 4. Set permissions
chown -R www-data:www-data /var/www/html/mulyasuchi
chmod 600 .env

# 5. Configure Apache virtual host
# (See Installation section above)

# 6. Enable SSL (Let's Encrypt)
sudo certbot --apache -d mulyasuchi.com -d www.mulyasuchi.com
```

## 🤝 Contributing

We welcome contributions! Here's how you can help:

### Reporting Bugs

1. Check if the bug has already been reported in [Issues](https://github.com/your-username/MulyaSuchi/issues)
2. If not, create a new issue with:
   - Clear title and description
   - Steps to reproduce
   - Expected vs actual behavior
   - Screenshots (if applicable)
   - Environment details (PHP version, OS, browser)

### Suggesting Features

1. Open an issue with the `enhancement` label
2. Describe the feature and its benefits
3. Provide use cases and examples

### Pull Requests

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Make your changes following our coding standards
4. Test thoroughly
5. Commit with clear messages: `git commit -m 'Add amazing feature'`
6. Push to your fork: `git push origin feature/amazing-feature`
7. Open a Pull Request

### Coding Standards

- Follow **PSR-12** coding standards for PHP
- Use **camelCase** for JavaScript variables and functions
- Write **descriptive comments** for complex logic
- Keep functions **small and focused**
- Use **meaningful variable names**
- Add **PHPDoc blocks** for all functions and classes

## 📝 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

```
MIT License

Copyright (c) 2024 MulyaSuchi

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

## 👥 Authors and Acknowledgments

### Core Team
- **Development Team** - Initial work and ongoing maintenance

### Contributors
Thank you to all contributors who have helped shape MulyaSuchi!

### Acknowledgments
- Market data sources across Nepal
- Community contributors and testers
- Open source libraries: Chart.js, Bootstrap Icons

## 📧 Contact and Support

- **Website**: [https://mulyasuchi.com](https://mulyasuchi.com)
- **Email**: contact@mulyasuchi.com
- **GitHub Issues**: [Report a bug or request a feature](https://github.com/your-username/MulyaSuchi/issues)

## 🗺️ Roadmap

### Version 2.0 (Planned)
- [ ] REST API for third-party integrations
- [ ] Mobile app (React Native)
- [ ] SMS price alerts
- [ ] Multilingual support (Nepali, English)
- [ ] Advanced analytics dashboard
- [ ] Price prediction using ML
- [ ] Vendor verification system
- [ ] Bulk price import via CSV/Excel

### Version 1.5 (In Progress)
- [ ] Email notifications for price changes
- [ ] User favorites and watchlists
- [ ] Export data to PDF/Excel
- [ ] Enhanced search with autocomplete

## 📊 Project Statistics

- **500+ Commodities** tracked across markets
- **50+ Markets** in Nepal
- **20+ Categories** including vegetables, fruits, grains, dairy, and more
- **Real-time updates** from verified contributors
- **Mobile-first** responsive design
- **Dark mode** support for comfortable viewing

---

**Made with ❤️ for Nepal's market transparency**

*Star ⭐ this repository if you find it useful!*
