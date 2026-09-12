# 🏀 H-COURT — Sports Court Booking System

<p align="left">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Badge">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP Badge">
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS Badge">
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript Badge">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL Badge">
  <img src="https://img.shields.io/badge/Figma-F24E1E?style=for-the-badge&logo=figma&logoColor=white" alt="Figma Badge">
</p>

**H-COURT** is a web-based sports court reservation platform developed as a school project. Built on top of the **Laravel** framework, H-COURT aims to streamline and simplify the process of searching, scheduling, and booking sports courts (such as badminton, futsal, and basketball) with real-time availability checks and an intuitive user interface.

---

## ✨ Features

- **🏟️ Court Directory & Search:** Browse available courts with details on pricing, location, facility specs, and images.
- **📅 Real-Time Availability & Booking:** Select dates and time slots with automated validation to prevent double-booking.
- **👤 User Authentication & Profiles:** Register, log in, manage personal account information, and track booking history.
- **⚙️ Admin Dashboard:** Management portal for court owners/admins to manage listings, schedule slots, and oversee bookings.
- **📱 Responsive Layout:** Styled with Tailwind CSS to provide a clean, fast, and mobile-friendly user experience.

---

## 🛠️ Tech Stack & Design Tools

| Category | Technology / Tool |
| :--- | :--- |
| **Framework** | [Laravel](https://laravel.com/) (v10+) |
| **Backend Language** | PHP ($\ge$ 8.2) |
| **Frontend** | HTML5, Tailwind CSS, JavaScript (ES6+) |
| **UI/UX Design** | [Figma](https://www.figma.com/) |
| **Database** | MySQL |
| **Asset Bundler** | Vite |

---

## 📋 Prerequisites

Make sure you have the following installed on your local machine before setting up the project:

- [PHP](https://www.php.net/) ($\ge$ 8.2 recommended)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) ($\ge$ 18.x recommended) & npm
- [MySQL Database](https://www.mysql.com/) (or XAMPP / Laragon / TablePlus)

---

## 🚀 Installation & Setup

Follow these steps to get the project up and running locally:

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/h-court.git
cd h-court
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Frontend Dependencies
```bash
npm install
```

### 4. Environment Configuration
Duplicate the example environment file and create your local `.env`:
```bash
cp .env.example .env
```

Open `.env` in your code editor and configure your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=h_court_db
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Run Database Migrations & Seeders
```bash
# Create the database 'h_court_db' in MySQL first, then run:
php artisan migrate

# Optional: Seed initial demo data (if seeders exist)
php artisan migrate --seed
```

### 7. Link Storage (for uploaded media/court images)
```bash
php artisan storage:link
```

---

## ⚡ Running the Application

To run the development server, you will need two terminal windows:

**Terminal 1 — Laravel Development Server:**
```bash
php artisan serve
```
> The application will be accessible at: `http://127.0.0.1:8000`

**Terminal 2 — Vite Asset Compiler:**
```bash
npm run dev
```

---

## 📁 Project Structure Overview

```text
h-court/
├── app/
│   ├── Http/Controllers/     # Application logic (Booking, Court, Auth controllers)
│   └── Models/               # Eloquent ORM Models (User, Court, Booking, etc.)
├── database/
│   ├── migrations/           # Database structure definitions
│   └── seeders/              # Initial sample data seeders
├── public/                   # Entry point (index.php) and compiled assets
├── resources/
│   ├── css/                  # Custom CSS & Tailwind setup
│   ├── js/                   # Frontend JavaScript files
│   └── views/                # Blade templating views
├── routes/
│   └── web.php               # Application web routes
└── .env.example              # Sample environment configuration file
```

---

## 👥 Development Team (Group 3)

| Name | Role |
| :--- | :--- |
| **Marvin Arif Pratama** | Team Leader & Full-Stack Developer |
| **Axel Lucius Efendi** | Front-End Developer |
| **Bryan Stevent** | UI/UX Designer |
| **Harry Wang** | UI/UX Designer |

---

## 📄 License

This project is open-source and created for educational purposes as part of a school coursework assignment.
