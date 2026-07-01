 KusiNay

#Overview

KusiNay is a PHP-based community nutrition application built as a capstone project. It supports family profile management, nutrition assessment, meal planning, grocery list creation, pantry tracking, feeding program workflows, and market vendor order processing.

# Problem Statement

Communities need a more organized way to manage nutrition programs, track household health information, support meal planning, and coordinate feeding activities. KusiNay addresses this need by consolidating nutrition services, family support, and market vendor functionality into a single platform.

# Objectives

- Provide secure authentication and role-based access for administrators, nutrition officers, mothers, BNS staff, and market vendors.
- Allow data entry, family profile validation, and nutrition assessment reporting.
- Enable meal plan creation, grocery list generation, and pantry consumption tracking.
- Support feeding program scheduling, attendance tracking, QR scanning attendance, and recovery validation.
- Offer a simple vendor marketplace for product listings and order management.

# Target Users

- Barangay Nutrition Scholars (BNS staff)
- Nutrition Officers
- Mothers and family members
- Committee Chairs and Secretaries
- Market vendors
- System administrators

# Technology Stack

- PHP
- MySQL / MariaDB
- Apache (XAMPP recommended)
- Composer
- PHPMailer
- JavaScript, HTML, CSS

# Installation

1. Clone the repository or copy the project folder into your web root.
2. Install dependencies with Composer:

```bash
cd "c:\xampp\htdocs\KusiNay(Capstone)"
composer install
```

3. Import the database schema:

- Open phpMyAdmin or MySQL
- Create a database named `kusinay_db`
- Import `database/kusinay_db.sql`

4. Configure the database settings in `config/database.php`.

## Project Structure

- `app/controllers/` - controller classes
- `app/models/` - models and data logic
- `app/views/` - HTML views and templates
- `config/` - configuration files
- `core/` - shared helper classes
- `database/` - SQL schema and data exports
- `public/` - public assets
- `uploads/` - user-uploaded files

## Author
- Nancy C. Ongayo
- BSIT - Davao Central College
