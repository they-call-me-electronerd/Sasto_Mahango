# MulyaSuchi - Project Structure

## Overview
MulyaSuchi is a comprehensive market intelligence platform for Nepal, providing real-time pricing information and market insights.

## Directory Structure

```
MulyaSuchi/
├── admin/                  # Admin panel files
│   ├── dashboard.php       # Admin dashboard
│   ├── login.php          # Admin authentication
│   ├── user_management.php # User management
│   ├── validation_queue.php # Price validation
│   ├── edit_item.php      # Item editing
│   ├── settings.php       # System settings
│   ├── system_logs.php    # Activity logs
│   └── test_upload.php    # Upload testing
│
├── assets/                 # Static assets
│   ├── css/               # Stylesheets
│   │   ├── animations/    # Animation styles
│   │   ├── components/    # Component styles
│   │   ├── core/          # Core styles
│   │   ├── pages/         # Page-specific styles
│   │   └── themes/        # Theme styles
│   ├── js/                # JavaScript files
│   │   ├── animations/    # Animation scripts
│   │   ├── components/    # Component scripts
│   │   ├── core/          # Core scripts
│   │   └── pages/         # Page-specific scripts
│   ├── images/            # Images and graphics
│   └── uploads/           # User uploaded files
│
├── classes/               # PHP Classes (OOP)
│   ├── Auth.php          # Authentication class
│   ├── Category.php      # Category management
│   ├── Database.php      # Database connection
│   ├── Item.php          # Item/product management
│   ├── Logger.php        # Activity logging
│   ├── RateLimiter.php   # Rate limiting
│   ├── User.php          # User management
│   └── Validation.php    # Data validation
│
├── config/                # Configuration files
│   ├── config.php        # Main configuration
│   ├── constants.php     # Constants definitions
│   ├── database.php      # Database configuration
│   ├── env.php           # Environment loader
│   └── security.php      # Security settings
│
├── contributor/           # Contributor panel
│   ├── dashboard.php     # Contributor dashboard
│   ├── add_item.php      # Add new items
│   ├── edit_item.php     # Edit items
│   ├── update_price.php  # Update prices
│   ├── login.php         # Contributor login
│   ├── register.php      # Contributor registration
│   └── assets/           # Contributor assets
│
├── docs/                  # Documentation
│   ├── DOCUMENTATION_INDEX.md    # Documentation index
│   ├── SETUP_NOTES.md           # Setup instructions
│   ├── COLOR_PALETTE.md         # Design color palette
│   ├── UI_COLOR_GUIDE.md        # UI guidelines
│   ├── PRODUCTS_PAGE_GUIDE.md   # Products page guide
│   ├── AD_CAROUSEL_GUIDE.md     # Ad carousel guide
│   ├── BANNER_ADS_IMPLEMENTATION.md
│   └── DASHBOARD_VISUAL_GUIDE.md
│
├── includes/              # Shared includes
│   ├── header.php        # Common header
│   ├── header_professional.php
│   ├── footer.php        # Common footer
│   ├── footer_professional.php
│   ├── nav.php           # Navigation
│   └── functions.php     # Helper functions
│
├── logs/                  # Application logs
│   └── rate_limits.json  # Rate limiting data
│
├── public/                # Public pages
│   ├── index.php         # Homepage
│   ├── products.php      # Products listing
│   ├── browse.php        # Browse items
│   ├── categories.php    # Categories page
│   ├── item.php          # Item detail page
│   ├── about.php         # About page
│   ├── privacy-policy.php
│   ├── terms-of-service.php
│   ├── cookie-policy.php
│   ├── error.php         # Error page
│   └── ajax/             # AJAX endpoints
│
├── scripts/               # Utility scripts
│   ├── backup_database.sh
│   ├── cleanup_orphaned_images.php
│   ├── seed_products.php
│   └── seed_new_categories.php
│
├── sql/                   # Database files
│   ├── schema.sql        # Database schema
│   ├── seed_data.sql     # Initial data
│   ├── items.sql         # Items data
│   ├── fresh_500_products.sql
│   ├── rebrand_database.sql
│   ├── migration_add_item_edit_support.sql
│   └── README.md         # SQL documentation
│
├── tests/                 # Testing files
│   └── README.md         # Testing documentation
│
├── .env.example          # Environment template
├── .gitignore            # Git ignore rules
├── .htaccess             # Apache configuration
├── CHANGELOG.md          # Version history
├── LICENSE               # MIT License
├── README.md             # Main documentation
├── robots.txt            # Search engine rules
└── sitemap.xml.php       # Dynamic sitemap

```

## Key Files

### Configuration
- `.env` - Environment variables (not in git)
- `.env.example` - Environment template
- `config/config.php` - Main configuration
- `config/database.php` - Database settings

### Entry Points
- `public/index.php` - Public homepage
- `admin/login.php` - Admin panel
- `contributor/login.php` - Contributor panel

### Core Classes
- `classes/Database.php` - Database singleton
- `classes/Auth.php` - Authentication
- `classes/Item.php` - Item management
- `classes/User.php` - User management

## Access Levels

1. **Public Users** - Browse and search products
2. **Contributors** - Add/update items and prices
3. **Administrators** - Full system access

## Security Features

- CSRF protection
- SQL injection prevention (PDO)
- XSS prevention
- Rate limiting
- Secure session management
- Input validation and sanitization

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Libraries**: Chart.js, Bootstrap Icons
- **Server**: Apache with mod_rewrite

## Environment Setup

1. Copy `.env.example` to `.env`
2. Configure database credentials
3. Import `sql/schema.sql`
4. Import `sql/seed_data.sql`
5. Set proper permissions on `assets/uploads/` and `logs/`

## Deployment

See `DEPLOYMENT_GUIDE.md` for detailed deployment instructions.

## Documentation

See `docs/DOCUMENTATION_INDEX.md` for complete documentation.
