# Content Approval System

A Role-Based Content Approval System built with Laravel 12. This application allows authors to submit posts and enables managers and admins to approve, reject, or delete them. It includes a frontend UI built with Blade and Bootstrap 5, as well as a fully documented REST API using Laravel Passport.

## Features
- **Role-Based Access Control**: 3 distinct roles (Author, Manager, Admin).
- **Post Management**: Authors can create and edit posts. Managers/Admins can review them.
- **Approval Workflow**: Pending posts can be Approved or Rejected (with a required reason).
- **Activity Logging**: All actions (create, update, approve, reject, delete) are automatically logged in the database.
- **RESTful API**: Secure API endpoints for all operations, authenticated via Laravel Passport.
- **Frontend UI**: Responsive Web Interface built with modern Bootstrap 5.

---

## 🚀 Quick Setup Guide

Follow these instructions to get the application running on your local machine.

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL or SQLite

### 1. Clone the Repository
```bash
git clone https://github.com/sachin-hadola/content_approval.git
cd content_approval
```

### 2. Install Dependencies
```bash
composer install
npm install && npm run build
```

### 3. Environment Setup
Copy the example environment file and generate your application encryption key:
```bash
cp .env.example .env
php artisan key:generate
```
*Configure your `.env` file with your database credentials (e.g., `DB_CONNECTION=sqlite` or `mysql`).*

### 4. Database Migration & Seeding
This will create the necessary tables and seed the database with test users:
```bash
php artisan migrate --seed
```

### 5. Install Laravel Passport
To enable API authentication, install Passport and generate the encryption keys and personal access client:
```bash
php artisan passport:install
php artisan passport:client --personal --name="Content Approval Token" --no-interaction
```

### 6. Start the Local Server
```bash
php artisan serve
```
The application will be accessible at `http://localhost:8000`.

---

## 👥 Test User Accounts

The database seeder automatically creates the following test accounts. The password for all accounts is `password`.

| Role    | Email                  | Password | Permissions |
|---------|------------------------|----------|-------------|
| Author  | `author@example.com`   | password | Create posts, Edit own pending/rejected posts, View own posts. |
| Manager | `manager@example.com`  | password | View all posts, Approve pending posts, Reject pending posts. |
| Admin   | `admin@example.com`    | password | View all posts, Approve posts, Reject posts, Delete any post. |

---

## 📚 API Documentation

A complete set of API endpoints has been built into the system. For full technical specifications, JSON payloads, and response examples, please see the [API Documentation](API_Documentation.md) file included in the root of this repository.

### Quick API Overview:
- `POST /api/login` - Authenticate & get Bearer Token
- `GET /api/posts` - List posts (Role-filtered)
- `POST /api/posts` - Create post (Authors only)
- `POST /api/posts/{id}/approve` - Approve post (Managers/Admins only)
- `POST /api/posts/{id}/reject` - Reject post (Managers/Admins only)
- `DELETE /api/posts/{id}` - Delete post (Admins only)

## License
Open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).