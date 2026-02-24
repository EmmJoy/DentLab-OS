<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12" />
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+" />
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <img src="https://img.shields.io/badge/Bootstrap-5-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap 5" />
  <img src="https://img.shields.io/badge/Vite-7-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite" />
</p>

<h1 align="center">🦷 SmileFlow</h1>
<p align="center">
  <strong>Dental Laboratory Management System</strong><br />
  <em>Lab workflow, one smile at a time.</em>
</p>

<p align="center">
  A full-featured internal management system for dental laboratories — patient records, inventory, payments, payment plans, production steps, and product requests. Built for <strong>Smile Care Limited</strong> with role-based access for <strong>Admin</strong> and <strong>Lab Technician</strong>.
</p>

---

## 📋 Table of Contents

- [Features](#-features)
- [Screenshots](#-screenshots)
- [Tech Stack](#-tech-stack)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Running the Application](#-running-the-application)
- [Project Structure](#-project-structure)
- [Documentation](#-documentation)
- [License](#-license)

---

## ✨ Features

| Area | Description |
|------|-------------|
| **👤 Patient Management** | Full CRUD with unique **Predict3DId**; shortlists (last 15 days); Excel export (all, last month, date range). |
| **📦 Products & Inventory** | Admin: products CRUD; Lab: inventory CRUD and product requests (approve/reject workflow). |
| **🛒 Cart & Orders** | Add/update/remove items, confirm orders; separate flows for Admin and Lab with confirmed-orders list. |
| **💰 Payments** | Record payments; payment plans and installments by patient; shortlists and Excel exports. |
| **⚙️ Production** | Production steps and case tracking (Upper/Lower) linked to **Predict3DId**. |
| **👥 Lab Technicians** | Admin-only CRUD for lab technician accounts. |
| **📁 File Handling** | Storage proxy for uploads when symlink is unavailable (e.g. XAMPP/Windows). |

---

## 📸 Screenshots

_Add screenshots of your dashboard, patient list, or cart here for a more attractive README._

```
<!-- Example: ![Dashboard](screenshots/dashboard.png) -->
```

---

## 🛠 Tech Stack

| Layer | Technology |
|-------|-------------|
| **Backend** | PHP 8.2+, Laravel 12 |
| **Database** | MySQL |
| **Frontend** | Blade, Bootstrap 5, Sass, Vite 7 |
| **Auth** | Laravel UI (session-based), role-based middleware |
| **Excel** | Maatwebsite Excel, PhpSpreadsheet |
| **Dev** | Laravel Pail, Pint, PHPUnit, Sail |

---

## 📌 Requirements

- **PHP** ≥ 8.2 (with extensions: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)
- **Composer** 2.x
- **Node.js** 18+ & **npm**
- **MySQL** 8.x (or MariaDB)
- **XAMPP** (optional, for local Windows setup)

---

## 🚀 Installation

### 1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/SmileFlow.git
cd SmileFlow
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure database

Edit `.env` and set your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=Smile_care_Limited
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 6. Run migrations

```bash
php artisan migrate
```

### 7. (Optional) Create storage link

```bash
php artisan storage:link
```

On Windows/XAMPP, if the link fails, the app can serve files via `/storage/{path}` and `/files/{path}`.

### 8. Build frontend assets

**Development:**

```bash
npm run dev
```

**Production:**

```bash
npm run build
```

---

## ⚙️ Configuration

| Variable | Description |
|----------|-------------|
| `APP_NAME` | Application name (e.g. `SmileFlow`) |
| `APP_URL` | Base URL (e.g. `http://localhost:8000`) |
| `DB_*` | MySQL connection settings |
| `SESSION_DRIVER` | `database` (default) or `file` |

---

## ▶️ Running the Application

**Option A — All-in-one (recommended for development)**

Runs PHP server, queue worker, Laravel Pail, and Vite together:

```bash
composer dev
```

**Option B — Manual**

```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite
npm run dev
```

Then open **http://localhost:8000** and log in. Create an admin user via tinker or a seeder if needed.

---

## 📁 Project Structure

```
├── app/
│   ├── Exports/          # Excel exports (Patients, Payments)
│   ├── Http/Controllers/ # Admin, Lab, Auth, Cart controllers
│   ├── Http/Middleware/   # Admin & Lab Technician guards
│   └── Models/           # User, Patient, Payment, Cart, Product, etc.
├── config/               # Laravel config
├── database/migrations/  # Schema (patients, payments, carts, etc.)
├── docs/                 # Smile Care Limited documentation
├── resources/views/      # Blade templates (admin/, lab/, auth/, layouts/)
├── routes/web.php        # Web routes (admin.*, lab.*)
└── public/               # Entry point, built assets
```

---

## 📚 Documentation

Detailed feature and API notes are in:

- **`docs/SmileCareLimited_Documentation.md`**

---

## 📄 License

This project is proprietary to **Smile Care Limited**. All rights reserved.

---

<p align="center">
  <strong>SmileFlow</strong> — Dental Lab Management System<br />
  Built with Laravel · Made for Smile Care Limited
</p>
