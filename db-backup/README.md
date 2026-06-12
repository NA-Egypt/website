# Database Backups

This directory contains the database backup script and the generated plain text backup file (`backup.sql`).

## Files
- `backup.sh`: A shell script that automatically reads database credentials from the root `.env` file, performs a `mysqldump`, commits the changes, and pushes to the Git remote repository.
- `backup.sql`: The exported MySQL database backup.

## Manual Run
To run the database backup manually, execute the script from the command line:
```bash
./db-backup/backup.sh
```

## Automating with Cron
To run this backup automatically on a schedule (for example, daily at 2:00 AM), add a cron job to your system.

1. Open your crontab configuration:
   ```bash
   crontab -e
   ```

2. Add the following line at the end of the file (make sure to use the absolute path to your backup script):
   ```cron
   0 2 * * * /var/www/html/new/db-backup/backup.sh > /dev/null 2>&1
   ```

3. Save and close the editor.
