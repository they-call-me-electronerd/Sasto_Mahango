#!/bin/bash
# ===========================================================================
# DATABASE BACKUP SCRIPT
# ===========================================================================
# Schedule with cron: 0 2 * * * /path/to/backup.sh
# Daily backup at 2 AM
# ===========================================================================

# Configuration
DB_NAME="mulyasuchi_db"
DB_USER="mulyasuchi_user"
DB_PASS="your_password_here"
BACKUP_DIR="/var/backups/mulyasuchi"
RETENTION_DAYS=30

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Generate filename with timestamp
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="$BACKUP_DIR/mulyasuchi_$TIMESTAMP.sql.gz"

# Perform backup
echo "Starting database backup..."
mysqldump -u "$DB_USER" -p"$DB_PASS" --single-transaction --routines --triggers "$DB_NAME" | gzip > "$BACKUP_FILE"

# Check if backup was successful
if [ $? -eq 0 ]; then
    echo "Backup completed successfully: $BACKUP_FILE"
    
    # Set permissions
    chmod 600 "$BACKUP_FILE"
    
    # Delete old backups (older than retention period)
    find "$BACKUP_DIR" -name "mulyasuchi_*.sql.gz" -mtime +$RETENTION_DAYS -delete
    echo "Old backups removed (retention: $RETENTION_DAYS days)"
else
    echo "Backup failed!"
    exit 1
fi

# Optional: Upload to cloud storage (AWS S3, Google Cloud, etc.)
# aws s3 cp "$BACKUP_FILE" s3://your-bucket/backups/

echo "Backup process completed."
