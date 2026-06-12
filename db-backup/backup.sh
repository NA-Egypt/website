#!/usr/bin/env bash

# Resolve the absolute directory of this script
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ENV_FILE="$SCRIPT_DIR/../.env"

# Check if .env file exists
if [ ! -f "$ENV_FILE" ]; then
    echo "Error: .env file not found at $ENV_FILE" >&2
    exit 1
fi

# Function to extract environment variables from the .env file
get_env_var() {
    local key=$1
    grep -E "^${key}=" "$ENV_FILE" | head -n 1 | cut -d'=' -f2- | sed -e 's/^"//' -e 's/"$//' -e "s/^'//" -e "s/'$//"
}

DB_HOST=$(get_env_var "DB_HOST")
DB_PORT=$(get_env_var "DB_PORT")
DB_DATABASE=$(get_env_var "DB_DATABASE")
DB_USERNAME=$(get_env_var "DB_USERNAME")
DB_PASSWORD=$(get_env_var "DB_PASSWORD")

# Use defaults if host or port are not set
DB_HOST=${DB_HOST:-"127.0.0.1"}
DB_PORT=${DB_PORT:-"3306"}

if [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ]; then
    echo "Error: Database name (DB_DATABASE) or username (DB_USERNAME) not found in .env" >&2
    exit 1
fi

BACKUP_FILE="$SCRIPT_DIR/backup.sql"

# Export password securely to environment variable for mysqldump
export MYSQL_PWD="$DB_PASSWORD"

echo "Starting database backup for database: $DB_DATABASE..."

# Perform mysqldump.
mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" "$DB_DATABASE" > "$BACKUP_FILE"
MYSQLED_STATUS=$?

if [ $MYSQLED_STATUS -ne 0 ]; then
    echo "Error: mysqldump failed with exit code $MYSQLED_STATUS" >&2
    # Unset password variable
    unset MYSQL_PWD
    exit 1
fi

# Unset password variable to clean up memory
unset MYSQL_PWD

echo "Backup completed successfully: $BACKUP_FILE"

# Change directory to the root of the Git repo to run Git commands
cd "$SCRIPT_DIR/.." || exit 1

# Stage the backup file
git add db-backup/backup.sql

# Check if there are changes staged
if ! git diff --cached --quiet; then
    echo "Changes detected in database backup. Committing..."
    git commit -m "auto-backup: database update $(date '+%Y-%m-%d %H:%M:%S')"
    
    # Check if a remote "origin" is set up and we are on a branch
    if git remote | grep -q "^origin$"; then
        echo "Pushing changes to origin..."
        git push origin
    else
        echo "No 'origin' remote found, skipped pushing."
    fi
else
    echo "No changes detected in database backup since last commit."
    # Unstage the file to keep clean
    git reset db-backup/backup.sql > /dev/null 2>&1
fi
