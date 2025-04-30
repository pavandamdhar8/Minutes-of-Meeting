# MOM Tracker – Meeting & Task Management System

A comprehensive Meeting and Task Management System built using **PHP**, **MySQL**, and **XAMPP**. MOM Tracker empowers **Admins** and **Employees** to efficiently manage meetings, assign and track tasks, mark attendance, share notes, and receive timely notifications via system alerts and email.

---

## 🔧 Key Features

### 🧑‍💼 User Role Management
- Supports **Admin** and **Employee** roles
- Role-based login and redirection to respective dashboards
- Secure session-based access control

### 📅 Meeting Management
- Create, edit, and delete meetings with **title**, **agenda**, and **scheduled time**
- Assign **multiple participants** to a meeting
- Upload and share meeting **notes and files** (PDFs, images)
- **Generate PDF reports** with participant attendance using TCPDF
- Schedule **email reminders** and integrate with **real-time calendar view**

### ✅ Task Management
- Create, assign, update, and delete tasks
- Set **due dates** and assign to specific employees
- Track progress by status: **Pending**, **In Progress**, **Completed**

### 👥 Attendance Tracking
- Admins can **manually mark attendance** after each meeting
- Stores attendance with **status: Present/Absent**
- Attendance data integrated into **PDF reports**

### 📝 Notes & File Upload
- Add detailed **meeting notes** and discussion points
- Upload **supporting documents** or images
- Share notes and files with employees post-meeting

### 🔐 Authentication & Authorization
- **Secure login system** with user role verification
- **Session management** to restrict unauthorized access

### 🔔 System Notifications
- Real-time alerts for **task assignments** and **meeting updates**
- Dashboard highlights upcoming and overdue tasks

### 📧 Email Notifications
- Automatically **send emails** to employees when a task is assigned or a meeting is scheduled
- **Upcoming meeting reminders** via email
- Configurable via **PHPMailer** or **native PHP `mail()`** function

### 📆 Real-Time Calendar Integration
- View all meetings and task deadlines in an **interactive calendar**
- Helps employees manage schedules and avoid overlaps
- Potential integration with tools like **FullCalendar.js**

---

## ⚙️ Requirements

- [XAMPP](https://www.apachefriends.org/) (Apache, PHP, MySQL)
- PHP 7.4+
- MySQL 5.7+
- PHPMailer (optional, for email notifications)
- Web browser (Chrome, Firefox, Edge, etc.)

---

## 🚀 Getting Started

1. Clone or download the repository.
2. Launch Apache and MySQL using **XAMPP Control Panel**.
3. Create a database and **import the `.sql` file** via phpMyAdmin.
4. Configure database credentials in `/inc/DB_connection.php`.
5. For email functionality:
   - Set up **PHPMailer** or configure SMTP in your email script.
   - Enter your email credentials in `/email/notify.php`.
6. Access the project via: `http://localhost/mom-tracker/`

---

## 📂 Project Structure

/app                -> Core business logic and form processing
/inc                -> Shared files (navbar, header, DB config)
/uploads            -> Uploaded notes and files
/email              -> Email handling scripts
/tcpdf              -> PDF generation library
index.php           -> Homepage
login.php           -> Login and authentication
dashboard.php       -> Role-based dashboards (Admin/Employee)
meetings.php        -> Meeting creation and management
tasks.php           -> Task assignment and tracking
calendar.php        -> Real-time calendar view (optional feature)
