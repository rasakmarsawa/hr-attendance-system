## 📋 HR Attendance System

A **Human Resources Attendance System** built with **Laravel 12**, providing secure and efficient management for employees, attendance tracking, and payroll.
It includes authentication, department handling, and PDF/Excel report exports — all wrapped in a clean, modern interface.

---

### 🖼️ Screenshots

| Dashboard                                      | Employee List                                      | Attendance Report                                              |
| ---------------------------------------------- | -------------------------------------------------- | -------------------------------------------------------------- |
|<img width="400" height="250" alt="Screenshot from 2025-10-18 19-06-15" src="https://github.com/user-attachments/assets/2f613776-1aa0-4883-8add-52fae7692e2e" />|<img width="400" height="250" alt="Screenshot from 2025-10-18 19-06-48" src="https://github.com/user-attachments/assets/dbb0c4f0-1556-45b4-857d-53525a1616b3" />|<img width="400" height="250" alt="Screenshot from 2025-10-18 19-07-08" src="https://github.com/user-attachments/assets/74cdb06e-09db-4f22-9959-0e6bf0dbeb04" />|

| Payroll                                    | Department Management                             |
| ------------------------------------------ | ------------------------------------------------- |
|<img width="400" height="250" alt="Screenshot from 2025-10-18 19-07-35" src="https://github.com/user-attachments/assets/afa31783-2b53-44c7-9c6b-64ec323d6617" />|<img width="400" height="250" alt="Screenshot from 2025-10-18 19-07-48" src="https://github.com/user-attachments/assets/439a1bd3-fb2b-4b5f-b486-095390c7c60d" />|

---

### 🚀 Features

* 🔐 **User Authentication & Roles**

  * Admin and Employee login via Laravel Breeze
  * Role-based middleware protection

* 🧑‍💼 **Employee Management**

  * Create, edit, and delete employee records
  * Department linkage and employee code generation

* 🕒 **Attendance Tracking**

  * Record daily presence and absences
  * View monthly attendance reports

* 💰 **Payroll Management**

  * Store, view, and calculate payroll data
  * Includes `department_name` field for clarity

* 📄 **Report Exporting**

  * Export **attendance and payroll** to **PDF** (Dompdf)
  * Export **Excel sheets** (Spatie Simple Excel)

* 🎨 **Modern UI**

  * TailwindCSS + Vite + Alpine.js frontend
  * Clean dashboard layout and responsive design

---

### 🧱 Tech Stack

| Layer        | Technology                    |
| ------------ | ----------------------------- |
| **Backend**  | Laravel 12 (PHP 8.2)          |
| **Frontend** | Tailwind CSS, Alpine.js, Vite |
| **Database** | MySQL                         |
| **Exports**  | Dompdf, Spatie Simple Excel   |
| **Auth**     | Laravel Breeze                |

---

### 🛠️ Installation Guide

#### 1. Clone Repository

```bash
git clone https://github.com/rasakmarsawa/hr-attendance-system.git
cd hr-attendance-system
```

#### 2. Install Dependencies

```bash
composer install
npm install
```

#### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

#### 4. Configure Database

Edit your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hr_system
DB_USERNAME=root
DB_PASSWORD=
```

#### 5. Run Migration and Seeder

```bash
php artisan migrate --seed
```

#### 6. Build Frontend

```bash
npm run dev
```

#### 7. Start Server

```bash
php artisan serve
```

Visit: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

Perfect 👌 — since your `entrypoint.sh` already handles migrations and starts PHP-FPM automatically, we’ll reflect that and include a clear **container architecture** section.

Here’s your revised and complete `README.md` Docker documentation (ready to commit):

---

### 🐳 Dockerized Laravel + Nginx + MySQL Setup

This project runs a Laravel application inside a Docker environment with Nginx, PHP-FPM, and MySQL.
The stack is fully automated — migrations run on startup and the PHP service starts automatically through the custom entrypoint script.

---

#### ⚙️ 1. Prerequisites

Make sure the following are installed on your machine:

* [Docker Engine](https://docs.docker.com/engine/install/)
* [Docker Compose](https://docs.docker.com/compose/install/)

---

#### ⚡ 2. Setup & Run

Copy environment file:

```bash
cp .env.example .env
```

Adjust `.env` as needed (database name, username, password, etc.).

Then build and start everything:

```bash
sudo docker compose up -d --build
```

That’s it — migrations will automatically run on container startup.

---

#### 🌐 3. Access Points

| Service     | URL / Host                                     | Notes                               |
| ----------- | ---------------------------------------------- | ----------------------------------- |
| Laravel App | [http://localhost:8000](http://localhost:8000) | Served via Nginx                    |
| MySQL DB    | `localhost:3307`                               | Connect using credentials in `.env` |
| PHP-FPM     | Internal only (`app` container)                | Handles PHP execution               |

---

#### 🧩 4. Container Architecture

```
+--------------------------+
|        nginx             |
|  (Port 8000 → 80 inside) |
|  → serves static files   |
|  → forwards PHP requests |
|    to app:9000 (PHP-FPM) |
+-----------▲--------------+
            |
            ▼
+--------------------------+
|          app             |
|  Laravel + PHP-FPM       |
|  Entrypoint:             |
|   • Wait for MySQL       |
|   • Run migrations       |
|   • Start PHP-FPM        |
|  Code mounted at /var/www|
+-----------▲--------------+
            |
            ▼
+--------------------------+
|          db              |
|  MySQL database          |
|  Persists data via       |
|  named volume `db_data`  |
+--------------------------+
```

---

#### 🧰 5. Useful Commands

**Rebuild containers (without cache):**

```bash
sudo docker compose build --no-cache
```

**Check logs (follow mode):**

```bash
sudo docker compose logs -f
```

**Stop and remove containers + volumes:**

```bash
sudo docker compose down -v
```

---

#### 📄 6. Notes

* The app runs migrations automatically on startup via `docker-entrypoint.sh`.
* Nginx serves the Laravel app using PHP-FPM.
* Data is persistent between rebuilds using Docker volumes.
* SSL setup (Let's Encrypt / Certbot) can be added later once deployment is confirmed stable.

---

### 🔐 Default Access (from Seeder)

| Role     | Email                                               | Password |
| -------- | --------------------------------------------------- | -------- |
| Admin    | admin@example.com| password |
| Employee | employee{number}@example.com| password |

---

### 📂 Project Structure

```
app/
 ├── Http/Controllers/
 │    ├── AttendanceController.php
 │    ├── DepartmentController.php
 │    ├── EmployeeController.php
 │    ├── PayrollController.php
 │    └── Auth/
 ├── Models/
 │    ├── Attendance.php
 │    ├── Department.php
 │    ├── Employee.php
 │    └── Payroll.php
resources/views/
 ├── attendance/
 ├── employees/
 ├── departments/
 ├── payroll/
 └── layouts/
routes/web.php
database/migrations/
```

---

### 🧾 Export Reports

You can export reports from the attendance or payroll views:

* **PDF Reports:** powered by `barryvdh/laravel-dompdf`
* **Excel Reports:** powered by `spatie/simple-excel`

---

### 🧑‍💻 Author

**Muhammad Yoga Affella Putra** — Laravel & PHP Developer  
Passionate about building efficient backend systems and clean web applications.

🌐 [GitHub: @rasakmarsawa](https://github.com/rasakmarsawa)
📧 [Contact via LinkedIn](www.linkedin.com/in/muhammad-yoga-affella-putra-a64774309)

---

### 📜 License

This project is open-source and available under the **MIT License**.
