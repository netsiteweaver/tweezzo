# Cron Job Setup Guide

## Task Due Date Reminders

This system automatically sends email reminders to developers about tasks that are approaching their due dates.

### What it does:

1. **Tasks Due in Next 3 Days** (`sendDueTaskReminders`):
   - Finds tasks due in 3, 2, and 1 days from today
   - Sends email reminders to assigned developers
   - Only includes active tasks (not completed or on hold)
   - Groups tasks by developer email

2. **Tasks Due Today** (`sendDueTodayReminders`):
   - Finds tasks due today
   - Sends urgent email reminders with subject "Tasks Due Today - Urgent"
   - Only includes active tasks (not completed or on hold)
   - Groups tasks by developer email

### Setup Instructions:

#### Option 1: Using crontab (Recommended)

Edit your crontab:
```bash
crontab -e
```

Add these lines to run daily:
```
# Send reminders for tasks due in 3, 2, and 1 days - daily at 8 AM
0 8 * * * cd /var/www/html/tweezzo && /usr/bin/php index.php cron sendDueTaskReminders >> /var/log/cron-due-tasks.log 2>&1

# Send reminders for tasks due today - daily at 9 AM
0 9 * * * cd /var/www/html/tweezzo && /usr/bin/php index.php cron sendDueTodayReminders >> /var/log/cron-due-today.log 2>&1
```

#### Option 2: Using wget/curl (if PHP CLI not available)

```
0 8 * * * wget -q -O- "https://yourdomain.com/cron/sendDueTaskReminders" >> /var/log/cron-due-tasks.log 2>&1
0 9 * * * wget -q -O- "https://yourdomain.com/cron/sendDueTodayReminders" >> /var/log/cron-due-today.log 2>&1
```

Or with curl:
```
0 8 * * * curl -s "https://yourdomain.com/cron/sendDueTaskReminders" >> /var/log/cron-due-tasks.log 2>&1
0 9 * * * curl -s "https://yourdomain.com/cron/sendDueTodayReminders" >> /var/log/cron-due-today.log 2>&1
```

### Manual Testing:

You can test the cron jobs manually by running:

```bash
cd /var/www/html/tweezzo
# Test 3-day reminders
php index.php cron sendDueTaskReminders

# Test today's reminders
php index.php cron sendDueTodayReminders

# Test specific day (e.g., tasks due in 2 days)
php index.php cron getDueTasks 2
```

Or visit in browser (make sure to secure this endpoint in production):
```
https://yourdomain.com/cron/sendDueTaskReminders
https://yourdomain.com/cron/sendDueTodayReminders
```

### Email Template:

The emails sent to developers use:
- Template: `/application/views/_email/dueTasks.php`
- Subject for upcoming tasks: "Tasks Due Reminder"
- Subject for today's tasks: "Tasks Due Today - Urgent"

### Configuration:

The reminders only include:
- Active tasks (`status = '1'` and `closed = '0'`)
- Tasks not in 'completed' or 'on_hold' stages
- Tasks from active sprints, projects, and customers
- Only developers with valid email addresses

To modify the reminder days, edit `/application/controllers/Cron.php`:
- `sendDueTaskReminders()` method (line ~114): Change the array `[3, 2, 1]` to different days
- `sendDueTodayReminders()` method (line ~123): Currently set to 0 (today)

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
2. **Send Due Task Reminders (3, 2, 1 days)**: `php index.php cron sendDueTaskReminders`
3. **Send Due Today Reminders**: `php index.php cron sendDueTodayReminders`
4. **Get Due Tasks**: `php index.php cron getDueTasks [days]`
5. **Fetch Quotes**: `php index.php cron fetchQuotes`

### Complete Crontab Example:

```
# Send emails from queue every 5 minutes
*/5 * * * * cd /var/www/html/tweezzo && /usr/bin/php index.php cron sendEmails >> /var/log/cron-emails.log 2>&1

# Send due task reminders (3, 2, 1 days ahead) daily at 8 AM
0 8 * * * cd /var/www/html/tweezzo && /usr/bin/php index.php cron sendDueTaskReminders >> /var/log/cron-due-tasks.log 2>&1

# Send due today reminders daily at 9 AM
0 9 * * * cd /var/www/html/tweezzo && /usr/bin/php index.php cron sendDueTodayReminders >> /var/log/cron-due-today.log 2>&1

# Suspend inactive developers weekly on Sundays at 2 AM
0 2 * * 0 cd /var/www/html/tweezzo && /usr/bin/php index.php cron suspendInactiveDevelopers >> /var/log/cron-suspend-developers.log 2>&1

# Fetch quotes daily at midnight
0 0 * * * cd /var/www/html/tweezzo && /usr/bin/php index.php cron fetchQuotes >> /var/log/cron-quotes.log 2>&1
```

### Security Note:

For production environments, consider adding authentication to the Cron controller or restricting access by IP address.

