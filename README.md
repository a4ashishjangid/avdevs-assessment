# User File Upload & Registration System

A simple PHP and MySQL-based web application for user registration, authentication, and secure file uploads, featuring client-side and server-side validation, CSRF protection, and a modular database layer.

## Features
- User registration and login with password hashing
- Secure file uploads with file type and size validation
- View and delete uploaded files
- CSRF protection on all forms
- Centralized, reusable database helper methods
- Clean Bootstrap 5 UI

## Requirements
- PHP 7.4+
- MySQL/MariaDB
- Apache/Nginx (or WAMP/XAMPP for local dev)
- Composer (optional, if you add dependencies)

## Setup Instructions

1. **Clone the Repository**
   ```sh
   git clone https://github.com/a4ashishjangid/avdevs-assessment.git
   cd avdevs-assessment
   ```

2. **Database Setup**
   - Create a MySQL database, e.g. `assessment_db`.
   - Import the provided `assessment_db.sql` file to set up the database schema.

3. **Configure Database Connection**
   - Edit `config/database.php` with your MySQL credentials:
     ```php
     private $host = 'localhost';
     private $db_name = 'assessment_db';
     private $username = 'root';
     private $password = '';
     ```

4. **Set Permissions**
   - Ensure the `uploads/` directory is writable by the web server.

5. **Run Locally**
   - Place the project in your web root (e.g. `www/avdevs-assessment` for WAMP).
   - Visit `http://localhost/avdevs-assessment/` in your browser.

6. **Security Notes**
   - Never commit `config/database.php` with real credentials to public repos.
   - The `.gitignore` is set to exclude sensitive files and uploads.

## Project Structure
- `index.php` — Main dashboard (was `dashboard.php`)
- `register.php` — User registration
- `login.php`, `logout.php` — Authentication
- `classes/` — Core PHP classes (User, FileUpload)
- `config/database.php` — Database connection and helpers
- `assets/js/common.js` — Client-side validation
- `uploads/` — User-uploaded files (gitignored)

## License
MIT or your choice

---
For questions or contributions, open an issue or PR!
