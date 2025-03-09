# Task Manager - Laravel + Vue.js

A simple **Task Management App** built with **Laravel (Backend) & Vue.js (Frontend)**.

---

## 🚀 How to Install & Run

### Clone the Project  
```sh
git clone https://github.com/Ahmed-Raza-1010/task-manager.git
cd task-manager

-> Install Backend Dependencies (Laravel)

composer install
cp .env.example .env  # Copy environment file
php artisan key:generate

-> Run Migrations & Seed Database

php artisan migrate --seed

-> Install Frontend Dependencies (Vue.js)

npm install

php artisan serve  # Start Laravel backend
npm run dev        # Start Vue frontend

-> Default Admin Login
Email: admin@example.com
Password: admin123

-> Default User Login
Email: test@example.com
Password: user123