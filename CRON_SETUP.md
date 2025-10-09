# Cron Job Setup Guide

## Suspend Inactive Developers

This system automatically suspends developers who have been inactive for 30 days.

### What it does:
1. Finds developers with no login activity for 30+ days
2. Checks both:
   - `login_history` (back-office logins)
   - `portal_login_history` (portal logins with type='developer')
3. Sets their status to '0' (suspended)
4. Sends an email notification to the suspended developer

### Setup Instructions:

#### Option 1: Using crontab (Recommended)

Edit your crontab:
```bash
crontab -e
```

Add this line to run daily at 2:00 AM:
```
0 2 * * * cd /var/www/html/tweezzo && /usr/bin/php index.php cron suspendInactiveDevelopers >> /var/log/cron-suspend-developers.log 2>&1
```

Or run weekly on Sundays at 2:00 AM:
```
0 2 * * 0 cd /var/www/html/tweezzo && /usr/bin/php index.php cron suspendInactiveDevelopers >> /var/log/cron-suspend-developers.log 2>&1
```

#### Option 2: Using wget/curl (if PHP CLI not available)

```
0 2 * * * wget -q -O- "https://yourdomain.com/cron/suspendInactiveDevelopers" >> /var/log/cron-suspend-developers.log 2>&1
```

Or with curl:
```
0 2 * * * curl -s "https://yourdomain.com/cron/suspendInactiveDevelopers" >> /var/log/cron-suspend-developers.log 2>&1
```

### Manual Testing:

You can test the cron job manually by running:

```bash
cd /var/www/html/tweezzo
php index.php cron suspendInactiveDevelopers
```

Or visit in browser (make sure to secure this endpoint in production):
```
https://yourdomain.com/cron/suspendInactiveDevelopers
```

### Email Template:

The email sent to suspended developers uses:
- Template: `/application/views/_email/developerSuspended.php`
- Subject: "Your developer account has been suspended"

### Configuration:

To change the inactivity threshold (default: 30 days), edit:
`/application/controllers/Cron.php` line 125:
```php
$thresholdDays = 30; // Change this value
```

### Other Available Cron Jobs:

1. **Send Email Queue**: `php index.php cron sendEmails`
2. **Send Due Task Reminders**: `php index.php cron sendDueTaskReminders`
3. **Get Due Tasks**: `php index.php cron getDueTasks [days]`
4. **Fetch Quotes**: `php index.php cron fetchQuotes`

### Complete Crontab Example:

```
# Send emails from queue every 5 minutes
*/5 * * * * cd /var/www/html/tweezzo && /usr/bin/php index.php cron sendEmails >> /var/log/cron-emails.log 2>&1

# Send due task reminders daily at 8 AM
0 8 * * * cd /var/www/html/tweezzo && /usr/bin/php index.php cron sendDueTaskReminders >> /var/log/cron-due-tasks.log 2>&1

# Suspend inactive developers weekly on Sundays at 2 AM
0 2 * * 0 cd /var/www/html/tweezzo && /usr/bin/php index.php cron suspendInactiveDevelopers >> /var/log/cron-suspend-developers.log 2>&1

# Fetch quotes daily at midnight
0 0 * * * cd /var/www/html/tweezzo && /usr/bin/php index.php cron fetchQuotes >> /var/log/cron-quotes.log 2>&1
```

### Security Note:

For production environments, consider adding authentication to the Cron controller or restricting access by IP address.

