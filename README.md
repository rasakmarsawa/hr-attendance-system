## 📋 HR Attendance System

A **Human Resources Attendance System** built with **Laravel 12**, providing secure and efficient management for employees, attendance tracking, and payroll.
It includes authentication, department handling, and PDF/Excel report exports — all wrapped in a clean, modern interface.

---

### 🖼️ Screenshots

| Dashboard                                                                                                              | Employee List                                                                                                          | Attendance Report                                                                                                      |
| ---------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------- |
| <img width="400" height="250" src="https://github.com/user-attachments/assets/2f613776-1aa0-4883-8add-52fae7692e2e" /> | <img width="400" height="250" src="https://github.com/user-attachments/assets/dbb0c4f0-1556-45b4-857d-53525a1616b3" /> | <img width="400" height="250" src="https://github.com/user-attachments/assets/74cdb06e-09db-4f22-9959-0e6bf0dbeb04" /> |

| Payroll                                                                                                                | Department Management                                                                                                  |
| ---------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------- |
| <img width="400" height="250" src="https://github.com/user-attachments/assets/afa31783-2b53-44c7-9c6b-64ec323d6617" /> | <img width="400" height="250" src="https://github.com/user-attachments/assets/439a1bd3-fb2b-4b5f-b486-095390c7c60d" /> |

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

### 🐳 Dockerized Setup

This project runs inside Docker containers with Nginx, PHP-FPM, and MySQL. Migrations run automatically at startup.

#### ⚙️ Prerequisites

* Git
* Docker Engine
* Docker Compose v2

---

#### 🚀 Installation & Run

1. **Clone repository**

```bash
git clone https://github.com/rasakmarsawa/hr-attendance-system.git
cd hr-attendance-system
```

2. **Copy environment file**

```bash
cp .env.example .env
```

3. **Build containers**

```bash
docker compose build
```

4. **Start application**

```bash
docker compose up -d
```

Visit: [http://localhost:8000](http://localhost:8000)

---

#### 🛑 Stop Containers

```bash
docker compose down
```

**Optional:** Remove all containers **and volumes**:

```bash
docker compose down -v
```

---

### 🌐 Access Points

| Service     | URL / Host                                     | Notes                               |
| ----------- | ---------------------------------------------- | ----------------------------------- |
| Laravel App | [http://localhost:8000](http://localhost:8000) | Served via Nginx                    |
| MySQL DB    | `localhost:3307`                               | Connect using credentials in `.env` |
| PHP-FPM     | Internal only (`app` container)                | Handles PHP execution               |

---

### 🧩 Container Architecture

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

### 🔐 Default Access (Seeder)

| Role     | Email                                         | Password |
| -------- | --------------------------------------------- | -------- |
| Admin    | [admin@example.com](mailto:admin@example.com) | password |
| Employee | employee{number}@example.com                  | password |

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

* **PDF Reports:** `barryvdh/laravel-dompdf`
* **Excel Reports:** `spatie/simple-excel`

---

### 🧑‍💻 Author

**Muhammad Yoga Affella Putra** — Laravel & PHP Developer
🌐 [GitHub: @rasakmarsawa](https://github.com/rasakmarsawa)
📧 [LinkedIn](www.linkedin.com/in/muhammad-yoga-affella-putra-a64774309)

---

### 📜 License

MIT License — open-source and free to use.

---
