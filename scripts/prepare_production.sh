#!/bin/bash

# MulyaSuchi - Pre-Deployment Cleanup Script
# This script removes development files and prepares for production

echo "🧹 Starting cleanup for production deployment..."

# Remove development documentation files
echo "Removing development documentation..."
rm -f COLOR_CONVERSION_SUMMARY.md
rm -f VISUAL_COLOR_GUIDE.md
rm -f REBRANDING_CHECKLIST.md
rm -f QUICK_REFERENCE.md
rm -f QUICK_START_ADS.md
rm -f DEPLOYMENT_SUMMARY.md
rm -f convert_to_green_theme.ps1

# Remove development documentation from docs/
echo "Removing development docs from docs/..."
rm -f docs/AD_CAROUSEL_VISUAL_GUIDE.md
rm -f docs/DASHBOARD_VISUAL_GUIDE.md

# Clean up test files
echo "Cleaning test files..."
rm -f admin/test_upload.php

# Clean up temporary database files
echo "Cleaning SQL backup files..."
rm -f sql/items_fixed.sql
rm -f sql/fresh_500_products_part2.sql
rm -f sql/fresh_500_products_part3.sql
rm -f sql/seed_500_products_master.sql
rm -f sql/fix_nepali_text.php

# Clean up logs (keep directory)
echo "Cleaning logs..."
> logs/rate_limits.json
echo "{}" > logs/rate_limits.json

# Clean uploads (keep structure)
echo "Cleaning uploads directory..."
find assets/uploads/ -type f -not -name '.gitkeep' -delete 2>/dev/null

# Remove any backup files
echo "Removing backup files..."
find . -type f \( -name "*.backup" -o -name "*.bak" -o -name "*.old" -o -name "*~" \) -delete 2>/dev/null

# Remove development editor files
echo "Removing editor files..."
rm -rf .vscode/
rm -rf .idea/
find . -type f \( -name "*.swp" -o -name "*.swo" -o -name ".DS_Store" \) -delete 2>/dev/null

# Clean node_modules if exists
if [ -d "node_modules" ]; then
    echo "Removing node_modules..."
    rm -rf node_modules/
fi

# Set proper permissions
echo "Setting proper permissions..."
chmod 755 scripts/*.sh 2>/dev/null
chmod 755 scripts/*.php 2>/dev/null
chmod 775 assets/uploads/ 2>/dev/null
chmod 775 logs/ 2>/dev/null

# Create production .env reminder
if [ ! -f ".env" ]; then
    echo "⚠️  WARNING: .env file not found! Copy from .env.example"
fi

echo ""
echo "✅ Cleanup complete!"
echo ""
echo "📋 Next steps:"
echo "1. Review and update .env file for production"
echo "2. Update config/database.php with production credentials"
echo "3. Ensure assets/uploads/ is writable (775)"
echo "4. Ensure logs/ is writable (775)"
echo "5. Test the application thoroughly"
echo "6. Run database migrations if needed"
echo ""
echo "🚀 Ready for deployment!"
