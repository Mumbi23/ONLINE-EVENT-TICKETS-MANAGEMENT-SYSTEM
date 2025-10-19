# OETMS - Online Event Ticket Management System

**Version:** Final commented package
**Generated:** 2025-09-10 11:51:20 UTC

## Overview
OETMS is a simple PHP + MySQL application that allows:
- Organizers to create events and view tickets purchased for their events.
- Attendees to browse events and purchase tickets.

This package includes descriptive inline comments to help you understand each file.

## Folder structure
```
OETMS_final_commented/
├─ assets/
│  ├─ css/
│  │  └─ style.css
│  └─ js/
│     └─ app.js
├─ templates/
│  ├─ header.php
│  └─ footer.php
├─ db.php
├─ init_db.sql
├─ index.php
├─ signup.php
├─ signin.php
├─ logout.php
├─ organizer_dashboard.php
├─ attendee_dashboard.php
├─ create_event.php
├─ browse_events.php
├─ event.php
├─ buy_ticket.php
├─ my_tickets.php
└─ manage_tickets.php
```

## Quick setup (detailed)
1. Copy the project folder into your web server root:
   - XAMPP: `C:\xampp\htdocs\OETMS_final_commented`
   - LAMP: `/var/www/html/OETMS_final_commented`

2. Start Apache and MySQL (e.g., via XAMPP control panel).

3. Import the database schema:
   - Open phpMyAdmin (http://localhost/phpmyadmin) or use MySQL client.
   - Import `init_db.sql` to create the `OETMS` database and tables.

4. Configure database credentials:
   - If your MySQL user/password are different, edit `db.php` and update `$user` and `$pass`.

5. Visit the app:
   - http://localhost/OETMS_final_commented

## Notes & Next steps
- This MVP does not include payment gateway integration. `buy_ticket.php` simply records a ticket row.
- Consider adding validation, CSRF protection, file uploads, or sending email confirmations for production use.
- Passwords are hashed using PHP's `password_hash()` for better security.

## Support
If you need changes (more comments, extra features, or deployment help), reply in the chat and I will update the package.
