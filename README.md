# MediCare Clinic - Appointment Management System

A modern, full-stack **Clinic Appointment Management System** built as a university project. Features a premium SaaS-style dashboard with role-based access for Admins, Doctors, and Patients.

![Dashboard Screenshot](docs/screenshots/dashboard.png)
*Placeholder for dashboard screenshot*

---

## Features

### Admin
- Dashboard with statistics and Chart.js analytics
- Manage doctors, patients, appointments, and users (full CRUD)
- Approve/reject pending appointments
- Search, filter, and paginate all records
- Dark/light theme toggle

### Doctor
- Personal dashboard with today's schedule
- View and manage appointments
- Mark appointments as completed with notes
- Patient history view
- Profile management

### Patient
- Self-registration and login
- Book appointments with doctors
- Browse doctors by specialization
- View and cancel appointments
- Profile management

### Security
- PDO prepared statements (no SQL injection)
- `password_hash()` / `password_verify()`
- CSRF protection on all forms
- Session regeneration on login
- Role-based route protection
- Input sanitization and output escaping

---

## Tech Stack

| Layer    | Technology                          |
|----------|-------------------------------------|
| Backend  | PHP 8+, PDO, MySQL, Sessions        |
| Frontend | HTML5, Tailwind CSS, JavaScript ES6 |
| UI       | Alpine.js, Chart.js, Lucide Icons   |
| Architecture | MVC-inspired, modular PHP       |

---

## Requirements

- PHP 8.0 or higher
- MySQL 5.7+ or MariaDB 10.3+
- PDO MySQL extension enabled
- Web server (Apache/Nginx) or PHP built-in server

---

## Installation

### 1. Clone or copy the project

```bash
cd clinic-management
```

### 2. Configure database

Edit `config/database.php` with your MySQL credentials:

```php
$host = 'localhost';
$dbname = 'clinic_management';
$username = 'root';
$password = '';
```

### 3. Import the database

```bash
mysql -u root -p < database/schema.sql
```

Or use phpMyAdmin to import `database/schema.sql`.

### 4. Set demo passwords

After importing, run the setup script to hash demo passwords:

```bash
php database/setup.php
```

Or visit `http://localhost:8000/database/setup.php` in your browser.

> **Important:** Delete `database/setup.php` after running in production.

### 5. Start the server

```bash
php -S localhost:8000 -t .
```

Open [http://localhost:8000](http://localhost:8000) in your browser.

---

## Default Login Credentials

| Role    | Email                        | Password   |
|---------|------------------------------|------------|
| Admin   | admin@clinic.com             | admin123   |
| Doctor  | sarah.mitchell@clinic.com    | doctor123  |
| Patient | john.anderson@email.com      | patient123 |

---

## Folder Structure

```
clinic-management/
├── admin/                  # Admin panel pages
│   ├── appointments/
│   ├── doctors/
│   ├── patients/
│   └── users/
├── auth/                   # Login, register, logout
├── config/                 # App and database config
├── database/               # SQL schema and setup script
├── doctor/                 # Doctor panel pages
├── patient/                # Patient panel pages
├── modules/                # Data models (MVC)
│   ├── appointments/
│   ├── dashboard/
│   ├── doctors/
│   ├── patients/
│   └── users/
├── helpers/                # Utility functions
├── middleware/             # Auth and role guards
├── layouts/                # Shared layout templates
├── includes/               # Components and model loader
├── assets/
│   ├── css/
│   └── js/
├── uploads/profiles/       # Profile image uploads
├── bootstrap.php           # App initialization
└── index.php               # Entry point
```

---

## How to Run

1. Ensure MySQL is running
2. Import `database/schema.sql`
3. Run `php database/setup.php`
4. Start server: `php -S localhost:8000 -t .`
5. Login with admin credentials
6. Explore all three role dashboards

---

## Screenshots

| Page | Description |
|------|-------------|
| `docs/screenshots/login.png` | Login page with glassmorphism design |
| `docs/screenshots/admin-dashboard.png` | Admin dashboard with charts |
| `docs/screenshots/appointments.png` | Appointment management table |
| `docs/screenshots/patient-book.png` | Patient booking form |

*Add your own screenshots after running the project.*

---

## Future Improvements

- Email notifications for appointment status changes
- Doctor availability time slots per day
- PDF export for reports
- Multi-language support (i18n)
- REST API for mobile app integration
- Two-factor authentication (2FA)
- Appointment reminders via SMS
- Medical records / prescription module
- Online payment integration

---

## License

This project is created for educational purposes as a university assignment.

---

## Author

University Project - Clinic Appointment Management System
