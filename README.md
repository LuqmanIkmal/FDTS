# Fixed Deposit Tracking System (Laravel)

Laravel 12 / PHP 8.2+ / MySQL version of the Java (Servlet + JSP + Oracle) Fixed Deposit Tracking System.
It runs on ordinary PHP hosting such as the Hostinger Business plan.

The database starts **empty**. Create the first account from the **Sign Up** page and choose
the role *Senior Finance Manager*. That role can see the Bank and User menus.

## Run locally

Requirements: PHP 8.2+ (with `pdo_mysql`, `bcmath`, `fileinfo`, `mbstring`), Composer, MySQL/MariaDB (XAMPP works),
Node.js 20.19+ or 22.12+ (only on your own computer, to build the CSS/JS with Vite).

```bash
composer install
npm install
cp .env.example .env            # then edit DB_* and MAIL_* values
php artisan key:generate
php artisan migrate             # creates the empty tables
php artisan serve               # http://localhost:8000
npm run dev                     # in a second terminal: serves resources/css and resources/js, reloads on save
```

`npm run dev` must stay running while you work. To run without it, build the files once with `npm run build`.

Run the tests with `php artisan test`.

## Deploy to Hostinger (Business plan)

1. **hPanel → Advanced → PHP Configuration**: choose PHP 8.2 or newer and make sure `bcmath` and `fileinfo` are enabled.
2. **hPanel → Databases → MySQL Databases**: create a database and user. Note the database name, user and password.
3. **Build the CSS/JS on your own computer** with `npm run build`. This writes `public/build/`, which the
   signed-in pages need. The server does not need Node.
4. **Upload the project** (everything except `vendor/`, `node_modules/`, `public/hot` and `.env`) into
   `domains/<your-domain>/public_html/` using File Manager or Git. The `.htaccess` in the project root sends
   all traffic into `public/`, so `.env`, `storage/` and the code are not reachable from the web.
   `public/build/` is in `.gitignore`: if you deploy with Git, upload that folder separately or remove that line.
5. **SSH in** (hPanel → Advanced → SSH Access) and run:
   ```bash
   cd domains/<your-domain>/public_html
   composer install --no-dev --optimize-autoloader
   cp .env.example .env
   php artisan key:generate
   ```
6. **Edit `.env`** on the server:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://<your-domain>
   DB_HOST=127.0.0.1
   DB_DATABASE=<from step 2>
   DB_USERNAME=<from step 2>
   DB_PASSWORD=<from step 2>
   MAIL_USERNAME=<gmail address>
   MAIL_PASSWORD=<gmail app password>
   MAIL_FROM_ADDRESS=<gmail address>
   ```
   If Gmail SMTP is blocked, use a Hostinger mailbox instead: `MAIL_HOST=smtp.hostinger.com`, `MAIL_PORT=465`, `MAIL_SCHEME=smtps`.
7. **Create the tables and cache the config**:
   ```bash
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
8. **hPanel → Advanced → Cron Jobs**: add a job that runs **every minute**:
   ```
   /usr/bin/php /home/<user>/domains/<your-domain>/public_html/artisan schedule:run
   ```
   This runs the background jobs:
   - `fd:auto-renew`: daily at 00:05. Renews matured Free FDs that have Auto Renewal = Yes.
   - `fd:send-reminders`: every 3 hours. Emails the FD creator about FDs maturing within 7 days and incomplete FD records.

After changing `.env` on the server, run `php artisan config:cache` again.

## How the Java project maps to Laravel

| Java | Laravel |
|---|---|
| `LoginServlet`, `SignUpServlet` | `app/Http/Controllers/AuthController.php` |
| `ForgotPassword`, `VerifyCode`, `ResetPassword` servlets | `PasswordResetController.php` |
| `Dashboard.jsp` (SQL inside JSP) | `DashboardController.php` |
| `BankServlet`, `BankList.jsp` | `BankController.php` |
| `UserListServlet`, `UpdateUserServlet` | `UserController.php` |
| `UpdateProfileServlet`, `ProfilePictureServlet` | `ProfileController.php` |
| `FDListServlet`, `CreateFDServlet`, `SubmitFDServlet`, `ViewFDServlet`, `ViewApplicationServlet`, `UpdateFDServlet`, `GenerateReportServlet` | `FixedDepositController.php` |
| `FixedDepositDAO` (reinvest, withdraw, auto-renew, duplicates) | `app/Services/FixedDepositService.php` |
| `FDReminderScheduler`, `EmailUtil` | `app/Services/ReminderService.php`, `app/Services/Mailer.php` |
| `AutoRenewalScheduler`, `FDReminderScheduler` timers | `routes/console.php` (Laravel scheduler + cron) |
| `*.jsp`, `includes/*.jsp` | `resources/views/**/*.blade.php`, `resources/views/partials/` |
| Oracle tables `BANK`, `STAFF`, `FIXEDDEPOSITRECORD`, `FREEFD`, `PLEDGEFD`, `FIXEDDEPOSITTRANSACTION` | `banks`, `staff`, `fixed_deposit_records`, `free_fds`, `pledge_fds`, `fixed_deposit_transactions` |

Profile pictures and FD certificates were BLOB columns in Oracle. Here they are files in `storage/app/private/`,
and the columns store the file path.

## Differences from the Java version

These fixes and security changes were made on purpose:

- Passwords are stored as bcrypt hashes instead of plain text. The Profile page shows `********` instead of the real password.
- Every internal page requires login. Bank and User pages require the *Senior Finance Manager* role, not just a hidden menu.
- Log Out now ends the session.
- Forms are protected against CSRF.
- Marking an FD as *Matured* sets its balance to the maturity amount. In the Java code this never ran, because the status was read after the update.
- Withdraw and reinvest transactions are recorded under the logged-in staff member. The Java code read the wrong session key and always used staff ID 1.
- Auto-renewal records the system as `staff_id = NULL`. In Java it used staff ID 0, which failed the foreign key.
- Reinvested FDs copy their Free/Pledge settings correctly and start with a remaining balance.
- The View FD page shows the real Auto Renewal and Collateral status. In Java these always showed "No" and "Partial".
- View FD shows the creator's name instead of a fixed "Admin".
- Report links open View and Update correctly. In Java they pointed to JSPs that redirected away.
- Going Back from the Application Form keeps the uploaded certificate.
