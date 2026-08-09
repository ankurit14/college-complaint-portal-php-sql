# 🎓 College Complaint Management Portal

A modern, role-based web application built with **PHP & MySQL** that streamlines student grievance management. It allows students to lodge complaints, upload supporting documents, and track resolution status in real-time, while empowering administrators to review, assign, and resolve issues efficiently.

---

## ✨ Key Features

### 👨‍🎓 Student & Faculty Module
- **User Authentication:** Secure registration and login with roll number and password hashing (`bcrypt`).
- **Dashboard:** Overview of total complaints, pending issues, in-progress reviews, and resolved grievances.
- **Lodge Complaint:** Categorized complaint submission with subject, detailed description, and file attachment support (Images, PDF, DOCX).
- **Complaint History & Tracking:** Real-time status badges (`Pending`, `In Progress`, `Closed`) with admin remarks and timestamps.
- **Profile Management:** View and update personal profile details.

### 🛡️ Admin Module
- **Admin Dashboard:** High-level analytics with status breakdown (Pending vs In-Progress vs Closed).
- **Complaint Management:** Detailed view of student complaints, download attachments, update status, and attach official remarks/action plans.
- **Category Management:** Add, edit, or remove grievance categories (e.g., Academic, Hostel & Mess, Infrastructure, Fees).
- **User Directory:** View registered students and faculty records.

---

## 🛠️ Tech Stack

- **Backend:** PHP 7.4+ / PHP 8.x
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, Modern Vanilla CSS3 (Custom Responsive Layouts), FontAwesome Icons
- **Server Environment:** Apache (XAMPP / WAMP / LAMP)

---

## 📁 Project Structure

```text
collegeComplaintPortal/
├── admin/                     # Admin Portal
│   ├── dashboard.php          # Admin statistics & overview
│   ├── login.php              # Admin authentication
│   ├── manage-complaints.php  # View & filter grievances
│   ├── complaint-details.php  # Triage, remarks & status update
│   ├── manage-categories.php  # CRUD for grievance categories
│   ├── manage-users.php       # Registered students list
│   ├── header.php             # Admin navbar & sidebar
│   └── footer.php
├── student/                   # Student Portal
│   ├── dashboard.php          # Student dashboard & stats
│   ├── lodge-complaint.php    # Submit new grievance form
│   ├── complaint-history.php  # All submitted complaints
│   ├── complaint-details.php  # Single complaint timeline
│   ├── profile.php            # Student profile details
│   ├── header.php             # Student navbar
│   └── footer.php
├── css/
│   └── style.css              # Global custom CSS styling
├── uploads/                   # Uploaded attachments directory
├── config.php                 # MySQL database connection
├── index.php                  # Public landing page
├── login.php                  # Student login page
├── register.php               # Student registration page
├── logout.php                 # Session destroy handler
├── schema.sql                 # Complete DB schema & seed data
├── .gitignore                 # Git ignore configuration
└── README.md                  # Project documentation
```

---

## 🚀 Installation & Setup Guide

### 1. Prerequisites
- Install [XAMPP](https://www.apachefriends.org/) (with Apache & MySQL enabled).

### 2. Clone the Repository
Place the project inside your XAMPP `htdocs` directory:
```bash
cd C:/xampp/htdocs
git clone https://github.com/<your-username>/collegeComplaintPortal.git
```

### 3. Setup Database
1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Create a new database named `college_complaint_db`.
3. Import the [`schema.sql`](schema.sql) file located in the root folder.

### 4. Configure Database Connection
Verify credentials in [`config.php`](config.php):
```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'college_complaint_db');
```

### 5. Run the Application
Open your browser and visit:
```text
http://localhost/collegeComplaintPortal/
```

---

## 🔐 Default Admin Credentials

- **Admin Login URL:** `http://localhost/collegeComplaintPortal/admin/login.php`
- **Username:** `admin`
- **Password:** `admin123`

---

## 🌿 Git Branching Workflow

This project follows a structured feature-branch workflow where each module is developed in an isolated branch and merged directly into `main`:

| Branch | Description |
| :--- | :--- |
| `main` | Production-ready stable release |
| `feature/database-setup` | Database schemas and connection configs |
| `feature/auth-and-landing` | Landing page, student authentication & global styles |
| `feature/student-module` | Student dashboard, grievance lodging & tracking |
| `feature/admin-module` | Admin panel, status triage & user management |
| `feature/documentation` | Readme documentation and setup guides |

---

## 📄 License
This project is open-source and available under the [MIT License](LICENSE).
