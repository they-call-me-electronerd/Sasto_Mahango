# Database Files

This directory contains the complete database SQL file for MulyaSuchi.

## File

### Complete Database
- **`mulyasuchi_complete.sql`** - Complete database with schema and all data
  - Database structure (tables, indexes, constraints)
  - Initial seed data (categories, users, settings)
  - All product data (500+ items)
  - Sample data for testing
  - All migrations applied

## Setup Instructions

### Fresh Installation

1. Create database:
```sql
CREATE DATABASE mulyasuchi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Create database user:
```sql
CREATE USER 'mulyasuchi_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON mulyasuchi.* TO 'mulyasuchi_user'@'localhost';
FLUSH PRIVILEGES;
```

3. Import complete database:
```bash
mysql -u mulyasuchi_user -p mulyasuchi < mulyasuchi_complete.sql
```

That's it! Your database is ready with all tables, data, and products.

### Database Structure

#### Main Tables
- `users` - User accounts (admin, contributor, user)
- `items` - Product/commodity listings with prices
- `categories` - Product categories (vegetables, fruits, grains, etc.)
- `markets` - Market locations across Nepal
- `price_history` - Historical price data for trend analysis
- `logs` - System activity and security logs
- `sessions` - User session management

#### Key Features
- **UTF-8MB4 support** for Nepali Devanagari script
- **Optimized indexes** for fast search and filtering
- **Foreign key constraints** for data integrity
- **Timestamp tracking** on all records
- **Full-text search** on product names and descriptions
- **Secure password hashing** for user accounts

## Maintenance

### Backup Database
```bash
# Windows
mysqldump -u mulyasuchi_user -p mulyasuchi > backup_%date:~-4,4%%date:~-7,2%%date:~-10,2%.sql

# Linux/Mac
mysqldump -u mulyasuchi_user -p mulyasuchi > backup_$(date +%Y%m%d).sql
```

### Restore Database
```bash
mysql -u mulyasuchi_user -p mulyasuchi < backup_20241215.sql
```

### Optimize Tables (Monthly Maintenance)
```sql
USE mulyasuchi;
OPTIMIZE TABLE items, users, categories, price_history, logs;
ANALYZE TABLE items, users, categories;
```

### Check Database Size
```sql
SELECT 
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables 
WHERE table_schema = 'mulyasuchi'
GROUP BY table_schema;
```

## Character Set & Collation

All tables use `utf8mb4_unicode_ci` collation to properly support:
- ✅ Nepali Devanagari script (नेपाली देवनागरी)
- ✅ Emojis and special characters (😊 ⭐ ✓)
- ✅ Full Unicode support
- ✅ Case-insensitive searching
- ✅ Proper sorting for Nepali text

## Default Admin Account

After importing the database, you can login with:
- **Username**: Check the database or create via registration
- **Password**: Set during first setup
- **Role**: Update to 'admin' in database after registration

## Troubleshooting

### Import Error: "Unknown database"
Make sure you created the database first:
```sql
CREATE DATABASE mulyasuchi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Import Error: "Access denied"
Verify user has proper permissions:
```sql
SHOW GRANTS FOR 'mulyasuchi_user'@'localhost';
```

### Nepali Text Shows as "????"
Ensure your database and tables use utf8mb4:
```sql
ALTER DATABASE mulyasuchi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE items CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Slow Queries
Check and add missing indexes:
```sql
SHOW INDEX FROM items;
EXPLAIN SELECT * FROM items WHERE category_id = 1;
```

## Database Requirements

- **MySQL**: 5.7 or higher
- **MariaDB**: 10.2 or higher
- **Storage Engine**: InnoDB (default)
- **Character Set**: utf8mb4
- **Collation**: utf8mb4_unicode_ci
- **Disk Space**: ~50MB (with 500+ products)

## Security Notes

⚠️ **Important**: 
- Change default passwords after installation
- Use strong database passwords
- Restrict database user permissions
- Don't expose database credentials in code
- Regular backups are essential
- Keep MySQL updated

## Support

For database-related issues:
1. Check MySQL error logs
2. Verify database credentials in `.env`
3. Test connection: `mysql -u mulyasuchi_user -p`
4. Review installation guide: `../INSTALLATION.md`
