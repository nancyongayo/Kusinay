# Kusinay - Barangay Nutrition Surveillance System

## Overview

A web-based system for managing nutrition and health data at the barangay level. It helps Barangay Nutrition Scholars (BNS) track family health profiles, conduct nutrition assessments, and generate reports. Mothers can register their families and view health records online.

## Problem Statement

Barangay health workers currently use paper-based records to track nutrition data, which leads to:
- Lost or damaged records
- Time-consuming manual report generation
- Difficulty accessing family health information
- Inconsistent data across different forms

## Objectives

- Digitize nutrition monitoring and family health records
- Allow mothers to register and view their family profiles online
- Automate report generation for BNS workers
- Enable nutrition officers to validate barangay reports efficiently
- Track children's growth, maternal health, and senior nutrition status

## Target Users

**Barangay Nutrition Scholar (BNS)**
- Community health worker who conducts weighing operations and nutrition assessments
- Needs to register residents, track health data, and generate monthly reports

**Mother/Guardian**
- Parent who wants to register family members and view health records
- Needs easy access to children's growth monitoring results and nutrition education sessions

**Nutrition Officer**
- Municipal-level supervisor who validates reports from multiple barangays
- Needs to review and approve barangay submissions

**Administrator**
- Manages user accounts and monitors system security

## Features

- Family profile registration and management
- Nutrition assessment with BMI and Z-score calculation
- Nutrition education session scheduling and attendance
- Accomplishment report generation
- Masterlist reports (pregnant, lactating, seniors)
- OPT monitoring for children 0-12 years
- Report validation workflow
- Email notifications with OTP verification

## Technology Stack

- PHP (MVC architecture)
- MySQL
- Bootstrap, HTML, CSS, JavaScript
- PHPMailer (email with OAuth2)

## Installation

1. Clone the repository
2. Create a MySQL database and import `database/kusinay_db.sql`
3. Run migration scripts in `database/migrate_*.sql`
4. Configure database credentials in `config/database.php`
5. Configure Google OAuth2 in `config/google.php`
6. Access via web browser

## Project Structure

```
├── app/
│   ├── controllers/     # Application logic
│   ├── models/          # Database models
│   └── views/           # UI templates
├── config/              # Configuration files
├── core/                # Core classes (Mailer, Security, etc.)
├── database/            # SQL schema and migrations
└── index.php            # Entry point
```

## License

[Specify your license here]

---

Built to support the Philippine Barangay Nutrition Surveillance Program.
