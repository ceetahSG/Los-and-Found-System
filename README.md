# Lost and Found System

A modern, responsive web application for reporting and managing lost and found items.

## Features

- ✅ User Authentication (Register/Login)
- ✅ Post Lost/Found Items
- ✅ Advanced Search & Filtering
- ✅ Smart Item Matching
- ✅ Direct Messaging Between Users
- ✅ User Dashboard
- ✅ Admin Panel for Moderation
- ✅ Responsive Design (Mobile, Tablet, Desktop)
- ✅ Security (CSRF, XSS Prevention, Password Hashing)

## Tech Stack

- **Frontend:** HTML5, CSS3, Tailwind CSS, JavaScript
- **Backend:** PHP 8.0+
- **Database:** MySQL 8.0+
- **Server:** Apache
- **Hosting:** Railway.app or local Apache/MySQL

## Local Development

### Requirements
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Composer (optional)

### Setup

1. Clone the repository
```bash
git clone https://github.com/ceetahSG/Lost-and-Found-System.git
cd Lost-and-Found-System
```

2. Create your local environment file
```bash
copy .env.example .env
```

3. Import the database schema into MySQL
- Create a database named `lost_and_found`
- Import `database.sql/database.sql`

4. Configure Apache for local access
- Place the project in your web server root, or point a virtual host to the project folder
- Make sure `mod_rewrite` is enabled if you want the friendly `/pages/...` URLs to work

5. Open the app locally
- `http://localhost/Lost-and-Found-System/`
- If you use a virtual host, open the host name you configured

### Local Database Settings

The app now falls back to `config/database.php` for local development. It reuses the shared MySQL bootstrap and defaults to:

- Host: `localhost`
- User: `root`
- Password: empty
- Database: `lost_and_found`

If your local MySQL credentials are different, set `DB_HOST`, `DB_USER`, `DB_PASSWORD`, and `DB_NAME` as environment variables or in your Apache/PHP environment.