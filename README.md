# Itqan — Quran Learning & Memorization Platform
## خيركم من تعلم القرآن وعلمه

**Itqan (إتقان)** is a Quran learning and memorization platform designed to digitally manage Quran education centers and provide a modern mobile experience for students and teachers.

The system allows **users** to Quran memorization, revision, and evaluation are managed efficiently.

---

## ✨ Overview

Traditional Quran centers rely heavily on paper records and manual follow-ups.
This causes problems such as:

* Difficulty tracking memorization progress
* Administrative overload on secretaries
* No measurable performance indicators

**Itqan solves this by digitizing the entire center workflow.**

---

## 👥 System Roles

### Administration / Secretary

* Manage students & groups
* Send broadcast messages
* View attendance reports
* Manage teachers schedules

---

## 🧠 Core Features

* Student memorization tracking (by Surah & Ayah)
* Broadcast messaging
* Authentication via API tokens
* Password reset using OTP code
* Mobile-ready REST API
* Swagger API documentation

---

## 🏗 Architecture

The backend is built using a **RESTful API architecture** to support mobile applications.

**Pattern:**
Clean Architecture + Service Layer

**Key Concepts:**

* Stateless authentication
* Token based access
* Modular domain separation
* Mobile-first design

---

## ⚙️ Technology Stack

* PHP 8.4
* Laravel 12
* Laravel Sanctum (API Authentication)
* MySQL
* Swagger / OpenAPI Documentation
* Notifications System
* SMTP Email Service

---

## 🔐 Authentication

The system uses **Token-Based Authentication**:

* Each customer (student) logs in using email & password
* API returns a personal access token
* Token is used in all protected endpoints

```
Authorization: Bearer {token}
```

---

## 🔑 Password Reset (OTP Based)

Instead of email links, Itqan uses a **verification code system** suitable for mobile apps.

### Flow

1. User requests password reset
2. System generates a 6-digit verification code
3. Code is hashed and stored in `password_reset_tokens`
4. Code is sent via email
5. User submits code + new password
6. Password is updated securely

Security features:

* Token hashing
* Expiration time
* Single-use verification
* No sensitive data returned in API responses

---

## 📚 API Documentation

Swagger documentation is available at:

```
/api/documentation
```

You can authorize using:

```
Bearer YOUR_TOKEN
```

---

## 🚀 Installation

```bash
git clone https://github.com/your-repo/itqan.git
cd itqan

composer install
cp .env.example .env
php artisan key:generate
```

Configure database inside `.env`:

```
DB_DATABASE=itqan
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations:

```bash
php artisan migrate
```

Generate storage link:

```bash
php artisan storage:link
```

Start server:

```bash
php artisan serve
```

---

## 📧 Email Configuration

Update `.env`:

```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=YOUR_USERNAME
MAIL_PASSWORD=YOUR_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@itqan.com
MAIL_FROM_NAME="Itqan"
```

Then clear cache:

```bash
php artisan optimize:clear
```

---

## 🧪 Testing API

You can test the API using:

* Swagger UI
* Postman
* Mobile App

Protected endpoints require Authorization header:

```
Accept: application/json
Authorization: Bearer {token}
```

---

## 📊 Future Enhancements

* AI-assisted Tajweed correction
* Voice recitation analysis
* Automatic mistake detection
* Parent mobile application
* WhatsApp notifications
* Online classes integration

---

## 📜 License

This project is proprietary and developed for Quran educational centers.

---

## ❤️ Mission

**Itqan aims to make Quran education organized, measurable, and accessible using modern technology while preserving the traditional learning spirit.**
