# SpeedNews — Automotive News Web Application

A full-stack PHP/MySQL web application built for a university assignment.
Dark automotive theme, REST API integration (NHTSA), admin panel, and responsive design.

---

## Tech Stack

| Layer    | Technology |
|----------|-----------|
| Frontend | HTML5, CSS3 (Grid + Flexbox), Vanilla JavaScript |
| Backend  | PHP 8+ |
| Database | MySQL 5.7+ / MariaDB |
| APIs     | NHTSA vPIC API (free, no key) |
| Maps     | Google Maps Embed API |
| Server   | Apache via XAMPP |

---

## Requirements

- [XAMPP](https://www.apachefriends.org/) with **Apache** and **MySQL** running
- PHP 8.0 or higher (`password_hash`, `finfo`, named arguments)
- `allow_url_fopen = On` in `php.ini` (default in XAMPP) — needed for API calls
- `file_uploads = On` in `php.ini` (default in XAMPP) — needed for image uploads

---

## Setup Instructions

### 1 — Copy the project

Place the `speednews/` folder inside your XAMPP `htdocs` directory:

```
C:\xampp\htdocs\speednews\
```

### 2 — Start XAMPP

Open the **XAMPP Control Panel** and click **Start** for both:
- ✅ Apache
- ✅ MySQL

### 3 — Import the database

1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click **Import** in the top navigation bar
3. Click **Choose File** and select:
   ```
   C:\xampp\htdocs\speednews\database\speednews.sql
   ```
4. Click **Go** at the bottom of the page
5. You should see a success message — the `speednews` database is now created
   with all tables and sample data

> **Note:** The SQL file contains `CREATE DATABASE` and `USE speednews` statements,
> so no manual database creation is needed.

### 4 — Verify database credentials

Open `config/db.php` and confirm the settings match your XAMPP:

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'speednews');
define('DB_USER', 'root');
define('DB_PASS', '');        // XAMPP default: empty password
```

If you have set a MySQL root password, update `DB_PASS` accordingly.

### 5 — Create upload directories

The application needs two writable directories for uploaded images.
Create them if they do not already exist:

```
speednews/assets/uploads/          ← news article images
speednews/assets/images/gallery/   ← gallery photos
```

On Windows with XAMPP these folders are writable by default.
If you see permission errors, right-click the folder → Properties → Security
and give the Apache user (or Everyone) write access.

### 6 — Add placeholder images

The sample data references these image filenames. Add real photos or use
any freely licensed car images from [Unsplash](https://unsplash.com) or
[Pexels](https://www.pexels.com):

| Path | Used for |
|------|----------|
| `assets/images/hero-bg.jpg` | Homepage hero background (1920×1080 recommended) |
| `assets/images/editorial.jpg` | Homepage editorial section (4:3 ratio) |
| `assets/uploads/news1.jpg` … `news5.jpg` | Sample news article thumbnails |
| `assets/images/gallery/gallery1.jpg` … `gallery6.jpg` | Sample gallery photos |

### 7 — Open the site

Visit in your browser:

```
http://localhost/speednews/
```

---

## Default Admin Accounts

Both accounts use the password **`password`**

| Name | Email | Role |
|------|-------|------|
| Alex Turner | admin@speednews.com | admin |
| Maria Santos | editor@speednews.com | admin |

> **Important:** Change these passwords before any public deployment.
> Use the Admin → Users → Edit page or register a new admin account.

---

## Project Structure

```
speednews/
│
├── index.php               Homepage
├── news.php                News listing (paginated)
├── news-single.php         Single article view
├── gallery.php             Photo gallery with lightbox
├── about.php               About page with YouTube embed
├── contact.php             Contact form + Google Maps
├── api-nhtsa.php           NHTSA vehicle search (JSON)
├── api-vin.php             VIN decoder (JSON + XML)
├── register.php            User registration
├── login.php               User login
├── logout.php              Session destroy + redirect
│
├── config/
│   └── db.php              PDO database connection
│
├── includes/
│   ├── header.php          Shared HTML head + navigation
│   ├── footer.php          Shared footer + social icons
│   └── auth.php            Session helpers + access control
│
├── admin/
│   ├── index.php           Dashboard (stats + 5 tabs)
│   ├── users.php           User management (list/edit/delete)
│   ├── news-add.php        Add news article
│   ├── news-edit.php       Edit news article
│   ├── news-delete.php     Delete confirmation + action
│   └── gallery-add.php     Gallery upload + management
│
├── assets/
│   ├── css/
│   │   └── style.css       Main stylesheet (dark theme)
│   ├── js/
│   │   └── main.js         Vanilla JS (nav, lightbox, API)
│   ├── images/
│   │   ├── favicon.svg     Site favicon
│   │   ├── hero-bg.jpg     ← ADD THIS
│   │   ├── editorial.jpg   ← ADD THIS
│   │   └── gallery/        ← ADD gallery1–6.jpg HERE
│   └── uploads/            ← news images saved here (auto-created)
│
└── database/
    └── speednews.sql       Full DB schema + sample data
```

---

## NHTSA API Usage

No API key or registration is required.

| Page | Endpoint |
|------|----------|
| `api-nhtsa.php` | `https://api.nhtsa.gov/vehicles/GetModelsForMakeYear/make/{make}/modelYear/{year}` |
| `api-nhtsa.php` | `https://api.nhtsa.gov/SafetyRatings/modelyear/{year}/make/{make}/model/{model}` |
| `api-vin.php` | `https://vpic.nhtsa.dot.gov/api/vehicles/decodevin/{vin}?format=json` |
| `api-vin.php` | `https://vpic.nhtsa.dot.gov/api/vehicles/decodevin/{vin}?format=xml` |

API calls are made both **server-side** (PHP `file_get_contents`) and
**client-side** (JavaScript `fetch`) for demonstration purposes.

---

## Security Notes

- Passwords hashed with `password_hash()` using `PASSWORD_DEFAULT` (bcrypt)
- All DB queries use PDO prepared statements — no SQL injection possible
- File uploads validated by MIME type (`finfo`) — not by extension alone
- Session fixation prevented with `session_regenerate_id(true)` on login
- `htmlspecialchars()` used on all output — no XSS
- Admin pages protected by `require_admin()` — role checked server-side

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Blank page | Enable error display: add `ini_set('display_errors', 1);` to `config/db.php` temporarily |
| Database connection failed | Check XAMPP MySQL is running; verify credentials in `config/db.php` |
| Images not showing | Check file exists in `assets/uploads/` or `assets/images/gallery/` |
| API returns empty | Check `allow_url_fopen = On` in `php.ini`; restart Apache after changes |
| Upload fails | Check folder permissions on `assets/uploads/` and `assets/images/gallery/` |
| 404 on all pages | Ensure the project folder is named exactly `speednews` inside `htdocs` |

---

## Academic Declaration

This project was created for educational purposes as part of a university
web development assignment. All automotive content is fictional editorial copy
written for demonstration. Vehicle data is sourced from the public NHTSA API.
